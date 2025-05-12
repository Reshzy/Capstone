<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierQuotation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'request_for_quotation_id',
        'supplier_id',
        'quotation_number',
        'quotation_date',
        'total_amount',
        'status',
        'remarks',
        'document_path',
        'is_awarded',
        'received_by',
        'received_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quotation_date' => 'date',
        'total_amount' => 'decimal:2',
        'is_awarded' => 'boolean',
        'received_at' => 'datetime',
    ];

    /**
     * Generate a unique quotation number.
     *
     * @return string
     */
    public static function generateQuotationNumber(): string
    {
        $latestQuotation = self::latest()->first();
        $year = date('Y');
        $number = $latestQuotation ? intval(substr($latestQuotation->quotation_number, -5)) + 1 : 1;
        
        return 'QUO-' . $year . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get the request for quotation associated with the supplier quotation.
     */
    public function requestForQuotation(): BelongsTo
    {
        return $this->belongsTo(RequestForQuotation::class);
    }

    /**
     * Get the supplier associated with the quotation.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the user who received the quotation.
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Get the items in this quotation.
     */
    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    /**
     * Calculate the total amount of the quotation.
     */
    public function calculateTotal(): float
    {
        return $this->items()->sum('total_price');
    }

    /**
     * Update the total amount based on the items.
     */
    public function updateTotal(): void
    {
        $this->total_amount = $this->calculateTotal();
        $this->save();
    }
}
