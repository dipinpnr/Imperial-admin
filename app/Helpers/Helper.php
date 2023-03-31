<?php

namespace App\Helpers;

use App\Models\admin\Mst_Coupon;
use App\Models\admin\Mst_ItemLevelTwoSubCategory;
use App\Models\admin\Mst_ItemSubCategory;
use App\Models\admin\Mst_OfferZone;
use App\Models\admin\Mst_ProductVariant;
use App\Models\admin\Mst_store_product;
use App\Models\admin\Mst_store_product_varient;
use App\Models\admin\Trn_ItemImage;
use App\Models\admin\Trn_ItemVariantAttribute;
use App\Models\admin\Trn_Order;
use App\Models\admin\Mst_dispute;
use App\Models\admin\Trn_store_order;
use App\Models\admin\Mst_AttributeValue;
use App\Models\admin\Trn_ReviewsAndRating;
use App\Models\admin\Trn_WishList;
use App\Models\admin\Mst_branch_product;
use App\Models\admin\Mst_Tax;
use App\Models\admin\Mst_Product;
use App\Models\admin\Trn_store_order_item;
use Illuminate\Support\Str;
use Crypt;
use  Carbon\Carbon;
use stdClass;
use DB;
use Validator;
use App\Models\admin\Mst_Brand;
use App\Models\admin\Mst_AttributeGroup;
class Helper
{

    public static function orderTotalDiscount($order_id)
    {
        $orderItems = Trn_store_order_item::select('product_id', 'quantity', 'unit_price', 'product_varient_id')->where('order_id', $order_id)->get();
        $orderItemsCount = Trn_store_order_item::where('order_id', $order_id)->count();
        $totalDis = 0;
        if ($orderItemsCount > 0) {
            foreach ($orderItems as $item) {
                $product_varient = Mst_store_product_varient::find($item->product_varient_id);
                $totalDis = $totalDis + ((@$product_varient->product_varient_price - @$product_varient->product_varient_offer_price) * $item->quantity);
            }
            return $totalDis;
        } else {
            return 0;
        }
    }
    public static function orderTotalTax($order_id)
    {
        //  $orderTotalAmount = Trn_store_order_item::where('order_id', $order_id)->sum('total_amount');

        $orderTotalTax = Trn_store_order_item::where('order_id', $order_id)->sum('tax_amount');
        if (isset($orderTotalTax) && ($orderTotalTax != 0)) {
            $orderItems = Trn_store_order_item::select('product_id', 'quantity', 'unit_price', 'product_varient_id')->where('order_id', $order_id)->get();
            $totalTax = 0;
            foreach ($orderItems as $item) {
                $productData = Mst_store_product::find($item->product_id);
                if (isset($productData->tax_id) && ($productData->tax_id != 0)) {

                    $taxData = Mst_Tax::find($productData->tax_id);

                    $product_varient = Mst_store_product_varient::find($item->product_varient_id);
                    //return $product_varient;
                    $tax = $item->quantity * (@$product_varient->product_varient_offer_price * @$taxData->tax_value / (100 + @$taxData->tax_value));

                    //  return   $tax = (@$taxData->tax_value / 100) * ($item->quantity * $item->unit_price);
                    $totalTax = $totalTax + $tax;
                }
            }
            return number_format((float)$totalTax, 2, '.', '');
        } elseif ($orderTotalTax == 0) {
            $orderItems = Trn_store_order_item::select('product_id', 'quantity', 'unit_price')->where('order_id', $order_id)->get();
            $totalTax = 0;
            foreach ($orderItems as $item) {
                $productData = Mst_branch_product::find($item->product_id);
                if (isset($productData->tax_id) && ($productData->tax_id != 0)) {
                    $taxData = Mst_Tax::find($productData->tax_id);

                    $tax = (@$taxData->tax_value / 100) * ($productData->quantity * $productData->unit_price);
                    $totalTax = $totalTax + $tax;
                }
            }
            return  number_format((float)$totalTax, 2, '.', '');;
        } else {
            return  '0.0';
        }
    }



