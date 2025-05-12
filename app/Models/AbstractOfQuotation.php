<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbstractOfQuotation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'request_for_quotation_id',
        'created_by',
        'awarded_supplier_id',
        'aoq_number',
        'aoq_date',
        'total_amount',
        'status',
        'remarks',
        'approved_by',
        'approved_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'aoq_date' => 'date',
        'total_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    /**
     * Generate a unique AOQ number.
     *
     * @return string
     */
    public static function generateAOQNumber(): string
    {
        $latestAOQ = self::latest()->first();
        $year = date('Y');
        $number = $latestAOQ ? intval(substr($latestAOQ->aoq_number, -5)) + 1 : 1;
        
        return 'AOQ-' . $year . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get the RFQ associated with this AOQ.
     */
    public function requestForQuotation(): BelongsTo
    {
        return $this->belongsTo(RequestForQuotation::class);
    }

    /**
     * Get the user who created the AOQ.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved the AOQ.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the awarded supplier.
     */
    public function awardedSupplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'awarded_supplier_id');
    }
}
