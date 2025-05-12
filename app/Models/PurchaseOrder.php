<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PurchaseOrder extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'abstract_of_quotation_id',
        'supplier_quotation_id',
        'created_by',
        'po_number',
        'po_date',
        'total_amount',
        'delivery_location',
        'delivery_days',
        'status',
        'document_path',
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
        'po_date' => 'date',
        'total_amount' => 'decimal:2',
        'delivery_days' => 'integer',
        'approved_at' => 'datetime',
    ];

    /**
     * Generate a unique PO number.
     *
     * @return string
     */
    public static function generatePONumber(): string
    {
        $latestPO = self::latest()->first();
        $year = date('Y');
        $number = $latestPO ? intval(substr($latestPO->po_number, -5)) + 1 : 1;
        
        return 'PO-' . $year . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get the abstract of quotation associated with this purchase order.
     */
    public function abstractOfQuotation(): BelongsTo
    {
        return $this->belongsTo(AbstractOfQuotation::class);
    }

    /**
     * Get the supplier quotation associated with this purchase order.
     */
    public function supplierQuotation(): BelongsTo
    {
        return $this->belongsTo(SupplierQuotation::class);
    }

    /**
     * Get the user who created the purchase order.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved the purchase order.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
    /**
     * Get the disbursement voucher for this purchase order.
     */
    public function disbursementVoucher(): HasOne
    {
        return $this->hasOne(DisbursementVoucher::class);
    }
}
