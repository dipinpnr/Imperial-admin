<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trn_ReviewsAndRating extends Model
{
    use SoftDeletes;


    protected $table = "trn__reviews_and_ratings";
    protected $primaryKey = "reviews_id";

    protected $fillable = [
        'customer_id',
        'product_id',
        'product_variant_id',
        'rating',
        'review',
    ];


    public function customerData()
    {
        return $this->belongsTo('App\Models\admin\Trn_store_customer', 'customer_id', 'customer_id');
    }

    public function productData()
    {
        return $this->belongsTo('App\Models\admin\Mst_branch_product', 'product_id', 'branch_product_id');
    }

    public function productVariantData()
    {
        return $this->belongsTo('App\Models\admin\Mst_branch_product_varient', 'product_varient_id', 'varient_id');
    }
}