    public static function getValuesByGroupId($attribute_group_id)
    {
        $attribute_values = Mst_AttributeValue::where('attribute_group_id',$attribute_group_id)->orderBy('attribute_value_id', 'DESC')->get();
        return $attribute_values;
    }
     public static function getBrandId($brand_id)
    {
        $details = Mst_Brand::select('brand_id','brand_name','brand_icon','is_active')->where('brand_id',$brand_id)->get();
        return $details;
    }
     public static function getAttributeId($attribute_group_id)
    {
        $value = Mst_AttributeGroup::where('attribute_group_id',$attribute_group_id)->get();
        return $value;
    }
      public static function getValuesByGroupattributeId($attribute_group_id)
    {
        $attribute= Mst_AttributeValue::where('attribute_group_id',$attribute_group_id)->get();
        return $attribute;
    }
     public static function getValuesBycatId($item_category_id)
    {
        $cat_values = Mst_Product::select('product_id')->where('item_category_id',$item_category_id)->orderBy('product_id', 'DESC')->get();
        return $cat_values;
    }

     public static function getValuesByproductId($product_id)
    {
        $product = Mst_ProductVariant::select('variant_name','variant_price_offer')->where('product_id',$product_id)->orderBy('product_variant_id', 'DESC')->get();
        return $product;
    }
    
    public static function getProductVarientID($product_id)
    {
        $product_variant_id = Mst_ProductVariant::where('product_id',$product_id)->orderBy('product_variant_id', 'DESC')->get();
        return $product_variant_id;
    }

    public static function findReviewData($product_variant_id)
    {
        $reviewData = Trn_ReviewsAndRating::where('product_variant_id', $product_variant_id)
            ->where('review', '!=', null)
            ->get();
        foreach ($reviewData as $c) {
            $c->customerData = $c->customerData;
        }
        return $reviewData;
    }

    public static function isWishListProduct($product_variant_id, $customer_id)
    {
        $data = Trn_WishList::where('product_variant_id', $product_variant_id)
            ->where('customer_id', $customer_id)
            ->count();
        if ($data < 1) {
            return 0;
        } else {
            return 1;
        }
    }


    public static function findReviewCount($product_variant_id)
    {
        return $ratingCount = Trn_ReviewsAndRating::where('product_variant_id', $product_variant_id)
            ->where('review', '!=', null)
            ->count();
    }

    public static function itemSubCategoryL2Data($item_sub_category_id)
    {
        return $list = Mst_ItemLevelTwoSubCategory::where('item_sub_category_id', $item_sub_category_id)
            ->where('is_active', 1)
            ->get();
    }

    public static function findRating($product_variant_id)
    {
        $ratingCount = Trn_ReviewsAndRating::where('product_variant_id', $product_variant_id)->count();
        $ratingSum = Trn_ReviewsAndRating::where('product_variant_id', $product_variant_id)->sum('rating');
        if ($ratingCount == 0)
            $ratingCount = 1;
        $avgRating = $ratingSum / $ratingCount;
        if ($avgRating > 1)
            return $avgRating;
        else
            return 0;
    }

    public static function findRatingCount($product_variant_id)
    {
        return  $ratingCount = Trn_ReviewsAndRating::where('product_variant_id', $product_variant_id)->count();
    }

    public static function validateCustomer($valid)
    {
        $validate = Validator::make(
            $valid,
            [
                'customer_name' => 'required',
                'customer_email'    => 'email',
                'customer_mobile'    => 'required|unique:mst__customers|numeric',
                'password'  => 'required|min:5|same:password_confirmation',


            ],
            [
                'customer_name.required'                => 'Customer name required',
                'customer_mobile.unique'                  => 'Mobile number already exists ',
                'customer_email.email'                  => 'Invalid email ',
                'password.required'                  => 'Password required ',

            ]
        );
        return $validate;
    }



