<?php

namespace App\Http\Controllers\admin;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\GlobalProductsImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Mst_branch;
use Illuminate\Support\Facades\Validator;
use App\Models\admin\Trn_ReviewsAndRating;
use App\Models\admin\Mst_store;
use App\Models\admin\Mst_Brand;
use App\Models\admin\Mst_store_product;
use App\Models\admin\Mst_branch_product;
use App\Models\admin\Sys_store_order_status;
use App\Models\admin\Mst_ItemCategory;
use App\Models\admin\Mst_ItemSubCategory;
use App\Models\admin\Mst_store_product_varient;
use App\Models\admin\Mst_branch_product_varient;
use App\Models\admin\Mst_attribute_group;
use App\Models\admin\Mst_store_agencies;
use App\Models\admin\Mst_business_types;
use App\Models\admin\Mst_attribute_value;
use App\Models\admin\Mst_product_image;
use App\Models\admin\Mst_store_link_agency;
use App\Models\admin\Mst_order_link_delivery_boy;
use App\Models\admin\Country;
use App\Models\admin\State;
use App\Models\admin\District;
use App\Models\admin\Trn_store_order;
use App\Models\admin\Trn_store_order_item;
use App\Models\admin\Mst_delivery_boy;
use App\Models\admin\Trn_order_invoice;

use App\Models\admin\Mst_dispute;
use App\Models\admin\Mst_Tax;
use App\Models\admin\Town;
use App\Models\admin\Trn_store_setting;
use App\Models\admin\Trn_StoreTimeSlot;
use App\Models\admin\Trn_DeliveryBoyLocation;
use App\Models\admin\Trn_DeliveryBoyDeviceToken;
use App\Models\admin\Trn_store_payments_tracker;

use App\Models\admin\Mst_store_documents;
use App\Models\admin\Mst_store_images;
use App\Models\admin\Mst_store_link_delivery_boy;

use App\Models\admin\Trn_GlobalProductVideo;
use App\Models\admin\Mst_GlobalProducts;
use App\Models\admin\Trn_GlobalProductImage;


use App\Models\admin\Trn_StoreAdmin;
use App\Models\admin\Trn_StoreDeliveryTimeSlot;
use App\Models\admin\Trn_store_payment_settlment;

use App\Models\admin\Trn_ProductVariantAttribute;
use App\Models\admin\Mst_SubCategory;
use App\Models\admin\Mst_Video;
use App\Models\admin\Trn_RecentlyVisitedStore;
use App\Models\admin\Trn_CustomerDeviceToken;
use App\Models\admin\Trn_StoreDeviceToken;
use App\Models\admin\Trn_configure_points;
use App\Models\admin\Trn_customer_reward;
use App\Models\admin\Trn_OrderPaymentTransaction;
use App\Models\admin\Sys_payment_type;

use App\Helpers\Helper;


use App\Models\admin\Trn_store_customer;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Response;
use Image;
use DB;
use Hash;
use Carbon\Carbon;
use Crypt;
use Mail;
use PDF;
use Illuminate\Support\Arr;

use App\Models\admin\Mst_StockDetail;
use App\Models\admin\Trn_ProductVideo;
use App\Models\admin\Trn_StoreBankData;
use App\Models\admin\Trn_WishList;

