<?php

namespace Tests\Unit;

use App\Models\PurchaseRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test PR number generation function.
     */
    public function test_pr_number_generation()
    {
        // Generate a PR number
        $prNumber = PurchaseRequest::generatePRNumber();

        // Check PR number format (should be PR-YYYY-MM-XXXX)
        $this->assertMatchesRegularExpression('/^PR-\d{4}-\d{2}-\d{4,}$/', $prNumber);
        
        // Current year should be in the PR number
        $currentYear = date('Y');
        $this->assertStringContainsString($currentYear, $prNumber);
        
        // Current month should be in the PR number
        $currentMonth = date('m');
        $this->assertStringContainsString($currentMonth, $prNumber);
    }

    /**
     * Test that PR belongs to a user.
     */
    public function test_purchase_request_belongs_to_user()
    {
        // Create a user
        $user = User::factory()->create();
        
        // Create a purchase request
        $purchaseRequest = PurchaseRequest::factory()->create([
            'user_id' => $user->id
        ]);
        
        // Test the relationship
        $this->assertInstanceOf(User::class, $purchaseRequest->user);
        $this->assertEquals($user->id, $purchaseRequest->user->id);
    }

    /**
     * Test purchase request statuses.
     */
    public function test_purchase_request_has_valid_status()
    {
        // Create a purchase request with draft status
        $draftPR = PurchaseRequest::factory()->create([
            'status' => 'draft'
        ]);
        
        // Test that the status is correctly set
        $this->assertEquals('draft', $draftPR->status);
        
        // Valid statuses should be: draft, submitted, approved, rejected
        $validStatuses = ['draft', 'submitted', 'approved', 'rejected'];
        $this->assertContains($draftPR->status, $validStatuses);
    }
} 