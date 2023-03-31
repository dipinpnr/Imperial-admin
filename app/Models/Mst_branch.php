<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use App\Models\admin\Mst_store_product;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Mst_branch extends Authenticatable
{
    use HasFactory,SoftDeletes;
    
    protected $guard = 'branch';
    
    protected $primaryKey="branch_id";
    
    protected $fillable = [
        'branch_id',
        'brand_name',
        'branch_code',
        'branch_contact_person',
        'branch_contact_number',
        'branch_address',
        'whatsapp_number',
        'location',
        'working_hours_from',
        'working_hours_to',
        'branch_email',
        'password',
        'branch_status',
        'branch_latitude',
        'branch_longitude',
        'branch_zip_code',
        'deleted_at'

    ];
    
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function scopeIsActive($query)
    {
        return $query->where('branch_status',1);
    }
    
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function areas()
    {
        return $this->belongsToMany(Mst_delivery_area::class,'trn_branch_deliveryarea','branch_id','area');
    }
    
    public function products()
    {
        return $this->belongsToMany(Mst_store_product::class,'trn_branch_product','branch_id','product');
    }
}
