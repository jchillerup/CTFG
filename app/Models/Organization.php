<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'airtable_id',
        'name',
        'description',
        'website_url',
        'type',
        'status'
    ];
    
    /**
     * Get the listings that belong to this organization
     */
    public function listings()
    {
        return $this->hasMany(Listing::class, 'organization_id');
    }
}
