<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mst_delivery_area extends Model
{
    use HasFactory;
    protected $primaryKey="area_id";
    
    public function scopeActive($query) 
    {
       return $query->where('is_active', 1);
        
    }
}
