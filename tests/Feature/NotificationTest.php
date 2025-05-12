<?php

namespace Tests\Feature;

use App\Models\PurchaseRequest;
use App\Models\User;
use App\Notifications\PurchaseRequestSubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;
    
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
     * Test that purchase request submission sends notifications.
     */
    public function test_purchase_request_submission_sends_notifications()
    {
        Notification::fake();
        
        $requestor = User::factory()->create();
        $requestor->assignRole('requestor');
        
        $approver = User::factory()->create();
        $approver->assignRole('approver');
        
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        $purchaseRequest = PurchaseRequest::factory()->draft()->create(['user_id' => $requestor->id]);
        
        // Simulate PR submission
        $this->actingAs($requestor)->post("/purchase-requests/{$purchaseRequest->id}/submit");
        
        // Verify notifications were sent
        Notification::assertSentTo(
            [$approver, $admin],
            PurchaseRequestSubmitted::class,
            function ($notification, $channels) use ($purchaseRequest) {
                return $notification->purchaseRequest->id === $purchaseRequest->id;
            }
        );
    }
    
    /**
     * Test that users can view their notifications.
     */
    public function test_users_can_view_their_notifications()
    {
        $user = User::factory()->create();
        $user->assignRole('requestor');
        
        // Create a notification in the database
        $notification = DatabaseNotification::create([
            'id' => '123e4567-e89b-12d3-a456-426614174000',
            'type' => 'App\\Notifications\\PurchaseRequestApproved',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => $user->id,
            'data' => [
                'purchase_request_id' => 1,
                'pr_number' => 'PR-2023-05-0001',
                'title' => 'Test Purchase Request',
                'approver' => 'Admin User',
                'message' => 'Your purchase request has been approved',
                'action_url' => '/purchase-requests/1',
            ],
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $response = $this->actingAs($user)->get('/notifications');
        $response->assertStatus(200);
        $response->assertSee('Your purchase request has been approved');
    }
    
    /**
     * Test marking notifications as read.
     */
    public function test_users_can_mark_notifications_as_read()
    {
        $user = User::factory()->create();
        $user->assignRole('requestor');
        
        // Create a notification in the database
        $notification = DatabaseNotification::create([
            'id' => '123e4567-e89b-12d3-a456-426614174000',
            'type' => 'App\\Notifications\\PurchaseRequestApproved',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => $user->id,
            'data' => [
                'purchase_request_id' => 1,
                'pr_number' => 'PR-2023-05-0001',
                'title' => 'Test Purchase Request',
                'approver' => 'Admin User',
                'message' => 'Your purchase request has been approved',
                'action_url' => '/purchase-requests/1',
            ],
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Mark as read
        $response = $this->actingAs($user)
            ->post("/notifications/{$notification->id}/mark-as-read");
        
        $response->assertRedirect();
        
        // Check if notification is marked as read
        $this->assertNotNull($notification->fresh()->read_at);
    }
    
    /**
     * Test users can mark all notifications as read.
     */
    public function test_users_can_mark_all_notifications_as_read()
    {
        $user = User::factory()->create();
        $user->assignRole('requestor');
        
        // Create multiple notifications
        for ($i = 1; $i <= 3; $i++) {
            DatabaseNotification::create([
                'id' => '123e4567-e89b-12d3-a456-42661417400' . $i,
                'type' => 'App\\Notifications\\PurchaseRequestApproved',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $user->id,
                'data' => [
                    'message' => "Test notification {$i}",
                    'action_url' => '/purchase-requests/1',
                ],
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Mark all as read
        $response = $this->actingAs($user)
            ->post('/notifications/mark-all-read');
        
        $response->assertRedirect();
        
        // Check if all notifications are marked as read
        $unreadCount = $user->unreadNotifications()->count();
        $this->assertEquals(0, $unreadCount);
    }
} 