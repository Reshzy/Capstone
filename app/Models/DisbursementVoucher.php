<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisbursementVoucher extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'purchase_order_id',
        'created_by',
        'dv_number',
        'dv_date',
        'total_amount',
        'payee',
        'particulars',
        'payment_method',
        'check_number',
        'check_date',
        'document_path',
        'status',
        'approved_by',
        'approved_at',
        'paid_by',
        'paid_at',
        'remarks',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'dv_date' => 'date',
        'total_amount' => 'decimal:2',
        'check_date' => 'date',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /**
     * Generate a unique DV number.
     *
     * @return string
     */
    public static function generateDVNumber(): string
    {
        $latestDV = self::latest()->first();
        $year = date('Y');
        $number = $latestDV ? intval(substr($latestDV->dv_number, -5)) + 1 : 1;
        
        return 'DV-' . $year . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get the purchase order associated with this disbursement voucher.
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Get the user who created the disbursement voucher.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved the disbursement voucher.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who paid the disbursement voucher.
     */
    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
