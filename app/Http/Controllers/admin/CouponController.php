<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;

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

use App\Models\Mst_branch;
use App\Models\admin\Mst_Coupon;
use App\Models\admin\Sys_payment_type;
use App\Models\admin\Trn_OrderPaymentTransaction;
use App\Models\admin\Mst_store;
use App\Models\admin\Mst_product_image;
use App\Models\admin\Mst_ItemCategory;
use App\Models\admin\Mst_ItemSubCategory;
use App\Models\admin\Mst_store_product;
use App\Models\admin\Mst_branch_product;
use App\Models\admin\Mst_branch_product_varient;
use App\Models\admin\Mst_store_product_varient;
use App\Models\admin\Trn_RecentlyVisitedStore;

use App\Models\admin\Trn_RecentlyVisitedProducts;
use App\Models\admin\Trn_store_customer;

use App\Models\admin\District;
use App\Models\admin\Town;
use App\Models\admin\Mst_store_agencies;
use App\Models\admin\Mst_categories;
use App\Models\admin\Mst_StockDetail;
use App\Models\admin\Mst_SubCategory;
use App\Models\admin\Trn_StoreWebToken;
use App\Models\admin\Trn_StoreAdmin;
use App\Models\admin\Trn_store_order;

use App\Models\admin\Mst_store_link_delivery_boy;
use App\Models\admin\Sys_store_order_status;

