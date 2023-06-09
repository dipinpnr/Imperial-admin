<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mst_store_product extends Model
{
  protected $primaryKey = 'product_id';
  use SoftDeletes;
  protected $fillable = [

    'product_id', 'product_name', 'SKU','product_name_slug', 'product_code',
    'business_type_id', 'product_cat_id', 'product_description',
    'product_delivery_info', 'product_offer_from_date',
    'product_offer_to_date', 'product_price', 'stock_count',
    'stock_status', 'product_price_offer', 'product_shipping_info',
    'product_base_image', 'store_id', 'product_status', 'product_commision_rate',
    'attr_value_id', 'attr_group_id',
    'tax_id', 'color_id', 'vendor_id', 'global_product_id', 'draft', 'product_brand',
    'product_type', 'service_type', 'sub_category_id', 'min_stock', 'is_removed', 'is_added_from_web','sub_category_leveltwo','is_must_try','is_must_recommended','food_type'

  ];

  public function tax()
  {
    return $this->belongsTo('App\Models\admin\Mst_Tax', 'tax_id', 'tax_id');
  }

  public function agency()
  {
    return $this->belongsTo('App\Models\admin\Mst_store_agencies', 'vendor_id', 'agency_id');
  }

  public function color()
  {

    return $this->belongsTo('App\Models\admin\Mst_attribute_value', 'color_id', 'attr_value_id');
  }
  public function sub_sub_category()
   {

     return $this->belongsTo('App\Models\admin\Mst_ItemSubCategory','sub_category_leveltwo', 'item_sub_category_id');
   }
  

  public function store()
  {
    return $this->belongsTo('App\Models\admin\Mst_store', 'store_id', 'store_id');
  }
  
   public function brand()
  {
    return $this->belongsTo('App\Models\admin\Mst_Brand', 'product_brand', 'brand_id');
  }


  public function business_type()
  {
    return $this->belongsTo('App\Models\admin\Mst_business_types', 'business_type_id', 'business_type_id');
  }
  public function categories()
  {

    return $this->belongsTo('App\Models\admin\Mst_ItemCategory', 'product_cat_id', 'item_category_id');
  }
  
    public function sub_category()
   {

     return $this->belongsTo('App\Models\admin\Mst_ItemSubCategory','sub_category_id', 'item_sub_category_id');
   }
  

  public function attr_value()
  {

    return $this->belongsTo('App\Models\admin\Mst_attribute_value', 'attr_value_id', 'attr_value_id');
  }
  public function attr_group()
  {

    return $this->belongsTo('App\Models\admin\Mst_attribute_group', 'attr_group_id', 'attr_group_id');
  }
  public function branches()
    {
        return $this->belongsToMany('App\Models\Mst_branch','trn_branch_product','product','branch_id');
    }
}