    public static function categoryIcon()
    {
        return '/assets/default/category.png';
    }

    public static function productIcon()
    {
        return '/assets/default/category.png';
    }

    public static function brandIcon()
    {
        return '/assets/default/category.png';
    }

    public static function totalProductCount()
    {
        $totalProductCount = Mst_store_product::join('mst__item_categories', 'mst__item_categories.item_category_id', '=', 'mst_store_products.product_cat_id')
          ->where('mst_store_products.is_removed', 0)
          ->orderBy('mst_store_products.product_id', 'DESC')->count();
          
        return $totalProductCount;
    }

    public static function todaySales()
    {
        $totalSales = Trn_store_order::whereDate('created_at', Carbon::today())
            ->sum('product_total_amount');
        return $totalSales;
    }

    public static function totalSales()
    {
        $totalSales = Trn_store_order::
        //  whereIn('status_id', [7])->       
        sum('product_total_amount');
        return $totalSales;
    }

    public static function todayCustomerVisit()
    {
        return 0;
    }

    public static function weeklySales()
    {
        return 0;
    }

    public static function currentIssues()
    {
        $curIssues = DB::table("mst_disputes")->where('dispute_status' ,'!=',1)
            ->count();
        return $curIssues;
    }

    public static function newIssues()
    {
        return 0;
    }

    public static function totalCategories()
    {
        return DB::table("mst__item_categories")->where('is_active',1)->count();
    }

    public static function deliveryBoysCount()
    {
        return 0;
    }

    public static function dailySales()
    {
        return 0;
    }

    public static function monthlyVisit()
    {
        return 0;
    }

    public static function totalIssues()
    {
       $totalIssues = DB::table("mst_disputes")->where('dispute_status' ,'!=',3)
            ->count();
        return $totalIssues;
    }

    public static function todayOrder()
    {
         return  Trn_store_order::whereDate('created_at', Carbon::today())->count();
        
    }
    
    public static function totalOrder()
    {
         return  Trn_store_order::count();
        
    }

    public static function findDeliveryCharge($customerd, $cusAddrId)
    {
        return 0;
    }

