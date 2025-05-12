<?php

namespace Tests\Feature;

use App\Models\PurchaseRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Setup roles for testing.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Create roles
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'requestor', 'guard_name' => 'web']);
        Role::create(['name' => 'approver', 'guard_name' => 'web']);
        Role::create(['name' => 'procurement_officer', 'guard_name' => 'web']);
    }
    
    /**
     * Test that unauthenticated users cannot access purchase requests.
     */
    public function test_unauthenticated_users_cannot_access_purchase_requests()
    {
        $response = $this->get('/purchase-requests');
        $response->assertRedirect('/login');
    }
    
    /**
     * Test that authenticated users can view their purchase requests.
     */
    public function test_authenticated_users_can_view_their_purchase_requests()
    {
        $user = User::factory()->create();
        $user->assignRole('requestor');
        
        $response = $this->actingAs($user)->get('/purchase-requests');
        $response->assertStatus(200);
    }
    
    /**
     * Test that users can create purchase requests.
     */
    public function test_users_can_create_purchase_requests()
    {
        $user = User::factory()->create();
        $user->assignRole('requestor');
        
        $response = $this->actingAs($user)->get('/purchase-requests/create');
        $response->assertStatus(200);
        
        $formData = [
            'title' => 'Test Purchase Request',
            'description' => 'This is a test purchase request',
            'department' => 'IT Department',
            'estimated_amount' => 5000,
        ];
        
        $response = $this->actingAs($user)->post('/purchase-requests', $formData);
        $response->assertRedirect();
        
        $this->assertDatabaseHas('purchase_requests', [
            'title' => 'Test Purchase Request',
            'user_id' => $user->id,
        ]);
    }
    
    /**
     * Test form validation for purchase requests.
     */
    public function test_purchase_request_validation()
    {
        $user = User::factory()->create();
        $user->assignRole('requestor');
        
        $invalidFormData = [
            'title' => '', // Required field is empty
            'estimated_amount' => 'not-a-number', // Not a number
        ];
        
        $response = $this->actingAs($user)->post('/purchase-requests', $invalidFormData);
        $response->assertSessionHasErrors(['title', 'estimated_amount']);
    }
    
    /**
     * Test that admin users can view all purchase requests.
     */
    public function test_admin_can_view_all_purchase_requests()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        $requestor = User::factory()->create();
        $requestor->assignRole('requestor');
        
        // Create purchase requests
        $prForAdmin = PurchaseRequest::factory()->create(['user_id' => $admin->id]);
        $prForRequestor = PurchaseRequest::factory()->create(['user_id' => $requestor->id]);
        
        $response = $this->actingAs($admin)->get('/purchase-requests');
        $response->assertStatus(200);
        $response->assertSee($prForAdmin->title);
        $response->assertSee($prForRequestor->title);
    }
    
    /**
     * Test that normal users can only view their own purchase requests.
     */
    public function test_users_can_only_view_their_own_purchase_requests()
    {
        $user1 = User::factory()->create();
        $user1->assignRole('requestor');
        
        $user2 = User::factory()->create();
        $user2->assignRole('requestor');
        
        // Create purchase requests for both users
        $prForUser1 = PurchaseRequest::factory()->create(['user_id' => $user1->id]);
        $prForUser2 = PurchaseRequest::factory()->create(['user_id' => $user2->id]);
        
        // User1 should only see their own request
        $response = $this->actingAs($user1)->get('/purchase-requests');
        $response->assertStatus(200);
        $response->assertSee($prForUser1->title);
        $response->assertDontSee($prForUser2->title);
    }
    
    /**
     * Test that approvers can see submitted purchase requests.
     */
    public function test_approvers_can_see_submitted_purchase_requests()
    {
        $approver = User::factory()->create();
        $approver->assignRole('approver');
        
        $requestor = User::factory()->create();
        $requestor->assignRole('requestor');
        
        // Create purchase requests in different statuses
        $draftPR = PurchaseRequest::factory()->draft()->create(['user_id' => $requestor->id]);
        $submittedPR = PurchaseRequest::factory()->submitted()->create(['user_id' => $requestor->id]);
        
        $response = $this->actingAs($approver)->get('/purchase-requests');
        $response->assertStatus(200);
        $response->assertSee($submittedPR->title);
        $response->assertDontSee($draftPR->title);
    }
    
    /**
     * Test purchase request approval process.
     */
    public function test_approvers_can_approve_purchase_requests()
    {
        $approver = User::factory()->create();
        $approver->assignRole('approver');
        
        $requestor = User::factory()->create();
        $requestor->assignRole('requestor');
        
        $submittedPR = PurchaseRequest::factory()->submitted()->create(['user_id' => $requestor->id]);
        
        $response = $this->actingAs($approver)
            ->post("/purchase-requests/{$submittedPR->id}/process-approval", ['action' => 'approve']);
        
        $response->assertRedirect();
        
        $this->assertDatabaseHas('purchase_requests', [
            'id' => $submittedPR->id,
            'status' => 'approved',
        ]);
    }
} 