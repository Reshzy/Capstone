<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PurchaseRequest extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'pr_number',
        'title',
        'description',
        'department',
        'estimated_amount',
        'status',
        'document_path',
        'rejection_reason',
        'approver_id',
        'approved_at',
    ];
    
    protected $casts = [
        'estimated_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
    
    /**
     * Get the budget approval for this purchase request.
     */
    public function budgetApproval(): HasOne
    {
        return $this->hasOne(BudgetApproval::class);
    }
    
    public static function generatePRNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $lastRecord = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
            
        $sequence = $lastRecord ? intval(substr($lastRecord->pr_number, -4)) + 1 : 1;
        
        return "PR-{$year}{$month}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