class CouponController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
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

      $isBaseVar = Mst_store_product_varient::where('product_varient_id', $dataImage->product_varient_id)->first();

      if (@$isBaseVar->is_base_variant == 1)
        Mst_store_product::where('product_id', $dataImage->product_id)->update(['product_base_image' => $dataImage->product_image]);


      return redirect()->back()->with('status', 'Base image successfully updated.');
    } catch (\Exception $e) {
      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }




  public function isPCodeAvailable(Request $request)
  {

    $proEx = Mst_store_product::where('product_code', $request->product_code);

    if (isset($request->product_id))
      $proEx = $proEx->where('product_id', '!=', $request->product_id);


    if ($proEx->exists()) {
      $a = 1;
      return $a;
    } else {
      $a = 0;
      return $a;
    }
  }


  public function geotest()
  {
    // $address = new Address(); Replace Address with the name of your Model
    //Converts address into Lat and Lng
    //  $pickLocation = "AGS Colony, Velachery, Chennai, Tamil Nadu, India,India";
    // $dropLocation = "Sathyam+Cinemas,+Thiru-vi-ka+Road,+Peters+Colony,+Royapettah,+Chennai,+Tamil Nadu,+India";

    // $senderAddrs = Str::of($pickLocation)->replace(' ', '+');
    // $sendrJson = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=$senderAddrs&key=AIzaSyDS0g9StwZ91xRSSCNnsVl-C5tBxl-aJpU");


    //   $json =json_decode($sendrJson);
    //   $sendLat =$json->results[0]->geometry->location->lat;
    //   $sendLong =$json->results[0]->geometry->location->lng;

    //   dd($json);

    $storeData = Mst_store::whereNotIn('store_id', [174, 58, 172, 173, 171])->get();
    //echo $storeData;die;
    foreach ($storeData as $s) {
      if (isset($s->town->town_name) && isset($s->district->district_name)) {



        $senderAddrs = Str::of($s->town->town_name . " " . $s->district->district_name)->replace(' ', '+');
        $sendrJson = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=$senderAddrs&key=AIzaSyDS0g9StwZ91xRSSCNnsVl-C5tBxl-aJpU");

        $json = json_decode($sendrJson);

        $sendLat = $json->results[0]->geometry->location->lat;
        $sendLong = $json->results[0]->geometry->location->lng;
        //  dd($json,$sendLat,$sendLong);

        //    $store = Mst_store::where('store_id',$s->store_id)->update(['latitude' => $sendLat , 'longitude' => $sendLong ]);

        //   $store->latitude = $sendLat;
        //   $store->longitude = $sendLong;
        //   $store->update();
      }
    }
  }

  public function makeQr(Request $request)
  {
    //   $sPro = Mst_store_product_varient::all();

    //   foreach($sPro as $s){

    //     $proImgs = Mst_product_image::where('product_id',$s->product_id)->get();

    //      foreach($proImgs as $p){

    //          if($s->product_varient_base_image == $p->product_image)
    //          Mst_product_image::where('product_image_id',$p->product_image_id)->update(['image_flag' => 1]);
    //      }
    //   }
    echo "not allowed";
    return redirect()->back();
  }

  public function listCoupon(Request $request)
  {
    try {

      $pageTitle = "Coupons";
      // $store_id  = Auth::guard('store')->user()->store_id;
      $store_id  = 1;
      $coupons = Mst_Coupon::where('store_id', $store_id)->orderBy('coupon_id', 'DESC')->get();
      if ($_GET) {
        $couponDetail =  Mst_Coupon::where('store_id', $store_id)->where('coupon_status', $request->status);

        // active coupon in the sense , active base on todays date
        // if ($request->coupon_status == 0) {
        //   $today = Carbon::now()->toDateTimeString();
        //   $couponDetail = $couponDetail->whereDate('valid_to', '>=', $today);
        // }

        $coupons = $couponDetail->orderBy('coupon_id', 'DESC')->get();


        return view('admin.elements.coupon.list', compact('coupons', 'store_id', 'pageTitle'));
      }
      return view('admin.elements.coupon.list', compact('coupons', 'store_id', 'pageTitle'));
    } catch (\Exception $e) {
      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }

  public function createcoupon(Request $request)
  {
    try {
      $pageTitle = "Create Coupon";
      return view('admin.elements.coupon.create', compact('pageTitle'));
    } catch (\Exception $e) {
      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }

  public function storecoupon(Request $request, Mst_Coupon $coupon)
  {
    try {
      // $store_id  = Auth::guard('store')->user()->store_id;
      $store_id  = 1;
      

      $validator = Validator::make(
        $request->all(),
        [
          'coupon_code'          => 'required',
          'coupon_type'          => 'required',
          'discount_type'          => 'required',
          'discount'          => $request->discount_type == '2' ? 'required' :  'required|lt:min_purchase_amt',
          'valid_to'          => 'required',
          'valid_from'          => 'required',
          'min_purchase_amt'          => 'required',
        ],
        [
          'coupon_code.required'             => 'Code required',
          'coupon_type.required'             => 'Type required',
          'discount.required'             => 'Discount required',
          'discount_type.required'             => 'Discount type required',
          'valid_to.required'             => 'Valid to required',
          'valid_from.required'             => 'Valid from required',
          'min_purchase_amt.required'             => 'Minimum purchase amount required',
        ]
      );

      if (!$validator->fails()) {
        $coupon->store_id = $store_id;
        $coupon->coupon_code = $request->coupon_code;
        $coupon->coupon_type = $request->coupon_type;
        $coupon->discount_type = $request->discount_type;
        $coupon->discount = $request->discount;
        $coupon->valid_to = $request->valid_to;
        $coupon->valid_from = $request->valid_from;
        $coupon->coupon_status = $request->coupon_status;
        $coupon->min_purchase_amt = $request->min_purchase_amt;
        $coupon->save();

        return redirect()->route('store.list_coupon')->with('status', 'Coupon created successfully');
      } else {
        return redirect()->back()->withErrors($validator)->withInput();
      }
    } catch (\Exception $e) {
      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }


  public function editcoupon(Request $request, $coupon_id)
  {
    try {

      $pageTitle = "Edit Coupon";
      $coupon_id  = Crypt::decryptString($coupon_id);
      $coupon = Mst_Coupon::find($coupon_id);
      $coupon->valid_from = Carbon::parse($coupon->valid_from)->format('Y-m-d');
      $coupon->valid_to = Carbon::parse($coupon->valid_to)->format('Y-m-d');
      return view('admin.elements.coupon.edit', compact('coupon', 'pageTitle'));
    } catch (\Exception $e) {
      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }

  public function updatecoupon(Request $request, $coupon_id)
  {
    try {

      $validator = Validator::make(
        $request->all(),
        [
          'coupon_code'          => 'required',
          'coupon_type'          => 'required',
          'discount'          => $request->discount_type == '2' ? 'required' :  'required|lt:min_purchase_amt',
          'discount_type'          => 'required',
          'valid_to'          => 'required',
          'valid_from'          => 'required',
          'min_purchase_amt'          => 'required',
        ],
        [
          'coupon_code.required'             => 'Code required',
          'discount.required'             => 'Discount required',
          'coupon_type.required'             => 'Type required',
          'discount_type.required'             => 'Discount type required',
          'valid_to.required'             => 'Valid to required',
          'valid_from.required'             => 'Valid from required',
          'min_purchase_amt.required'             => 'Minimum purchase amount required',
        ]
      );
      //   $coupon_id  = Crypt::decryptString($coupon_id);
      if (!$validator->fails()) {
        $coupon['coupon_code'] = $request->coupon_code;
        $coupon['coupon_type'] = $request->coupon_type;
        $coupon['discount'] = $request->discount;
        $coupon['discount_type'] = $request->discount_type;
        $coupon['valid_to'] = $request->valid_to;
        $coupon['valid_from'] = $request->valid_from;
        $coupon['coupon_status'] = $request->coupon_status;
        $coupon['min_purchase_amt'] = $request->min_purchase_amt;

        Mst_Coupon::where('coupon_id', $coupon_id)->update($coupon);

        return redirect()->route('store.list_coupon')->with('status', 'Coupon updated successfully');
      } else {
        return redirect()->back()->withErrors($validator)->withInput();
      }
    } catch (\Exception $e) {
      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }

  public function removecoupon(Request $request, $coupon_id)
  {
    try {
      //  $coupon_id  = Crypt::decryptString($coupon_id);
      Mst_Coupon::where('coupon_id', $coupon_id)->delete();
      return redirect()->route('store.list_coupon')->with('status', 'Coupon deleted successfully');
    } catch (\Exception $e) {
      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }



  public function showReport(Request $request)
  {
    try {

      $pageTitle = "Product Wise Reports";

      $products = Mst_store_product::join('mst__item_categories', 'mst__item_categories.item_category_id', '=', 'mst_store_products.product_cat_id')
        ->select('mst_store_products.product_id', 'mst_store_products.product_name')
        // ->where('mst_store_products.store_id', Auth::guard('store')->user()->store_id)
        ->where('mst_store_products.store_id', 1)
        ->orderBy('mst_store_products.product_id', 'DESC')->get();

      $productVAriants =  Mst_store_product_varient::join('mst_store_products', 'mst_store_products.product_id', '=', 'mst_store_product_varients.product_id')
        ->where('mst_store_products.is_removed', 0)
        ->where('mst_store_product_varients.is_removed', 0)
        // ->where('mst_store_products.store_id', Auth::guard('store')->user()->store_id)
        ->where('mst_store_products.store_id', 1)
        ->get();



      $customers = Trn_store_customer::all();

      $agencies = Mst_store_agencies::orderBy('agency_id', 'DESC')->where('agency_account_status', 1)->get();
      $categories = Mst_Itemcategory::orderBy('item_category_id', 'DESC')->where('is_active', 1)->get();
      $subCategories = Mst_ItemSubCategory::orderBy('item_sub_category_id', 'DESC')->where('is_active', 1)->get();


      $data = Trn_RecentlyVisitedProducts::select(
        'trn__recently_visited_products.rvp_id',
        'trn__recently_visited_products.visit_count',
        'trn__recently_visited_products.created_at',
        'trn__recently_visited_products.updated_at',
        'trn__recently_visited_products.customer_id',
        'trn_store_customers.customer_first_name',
        'trn_store_customers.customer_last_name',
        'trn_store_customers.customer_mobile_number',
        'mst_stores.store_id',
        'mst_stores.store_name',
        'mst_stores.store_mobile',
        'trn__recently_visited_products.product_id',
        'mst_store_products.product_code',
        'mst_store_products.product_name', 
        'mst_store_products.product_brand',
        'trn__recently_visited_products.product_varient_id',
        'mst_store_product_varients.variant_name',
        'mst_store_agencies.agency_id',
        'mst_store_agencies.agency_name',
        'mst__item_categories.item_category_id',
        'mst__item_categories.category_name',
        'mst__item_sub_categories.item_sub_category_id',
        'mst__item_sub_categories.sub_category_name'
      )
        ->join('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn__recently_visited_products.customer_id')
        ->join('mst_stores', 'mst_stores.store_id', '=', 'trn__recently_visited_products.store_id')
        ->join('mst_store_products', 'mst_store_products.product_id', '=', 'trn__recently_visited_products.product_id')
        ->leftJoin('mst_store_product_varients', 'mst_store_product_varients.product_varient_id', '=', 'trn__recently_visited_products.product_varient_id')
        ->leftJoin('mst_store_agencies', 'mst_store_agencies.agency_id', '=', 'mst_store_products.vendor_id')
        ->join('mst__item_categories', 'mst__item_categories.item_category_id', '=', 'mst_store_products.product_cat_id')
        ->leftJoin('mst__item_sub_categories', 'mst__item_sub_categories.item_sub_category_id', '=', 'mst_store_products.sub_category_id')
        // ->where('mst_stores.store_id', Auth::guard('store')->user()->store_id)
        ->where('mst_stores.store_id', 1)
        //         ->orderBy('trn__recently_visited_products.rvp_id', 'DESC')
        //   ->groupBy('trn__recently_visited_products.product_varient_id', DB::raw("DATE_FORMAT(trn__recently_visited_products.created_at, '%d-%m-%Y')"))
        ->groupBy(DB::raw("DATE_FORMAT(trn__recently_visited_products.created_at, '%d-%m-%Y')"), 'trn__recently_visited_products.product_varient_id')->orderBy('trn__recently_visited_products.rvp_id', 'DESC')->get();

      // dd($data);
      if ($_GET) {

        $fetchCustomerData = Trn_store_customer::where('trn_store_customers.customer_mobile_number', 'LIKE', '%' . $request->customer_mobile_number . '%')->first();

        $datefrom = $request->date_from;
        $dateto = $request->date_to;

        $a1 = Carbon::parse($request->date_from)->startOfDay();
        $a2  = Carbon::parse($request->date_to)->endOfDay();


        $data = Trn_RecentlyVisitedProducts::select(
          'trn__recently_visited_products.rvp_id',
          'trn__recently_visited_products.visit_count',
          'trn__recently_visited_products.created_at',
          'trn__recently_visited_products.updated_at',
          'trn_store_customers.customer_id',
          'trn_store_customers.customer_first_name',
          'trn_store_customers.customer_last_name',
          'trn_store_customers.customer_mobile_number',
          'mst_stores.store_id',
          'mst_stores.store_name',
          'mst_stores.store_mobile',
          'mst_store_products.product_id',
          'mst_store_products.product_code',
          'mst_store_products.product_name',
          'mst_store_products.product_brand',
          'mst_store_product_varients.product_varient_id',
          'mst_store_product_varients.variant_name',
          'mst_store_agencies.agency_id',
          'mst_store_agencies.agency_name',
          'mst__item_categories.item_category_id',
          'mst__item_categories.category_name',
          'mst__item_sub_categories.item_sub_category_id',
          'mst__item_sub_categories.sub_category_name'
            )
          ->join('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn__recently_visited_products.customer_id')
          ->join('mst_stores', 'mst_stores.store_id', '=', 'trn__recently_visited_products.store_id')
          ->join('mst_store_products', 'mst_store_products.product_id', '=', 'trn__recently_visited_products.product_id')
          ->leftJoin('mst_store_product_varients', 'mst_store_product_varients.product_varient_id', '=', 'trn__recently_visited_products.product_varient_id')
          ->leftJoin('mst_store_agencies', 'mst_store_agencies.agency_id', '=', 'mst_store_products.vendor_id')
          ->join('mst__item_categories', 'mst__item_categories.item_category_id', '=', 'mst_store_products.product_cat_id')
          ->leftJoin('mst__item_sub_categories', 'mst__item_sub_categories.item_sub_category_id', '=', 'mst_store_products.sub_category_id');
        
        // $data = $data->where('mst_stores.store_id', Auth::guard('store')->user()->store_id);
         
        $data = $data->where('mst_stores.store_id', 1);

        if (isset($request->date_from)) {
          $data = $data->whereDate('trn__recently_visited_products.created_at', '>=', $a1);
        }

        if (isset($request->date_to)) {
          $data = $data->whereDate('trn__recently_visited_products.created_at', '<=', $a2);
        }

        if (isset($request->product_id)) {
          // $data = $data->where('mst_store_product_varients.product_varient_id', $request->product_id);
          $data = $data->where('mst_store_products.product_id', $request->product_id);
          
        }

        if (isset($request->vendor_id)) {
          $data = $data->where('mst_store_products.vendor_id', $request->vendor_id);
        }

        if (isset($request->category_id)) {
          $data = $data->where('mst_store_products.product_cat_id', $request->category_id);
        }

        if (isset($request->sub_category_id)) {
          $data = $data->where('mst_store_products.sub_category_id', $request->sub_category_id);
        }

       
        if (isset($request->customer_mobile_number)) {
          $data = $data->where('trn_store_customers.customer_mobile_number', 'LIKE', '%' . $request->customer_mobile_number . '%');
        }




        // $data = $data->orderBy('trn__recently_visited_products.rvp_id', 'DESC')

        $data = $data->groupBy(DB::raw("DATE_FORMAT(trn__recently_visited_products.created_at, '%d-%m-%Y')"), 'trn__recently_visited_products.product_varient_id')->orderBy('trn__recently_visited_products.rvp_id', 'DESC')->get();
        //dd($data);
        return view('admin.elements.reports.product_report', compact('productVAriants', 'categories', 'agencies', 'products', 'customers', 'dateto', 'datefrom', 'data', 'pageTitle'));
      }

      return view('admin.elements.reports.product_report', compact('productVAriants', 'categories', 'agencies', 'products', 'customers', 'data', 'pageTitle'));
    } catch (\Exception $e) {
      return redirect()->back()->withErrors([$e->getMessage()])->withInput();
      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }


  public function showStoreVisitReport(Request $request)
  {
    
    try {

      $pageTitle = "Store Visit Reports";
      $customers = Trn_store_customer::all();


      $data = Trn_RecentlyVisitedStore::select(
        'trn__recently_visited_stores.rvs_id',
        'trn__recently_visited_stores.visit_count',
        'trn__recently_visited_stores.created_at',
        'trn__recently_visited_stores.updated_at',
        'trn_store_customers.customer_id',
        'trn_store_customers.customer_first_name',
        'trn_store_customers.customer_last_name',
        'trn_store_customers.customer_mobile_number',
        'trn_store_customers.place',
        'trn_store_customers.town_id',
        'mst_stores.store_id',
        'mst_stores.store_name',
        'mst_stores.store_mobile',
        'mst_towns.town_name'

      )
        ->join('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn__recently_visited_stores.customer_id')
        ->join('mst_stores', 'mst_stores.store_id', '=', 'trn__recently_visited_stores.store_id')
        ->join('mst_towns', 'mst_towns.town_id', '=', 'mst_stores.town_id')
        // ->where('mst_stores.store_id', Auth::guard('store')->user()->store_id)
        ->where('mst_stores.store_id', 1)
        ->groupBy('trn__recently_visited_stores.customer_id', DB::raw("DATE_FORMAT(trn__recently_visited_stores.created_at, '%d-%m-%Y')"))
        //->groupBy('trn__recently_visited_stores.customer_id')
        ->orderBy('trn__recently_visited_stores.rvs_id', 'DESC')
        ->get();


      if ($_GET) {
        $datefrom = $request->date_from;
        $dateto = $request->date_to;

        $a1 = Carbon::parse($request->date_from)->startOfDay();
        $a2  = Carbon::parse($request->date_to)->endOfDay();

        $data = Trn_RecentlyVisitedStore::select(
          'trn__recently_visited_stores.rvs_id',
          'trn__recently_visited_stores.visit_count',
          'trn__recently_visited_stores.created_at',
          'trn__recently_visited_stores.updated_at',
          'trn_store_customers.customer_id',
          'trn_store_customers.customer_first_name',
          'trn_store_customers.customer_last_name',
          'trn_store_customers.customer_mobile_number',
          'trn_store_customers.place',
          'trn_store_customers.town_id',
          'mst_stores.store_id',
          'mst_stores.store_name',
          'mst_stores.store_mobile',
          'mst_towns.town_name'
        )
          ->join('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn__recently_visited_stores.customer_id')
          ->join('mst_stores', 'mst_stores.store_id', '=', 'trn__recently_visited_stores.store_id')
          ->join('mst_towns', 'mst_towns.town_id', '=', 'mst_stores.town_id');

        // $data = $data->where('mst_stores.store_id', Auth::guard('store')->user()->store_id);
        
        $data = $data->where('mst_stores.store_id', 1);

        if (isset($request->date_from)) {
          $data = $data->whereDate('trn__recently_visited_stores.created_at', '>=', $a1);
        }

        if (isset($request->date_to)) {
          $data = $data->whereDate('trn__recently_visited_stores.created_at', '<=', $a2);
        }

        if (isset($request->customer_mobile_number)) {


          $data = $data->where('trn_store_customers.customer_mobile_number', 'LIKE', '%' . $request->customer_mobile_number . '%');
       
    }

        // if (isset($request->customer_id)) {
        //   $data = $data->where('trn_store_customers.customer_id', '=', $request->customer_id);
        // }

        if (isset($request->town_id)) {
          $data = $data->where('trn_store_customers.town_id', '=', $request->town_id);
        }

        $data = $data->orderBy('trn__recently_visited_stores.rvs_id', 'DESC')
          ->groupBy('trn__recently_visited_stores.store_id', 'trn__recently_visited_stores.customer_id', DB::raw("DATE_FORMAT(trn__recently_visited_stores.created_at, '%d-%m-%Y')"))
          ->get();


        return view('admin.elements.reports.store_visit_report', compact('dateto', 'datefrom', 'customers', 'data', 'pageTitle'));
      }

      return view('admin.elements.reports.store_visit_report', compact('customers', 'data', 'pageTitle'));
    } catch (\Exception $e) {
      return redirect()->back()->withErrors([$e->getMessage()])->withInput();
      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }

  public function listTownNames(Request $request)
  {
    $town = Town::pluck("town_name", "town_id");

    return response()->json($town);
  }

  public function saveBrowserToken(Request $request)
  {
    // echo "token : ".$request->token;

    //$store_id  = Auth::guard('store')->user()->store_id;
    $store_id = 1;
    //Trn_StoreWebToken::where('store_id',$store_id)->delete();

    $storeData = Trn_StoreAdmin::where('store_id', '=', $store_id)->where('role_id', '=', 0)->first();

    Trn_StoreWebToken::where('store_id', $store_id)->where('store_admin_id', $storeData->store_admin_id)->where('store_web_token', $request->token)->delete();

    $tok =  new Trn_StoreWebToken;
    $tok->store_id = $store_id;
    $tok->store_admin_id = $storeData->store_admin_id;
    $tok->store_web_token = $request->token;
    $tok->save();

    echo "Oops! response";
  }



  public function showSalesReport(Request $request)
  {
    // echo "working...";die;
    try {

      $pageTitle = "Sales Reports";
      //$store_id  = Auth::guard('store')->user()->store_id;
      $datefrom = '';
      $dateto = '';

      $customers = Trn_store_customer::all();
      
      $branches = Mst_branch::all();
      
      $orderStatus = Sys_store_order_status::all();
      
      $data = [];

      if ($_GET) {
          
         $data = Trn_store_order::select(

        'trn_store_orders.order_id',
        'trn_store_orders.order_number',
        'trn_store_orders.customer_id',
        'trn_store_orders.store_id',
        'trn_store_orders.subadmin_id',
        'trn_store_orders.product_total_amount',
        'trn_store_orders.delivery_charge',
        'trn_store_orders.packing_charge',
        'trn_store_orders.payment_type_id',
        'trn_store_orders.status_id',
        'trn_store_orders.payment_status',
        'trn_store_orders.delivery_status_id',
        'trn_store_orders.delivery_boy_id',
        'trn_store_orders.coupon_id',
        'trn_store_orders.coupon_code',
        'trn_store_orders.reward_points_used',
        'trn_store_orders.amount_before_applying_rp',
        'trn_store_orders.trn_id',
        'trn_store_orders.created_at',
        'trn_store_orders.amount_reduced_by_coupon',
        'trn_store_orders.order_type',

        'trn_store_customers.customer_id',
        'trn_store_customers.customer_first_name',
        'trn_store_customers.customer_last_name',
        'trn_store_customers.customer_mobile_number',
        'trn_store_customers.place'


      )
        ->join('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn_store_orders.customer_id');

       
        $datefrom = $request->date_from;
        $dateto = $request->date_to;
        // dd($request->all());
        $a1 = Carbon::parse($request->date_from)->startOfDay();
        $a2  = Carbon::parse($request->date_to)->endOfDay();

        if (isset($request->date_from)) {
          $data = $data->whereDate('trn_store_orders.created_at', '>=', $a1);
        }

        if (isset($request->date_to)) {
          $data = $data->whereDate('trn_store_orders.created_at', '<=', $a2);
        }
        
        if (isset($request->branch_id)) {
            
          $data = $data->where('trn_store_orders.branch_id', '=', $request->branch_id);
        }

         if (isset($request->customer_id)) {
           $data = $data->where('trn_store_orders.customer_id', '=', $request->customer_id);
         }
         
         if (isset($request->customer_id)) {
           $data = $data->where('trn_store_orders.customer_id', '=', $request->customer_id);
         }

        if (isset($request->customer_mobile_number)) {


          $data = $data->where('trn_store_customers.customer_mobile_number', 'LIKE', '%' . $request->customer_mobile_number . '%');
       
        }

        if (isset($request->delivery_boy_id)) {
          $data = $data->where('trn_store_orders.delivery_boy_id', '=', $request->delivery_boy_id);
        }

        if (isset($request->status_id)) {
          $data = $data->where('trn_store_orders.status_id', '=', $request->status_id);
        }

        if (isset($request->order_type)) {
          $data = $data->where('trn_store_orders.order_type', '=', $request->order_type);
        }
        
        $data = $data->orderBy('trn_store_orders.order_id', 'DESC')
        ->get();

      }
      
      return view('admin.elements.reports.sales_report', compact('orderStatus','customers', 'dateto', 'datefrom', 'data', 'pageTitle','branches'));
    } catch (\Exception $e) {
      return redirect()->back()->withErrors([$e->getMessage()])->withInput();
      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }

  public function showInventoryReport(Request $request)
  {
    //echo "working..";die;
    $pageTitle = "Inventory Reports";
    $datefrom = '';
    $dateto = '';
    
    $branches = Mst_branch::all();

    $products = Mst_branch_product::join('mst__item_categories', 'mst__item_categories.item_category_id', '=', 'mst_branch_products.product_cat_id')
      ->select('mst_branch_products.branch_product_id', 'mst_branch_products.product_name')
      ->orderBy('mst_branch_products.branch_product_id', 'DESC')->get();

    $agencies = Mst_store_agencies::orderBy('agency_id', 'DESC')->where('agency_account_status', 1)->get();
    $categories = Mst_ItemCategory::orderBy('item_category_id', 'DESC')->where('is_active', 1)->get();
    //$subCategories = Mst_SubCategory::orderBy('sub_category_id', 'DESC')->where('sub_category_status', 1)->get();

    $subCategories =[];

    $inventoryData =[];


       

    if ($_GET) {
        
        $inventoryData = Mst_branch_product_varient::join('mst_branch_products', 'mst_branch_products.branch_product_id', '=', 'mst_branch_product_varients.product_id')
      ->join('mst__item_categories', 'mst__item_categories.item_category_id', '=', 'mst_branch_products.product_cat_id')
      ->leftjoin('mst__stock_details', 'mst__stock_details.product_varient_id', '=', 'mst_branch_product_varients.varient_id')

      ->where('mst_branch_product_varients.stock_count', '>', 0)

      ->where('mst_branch_products.product_type', 1)
      ->orderBy('mst__stock_details.created_at', 'DESC')

      ->select(
        'mst_branch_products.branch_product_id',
        'mst_branch_products.product_name',
        'mst_branch_products.product_code',
        'mst_branch_products.product_cat_id',
        'mst_branch_products.product_base_image',
        'mst_branch_products.product_status',
        'mst_branch_products.product_brand',
        'mst_branch_products.min_stock',

        'mst_branch_products.tax_id',
        'mst_branch_product_varients.varient_id',
        'mst_branch_product_varients.branch_id',
        'mst_branch_product_varients.variant_name',
        'mst_branch_product_varients.product_varient_price',
        'mst_branch_product_varients.product_varient_offer_price',
        'mst_branch_product_varients.product_varient_base_image',
        'mst_branch_product_varients.stock_count',
        'mst_branch_product_varients.created_at',
        'mst__item_categories.category_name',
        'mst__stock_details.stock',
        'mst__stock_details.prev_stock',
        'mst__stock_details.created_at AS updated_time'
      );

        
        
        
      $datefrom = $request->date_from;
      $dateto = $request->date_to;

      $a1 = Carbon::parse($request->date_from)->startOfDay();
      $a2  = Carbon::parse($request->date_to)->endOfDay();

      // if(isset($request->date_from))
      // {
      //   $data = $data->whereDate('trn_store_orders.created_at','>=',$a1);
      // }

      // if(isset($request->date_to))
      // {
      //   $data = $data->whereDate('trn_store_orders.created_at','<=',$a2);
      // }
      
      if (isset($request->branch_id)) {
            
          $inventoryData = $inventoryData->where('mst_branch_product_varients.branch_id', '=', $request->branch_id);
        }


      if (isset($request->product_id)) {
        $inventoryData = $inventoryData->where('mst_branch_products.branch_product_id', $request->product_id);
      }

      if (isset($request->category_id)) {
        $inventoryData = $inventoryData->where('mst__item_categories.item_category_id', $request->category_id);
      }
      
      $inventoryData = $inventoryData->get();

    
    

    }

    $inventoryData = collect($inventoryData);
    // $inventoryDatas = $inventoryData->unique('product_varient_id');
    $data =   $inventoryData->values()->all();

    
 

    return view('admin.elements.reports.inventory_report', compact('branches','subCategories', 'categories', 'agencies', 'products', 'dateto', 'datefrom', 'data', 'pageTitle'));
  }



  public function showOutofStockReport(Request $request)
  {
    //echo "working..";die;
    $pageTitle = "Out of Stock Reports";
    //$store_id  = Auth::guard('store')->user()->store_id;
    $datefrom = '';
    $dateto = '';

    $branches = Mst_branch::all();
    
    $products = Mst_branch_product::join('mst__item_categories', 'mst__item_categories.item_category_id', '=', 'mst_branch_products.product_cat_id')
      ->select('mst_branch_products.branch_product_id', 'mst_branch_products.product_name')
      ->orderBy('mst_branch_products.branch_product_id', 'DESC')->get();

    $agencies = Mst_store_agencies::orderBy('agency_id', 'DESC')->where('agency_account_status', 1)->get();
    $categories = Mst_ItemCategory::orderBy('item_category_id', 'DESC')->where('is_active', 1)->get();
    //$subCategories = Mst_SubCategory::orderBy('sub_category_id', 'DESC')->where('sub_category_status', 1)->get();

    $subCategories =[];


     $inventoryData = [];

    
    if ($_GET) {
        
        $inventoryData =   Mst_branch_product_varient::join('mst_branch_products', 'mst_branch_products.branch_product_id', '=', 'mst_branch_product_varients.product_id')
      ->join('mst__item_categories', 'mst__item_categories.item_category_id', '=', 'mst_branch_products.product_cat_id')
      
      ->where('mst_branch_product_varients.stock_count', '<=', 0)

      ->where('mst_branch_products.product_type', 1)
      

      ->select(
        'mst_branch_products.branch_product_id',
        'mst_branch_products.product_name',
        'mst_branch_products.product_code',
        'mst_branch_products.product_cat_id',
        'mst_branch_products.product_base_image',
        'mst_branch_products.product_status',
        'mst_branch_products.product_brand',
        'mst_branch_products.min_stock',

        'mst_branch_products.tax_id',
        'mst_branch_product_varients.varient_id',
        'mst_branch_product_varients.branch_id',
        'mst_branch_product_varients.variant_name',
        'mst_branch_product_varients.product_varient_price',
        'mst_branch_product_varients.product_varient_offer_price',
        'mst_branch_product_varients.product_varient_base_image',
        'mst_branch_product_varients.stock_count',
        'mst_branch_product_varients.created_at',
        'mst__item_categories.category_name'
        );


        
      $datefrom = $request->date_from;
      $dateto = $request->date_to;

      $a1 = Carbon::parse($request->date_from)->startOfDay();
      $a2  = Carbon::parse($request->date_to)->endOfDay();

      // if(isset($request->date_from))
      // {
      //   $data = $data->whereDate('trn_store_orders.created_at','>=',$a1);
      // }

      // if(isset($request->date_to))
      // {
      //   $data = $data->whereDate('trn_store_orders.created_at','<=',$a2);
      // }

      if (isset($request->product_id)) {
        $inventoryData = $inventoryData->where('mst_branch_products.branch_product_id', $request->product_id);
      }

      if (isset($request->category_id)) {
        $inventoryData = $inventoryData->where('mst__item_categories.item_category_id', $request->category_id);
      }
      
      if (isset($request->branch_id)) {
            
          $inventoryData = $inventoryData->where('mst_branch_product_varients.branch_id', '=', $request->branch_id);
        }
    
    $inventoryData = $inventoryData->get();

    }


    

    
    $inventoryData = collect($inventoryData);
    // $inventoryDatas = $inventoryData->unique('product_varient_id');
    $data =   $inventoryData->values()->all();

 

    return view('admin.elements.reports.out_of_stock_report', compact('branches','subCategories', 'categories', 'agencies', 'products', 'dateto', 'datefrom', 'data', 'pageTitle'));
  }


  public function showOnlineSalesReport(Request $request)
  {
    // echo "working...";die;
    try {

      $pageTitle = "Online Sales Reports";
      //$store_id  = Auth::guard('store')->user()->store_id;
      $store_id = 1;
      $datefrom = '';
      $dateto = '';

      $customers = Trn_store_customer::all();
      $deliveryBoys =  Mst_store_link_delivery_boy::join('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'mst_store_link_delivery_boys.delivery_boy_id')
        ->where('mst_store_link_delivery_boys.store_id', $store_id)->get();

      $orderStatus = Sys_store_order_status::all();


      $data = Trn_store_order::select(

        'trn_store_orders.order_id',
        'trn_store_orders.order_number',
        'trn_store_orders.customer_id',
        'trn_store_orders.store_id',
        'trn_store_orders.subadmin_id',
        'trn_store_orders.product_total_amount',
        'trn_store_orders.delivery_charge',
        'trn_store_orders.packing_charge',
        'trn_store_orders.payment_type_id',
        'trn_store_orders.status_id',
        'trn_store_orders.payment_status',
        'trn_store_orders.delivery_status_id',
        'trn_store_orders.delivery_boy_id',
        'trn_store_orders.coupon_id',
        'trn_store_orders.coupon_code',
        'trn_store_orders.reward_points_used',
        'trn_store_orders.amount_before_applying_rp',
        'trn_store_orders.trn_id',
        'trn_store_orders.created_at',
        'trn_store_orders.amount_reduced_by_coupon',
        'trn_store_orders.order_type',

        'trn_store_customers.customer_id',
        'trn_store_customers.customer_first_name',
        'trn_store_customers.customer_last_name',
        'trn_store_customers.customer_mobile_number',
        'trn_store_customers.place',

        'mst_stores.store_id',
        'mst_stores.store_name',
        'mst_stores.store_mobile',

        'mst_delivery_boys.delivery_boy_name',
        'mst_delivery_boys.delivery_boy_mobile'



      )
        ->join('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn_store_orders.customer_id')
        ->leftjoin('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'trn_store_orders.delivery_boy_id')
        ->leftjoin('mst_stores', 'mst_stores.store_id', '=', 'trn_store_orders.store_id');

      if ($_GET) {
        $datefrom = $request->date_from;
        $dateto = $request->date_to;

        $a1 = Carbon::parse($request->date_from)->startOfDay();
        $a2  = Carbon::parse($request->date_to)->endOfDay();

        if (isset($request->date_from)) {
          $data = $data->whereDate('trn_store_orders.created_at', '>=', $a1);
        }

        if (isset($request->date_to)) {
          $data = $data->whereDate('trn_store_orders.created_at', '<=', $a2);
        }


        if (isset($request->customer_mobile_number)) {


          $data = $data->where('trn_store_customers.customer_mobile_number', 'LIKE', '%' . $request->customer_mobile_number . '%');
       
    }

        // if (isset($request->customer_id)) {
        //   $data = $data->where('trn_store_orders.customer_id', '=', $request->customer_id);
        // }

        if (isset($request->delivery_boy_id)) {
          $data = $data->where('trn_store_orders.delivery_boy_id', '=', $request->delivery_boy_id);
        }

        if (isset($request->status_id)) {
          $data = $data->where('trn_store_orders.status_id', '=', $request->status_id);
        }

        if (isset($request->order_type)) {
          $data = $data->where('trn_store_orders.order_type', '=', $request->order_type);
        }
      }

      $data = $data->where('trn_store_orders.store_id', $store_id)->where('trn_store_orders.order_type', 'APP')
        ->orderBy('trn_store_orders.order_id', 'DESC')
        ->get();


      return view('admin.elements.reports.online_sales_report', compact('orderStatus', 'deliveryBoys', 'customers', 'dateto', 'datefrom', 'data', 'pageTitle'));
    } catch (\Exception $e) {
      return redirect()->back()->withErrors([$e->getMessage()])->withInput();
      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }



  public function showOfflineSalesReport(Request $request)
  {
    // echo "working...";die;
    try {

      $pageTitle = "Offline Sales Reports";
      //$store_id  = Auth::guard('store')->user()->store_id;
      $store_id = 1;
      $datefrom = '';
      $dateto = '';


      $customers = Trn_store_customer::all();
      $deliveryBoys =  Mst_store_link_delivery_boy::join('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'mst_store_link_delivery_boys.delivery_boy_id')
        ->where('mst_store_link_delivery_boys.store_id', $store_id)->get();

      $orderStatus = Sys_store_order_status::all();


      $data = Trn_store_order::select(

        'trn_store_orders.order_id',
        'trn_store_orders.order_number',
        'trn_store_orders.customer_id',
        'trn_store_orders.store_id',
        'trn_store_orders.subadmin_id',
        'trn_store_orders.product_total_amount',
        'trn_store_orders.delivery_charge',
        'trn_store_orders.packing_charge',
        'trn_store_orders.payment_type_id',
        'trn_store_orders.status_id',
        'trn_store_orders.payment_status',
        'trn_store_orders.delivery_status_id',
        'trn_store_orders.delivery_boy_id',
        'trn_store_orders.coupon_id',
        'trn_store_orders.coupon_code',
        'trn_store_orders.reward_points_used',
        'trn_store_orders.amount_before_applying_rp',
        'trn_store_orders.trn_id',
        'trn_store_orders.created_at',
        'trn_store_orders.amount_reduced_by_coupon',
        'trn_store_orders.order_type',

        'trn_store_customers.customer_id',
        'trn_store_customers.customer_first_name',
        'trn_store_customers.customer_last_name',
        'trn_store_customers.customer_mobile_number',
        'trn_store_customers.place',

        'mst_stores.store_id',
        'mst_stores.store_name',
        'mst_stores.store_mobile',

        'mst_delivery_boys.delivery_boy_name',
        'mst_delivery_boys.delivery_boy_mobile'



      )
        ->join('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn_store_orders.customer_id')
        ->leftjoin('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'trn_store_orders.delivery_boy_id')
        ->leftjoin('mst_stores', 'mst_stores.store_id', '=', 'trn_store_orders.store_id');

      if ($_GET) {
        $datefrom = $request->date_from;
        $dateto = $request->date_to;

        $a1 = Carbon::parse($request->date_from)->startOfDay();
        $a2  = Carbon::parse($request->date_to)->endOfDay();

        if (isset($request->date_from)) {
          $data = $data->whereDate('trn_store_orders.created_at', '>=', $a1);
        }

        if (isset($request->date_to)) {
          $data = $data->whereDate('trn_store_orders.created_at', '<=', $a2);
        }


        if (isset($request->customer_mobile_number)) {


          $data = $data->where('trn_store_customers.customer_mobile_number', 'LIKE', '%' . $request->customer_mobile_number . '%');
       
    }

        if (isset($request->delivery_boy_id)) {
          $data = $data->where('trn_store_orders.delivery_boy_id', '=', $request->delivery_boy_id);
        }

        if (isset($request->status_id)) {
          $data = $data->where('trn_store_orders.status_id', '=', $request->status_id);
        }

        if (isset($request->order_type)) {
          $data = $data->where('trn_store_orders.order_type', '=', $request->order_type);
        }
      }

      $data = $data->where('trn_store_orders.store_id', $store_id)->where('trn_store_orders.order_type', 'POS')
        ->orderBy('trn_store_orders.order_id', 'DESC')
        ->get();


      return view('admin.elements.reports.offline_sales_report', compact('orderStatus', 'deliveryBoys', 'customers', 'dateto', 'datefrom', 'data', 'pageTitle'));
    } catch (\Exception $e) {
      return redirect()->back()->withErrors([$e->getMessage()])->withInput();
      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }



  public function deliveryReport(Request $request)
  {
    // echo "working...";die;
    try {

      $pageTitle = "Delivery Reports";
      //$store_id  = Auth::guard('store')->user()->store_id;
      $store_id = 1;
      $datefrom = '';
      $dateto = '';


      $data = Trn_store_order::select(

        'trn_store_orders.order_id',
        'trn_store_orders.order_number',
        'trn_store_orders.customer_id',
        'trn_store_orders.store_id',
        'trn_store_orders.subadmin_id',
        'trn_store_orders.product_total_amount',
        'trn_store_orders.delivery_charge',
        'trn_store_orders.packing_charge',
        'trn_store_orders.payment_type_id',
        'trn_store_orders.status_id',
        'trn_store_orders.payment_status',
        'trn_store_orders.delivery_status_id',
        'trn_store_orders.delivery_boy_id',
        'trn_store_orders.coupon_id',
        'trn_store_orders.coupon_code',
        'trn_store_orders.reward_points_used',
        'trn_store_orders.amount_before_applying_rp',
        'trn_store_orders.trn_id',
        'trn_store_orders.created_at',
        'trn_store_orders.amount_reduced_by_coupon',
        'trn_store_orders.order_type',
        'trn_store_orders.delivery_charge',
        'trn_store_orders.packing_charge',

        'trn_store_customers.customer_id',
        'trn_store_customers.customer_first_name',
        'trn_store_customers.customer_last_name',
        'trn_store_customers.customer_mobile_number',
        'trn_store_customers.place',

        'mst_stores.store_id',
        'mst_stores.store_name',
        'mst_stores.store_mobile',

        'mst_delivery_boys.delivery_boy_name',
        'mst_delivery_boys.delivery_boy_mobile'



      )
        ->join('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn_store_orders.customer_id')
        ->leftjoin('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'trn_store_orders.delivery_boy_id')
        ->leftjoin('mst_stores', 'mst_stores.store_id', '=', 'trn_store_orders.store_id');

      if ($_GET) {
        $datefrom = $request->date_from;
        $dateto = $request->date_to;

        $a1 = Carbon::parse($request->date_from)->startOfDay();
        $a2  = Carbon::parse($request->date_to)->endOfDay();

        if (isset($request->date_from)) {
          $data = $data->whereDate('trn_store_orders.created_at', '>=', $a1);
        }

        if (isset($request->date_to)) {
          $data = $data->whereDate('trn_store_orders.created_at', '<=', $a2);
        }

        if (isset($request->customer_mobile_number)) {


          $data = $data->where('trn_store_customers.customer_mobile_number', 'LIKE', '%' . $request->customer_mobile_number . '%');
       
    }

        // if (isset($request->customer_id)) {
        //   $data = $data->where('trn_store_orders.customer_id', '=', $request->customer_id);
        // }

        if (isset($request->delivery_boy_id)) {
          $data = $data->where('trn_store_orders.delivery_boy_id', '=', $request->delivery_boy_id);
        }

        if (isset($request->status_id)) {
          $data = $data->where('trn_store_orders.status_id', '=', $request->status_id);
        }

        if (isset($request->order_type)) {
          $data = $data->where('trn_store_orders.order_type', '=', $request->order_type);
        }
      }

      $data = $data->where('trn_store_orders.store_id', $store_id)->where('trn_store_orders.status_id', 9)
        ->orderBy('trn_store_orders.order_id', 'DESC')
        ->get();


      return view('admin.elements.reports.delivery_report', compact('dateto', 'datefrom', 'data', 'pageTitle'));
    } catch (\Exception $e) {
      return redirect()->back()->withErrors([$e->getMessage()])->withInput();
      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }


  public function paymentReport(Request $request)
  {
    // echo "working...";die;
    try {

      $pageTitle = "Payment Reports";
      //$store_id  = Auth::guard('store')->user()->store_id;
      $store_id = 1;
      $datefrom = '';
      $dateto = '';


      $data = Trn_store_order::select(

        'trn_store_orders.order_id',
        'trn_store_orders.order_number',
        'trn_store_orders.customer_id',
        'trn_store_orders.store_id',
        'trn_store_orders.subadmin_id',
        'trn_store_orders.product_total_amount',
        'trn_store_orders.delivery_charge',
        'trn_store_orders.packing_charge',
        'trn_store_orders.payment_type_id',
        'trn_store_orders.payment_status',
        'trn_store_orders.trn_id',
        'trn_store_orders.referenceId',

        'trn_store_orders.status_id',
        'trn_store_orders.payment_status',
        'trn_store_orders.delivery_status_id',
        'trn_store_orders.delivery_boy_id',
        'trn_store_orders.coupon_id',
        'trn_store_orders.coupon_code',
        'trn_store_orders.reward_points_used',
        'trn_store_orders.amount_before_applying_rp',
        'trn_store_orders.trn_id',
        'trn_store_orders.created_at',
        'trn_store_orders.amount_reduced_by_coupon',
        'trn_store_orders.order_type',

        'trn_store_customers.customer_id',
        'trn_store_customers.customer_first_name',
        'trn_store_customers.customer_last_name',
        'trn_store_customers.customer_mobile_number',
        'trn_store_customers.place',

        'mst_stores.store_id',
        'mst_stores.store_name',
        'mst_stores.store_mobile',

        'mst_delivery_boys.delivery_boy_name',
        'mst_delivery_boys.delivery_boy_mobile'



      )
        ->join('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn_store_orders.customer_id')
        ->leftjoin('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'trn_store_orders.delivery_boy_id')
        ->leftjoin('mst_stores', 'mst_stores.store_id', '=', 'trn_store_orders.store_id');

      if ($_GET) {
        $datefrom = $request->date_from;
        $dateto = $request->date_to;

        $a1 = Carbon::parse($request->date_from)->startOfDay();
        $a2  = Carbon::parse($request->date_to)->endOfDay();

        if (isset($request->date_from)) {
          $data = $data->whereDate('trn_store_orders.created_at', '>=', $a1);
        }

        if (isset($request->date_to)) {
          $data = $data->whereDate('trn_store_orders.created_at', '<=', $a2);
        }

        if (isset($request->customer_mobile_number)) {


          $data = $data->where('trn_store_customers.customer_mobile_number', 'LIKE', '%' . $request->customer_mobile_number . '%');
       
    }


        // if (isset($request->customer_id)) {
        //   $data = $data->where('trn_store_orders.customer_id', '=', $request->customer_id);
        // }

        if (isset($request->delivery_boy_id)) {
          $data = $data->where('trn_store_orders.delivery_boy_id', '=', $request->delivery_boy_id);
        }

        if (isset($request->status_id)) {
          $data = $data->where('trn_store_orders.status_id', '=', $request->status_id);
        }

        if (isset($request->order_type)) {
          $data = $data->where('trn_store_orders.order_type', '=', $request->order_type);
        }
      }

      $data = $data->where('trn_store_orders.store_id', $store_id)->where('trn_store_orders.order_type', 'APP')
        ->orderBy('trn_store_orders.order_id', 'DESC')
        ->get();


      return view('admin.elements.reports.payment_report', compact('dateto', 'datefrom', 'data', 'pageTitle'));
    } catch (\Exception $e) {
      return redirect()->back()->withErrors([$e->getMessage()])->withInput();
      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }
  public function storeIncomingPayments(Request $request)
  {
    $pageTitle = "Incoming Payments Reports";
    $customer = Trn_store_customer::all();
    $payment_type = Sys_payment_type::all();

    // $store_id  = Auth::guard('store')->user()->store_id;
    $store_id = 1;

    $payments = Trn_OrderPaymentTransaction::join('trn__order_split_payments', 'trn__order_split_payments.opt_id', '=', 'trn__order_payment_transactions.opt_id')
      ->join('trn_store_orders', 'trn_store_orders.order_id', '=', 'trn__order_payment_transactions.order_id')
      ->where('trn__order_split_payments.paymentRole', '=', 1)
      ->where('trn_store_orders.store_id', '=', $store_id)
      ->orderBy('trn__order_split_payments.opt_id', 'DESC')
      ->get();

    if ($_GET) {

      $datefrom = $request->date_from;
      $dateto = $request->date_to;

      $a1 = Carbon::parse($request->date_from)->startOfDay();
      $a2  = Carbon::parse($request->date_to)->endOfDay();


      $payments = Trn_OrderPaymentTransaction::join('trn__order_split_payments', 'trn__order_split_payments.opt_id', '=', 'trn__order_payment_transactions.opt_id')
        ->join('trn_store_orders', 'trn_store_orders.order_id', '=', 'trn__order_payment_transactions.order_id')
        ->where('trn__order_split_payments.paymentRole', '=', 1)
        ->where('trn_store_orders.store_id', '=', $store_id);


      if (isset($request->date_from)) {
        $payments = $payments->whereDate('trn_store_orders.created_at', '>=', $a1);
      }

      if (isset($request->date_to)) {
        $payments = $payments->whereDate('trn_store_orders.created_at', '<=', $a2);
      }
      $payments = $payments->orderBy('trn__order_split_payments.opt_id', 'DESC')->get();

      return view('admin.elements.reports.list', compact('dateto', 'datefrom', 'payments', 'pageTitle', 'customer', 'payment_type'));
    }

    return view('admin.elements.reports.list', compact('payments', 'pageTitle', 'customer', 'payment_type'));
  }

  public function storeRefundReports(Request $request)
  {
  
  try {

    $pageTitle = "Refund Reports";
  //$store_id  = Auth::guard('store')->user()->store_id;
    $store_id = 1;
    $datefrom = '';
    $dateto = '';

    $customers = Trn_store_customer::all();
    $deliveryBoys =  Mst_store_link_delivery_boy::join('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'mst_store_link_delivery_boys.delivery_boy_id')
      ->where('mst_store_link_delivery_boys.store_id', $store_id)->get();

    $orderStatus = Sys_store_order_status::all();


    $data = Trn_store_order::select(

      'trn_store_orders.order_id',
      'trn_store_orders.order_number',
      'trn_store_orders.customer_id',
      'trn_store_orders.store_id',
      'trn_store_orders.subadmin_id',
      'trn_store_orders.product_total_amount',
      'trn_store_orders.delivery_charge',
      'trn_store_orders.packing_charge',
      'trn_store_orders.payment_type_id',
      'trn_store_orders.status_id',
      'trn_store_orders.payment_status',
      'trn_store_orders.delivery_status_id',
      'trn_store_orders.delivery_boy_id',
      'trn_store_orders.coupon_id',
      'trn_store_orders.coupon_code',
      'trn_store_orders.reward_points_used',
      'trn_store_orders.amount_before_applying_rp',
      'trn_store_orders.trn_id',
      'trn_store_orders.created_at',
      'trn_store_orders.amount_reduced_by_coupon',
      'trn_store_orders.order_type',
      'trn_store_orders.isRefunded',
      'trn_store_orders.refundId',
      //'trn_store_orders.refundStatus',
      'trn_store_orders.refundNote',
      'trn_store_orders.refundProcessStatus',
      // 'trn_store_orders.refundProcessDate',
      // 'trn_store_orders.refundStartDate',

      'trn_store_customers.customer_id',
      'trn_store_customers.customer_first_name',
      'trn_store_customers.customer_last_name',
      'trn_store_customers.customer_mobile_number',
      'trn_store_customers.place',

      'mst_stores.store_id',
      'mst_stores.store_name',
      'mst_stores.store_mobile',

      'mst_delivery_boys.delivery_boy_name',
      'mst_delivery_boys.delivery_boy_mobile'



    )
      ->join('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn_store_orders.customer_id')
      ->leftjoin('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'trn_store_orders.delivery_boy_id')
      ->leftjoin('mst_stores', 'mst_stores.store_id', '=', 'trn_store_orders.store_id');

    if ($_GET) {
      $datefrom = $request->date_from;
      $dateto = $request->date_to;

      $a1 = Carbon::parse($request->date_from)->startOfDay();
      $a2  = Carbon::parse($request->date_to)->endOfDay();

      if (isset($request->date_from)) {
        $data = $data->whereDate('trn_store_orders.created_at', '>=', $a1);
      }

      if (isset($request->date_to)) {
        $data = $data->whereDate('trn_store_orders.created_at', '<=', $a2);
      }


      if (isset($request->customer_id)) {
        $data = $data->where('trn_store_orders.customer_id', '=', $request->customer_id);
      }

      if (isset($request->delivery_boy_id)) {
        $data = $data->where('trn_store_orders.delivery_boy_id', '=', $request->delivery_boy_id);
      }

      if (isset($request->status_id)) {
        $data = $data->where('trn_store_orders.status_id', '=', $request->status_id);
      }

      if (isset($request->order_type)) {
        $data = $data->where('trn_store_orders.order_type', '=', $request->order_type);
      }
    }

    $data = $data->where('trn_store_orders.store_id', $store_id)->where('trn_store_orders.order_type', 'APP')
      ->whereIn('isRefunded', ['1', '2'])
      ->orderBy('trn_store_orders.order_id', 'DESC')
      ->get();


    return view('admin.elements.reports.refund_report', compact('orderStatus', 'deliveryBoys', 'customers', 'dateto', 'datefrom', 'data', 'pageTitle'));
  } catch (\Exception $e) {
    return redirect()->back()->withErrors([$e->getMessage()])->withInput();
    return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
  }
}

}
