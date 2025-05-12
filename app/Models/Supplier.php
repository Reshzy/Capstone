<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'tax_id',
        'is_active',
        'rating',
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'decimal:2',
    ];
    
    public function procurementCategories(): BelongsToMany
    {
        return $this->belongsToMany(ProcurementCategory::class);
    }
    
    /**
     * Get the performance records for this supplier.
     */
    public function performances(): HasMany
    {
        return $this->hasMany(SupplierPerformance::class);
    }
    
    /**
     * Get the quotations submitted by this supplier.
     */
    public function quotations(): HasMany
    {
        return $this->hasMany(SupplierQuotation::class);
    }
    
    /**
     * Calculate the average rating based on performance records.
     */
    public function calculateAverageRating()
    {
        return $this->performances()->avg('rating');
    }
    
    /**
     * Update the rating based on performance records.
     */
    public function updateRating()
    {
        $averageRating = $this->calculateAverageRating();
        if ($averageRating) {
            $this->update(['rating' => $averageRating]);
        }
    }
}