    public static function reduceCouponDiscount($customer_id, $coupon_code, $totalAmount)
    {
        $current_time = Carbon::now()->toDateTimeString();
        $coupon = Mst_Coupon::where('coupon_code', $coupon_code)->where('coupon_status', 1)->first();
        if (isset($coupon)) {
            if (($coupon->valid_from <= $current_time) && ($coupon->valid_to >= $current_time)) {
                // echo "here " . $totalAmount . " - " . $coupon->min_purchase_amt;
                // die;
                if ($totalAmount >= $coupon->min_purchase_amt) {

                    if ((Trn_Order::where('customer_id', $customer_id)->where('coupon_id', $coupon->coupon_id)->count()) <= 0) {
                        // ->whereIn('status_id', [6, 9, 4, 7, 8, 1]) order status not added to previous query
                        if ($coupon->discount_type == 1) {
                            //fixedAmt
                            $amtToBeReduced = $coupon->discount;
                        } else {
                            //percentage
                            $amtToBeReduced = ($coupon->discount * 100) / $totalAmount;
                        }
                        return number_format((float)$amtToBeReduced, 2, '.', '');
                    } else {
                        if ($coupon->coupon_type == 2) {
                            if ($coupon->discount_type == 1) {
                                //fixedAmt
                                $amtToBeReduced = $coupon->discount;
                            } else {
                                //percentage
                                $amtToBeReduced = ($coupon->discount * 100) / $totalAmount;
                            }
                            return number_format((float)$amtToBeReduced, 2, '.', '');
                        } else {
                            return 0;
                        }
                    }
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }


    public static function subCategoryLevOne($item_category_id)
    {
        $subCategoryLevOne = Mst_ItemSubCategory::where('is_active', 1)
            ->where('item_category_id', $item_category_id)
            ->orderBy('sub_category_name', 'ASC')->get();

        foreach ($subCategoryLevOne as $c) {
            $c->subCategoryLevTwo = Helper::subCategoryLevTwo($c->item_sub_category_id);
        }
        return $subCategoryLevOne;
    }

    private static function subCategoryLevTwo($item_sub_category_id)
    {
        $subCategoryLevTwo = Mst_ItemLevelTwoSubCategory::where('is_active', 1)
            ->where('item_sub_category_id', $item_sub_category_id)
            ->orderBy('iltsc_name', 'ASC')->get();

        return $subCategoryLevTwo;
    }



    public static function productBaseImage($product_id)
    {
        $productBaseImage = Trn_ItemImage::where('is_default', 1)
            ->where('product_id', $product_id)
            ->where('product_variant_id', null)->first();

        if (isset($productBaseImage->item_image_name)) {
            return '/assets/uploads/products/' . $productBaseImage->item_image_name;
        } else {
            $productBaseImage2 = Trn_ItemImage::where('product_id', $product_id)->first();
            if (isset($productBaseImage2->item_image_name)) {
                return '/assets/uploads/products/' . $productBaseImage2->item_image_name;
            } else {
                return '/assets/default/category.png';
            }
        }
    }

    public static function productVarBaseImage($product_id, $product_variant_id)
    {
        $productBaseImage = Trn_ItemImage::where('is_default', 1)
            ->where('product_variant_id', $product_variant_id);

        if ($product_id != 0)
            $productBaseImage = $productBaseImage->where('product_id', $product_id);

        $productBaseImage = $productBaseImage->first();

        if (isset($productBaseImage->item_image_name)) {
            return '/assets/uploads/products/' . $productBaseImage->item_image_name;
        } else {
            $productBaseImage2 = Trn_ItemImage::where('product_id', $product_id)
                ->where('product_variant_id', $product_variant_id)
                ->first();
            if (isset($productBaseImage2->item_image_name)) {
                return '/assets/uploads/products/' . $productBaseImage2->item_image_name;
            } else {
                return '/assets/default/category.png';
            }
        }
    }

    public static function variantArrtibutes($product_variant_id)
    {
        $variantArrtibutes = Trn_ItemVariantAttribute::where('product_variant_id', $product_variant_id)
            ->orderBy('variant_attribute_id', 'DESC')->get();

        foreach ($variantArrtibutes as $c) {
            $c->attribute_group_name = $c->attributeGroup->attribute_group;
            $c->attribute_value_name = $c->attributeValue->attribute_value;
        }
        return $variantArrtibutes;
    }

    public static function variantImages($product_variant_id)
    {
        $itemImages = Trn_ItemImage::where('product_variant_id', $product_variant_id)
            ->where('is_active', 1)
            ->orderBy('is_default', 'DESC')
            ->orderBy('item_image_id', 'DESC')
            ->get();

        foreach ($itemImages as $c) {
            $c->item_image_name = '/assets/uploads/products/' . $c->item_image_name;
        }

        return $itemImages;
    }

    public static function isOfferAvailable($product_variant_id)
    {
        $offerData = Mst_OfferZone::where('product_variant_id', $product_variant_id)
            ->whereDate('date_start', '<=', Carbon::now()->format('Y-m-d'))
            ->whereDate('date_end', '>=', Carbon::now()->format('Y-m-d'))
            // ->whereTime('time_start', '<=', Carbon::now()->format('H:i'))
            // ->whereTime('time_end', '>=', Carbon::now()->format('H:i'))
            ->whereTime('time_start', '<=', Carbon::now()->format('H:i'))
            ->whereTime('time_end', '>', Carbon::now()->format('H:i'))
            ->where('is_active', 1)
            ->first();

        if ($offerData)
            return $offerData;
        else
            return false;
    }


    public static function otherVariants($product_variant_id, $similar_products_limit)
    {
        $varData = Mst_ProductVariant::join('mst__products', 'mst__products.product_id', '=', 'mst__product_variants.product_id')
            ->where('mst__product_variants.product_variant_id', $product_variant_id)->first();

        $otherVariants = Mst_ProductVariant::join('mst__products', 'mst__products.product_id', '=', 'mst__product_variants.product_id')
            ->where('mst__products.iltsc_id', $varData->iltsc_id)
            ->where('mst__product_variants.product_variant_id', '!=', $product_variant_id)
            ->where('mst__product_variants.product_id', '=', $varData->product_id);

        if (isset($similar_products_limit)) {
            $otherVariants = $otherVariants->limit($similar_products_limit);
        }

        $otherVariants = $otherVariants->get();

        foreach ($otherVariants as $c) {
            $c->productBaseImage  = Helper::productBaseImage($c->product_id);
            $c->productVariantBaseImage  = Helper::productVarBaseImage($c->product_id, $c->product_variant_id);
            $c->proVarAttributes  = Helper::variantArrtibutes($c->product_variant_id);
            $c->proVarImages  = Helper::variantImages($c->product_variant_id);

            // offer-details
            if (Helper::isOfferAvailable($c->product_variant_id)) {
                $c->isOfferAvailable  = 1;
                $c->offerData  = Helper::isOfferAvailable($c->product_variant_id);
            } else {
                $c->isOfferAvailable  = 0;
                $c->offerData  = new stdClass();;
            }
        }

        return $otherVariants;
    }

    public static function similarProducts($product_variant_id, $similar_products_limit)
    {
        $varData = Mst_ProductVariant::join('mst__products', 'mst__products.product_id', '=', 'mst__product_variants.product_id')
            ->where('mst__product_variants.product_variant_id', $product_variant_id)->first();

        $similarProducts = Mst_ProductVariant::join('mst__products', 'mst__products.product_id', '=', 'mst__product_variants.product_id')
            ->where('mst__products.iltsc_id', $varData->iltsc_id)
            ->where('mst__product_variants.product_variant_id', '!=', $product_variant_id)
            ->where('mst__product_variants.product_id', '!=', $varData->product_id)
            ->whereOr('mst__products.item_sub_category_id', $varData->item_sub_category_id);

        if (isset($similar_products_limit)) {
            $similarProducts = $similarProducts->limit($similar_products_limit);
        }

        $similarProducts = $similarProducts->get();

        foreach ($similarProducts as $c) {
            $c->productBaseImage  = Helper::productBaseImage($c->product_id);
            $c->productVariantBaseImage  = Helper::productVarBaseImage($c->product_id, $c->product_variant_id);
            $c->proVarAttributes  = Helper::variantArrtibutes($c->product_variant_id);
            $c->proVarImages  = Helper::variantImages($c->product_variant_id);

            // offer-details
            if (Helper::isOfferAvailable($c->product_variant_id)) {
                $c->isOfferAvailable  = 1;
                $c->offerData  = Helper::isOfferAvailable($c->product_variant_id);
            } else {
                $c->isOfferAvailable  = 0;
                $c->offerData  = new stdClass();;
            }
        }

        return $similarProducts;
    }


    public static function ajaxLoader()
    {
        echo '<div style="display: none; background-color: transparent; z-index: 30001; opacity: 1;" id="loaderCard" class="card"> 
         <div class="dimmer active">
        <div class="spinner1">
            <div class="double-bounce1"></div>
            <div class="double-bounce2"></div>
        </div>
            </div>
            </div>
        </div>';
    }

    public static function ajaxModalLoader()
    {
        echo '';
    }

    public static function stockAvailable($product_id)
    {
        $stockSum = Mst_ProductVariant::where('product_id', $product_id)->sum('stock_count');
        return $stockSum;
    }

}