class ProductController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function listProductNames(Request $request)
   {
//$subadmin_id = $request->subadmin_id;
   // $store_id = $request->store_id;
      
      $subadmin_id = 1;
      
      $store_id = 1 ;
      
    $products  = Mst_store_product::join('mst_stores', 'mst_stores.store_id', '=', 'mst_store_products.store_id');

    if (isset($store_id))
      $products = $products->where("mst_store_products.store_id", '=', $store_id);

    if (isset($subadmin_id))
      $products = $products->where("mst_stores.subadmin_id", '=', $subadmin_id);

    $products = $products->pluck("mst_store_products.product_name", "mst_store_products.product_id");

    return response()->json($products);
   }


    public function listProduct(Request $request)
    {
      try {
  
        $branches = Mst_branch::isActive()->get();
        $pageTitle = "Products";
        
        $store_id =  1;
        $products = Mst_store_product::join('mst__item_categories', 'mst__item_categories.item_category_id', '=', 'mst_store_products.product_cat_id')
          ->where('mst_store_products.store_id', $store_id)
          ->where('mst_store_products.is_removed', 0)
          ->orderBy('mst_store_products.product_id', 'DESC')->get();
        
            $store = Mst_store::all();
  
        if ($_GET) {
        
          $product_name = $request->product_name;
          $product_code = $request->product_code;
          $stock_status =  $request->stock_status;
          $product_status =  $request->product_status;
  
          $a1 = Carbon::parse($request->From_date)->startOfDay();
          $a2 = Carbon::parse($request->To_date)->endOfDay();
          $b1 = $request->start_price;
          $b2 = $request->end_price;
  
          
          DB::enableQueryLog();
          
          $store_id = 1;
  
          $query =  Mst_store_product::join('mst__item_categories', 'mst__item_categories.item_category_id', '=', 'mst_store_products.product_cat_id')
            ->where('mst_store_products.store_id', $store_id)
            ->where('is_removed', 0)
            ->orderBy('mst_store_products.product_id', 'DESC');
  
  
          if (isset($request->product_name)) {
            $query = $query->where('mst_store_products.product_name', 'LIKE', '%' . $product_name . '%');
           
            }
  
          if (isset($request->From_date) && isset($request->To_date)) {
            $query = $query->whereBetween('created_at', [$a1, $a2]);
          }
  
          if (isset($request->start_price) && isset($request->end_price)) {
            $query = $query->whereBetween('product_price_offer', [$b1, $b2]);
          }
  
          if (isset($request->start_price) && !isset($request->end_price)) {
            $query = $query->where('product_price_offer', '>=', $b1);
          }
  
          if (!isset($request->start_price) && isset($request->end_price)) {
            $query = $query->where('product_price_offer', '<=', $b2);
          }
  
          if (isset($product_code)) {
            $query = $query->where('product_code', 'LIKE', $product_code);
            
            
          }
  
          
          if (isset($product_status)) {
            $query = $query->where('product_status', $product_status);
          }
  
  
          $productsz = $query->get();
  
          $products = array();
          if (isset($stock_status)) {
            if ($stock_status == 0) {
  
              foreach ($productsz as $key => $product) {
                $stock_count_sum = \DB::table('mst_store_product_varients')->where('product_id', $product->product_id)->where('is_removed', 0)->sum('stock_count');
                if ($stock_count_sum == 0) {
                  $products[] = $product;
                }
              }
            } else {
              foreach ($productsz as $key => $product) {
                $stock_count_sum = \DB::table('mst_store_product_varients')->where('product_id', $product->product_id)->where('is_removed', 0)->sum('stock_count');
                if ($stock_count_sum > 0) {
                  $products[] = $product;
                }
              }
            }
          } else {
            $products = $productsz;
          }
  
  
          //dd(DB::getQueryLog());
          
          
  
          return view('admin.elements.product.list', compact('products', 'branches','pageTitle', 'store'));
        }
  
        return view('admin.elements.product.list', compact('products', 'branches','pageTitle', 'store'));
      } catch (\Exception $e) {

        
            
        return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
      }
    }
  
  
    public function createProduct()
    {
      $pageTitle = "Create Products";
  
      $products = Mst_store_product::all();
      $attr_groups = Mst_attribute_group::all();
      
      $tax = Mst_Tax::where('is_removed', '!=', 1)->get();
  
      $colors = Mst_attribute_value::join('mst_attribute_groups', 'mst_attribute_groups.attr_group_id', '=', 'mst_attribute_values.attribute_group_id')
        ->where('mst_attribute_groups.group_name', 'LIKE', '%color%')
        ->select('mst_attribute_values.*')
        ->get();
      $agencies = Mst_store_agencies::where('agency_account_status', 1)->get();
      $category = Mst_ItemCategory::where('is_active', 1)->get();
  
      $store_id =  Auth::user()->store_id;
      $products_global_products_id = Mst_store_product::where('store_id', $store_id)
        ->where('global_product_id', '!=', null)
        ->orderBy('product_id', 'DESC')
        ->pluck('global_product_id')
        ->toArray();
  
      $global_product = Mst_GlobalProducts::whereNotIn('global_product_id', $products_global_products_id)->get();
  
      $business_types = Mst_business_types::all();
      $store = Mst_store::all();
      $brands = Mst_Brand::where('is_active',1)->get();
  
      return view('admin.elements.product.create', compact('category', 'global_product', 'agencies','brands','colors', 'tax', 'products', 'pageTitle', 'attr_groups', 'store', 'business_types'));
    }
  
    public function GetAttr_Value(Request $request)
    {
      $grp_id = $request->attr_group_id;
      // dd($grp_id);
      $attr_values  = Mst_attribute_value::where("attribute_group_id", '=', $grp_id)
        ->pluck("group_value", "attr_value_id");
  
  
      return response()->json($attr_values);
    }
    public function GetCategory(Request $request)
    {
      $business_id = $request->business_type_id;
  
      $category  = Mst_categories::where("business_type_id", '=', $business_id)->where('category_status', 1)->pluck("category_name", "category_id");
      return response()->json($category);
    }
    public function GetSubCategory(Request $request)
    {
      $category_id = $request->category_id;
  
      $subcategory  = Mst_SubCategory::where("category_id", '=', $category_id)->where('sub_category_status', 1)->pluck("sub_category_name", "sub_category_id");
      return response()->json($subcategory);
    }
  
    public function storeProduct(Request $request, Mst_store_product $product, Mst_store_product_varient $varient_product, Mst_product_image $product_img)
    {
      //dd($request->all());
      //$store_id =  Auth::user()->store_id; commented by s

      $store_id = 1;
  
      // if(isset($request->product_name))
      // {
      //   $s = DB::table('mst_store_products')
      //   ->where('product_name','LIKE', '%'.$request->product_name.'%')
      //   ->where('store_id',$store_id)
      //   ->groupBy('product_name')->count();
      //   if($s > 0)
      //   {
      //     return redirect()->back()->withErrors(['store' => 'Product name already exist'])->withInput();
      //   }
      // }

    

       
  
  
      $validator = Validator::make(
        $request->all(),
        [
          // 'product_name'          => 'required|unique:mst_store_products,product_name,'.$store_id.',store_id',
          'product_name'          => 'required',
          'product_description'   => 'required',
          'sku' => 'required',
          'sale_price'   => 'required|numeric|gt:0',
          'min_stock'   => 'required',
          'product_code'   => 'required',
          'product_cat_id'   => 'required',
          'food_type'   => 'required',
          // 'vendor_id'   => 'required',
          // 'color_id'   => 'required',
          //'regular_price'   => 'required',
          //'tax_id'   => 'required',
          // 'business_type_id'   => 'required',
          //'attr_group_id'   => 'required',
          // 'attr_value_id'   => 'required',
          //'var_sale_price.*'=>'sometimes|required|numeric|gt:0',
          'product_image.*' => 'dimensions:min_width=1000,min_height=800|mimes:jpeg,jpg,png|max:10000',
          'product_image.*' => 'required|mimes:jpeg,jpg,png|max:10000',
  
  
  
        ],
        [
  
          'product_name.required'             => 'Product name required',
          'product_name.unique'             => 'Product name already exist',
          'product_description.required'      => 'Product description required',
          'sale_price.required'      => 'Sale price required',
          'sale_price.gt'      => 'Sale price Must be greater than Zero',
          //'var_sale_price.*.gt'=>'Varient Sale Price must be greater than zero',
          //'var_sale_price.*.numeric'=>'Varient Sale Price must be a number',
          'tax_id.required'      => 'Tax required',
          'min_stock.required'      => 'Minimum stock required',
          'product_code.required'      => 'Product code required',
          'business_type_id.required'        => 'Product type required',
          'attr_group_id.required'        => 'Attribute group required',
          'attr_value_id.required'        => 'Attribute value required',
          'product_cat_id.required'        => 'Product category required',
          'vendor_id.required'        => 'Vendor required',
          'color_id.required'        => 'Color required',
          'product_image.required'        => 'Product image required',
          'product_image.dimensions'        => 'Product image dimensions invalid',
  
  
        ]
      );
  
     
  
      if (!$validator->fails()) {
  
        $ChkCodeExstnce = DB::table('mst_store_products')->where('store_id','=',$store_id)->where('product_code',$request->product_code)->count();
          
        if($ChkCodeExstnce > 0)
        {
            return redirect()->back()->with('status-error', 'Product code already used by the store.')->withInput();
        }else{
  
        $product->product_name           = $request->product_name;
        $product->product_description    = $request->product_description;
        //$product->product_price        = $request->regular_price;
        $product->SKU                    = $request->sku;
        $product->product_price_offer    = $request->sale_price;
        //$product->tax_id                 = $request->tax_id; // new
        $product->stock_count                 = $request->min_stock; // stock count
        $product->min_stock                 = $request->min_stock; // stock count
        $product->sub_category_id   = $request->sub_category_id; // new
        
        $product->sub_category_leveltwo  = $request->sub_category_id_lvltwo;
  
        //$product->product_code          = "PRDCT00"; // old
        $product->product_code           = $request->product_code;
  
        if ($request->business_type_id)
          $product->business_type_id       = $request->business_type_id; // product type
        else
          $product->business_type_id       = 0;
  
        if ($request->color_id)
          $product->color_id               = $request->color_id;
        else
          $product->color_id       = 0;
  
        //$product->attr_group_id          = $request->attr_group_id;
        // $product->attr_value_id          = $request->attr_value_id;
        $product->product_cat_id         = $request->product_cat_id;
        $product->vendor_id              = $request->vendor_id; // new
        $product->product_brand              = $request->product_brand; // new
        $product->is_must_try=$request->is_must_try;
        $product->is_must_recommended=$request->is_must_recommended;
        $product->food_type=$request->food_type;
  
        $product->product_name_slug      = Str::of($request->product_name)->slug('-');
        // $product->product_specification  = $request->product_specification;  // removed
        $product->store_id               = $store_id;
        $product->global_product_id               =  @$request->global_product_id; // new
  
  
        $product->product_type               =  @$request->product_type; // new
        if ($request->product_type == 2)
          $product->service_type               =  @$request->service_type; // new
        else
          $product->service_type               =  0; // new
  
        $product->is_added_from_web               =  1;
  
        // $product->product_offer_from_date = $request->product_offer_from_date;
        // $product->product_offer_to_date   = $request->product_offer_to_date;
        // $product->product_delivery_info   = $request->product_delivery_info;
        //  $product->product_shipping_info   =$request->product_shipping_info;
  
        if ($request->min_stock == 0) {
          $product->stock_status = 0;
        } else {
          $product->stock_status = 1;
        }
  
        $product->product_status         = 1;
  
  
  
  
        $product->save();
        $id = DB::getPdo()->lastInsertId();
  
  
        if ($request->hasFile('product_image')) {
          $allowedfileExtension = ['jpg', 'png', 'jpeg',];
          $files = $request->file('product_image');
          $c = 1;
          foreach ($files as $file) {
  
  
  
            //   $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $filename = rand(1, 5000) . time() . '.' . $file->getClientOriginalExtension();
  
            // $fullpath = $filename . '.' . $extension ;
            $file->move('assets/uploads/products/base_product/base_image', $filename);
            $date = Carbon::now();
            $data1 = [
              [
                'product_image'      => $filename,
                'product_id' => $id,
                'product_varient_id' => 0,
                'image_flag'         => 0,
                'created_at'         => $date,
                'updated_at'         => $date,
              ],
            ];
  
            Mst_product_image::insert($data1);
  
            $proImg_Id = DB::getPdo()->lastInsertId();
            $productBaseImg = "";
  
            if ($c == 1) {
              DB::table('mst_store_products')->where('product_id', $id)->update(['product_base_image' => $filename]);
              $productBaseImg = $filename;
              $c++;
              DB::table('mst_product_images')->where('product_image_id', $proImg_Id)->update(['image_flag' => 1]);
            }
          }
        }
  
        // new cases product variant
        $varImages = Mst_product_image::where('product_id', $id)->orderBy('product_image_id', 'DESC')->get();
  
        $date = Carbon::now();
  
        $sCount = 0;
        if (($request->service_type == 1) || ($request->product_type == 2)) {
          $sCount = 1;
        }
  
        $data3 = [
          'product_id' => $id,
          'store_id' => $store_id,
          'variant_name' => $request->product_name,
        //   'product_varient_price' => $request->regular_price,
          'sku' => $request->sku,
          'product_varient_offer_price' => $request->sale_price,
          'product_varient_base_image' => null,
          'stock_count' => $sCount,
          'color_id' =>  0,
          'is_base_variant' => 1,
          'created_at' => $date,
          'updated_at' => $date,
        ];
        Mst_store_product_varient::create($data3);
        $vari_id = DB::getPdo()->lastInsertId();
  
        $sd = new Mst_StockDetail;
        $sd->store_id = $store_id;
        $sd->product_id = $id;
        $sd->stock = 0;
        $sd->product_varient_id = $vari_id;
        $sd->prev_stock = 0;
        $sd->save();
  
  
  
        $vac = 0;
        foreach ($request->attr_group_id[500] as $attrGrp) {
          if (isset($attrGrp) && isset($request->attr_value_id[500][$vac])) {
            $data4 = [
              'product_varient_id' => $vari_id,
              'attr_group_id' => $attrGrp,
              'attr_value_id' => $request->attr_value_id[500][$vac],
            ];
            Trn_ProductVariantAttribute::create($data4);
          }
          $vac++;
        }
  
        // dd($request->all());
  
        $vic = 0;
        foreach ($varImages as $vi) {
          $data77 = [
            [
              'product_image'      => $vi->product_image,
              'product_id' => $id,
              'product_varient_id' => $vari_id,
              'image_flag'         => 0,
              'created_at'         => Carbon::now(),
              'updated_at'         => Carbon::now(),
            ],
          ];
          Mst_product_image::insert($data77);
          $proImg_Id = DB::getPdo()->lastInsertId();
  
          $varImgsBase =   DB::table('mst_product_images')
            ->where('product_id', $id)
            ->where('product_varient_id', 0)
            ->where('image_flag', 1)->first();
  
  
          if (($varImgsBase->product_image ==  $vi->product_image) && ($vic == 0)) {
            DB::table('mst_store_product_varients')->where('product_varient_id', $vari_id)->update(['product_varient_base_image' => $vi->product_image]);
            $vic++;
            DB::table('mst_product_images')->where('product_image_id', $proImg_Id)->update(['image_flag' => 1]);
          }
        }
  
        //end 
  
        $date = Carbon::now();
        $vc = 0;
  
        foreach ($request->variant_name as  $varName) {
         
  
          if (isset($varName)) {
             if($request->var_sale_price[$vc]<=0)
          {
           return redirect()->back()->with('status-error', 'Varient Sale Price Must be greater than zero')->withInput();
          }
            $sCount = 0;
            if (($request->service_type == 1) || ($request->product_type == 2)) {
              $sCount = 1;
            }
  
            $data3 = [
              'product_id' => $id,
              'store_id' => $store_id,
              'variant_name' => $request->variant_name[$vc],
              'sku' => $request->sku,
              //'product_varient_price' => $request->var_regular_price[$vc],
              'product_varient_offer_price' => $request->var_sale_price[$vc],
              'product_varient_base_image' => null,
              'stock_count' => $sCount,
              'color_id' =>  0,
              'is_base_variant' =>  0,
              'created_at' => $date,
              'updated_at' => $date,
            ];
  
            Mst_store_product_varient::create($data3);
            $vari_id = DB::getPdo()->lastInsertId();
  
            $vac = 0; //varient_name0
  
            foreach ($request->attr_group_id[$vc] as $attrGrp) {
              $data4 = [
                'product_varient_id' => $vari_id,
                'attr_group_id' => $attrGrp,
                'attr_value_id' => $request->attr_value_id[$vc][$vac],
              ];
              Trn_ProductVariantAttribute::create($data4);
              $vac++;
            }
  
            $vic = 0;
            // dd( $request->file('var_images'.$vc));
            if (isset($request->file('var_images')[$vc])) {
  
              $files = $request->file('var_images')[$vc];
              //dd($files);
              foreach ($files as $file) {
                //   $filename = $file->getClientOriginalName();
                $filename = rand(1, 5000) . time() . '.' . $file->getClientOriginalExtension();
  
                $extension = $file->getClientOriginalExtension();
                $file->move('assets/uploads/products/base_product/base_image', $filename);
                $date = Carbon::now();
  
                $data5 = [
                  [
                    'product_image'      => $filename,
                    'product_id' => $id,
                    'product_varient_id' => $vari_id,
                    'image_flag'         => 0,
                    'created_at'         => $date,
                    'updated_at'         => $date,
                  ],
                ];
                Mst_product_image::insert($data5);
                $proImg_Id = DB::getPdo()->lastInsertId();
  
                if ($vic == 0) {
                  DB::table('mst_store_product_varients')->where('product_varient_id', $vari_id)->update(['product_varient_base_image' => $filename]);
                  $vic++;
                  DB::table('mst_product_images')->where('product_image_id', $proImg_Id)->update(['image_flag' => 1]);
                }
              }
            }
            $vc++;
          }
  
          $sd = new Mst_StockDetail;
          $sd->store_id = $store_id;
          $sd->product_id = $id;
          $sd->stock = 0;
          $sd->product_varient_id = $vari_id;
          $sd->prev_stock = 0;
          $sd->save();
        }
        //--------------------------    avoided ------------------------------------------
  
        //   $countVariants = Mst_store_product_varient::where('product_id', $id)->count();
        //   $varImages = Mst_product_image::where('product_id', $id)->orderBy('product_image_id', 'DESC')->get();
  
        //  if ($countVariants < 1) {
        //     $date = Carbon::now();
  
        //     $sCount = 0;
        //     if (($request->service_type == 1) || ($request->product_type == 2)) {
        //       $sCount = 1;
        //     }
  
        //     $data3 = [
        //       'product_id' => $id,
        //       'store_id' => $store_id,
        //       'variant_name' => $request->product_name,
        //       'product_varient_price' => $request->regular_price,
        //       'product_varient_offer_price' => $request->sale_price,
        //       'product_varient_base_image' => null,
        //       'stock_count' => $sCount,
        //       'color_id' =>  0,
        //       'is_base_variant' => 1,
        //       'created_at' => $date,
        //       'updated_at' => $date,
        //     ];
        //     Mst_store_product_varient::create($data3);
        //     $vari_id = DB::getPdo()->lastInsertId();
  
        //     $vac = 0;
        //     foreach ($request->attr_group_id[500] as $attrGrp) {
        //       if (isset($attrGrp) && isset($request->attr_value_id[500][$vac])) {
        //         $data4 = [
        //           'product_varient_id' => $vari_id,
        //           'attr_group_id' => $attrGrp,
        //           'attr_value_id' => $request->attr_value_id[500][$vac],
        //         ];
        //         Trn_ProductVariantAttribute::create($data4);
        //       }
        //       $vac++;
        //     }
  
        //     $vic = 0;
  
        //     foreach ($varImages as $vi) {
  
        //       $data77 = [
        //         [
        //           'product_image'      => $vi->product_image,
        //           'product_id' => $id,
        //           'product_varient_id' => $vari_id,
        //           'image_flag'         => 0,
        //           'created_at'         => Carbon::now(),
        //           'updated_at'         => Carbon::now(),
        //         ],
        //       ];
        //       Mst_product_image::insert($data77);
        //       $proImg_Id = DB::getPdo()->lastInsertId();
  
        //       if ($vic == 0) {
        //         DB::table('mst_store_product_varients')->where('product_varient_id', $vari_id)->update(['product_varient_base_image' => $vi->product_image]);
        //         $vic++;
        //         DB::table('mst_product_images')->where('product_image_id', $proImg_Id)->update(['image_flag' => 1]);
        //       }
        //     }
        //   }
  
  
        return redirect('store/product/list')->with('status', 'Product added successfully.');
          
        }
  
  
        
      } else {
  
        return redirect()->back()->withErrors($validator)->withInput();
      }
    }
    public function assignProducts(Request $request){
      
     try {

      $validator = Validator::make(
        $request->all(),
        [
            'branches'          => 'required',
            'assign_product'  => 'required'
        
        ],
        [
  
          'branches.required'             => 'Please select a Branch',
          'assign_product.required'       => 'Please select a Product'
          
        ]);

      

       if ($request->assign_product[0] == null) {
           
           return redirect()->back()->withErrors(['Please Select A Product'])->withInput();
           
       }
        
            
          foreach($request->branches as $branch)
            {
             $product = implode(',',$request->assign_product);
             $p = explode(',',$product);
            
              foreach($p as $global_product_id)
              {
                  
                $global_product = Mst_store_product::find($global_product_id);
                // dd($global_product);
                $branch_id =  $branch;
          
          
                // $product = Mst_store_product::where('store_id','=',$user_id)->get()->count();
          
                //check if product code exist in store table
                $ChkCodeExstnce = DB::table('mst_branch_products')->where('branch_id','=',$branch_id)->where('product_code',$global_product->product_code)->count();
                  
                if($ChkCodeExstnce >= 1)
                {
                    continue;
                }else{
  
  
                $product = new Mst_branch_product;
  
                $product->product_name = $global_product->product_name;
                $product->product_name_slug = Str::of($global_product->product_name)->slug('-');
                $product->product_code = $global_product->product_code;
                if (isset($global_product->business_type_id))
                {$product->business_type_id = $global_product->business_type_id;}

                else
                {$product->business_type_id = 0;}
                  
          
                if (isset($global_product->product_cat_id))
                 {
                   $product->product_cat_id = $global_product->product_cat_id;}
                else
                       {           $product->product_cat_id = 0;}
          
                if (isset($global_product->sub_category_id))
                 { $product->sub_category_id = $global_product->sub_category_id;}
                else
                 { $product->sub_category_id = 0;}
          
                if (isset($global_product->product_price))
                 { $product->product_price = $global_product->product_price;}
                else
                 { $product->product_price = 0;}
          
                if (isset($global_product->product_price_offer))
                 { $product->product_price_offer = $global_product->product_price_offer; }
                else
                 { $product->product_price_offer = 0; }
          
                if (isset($global_product->attr_group_id))
                 { $product->attr_group_id = $global_product->attr_group_id; }
                else
                 { $product->attr_group_id = 0; }
          
                if (isset($global_product->attr_value_id))
                 { $product->attr_value_id = $global_product->attr_value_id; }
                else
                 { $product->attr_value_id = 0; }
          
                if (isset($global_product->tax_id))
                 { $product->tax_id = $global_product->tax_id; }
                else
                 { $product->tax_id = 0; }
          
          
                if (isset($global_product->color_id))
                 { $product->color_id = $global_product->color_id; }
                else
                 { $product->color_id = 0; }
          
                if (isset($global_product->vendor_id))
                {  $product->vendor_id = $global_product->vendor_id; }
                else
                 { $product->vendor_id = 0;}
          
          
                $product->product_description = $global_product->product_description;
                $product->product_base_image = $global_product->product_base_image;
                $product->branch_id = $branch_id;
                $product->product_brand = $global_product->product_brand;
                $product->stock_count = $global_product->min_stock;
                $product->global_product_id = $global_product->product_id;
          
                $product->product_status = $global_product->product_status;
                $product->product_type = 1;
                $product->draft = 1;
          
                $product->save();
                $id = DB::getPdo()->lastInsertId();
          
          
              
          
                $data3 = [
                  'product_id' => $id,
                  'branch_id' => $branch_id,
                  'variant_name' => $global_product->product_name,
                  'product_varient_price' => $global_product->product_price,
                  'sku' => $global_product->sku,
                  'product_varient_offer_price' => $global_product->product_price_offer,
                  'product_varient_base_image' => $global_product->product_base_image,
                  'stock_count' => 0,
                  'is_base_variant' => 1,
                  'color_id' =>  0,
                  'created_at' => Carbon::now(),
                  'updated_at' => Carbon::now(),
                ];
          
                Mst_branch_product_varient::create($data3);
                
                Mst_store_product_varient::where('product_id',$global_product_id)->where('is_base_variant',0)
                 
                ->each(function ($oldRecord) use($id,$branch_id) {
                      
                      $newRecord = new Mst_branch_product_varient;
                      
                      $newRecord->product_id = $id;

                      $newRecord->branch_id = $branch_id;

                      $newRecord->variant_name = $oldRecord->variant_name;

                      $newRecord->product_varient_price = $oldRecord->product_varient_price;
                      
                      $newRecord->sku  = $oldRecord->sku;

                      $newRecord->product_varient_offer_price = $oldRecord->product_varient_offer_price;

                      $newRecord->product_varient_base_image = $oldRecord->product_varient_base_image;

                      $newRecord->stock_count = 0;

                      $newRecord->is_base_variant = 0;

                      $newRecord->created_at = Carbon::now();

                      $newRecord->updated_at = Carbon::now();
                         
                      $newRecord->save();

                      $newVarientId = $newRecord->varient_id;

                      Trn_ProductVariantAttribute::where('product_varient_id',$oldRecord->product_varient_id)

                      ->each(function ($old) use($newVarientId) {

                        $new = new Trn_ProductVariantAttribute;
                        
                        $new->attr_group_id = $old->attr_group_id;
                        
                        $new->attr_value_id = $old->attr_value_id;

                        $new->product_varient_id = $newVarientId;
                        
                        $new->save();


                      });  

                 });

               
                
                
                  }
             
          
             }
          }
             
         return redirect()->back()->with('status', 'Products assigned to Branches.');
     }catch (\Exception $e) {
      
      
      return redirect()->back()->withErrors($validator)->withInput();
    }
 
      
    }    
    
    public function assignProductsOld(Request $request){
        
        $validator = Validator::make(
        $request->all(),
        [
            'branches'          => 'required',
            'assign_product'  => 'required'
        
        ],
        [
  
          'branches.required'             => 'Please select a Branch',
          'assign_product.required'       => 'Please select a Product'
          
        ]);
       
        if (!$validator->fails() && ($request->assign_product[0] != null)) {
            
         foreach($request->branches as $branch)
            
            $cur_branch = Mst_branch::find($branch);
            
            $product = implode(',',$request->assign_product);
            $p = explode(',',$product);
            $cur_branch->products()->sync($p);
            
            return redirect()->back()->with('status', 'Branches assigned to Products.');
        }
        
        else {
  
        return redirect()->back()->withErrors($validator)->withInput();
      }
     
    }
  
    public function setDefaultImage(Request $request)
    {
      //dd($request->all());
      $imageData = Mst_product_image::where('product_image_id', $request->product_image_id)->where('product_varient_id', $request->product_varient_id)->first();
  
      Mst_product_image::where('product_id', $imageData->product_id)->where('product_varient_id', $request->product_varient_id)->update(['image_flag' => 0]);
  
      if ($request->product_varient_id == 0) {
  
        Mst_product_image::where('product_image_id', $request->product_image_id)->where('product_varient_id', $request->product_varient_id)->update(['image_flag' => 1]);
        Mst_store_product::where('product_id', $imageData->product_id)->update(['product_base_image' => $imageData->product_image]);
        return true;
      } else {
        Mst_product_image::where('product_image_id', $request->product_image_id)->where('product_varient_id', $request->product_varient_id)->update(['image_flag' => 1]);
        Mst_store_product_varient::where('product_varient_id', $imageData->product_varient_id)->update(['product_varient_base_image' => $imageData->product_image]);
        return true;
      }
      return false;
    }
  
  
  
  
    public function viewProduct(Request $request, $id)
    {
      $pageTitle = "View Product";
  
      $product = Mst_store_product::with('branches')->where('product_id', '=', $id)->first();
      
      $branch_products = Mst_branch_product::where('global_product_id',$id)->get()->pluck('branch_id');
      
      $branches = Mst_branch::whereIn('branch_id',$branch_products)->get(['branch_id','branch_name']);
  
      $product_base_varient = Mst_store_product_varient::where('product_id', $id)
        ->where('is_base_variant', 1)
        ->where('is_removed', 0)
        ->first();
      $product_base_varient_attrs = Trn_ProductVariantAttribute::where('product_varient_id', @$product_base_varient->product_varient_id)
        ->get();
  
      $product_id = $product->product_id;
      $product_varients = Mst_store_product_varient::where('product_id', $product_id)
        ->where('is_base_variant', '!=', 1)
        ->where('is_removed', 0)->orderBy('product_varient_id', 'DESC')
        ->get();
      // $varient_product = Mst_store_product_varient::where('product_id', '=',$product_id)->first();
  
      //$product_varient_id = $varient_product->product_varient_id;
  
      // $attr_groups = Mst_attribute_group::all();
      $product_images = Mst_product_image::where('product_id', '=', $product_id)->get();

  
      //dd($product_images);
  
      //   $store = Mst_store::all();
      // $categories = Mst_categories::where([['category_status', '=', '1'],['parent_id', '==', '0'],])->whereIn('category_id',['1','4','9'])->get();
  
  
      return view('admin.elements.product.view', compact('product_base_varient_attrs', 'product_base_varient', 'product_varients', 'product', 'pageTitle', 'product_images','branches'));
    }
  
  
    public function editProduct(Request $request, $id)
    {
      $pageTitle = "Edit Product";
  
      $product = Mst_store_product::where('product_id', '=', $id)->first();
      $product_id = $product->product_id;
      $videos = Trn_ProductVideo::where('product_id', '=', $id)->get();
  
      $product_varients = Mst_store_product_varient::where('product_id', $id)
        ->where('is_base_variant', '!=', 1)
        ->where('is_removed', 0)
        ->orderBy('product_varient_id', 'DESC')
        ->get();
  
      $product_base_varient = Mst_store_product_varient::where('product_id', $id)
        ->where('is_base_variant', 1)
        ->where('is_removed', 0)
        ->first();
      // if (isset($product_base_varient->product_varient_id)) {
      $product_base_varient_attrs = Trn_ProductVariantAttribute::where('product_varient_id', @$product_base_varient->product_varient_id)
        ->get();
      // } else {
      //   $product_base_varient_attrs = null;
      // }
      // dd($product_varients, $product_base_varient, $product_base_varient_attrs);
      @$category_id = $product->product_cat_id;
      $subcategories = Mst_ItemSubCategory::where('is_active', 1)->get();
      // dd($subcategories);
  
      // $varient_product = Mst_store_product_varient::where('product_id', '=',$product_id)->first();
  
      // $product_varient_id = $varient_product->product_varient_id;
      $business_types = Mst_business_types::all();
      $attr_groups = Mst_attribute_group::all();
      $product_images = Mst_product_image::where('product_id', '=', $product_id)->where('product_varient_id', '=', 0)->get();
      // $product_images = Mst_product_image::with('variant')->whereHas('variant', function (Builder $query) use($product_id) {
      //     return $query->where('product_id','=',$product_id);
      //   })->get();
      $tax = Mst_Tax::where('is_removed', '!=', 1)->get();
      $category = Mst_ItemCategory::where('is_active', 1)->get();
  
      $colors = Mst_attribute_value::join('mst_attribute_groups', 'mst_attribute_groups.attr_group_id', '=', 'mst_attribute_values.attribute_group_id')
        ->where('mst_attribute_groups.group_name', 'LIKE', '%color%')
        ->select('mst_attribute_values.*')
        ->get();
      $brands = Mst_Brand::where('is_active', 1)->get();
  
  
      $store = Mst_store::all();
  
      return view('admin.elements.product.edit', compact(
        'product_base_varient_attrs',
        'product_base_varient',
        'subcategories',
        'product_varients',
        'category',
        'brands',
        'colors',
        'tax',
        'product',
        'pageTitle',
        'attr_groups',
        'store',
        'product_images',
        'business_types',
        'videos'
      ));
    }
  
  
    public function GetGlobal_Product(Request $request)
    {
      $vendor_id = $request->vendor_id;
  
      $store_id =  Auth::user()->store_id;
  
      $products_global_products_id = Mst_store_product::where('store_id', $store_id)
        ->where('global_product_id', '!=', null)
        ->orderBy('product_id', 'DESC')
        ->pluck('global_product_id')
        ->toArray();
  
      $global_product = Mst_GlobalProducts::whereNotIn('global_product_id', $products_global_products_id)
        ->where('vendor_id', $vendor_id)->pluck("product_name", "global_product_id");
  
  
  
  
      return response()->json($global_product);
    }
  
    public function destroyProductImage(Request $request, $product_image_id, Mst_product_image $pro_image)
    {
      // echo $product_image_id;die;
      //check if base image
      $proImg = Mst_product_image::where('product_image_id', '=', $product_image_id)->first();
      
      $product = Mst_store_product::where('product_id',$proImg->product_id)->first();
  
      $proImgCount = Mst_product_image::where('product_varient_id', '=', $proImg->product_varient_id)->count(); 
  
      if($proImgCount >  1) //has subimages
      {
        if($proImg->image_flag == 1)  //base imge
        {
  
        
          $pro_image = Mst_product_image::where('product_image_id', '=', $product_image_id);
          $pro_image->delete();
          $pro_imageTwo = Mst_product_image::where('product_varient_id', '=', $proImg->product_varient_id)->first();
          //dd($pro_imageTwo);
  
          Mst_product_image::where('product_image_id', '=', $pro_imageTwo->product_image_id)
          ->update(['image_flag' => 1]);
  
          Mst_store_product_varient::where('product_varient_id', '=', $pro_imageTwo->product_varient_id)
          ->update(['product_varient_base_image' => $pro_imageTwo->product_image]);
  
          
          //$checkIfbase = Mst_store_product_varient::where('product_varient_id', '=', $pro_imageTwo->product_varient_id)->where('is_base_variant',1)->count();
          
          if($proImg->product_image == $product->product_base_image )  // base image
            {
             
              Mst_store_product::where('product_id','=',$proImg->product_id)->update([
            'product_base_image' => NULL]);
            }
  
          
  
        }else{
          $pro_image = Mst_product_image::where('product_image_id', '=', $product_image_id);
          $pro_image->delete();
        }
  
  
      }else{
        return redirect()->back()->with('status', 'Base image cannot be deleted.');
      }
  
  
      return redirect()->back()->with('status', 'Product Image Deleted Successfully.');
  
    }
  
    public function updateProductImages(Request $request, $product_id)
    {
      try {
  
        //if (isset($request->product_varient_id)) {
          $product_var_id = $request->product_varient_id;
        //} else {
          //$product_var_id = 0;
        //}
        $product_var_id = 0;
        if ($request->hasFile('var_image')) {
          $allowedfileExtension = ['jpg', 'png', 'jpeg',];
          $files = $request->file('var_image');
          foreach ($files as $file) {
            $filename = rand(1, 5000) . time() . '.' . $file->getClientOriginalExtension();
            $extension = $file->getClientOriginalExtension();
            $file->move('assets/uploads/products/base_product/base_image', $filename);
            $date = Carbon::now();
            $data1 = [
              [
                'product_image'      => $filename,
                'product_id' => $product_id,
                'product_varient_id' => $product_var_id,
                'image_flag'         => 0,
                'created_at'         => $date,
                'updated_at'         => $date,
              ],
            ];
            Mst_product_image::insert($data1);
          }
        }
        return redirect()->back()->with('status', 'Image upadated successfully.');
      } catch (\Exception $e) {
  
        return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
      }
    }
  
  
  
  
    public function updateProduct(Request $request, $product_id, Mst_store_product_varient $varient_product)
    {
  
      

      $store_id = 1;
      
      $product_id = $request->product_id;
     
      $validator = Validator::make(
        $request->all(),
        [
          // 'product_name'          => 'required|unique:mst_store_products,product_name,'.$store_id.',store_id',
          'product_name'          => 'required',
          'product_description'   => 'required',
          'sku' => 'required',
          'sale_price'   => 'required|numeric|gt:0',
          'min_stock'   => 'required',
          'product_code'   => 'required',
          'product_cat_id'   => 'required',
          'food_type'   => 'required',
          // 'vendor_id'   => 'required',
          // 'color_id'   => 'required',
          //'regular_price'   => 'required',
          //'tax_id'   => 'required',
          // 'business_type_id'   => 'required',
          //'attr_group_id'   => 'required',
          // 'attr_value_id'   => 'required',
          //'var_sale_price.*'=>'sometimes|required|numeric|gt:0',
          'product_image.*' => 'dimensions:min_width=1000,min_height=800|mimes:jpeg,jpg,png|max:10000',
          'product_image.*' => 'required|mimes:jpeg,jpg,png|max:10000',
  
  
  
        ],
        [
  
          'product_name.required'             => 'Product name required',
          'product_name.unique'             => 'Product name already exist',
          'product_description.required'      => 'Product description required',
          'sale_price.required'      => 'Sale price required',
          'sale_price.gt'      => 'Sale price Must be greater than Zero',
          //'var_sale_price.*.gt'=>'Varient Sale Price must be greater than zero',
          //'var_sale_price.*.numeric'=>'Varient Sale Price must be a number',
          'tax_id.required'      => 'Tax required',
          'min_stock.required'      => 'Minimum stock required',
          'product_code.required'      => 'Product code required',
          'business_type_id.required'        => 'Product type required',
          'attr_group_id.required'        => 'Attribute group required',
          'attr_value_id.required'        => 'Attribute value required',
          'product_cat_id.required'        => 'Product category required',
          'vendor_id.required'        => 'Vendor required',
          'color_id.required'        => 'Color required',
          'product_image.required'        => 'Product image required',
          'product_image.dimensions'        => 'Product image dimensions invalid',
  
  
        ]
      );
  
      if (!$validator->fails()) {
        $ChkCodeExstnce = DB::table('mst_store_products')->where('store_id','=',$store_id)->where('product_code',$request->product_code)->count();
          
        if($ChkCodeExstnce > 1)
        {
  
          return redirect()->back()->with('status-error', 'Product code already used by the store.')->withInput();
  
        }else{
  
        
        $product['product_name']          = $request->product_name;
        $product['product_description']    = $request->product_description;
        $product['product_price_offer']    = $request->sale_price;
        $product['sku']    = $request->sku;
        if (isset($request->regular_price) || isset($request->sale_price)) {
          $provarUp = array();
          $provarUp['product_varient_price'] = $request->regular_price;
          $provarUp['product_varient_offer_price']  = $request->sale_price;
  
          Mst_store_product_varient::where('product_id', $product_id)
            ->where('is_base_variant', 1)->update($provarUp);
        }
  
  
          
        $product['stock_count']                = $request->min_stock; // stock count
        $product['product_code']          = $request->product_code;
  
        if (isset($request->business_type_id))
          $product['business_type_id']       = $request->business_type_id; // product type
        else
          $product['business_type_id']       = 0;
  
        $product['product_cat_id']         = $request->product_cat_id;
        $product['vendor_id']            = $request->vendor_id; // new
        $product['product_brand']            = $request->product_brand; // new
        $product['is_must_try']=$request->is_must_try;
        $product['is_must_recommended']=$request->is_must_recommended;
        $product['food_type']=$request->food_type;
  
        $product['sub_category_id']            = $request->sub_category_id; // 
        $product['sub_category_leveltwo']            =$request->sub_category_id_lvltwo;
        $product['product_type']            = $request->product_type; // new
        if ($request->product_type == 2)
          $product['service_type']            = $request->service_type; // new
        else
          $product['service_type']            = 0; // new
  
        $product['product_name_slug']      = Str::of($request->product_name)->slug('-');
        $product['store_id']               = $store_id;
  
        if ($request['min_stock'] == 0) {
          $product['stock_status'] = 0;
        } else {
          $product['stock_status'] = 1;
        }
  

  
  
        DB::table('mst_store_products')->where('product_id', $product_id)->update($product);
  
        // adding product images
        $baseVar =   Mst_store_product_varient::where('product_id', $product_id)->where('is_base_variant', 1)->first();
  
        if ($request->hasFile('product_image')) {
          // echo "here";die;
          $allowedfileExtension = ['jpg', 'png', 'jpeg',];
          $files = $request->file('product_image');
          foreach ($files as $file) {
  
  
  
            // $filename = $file->getClientOriginalName();
            $filename = rand(1, 5000) . time() . '.' . $file->getClientOriginalExtension();
  
            $extension = $file->getClientOriginalExtension();
  
            // $fullpath = $filename . '.' . $extension ;
            $file->move('assets/uploads/products/base_product/base_image', $filename);
            $date = Carbon::now();
            $data1 = [
              [
                'product_image'      => $filename,
                'product_id' => $product_id,
                'product_varient_id' => @$baseVar->product_varient_id,
                'image_flag'         => 0,
                'created_at'         => $date,
                'updated_at'         => $date,
              ],
            ];
  
            Mst_product_image::insert($data1);
          }
        }
  
  
        $data3 = [
          'variant_name' => $request->product_name,
          //'product_varient_price' => $request->regular_price,
          'product_varient_offer_price' => $request->sale_price,
          'sku' => $request->sku,
        ];
  
        Mst_store_product_varient::where("product_varient_id", @$baseVar->product_varient_id)->update($data3);
  
        $vac = 0;
  
        foreach ($request->attr_group_id[500] as $attrGrp) {
          if (isset($attrGrp) && isset($request->attr_value_id[500][$vac])) {
            $data4 = [
              'product_varient_id' => @$baseVar->product_varient_id,
              'attr_group_id' => $attrGrp,
              'attr_value_id' => $request->attr_value_id[500][$vac],
            ];
            Trn_ProductVariantAttribute::create($data4);
            echo @$baseVar->product_varient_id . " : " . " : " . $attrGrp . " : " . $request->attr_value_id[500][$vac] . "<br>";
          }
  
          $vac++;
        }
  
        //  dd($request->all());
  
        $date = Carbon::now();
        $vc = 0;
  
        foreach ($request->variant_name as  $varName) {
  
          if (isset($varName)) {
            if($request->var_sale_price[$vc]<=0)
          {
           return redirect()->back()->with('status-error', 'Varient Sale Price Must be greater than zero')->withInput();
          }
  
            if(isset($request->var_sale_price[$vc]) && $request->attr_group_id[$vc] && isset($request->file('var_images')[$vc]))
              
                  {
  
            $sCount = 0;
            if (($request->product_type == 2) || ($request->service_type == 1)) {
              $sCount = 1;
            }
            $data3 = [
              'product_id' => $product_id,
              'store_id' => $store_id,
              'variant_name' => $request->variant_name[$vc],
              //'product_varient_price' => $request->var_regular_price[$vc],
              'sku' => $request->sku,
              'product_varient_offer_price' => $request->var_sale_price[$vc],
              'product_varient_base_image' => null,
              'stock_count' => $sCount,
              'color_id' =>  0,
              'created_at' => $date,
              'updated_at' => $date,
              // 'attr_group_id' => $request->attr_group_id[$vc],
              // 'attr_value_id' => $request->attr_value_id[$vc],
            ];
  
            Mst_store_product_varient::create($data3);
            $vari_id = DB::getPdo()->lastInsertId();
            
  
  
            $sd = new Mst_StockDetail;
            $sd->store_id = @$store_id;
            $sd->product_id = $product_id;
            $sd->stock = 0;
            $sd->product_varient_id = $vari_id;
            $sd->prev_stock = 0;
            $sd->save();
  
  
  
            $vac = 0;
  
            foreach ($request->attr_group_id[$vc] as $attrGrp) {
              $data4 = [
                'product_varient_id' => $vari_id,
                'attr_group_id' => $attrGrp,
                'attr_value_id' => $request->attr_value_id[$vc][$vac],
              ];
              Trn_ProductVariantAttribute::create($data4);
              $vac++;
            }
  
            $vic = 0;
            // dd( $request->file('var_images'.$vc));
            if (isset($request->file('var_images')[$vc])) {
  
              $files = $request->file('var_images')[$vc];
              //dd($files);
              foreach ($files as $file) {
                // $filename = $file->getClientOriginalName();
                $filename = rand(1, 5000) . time() . '.' . $file->getClientOriginalExtension();
  
                $extension = $file->getClientOriginalExtension();
                $file->move('assets/uploads/products/base_product/base_image', $filename);
                $date = Carbon::now();
  
                $data5 = [
                  [
                    'product_image'      => $filename,
                    'product_id' => $product_id,
                    'product_varient_id' => $vari_id,
                    'image_flag'         => 1,
                    'created_at'         => $date,
                    'updated_at'         => $date,
                  ],
                ];
                Mst_product_image::insert($data5);
                if ($vic == 0) {
                  DB::table('mst_store_product_varients')->where('product_varient_id', $vari_id)
                    ->update(['product_varient_base_image' => $filename]);
                  $vic++;
                }
              }
            }
            $vc++;
          }else{
            return redirect()->back()->with('status-empty-field', 'Unable to add varient. One or more varient fields are empty');
          }
          }
        }
        
        
           $product__ = Arr::except($product,['store_id','is_must_try','is_must_recommended','food_type']);
         
           Mst_branch_product::where('global_product_id',$request->product_id)->update($product__);
           
           $branch_products =  Mst_branch_product::where('global_product_id',$request->product_id)->get();
          
           $branch_products_varients = Mst_branch_product_varient::whereIn('product_id',$branch_products->pluck('branch_product_id'))->update(['variant_name' => $request->product_name]);
           
           
         
         
        return redirect('store/product/list')->with('status', 'Product Updated Successfully.');
  
        }
  
  
        
      } else {
  
        return redirect()->back()->withErrors($validator)->withInput();
      }
    }
  
  
  
  
  
    public function destroyProduct(Request $request, $product)
    {
  
      $removeProduct = array();
      $removeProduct['is_removed'] = 1;
      $removeProduct['product_status'] = 0;
  
      $removeProductVar = array();
      $removeProductVar['is_removed'] = 1;
      $removeProductVar['stock_count'] = 0;
  
      $productData  = Mst_store_product::find($product);
  
      if (isset($productData->global_product_id))
        $removeProduct['global_product_id'] = 0;
  
      Mst_store_product::where('product_id', $product)->update($removeProduct);
  
      Mst_store_product_varient::where('product_id', $product)->update($removeProductVar);
      
      Mst_branch_product::where('global_product_id', $product)->update($removeProduct);
      
      $varients = Mst_branch_product::where('global_product_id', $product)->get()->pluck('branch_product_id');
      
      Mst_branch_product_varient::whereIn('product_id',$varients)->update($removeProductVar);
      
      
      Trn_WishList::whereIn('product_variant_id',Mst_branch_product_varient::whereIn('product_id',$varients)->get()->pluck('varient_id'))->delete();
  
  
      return redirect('store/product/list')->with('status', 'Product deleted Successfully');
    }
  
    public function restoreProduct(Request $request)
    {
           try {
               
  
  
        $pageTitle = "Restore Products";
        // $store_id =  Auth::user()->store_id;
        $store_id = 1;
        $products = Mst_store_product::join('mst_store_categories', 'mst_store_categories.category_id', '=', 'mst_store_products.product_cat_id')
          ->where('mst_store_products.store_id', $store_id)
          ->where('is_removed', 1)
          ->orderBy('mst_store_products.product_id', 'DESC')->get();
        //dd($products);
        $store = Mst_store::all();
        
      
  
        return view('admin.elements.product.restore', compact('products', 'pageTitle', 'store'));
      } catch (\Exception $e) {
  
        return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
      }
        
      }
  
  
  
  
   public function restoreProductSave(Request $request, $product)
    {
       
  
      $removeProduct = array();
      $removeProduct['is_removed'] = 0;
      $removeProduct['product_status'] = 0;
  
      $removeProductVar = array();
      $removeProductVar['is_removed'] = 0;
      $removeProductVar['stock_count'] = 0;
  
      // $productData  = Mst_store_product::find($product);
  
      // if (isset($productData->global_product_id))
      //   $removeProduct['global_product_id'] = 0;
  
      Mst_store_product::where('product_id', $product)->update($removeProduct);
  
      Mst_store_product_varient::where('product_id', $product)->update($removeProductVar);
      
      Mst_branch_product::where('global_product_id', $product)->update($removeProduct);
      
      $vaients = Mst_branch_product::where('global_product_id', $product)->get()->pluck('branch_product_id');
      
      Mst_branch_product_varient::whereIn('product_id',$vaients)->update($removeProductVar);
      
  
  
  
      return redirect('store/product/restore')->with('status', 'Product Restored Successfully');
    }
    
    
  
    public function statusProduct(Request $request, Mst_store_product $product, $product_id)
    {
  
      $pro_id = $request->product_id;
      $product = Mst_store_product::Find($pro_id);
      $status = $product->product_status;
      
      $varCount = Mst_store_product_varient::where('product_id', $product_id)->count();
      if ($varCount > 0) {
        
        if ($status == 0) {
          
          $product->product_status  = 1;
        } else {
          $product->product_status  = 0;
        }
        $product->update();
        
        
        return redirect()->back()->with('status', 'Product Status Changed Successfully');
      } else {
        return redirect()->back()->with('err_status', 'No variant exists.');
      }
    }
    
    public function editStatusProduct(Request $request)
    {
        $pro_id = $request->product_id;
        
        if ($c = Mst_store_product::Find($pro_id)) {
            if ($c->product_status == 0) {
                Mst_store_product::where('product_id', $pro_id)->update(['product_status' => 1]);
                echo "active";
            } else {
                Mst_store_product::where('product_id', $pro_id)->update(['product_status' => 0]);
                echo "inactive";
            }
        }
    }
    
    public function addToMissMeProduct(Request $request)
    {
        $pro_id = $request->product_id;
        
        
        if ($c = Mst_store_product::Find($pro_id)) {
            if ($c->did_u_miss_me == 0) {
                Mst_store_product::where('product_id', $pro_id)->update(['did_u_miss_me' => 1]);
                
                $branch_products = Mst_branch_product::where('global_product_id' , $c->product_id)->get(['branch_product_id'])->pluck('branch_product_id')->all();
                
                Mst_branch_product_varient::whereIn('product_id',$branch_products)->update(['did_u_miss_me' => 1]);
                
                
                echo "active";
            } else {
                
                Mst_store_product::where('product_id', $pro_id)->update(['did_u_miss_me' => 0]);
                
                $branch_products = Mst_branch_product::where('global_product_id' , $c->product_id)->get(['branch_product_id'])->pluck('branch_product_id')->all();
                
                Mst_branch_product_varient::whereIn('product_id',$branch_products)->update(['did_u_miss_me' => 0]);
                
                echo "inactive";
            }
        }
    }

  
    public function stockUpdate(
      Request $request,
      Mst_store_product $product,
      $product_id
    ) {
  
  
      $product_id = $request->product_id;
      $product = Mst_store_product::Find($product_id);
  
      $validator = Validator::make(
        $request->all(),
        [
  
          'stock_count'   => 'required',
  
        ],
        [
          'stock_count.required' => 'Status required',
  
  
        ]
      );
      // $this->uploads($request);
      if (!$validator->fails()) {
        $data = $request->except('_token');
  
  
        $product->stock_count = $request->stock_count;
        if ($request->stock_count == 0) {
          $product->stock_status = 0;
        } else {
          $product->stock_status = 1;
        }
  
        $product->update();
  
        return redirect()->back()->with('status', 'Stock Updated successfully.');
      } else {
        return redirect()->back()->withErrors($validator)->withInput();
      }
    }
  // inventory management


   function listInventory(Request $request)
  {
        
       $pageTitle = "Inventory Management";
    
       
       
       $products = Mst_branch_product_varient::join('mst_branch_products', 'mst_branch_products.branch_product_id', '=', 'mst_branch_product_varients.product_id')
       ->join('mst_branches', 'mst_branches.branch_id', '=', 'mst_branch_product_varients.branch_id')
      ->join('mst__item_categories', 'mst__item_categories.item_category_id', '=', 'mst_branch_products.product_cat_id')
       
      
      
      ->where('mst_branch_products.is_removed', 0)
      ->where('mst__item_categories.is_active', 1)
      ->orderBy('mst_branch_product_varients.stock_count', 'ASC')
      ->where('mst_branch_product_varients.is_removed', 0);

    if (isset($request->stock_status)) {
          
          if($request->stock_status == 2 )
          {
             
            $products =  $products->where('mst_branch_product_varients.stock_count','>' ,0);
          }
          else
          {
            $products =  $products->where('mst_branch_product_varients.stock_count', '<=',0);      
          }      
          
      }

    if ($request->product_cat_id) {
      $products = $products->where('mst_branch_products.product_cat_id', $request->product_cat_id);
    }
    if ($request->branch_id) {
      $products = $products->where('mst_branch_product_varients.branch_id', $request->branch_id);
    }
    if ($request->product_name) {
      $products = $products->where('mst_branch_product_varients.variant_name', 'LIKE', '%' . $request->product_name . '%');
    }

    $products = $products->orderBy('mst_branch_product_varients.stock_count', 'ASC')
      ->select(
        'mst_branch_products.branch_product_id',
        'mst_branch_products.product_name',
        'mst_branch_products.product_code',
        'mst_branch_products.product_cat_id',
        'mst_branch_products.product_base_image',
        'mst_branch_products.product_status',
        'mst_branch_products.product_brand',
        'mst_branch_product_varients.varient_id',
        'mst_branch_product_varients.variant_name',
        'mst_branch_product_varients.product_varient_price',
        'mst_branch_product_varients.product_varient_offer_price',
        'mst_branch_product_varients.product_varient_base_image',
        'mst_branch_product_varients.stock_count',
        'mst_branches.branch_name'
      )
      ->paginate(10);
    $category = Mst_ItemCategory::where('is_active', 1)->get();

    $branches = Mst_branch::get();
    
    return view('admin.elements.inventory.list', compact('category', 'products', 'branches','pageTitle'));
  }



  public function UpdateStock(Request $request)
  {
     
    $updated_stock = $request->updated_stock;
    $product_varient_id = $request->product_varient_id;

    $usOld = DB::table('mst_branch_product_varients')->where('varient_id', $product_varient_id)->first();

    if ($us = DB::table('mst_branch_product_varients')->where('varient_id', $product_varient_id)->increment('stock_count', $updated_stock)) {
      $usData = DB::table('mst_branch_product_varients')->where('varient_id', $product_varient_id)->first();
      $usProData =  DB::table('mst_branch_products')->where('branch_product_id', $usData->product_id)->first();

      $productData2['product_status'] = 1;
      //Mst_store_product::where('product_id', $usData->product_id)->update($productData2);

      $sd = new Mst_StockDetail;
      $sd->store_id = $usProData->branch_id;
      $sd->product_id = $usData->product_id;
      $sd->stock = $request->updated_stock;
      $sd->product_varient_id = $request->product_varient_id;
      $sd->prev_stock = $usOld->stock_count;

      $sd->save();

      $s = DB::table('mst_branch_product_varients')->where('varient_id', $product_varient_id)->pluck("stock_count");

      return response()->json($s);
    } else {
      echo "error";
    }
  }

  public function resetStock(Request $request)
  {
     
    $product_varient_id = $request->product_varient_id;

    $usData = DB::table('mst_branch_product_varients')->where('varient_id', $product_varient_id)->first();
    

    if ($us = DB::table('mst_branch_product_varients')->where('varient_id', $product_varient_id)->update(['stock_count' => 0, 'updated_at' => Carbon::now()])) {
      DB::table('mst__stock_details')->where('product_varient_id', $product_varient_id)->update(['created_at' => Carbon::now()]);
      $s = DB::table('mst_branch_product_varients')->where('varient_id', $product_varient_id)->pluck("stock_count");

      // $prodctCnt = DB::table('mst_branch_product_varients')->where('product_id', $usData->product_id)->count();
      // if($prodctCnt > 0)
      // {
            //insert a product status column in varient table
      // }

      //$productData2['product_status'] = 1;
     // Mst_branch_product::where('product_id', $usData->product_id)->update($productData2);

      return response()->json($s);
    } else {
      echo "error";
    }
  }
  public function removeProductVideo(Request $request, $product_video_id)
  {
    $pro_variant = Trn_ProductVideo::where('product_video_id', '=', $product_video_id)->delete();
    return redirect()->back()->with('status', 'Video deleted');
  }
  public function statusStoreIMG(Request $request, $imgId)
  {
    try {

       $dataImage = Mst_product_image::find($imgId);
      
      
     
       $coImg = Mst_product_image::where('product_id', $dataImage->product_id)->where('product_varient_id', $dataImage->product_varient_id)
         ->update(['image_flag' => 0]);

       $coImg = Mst_product_image::where('product_image_id', $imgId)->update(['image_flag' => 1]);


       Mst_product_image::where('product_image_id', $dataImage->product_image_id)->where('product_varient_id', $dataImage->product_varient_id)->update(['image_flag' => 1]);
       Mst_store_product_varient::where('product_varient_id', $dataImage->product_varient_id)->update(['product_varient_base_image' => $dataImage->product_image]);
       Mst_store_product::where('product_id', $dataImage->product_id)->update(['product_base_image' => $dataImage->product_image]);
       Mst_branch_product::where('global_product_id', $dataImage->product_id)->update(['product_base_image' => $dataImage->product_image]);
       
       $base_vairents = Mst_branch_product::where('global_product_id', $dataImage->product_id)->get()->pluck('branch_product_id');
       
       Mst_branch_product_varient::whereIn('product_id',$base_vairents)->where('is_base_variant',1)->update(['product_varient_base_image' => $dataImage->product_image]);

       $isBaseVar = Mst_store_product_varient::where('product_varient_id', $dataImage->product_varient_id)->first();

       if (@$isBaseVar->is_base_variant == 1)
      Mst_store_product::where('product_id', $dataImage->product_id)->update(['product_base_image' => $dataImage->product_image]);


      return redirect()->back()->with('status', 'Base image successfully updated.');
    } catch (\Exception $e) {
      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }

  public function listProductVariant(Request $request, $product_id)
  {
    $pageTitle = "Product Variants";
    //$store_id  = Auth::guard('store')->user()->store_id;
    $store_id = 1;
    $attr_groups = Mst_attribute_group::all();

    $product_variants = Mst_store_product_varient::where('product_id', '=', $product_id)
      ->where('is_base_variant', '!=', 1)
      ->where('is_removed', 0)->get();
    return view('admin.elements.product.view_variants', compact('attr_groups', 'product_variants', 'pageTitle', 'store_id'));
  }
  public function editProductVariant(Request $request, $product_varient_id)
  {
    $pageTitle = "Edit Product Variant";
    // $store_id  = Auth::guard('store')->user()->store_id;
    $store_id = 1;
    $attr_groups = Mst_attribute_group::all();
    $product_base_varient_attrs  = Trn_ProductVariantAttribute::where("product_varient_id", '=', $product_varient_id)->get();

    $product_variant = Mst_store_product_varient::find($product_varient_id);
    return view('admin.elements.product.edit_variant', compact('product_base_varient_attrs', 'attr_groups', 'product_variant', 'pageTitle', 'store_id'));
  }

  public function updateProductVariant(Request $request, $product_varient_id)
  {
    $data['variant_name'] = $request->variant_name;
    $data['product_varient_price'] = $request->product_varient_price;
    $data['product_varient_offer_price'] = $request->product_varient_offer_price;
    $data['stock_count'] = $request->stock_count;
    if ($request->hasFile('base_image')) {

      $file = $request->file('base_image');
      // $filename = $file->getClientOriginalName();
      $filename = rand(1, 5000) . time() . '.' . $file->getClientOriginalExtension();

      $file->move('assets/uploads/products/base_product/base_image', $filename);
      $data['product_varient_base_image'] = $filename;
    }
    Mst_store_product_varient::where('product_varient_id', $product_varient_id)->update($data);

    return redirect('store/product/list')->with('status', 'Product variant updated successfully.');
  }
  public function ShareItems(Request $request)
  {
    //($request->all());

    $order = Trn_store_order::where('order_id', $request->order_id)->first();

    $url = url('item/list/' . Crypt::encryptString($request->order_id));
    $msg = 'Order number ' . $order->order_number . ' items list.       ' . $url;
    //$msg = htmlentities($msg);

    return redirect()->away('https://api.whatsapp.com/send?phone=+91' . $request->mobile_number . '&text=' . $msg);
  }
  public function addProductVariantAttr(Request $request, Trn_ProductVariantAttribute $var_att)
  {

    $validator = Validator::make(
      $request->all(),
      [
        'attr_grp_id' => ['required'],
        'attr_val_id' => ['required'],
      ],
      [
        'attr_grp_id.required'         => 'Attribute group required',
        'attr_val_id.required'         => 'Attribute value required',
      ]
    );

    if (!$validator->fails()) {

      $var_att->product_varient_id = $request->product_varient_id;
      $var_att->attr_group_id = $request->attr_grp_id;
      $var_att->attr_value_id = $request->attr_val_id;
      $var_att->save();
      return redirect()->back()->with('status', 'New attribute added to product variant successfully.');
    } else {
      return redirect()->back()->withErrors($validator)->withInput();
    }
  }
  public function destroyProductVariant(Request $request, $product_varient_id)
  {
    $pro_variant = Mst_store_product_varient::where('product_varient_id', '=', $product_varient_id)->first();

    $removeProduct = array();
    $removeProduct['is_removed'] = 1;
    $removeProduct['product_status'] = 0;

    $removeProductVar = array();
    $removeProductVar['is_removed'] = 1;
    $removeProductVar['stock_count'] = 0;
    $productVar = Mst_store_product_varient::where('product_varient_id', $request->product_varient_id)->first();
    $productVarCount = Mst_store_product_varient::where('product_id', $productVar->product_id)
          ->where('is_base_variant', '!=', 1)
          ->where('is_removed', '!=', 1)->count();

      if ($productVarCount <= 1) {
          Mst_store_product_varient::where('product_varient_id', $request->product_varient_id)->update($removeProductVar);
          //  Mst_store_product::where('product_id', $productVar->product_id)->update($removeProduct);
          // update(['product_status' => 0]);

      } else {
          Mst_store_product_varient::where('product_varient_id', $request->product_varient_id)->update($removeProductVar);
      }

    // Mst_store_product_varient::where('product_varient_id', '=', $product_varient_id)->update($removeProductVar);

    // $productVarCount = Mst_store_product_varient::where('product_id', $pro_variant->product_id)
    //   ->where('is_base_variant', '!=', 1)
    //   ->where('is_removed', '!=', 1)->count();

    // if ($productVarCount <= 1) {
    //   Mst_store_product::where('product_id', $pro_variant->product_id)->update($removeProduct);
    // }

    return redirect()->back()->with('status', 'Product variant deleted successfully.');
  }

  public function destroyProductVariantAttr(Request $request, $variant_attribute_id)
  {
    $pro_variant_attr = Trn_ProductVariantAttribute::where('variant_attribute_id', '=', $variant_attribute_id);
    $pro_variant_attr->delete();
    return redirect()->back()->with('status', 'Product variant attribute deleted successfully.');
  }

  public function GetVarAttr_Count(Request $request)
  {
    $product_varient_id = $request->product_varient_id;
    // dd($grp_id);
    $provar = Mst_store_product_varient::find($product_varient_id);

    $productVarCount = Mst_store_product_varient::where('product_id', $provar->product_id)
      ->where('is_base_variant', '!=', 1)
      ->where('is_removed', '!=', 1)->count();

    $attrCount  = Trn_ProductVariantAttribute::where("product_varient_id", '=', $product_varient_id)->count();

    if ($productVarCount <= 0) {
      return '0';
    } else {
      if ($attrCount <= 1) {
        return '1';
      } else {
        return '0';
      }
    }

    echo $productVarCount;
  }

  public function importGlobalProduct(Request $request)
  {
    $pageTitle = "Import Products";

    return view('admin.elements.product.import', compact('pageTitle'));
  }

  public function postImportGlobalProduct(Request $request)
  {
    //dd($request->all());

    $validator = Validator::make(
      $request->all(),
      [
        'products_file'                  => 'required|mimes:xlsx',


      ],
      [
        'products_file.required'         => 'Products file  required',
        'products_file.mimes'         => 'Invalid file format',


      ]
    );

   

    $file = $request->file('products_file')->store('import');

    (new GlobalProductsImport)->import($file);
    return redirect()->back()->with('status', 'products imported successfully.');
    

  }  
  
  public function listReviews()
  {
     $pageTitle = "Reviews";
     $ratings = Trn_ReviewsAndRating::get();
     return view('admin.elements.reviews.list', compact('pageTitle','ratings'));
 
  }
  
  public function editStatusReviews(Request $request)
    {
        $review_id = $request->review_id;
        
        
        if ($c = Trn_ReviewsAndRating::Find($review_id)) {
            if ($c->isVisible == 0) {
                Trn_ReviewsAndRating::where('reviews_id', $review_id)->update(['isVisible' => 1]);
                echo "active";
            } else {
                Trn_ReviewsAndRating::where('reviews_id', $review_id)->update(['isVisible' => 0]);
                echo "inactive";
            }
        }
    }
 
    
}
