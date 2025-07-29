<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Property extends Model
{
    protected $fillable = [
        'user_id',
        'property_name',
        'description',
        'property_type',
        'location', 
    ];

    /**
     * Get the user that owns the property.
     */
    public function user() : BelongsTo  
    {
        return $this->belongsTo(User::class , 'user_id');
    }
}
