<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RequestForQuotation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'purchase_request_id',
        'created_by',
        'rfq_number',
        'purpose',
        'rfq_date',
        'deadline',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rfq_date' => 'date',
        'deadline' => 'date',
    ];

    /**
     * Generate a unique RFQ number.
     *
     * @return string
     */
    public static function generateRFQNumber(): string
    {
        $latestRFQ = self::latest()->first();
        $year = date('Y');
        $number = $latestRFQ ? intval(substr($latestRFQ->rfq_number, -5)) + 1 : 1;
        
        return 'RFQ-' . $year . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get the purchase request associated with the RFQ.
     */
    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    /**
     * Get the user who created the RFQ.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the supplier quotations for this RFQ.
     */
    public function supplierQuotations(): HasMany
    {
        return $this->hasMany(SupplierQuotation::class);
    }

    /**
     * Get the abstract of quotation for this RFQ.
     */
    public function abstractOfQuotation(): HasOne
    {
        return $this->hasOne(AbstractOfQuotation::class);
    }

    /**
     * Get the awarded supplier quotation.
     */
    public function awardedQuotation()
    {
        return $this->supplierQuotations()->where('is_awarded', true)->first();
    }
}
