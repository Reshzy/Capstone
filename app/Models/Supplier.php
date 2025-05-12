<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
}
