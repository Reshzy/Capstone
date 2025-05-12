<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'supplier_quotation_id',
        'item_name',
        'description',
        'unit',
        'quantity',
        'unit_price',
        'total_price',
        'specifications',
        'meets_specifications',
        'remarks',
        'delivery_days',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'meets_specifications' => 'boolean',
        'delivery_days' => 'integer',
    ];

    /**
     * Get the supplier quotation that this item belongs to.
     */
    public function supplierQuotation(): BelongsTo
    {
        return $this->belongsTo(SupplierQuotation::class);
    }

    /**
     * Calculate the total price based on quantity and unit price.
     */
    public function calculateTotalPrice(): float
    {
        return $this->quantity * $this->unit_price;
    }

    /**
     * Update the total price based on quantity and unit price.
     */
    public function updateTotalPrice(): void
    {
        $this->total_price = $this->calculateTotalPrice();
        $this->save();
    }
}
