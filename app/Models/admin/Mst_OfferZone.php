<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mst_OfferZone extends Model
{
    use SoftDeletes;

   protected $table = "mst__offer_zones";
    protected $primaryKey = "offer_id";

    protected $fillable = [
        'product_variant_id',
        'date_start',
        'time_start',
        'date_end',
        'time_end',
        'link',
        'is_active',
        'offer_price',
        'offer_type'
    ];

    public function productVariantData()
    {
        return $this->belongsTo('App\Models\admin\Mst_branch_product_varient', 'product_variant_id', 'varient_id');
    }
    public function storeProduct()
    {
        return $this->belongsTo('App\Models\admin\Mst_branch_product_varient', 'product_id', 'product_id');
    }
}
