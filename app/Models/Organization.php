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
        'slug',
        'description',
        'website_url',
        'type',
        'status'
    ];
    
    /**
     * Generate slug from name
     */
    public static function generateSlug($name, $excludeId = null)
    {
        if (empty($name)) {
            return null;
        }
        
        // Trim whitespace before generating slug
        $name = trim($name);
        
        $slug = \Illuminate\Support\Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;
        
        // Ensure uniqueness (exclude current record if updating)
        $query = self::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        while ($query->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
            $query = self::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }
        
        return $slug;
    }
    
    /**
     * Get the listings that belong to this organization
     */
    public function listings()
    {
        return $this->hasMany(Listing::class, 'organization_id');
    }
}
