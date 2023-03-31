<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\admin\Trn_TermsAndCondition;
use App\Models\admin\Sys_DisputeStatus;
use App\Models\admin\Sys_OrderStatus;
use App\Models\admin\Mst_store_product_varient;
use App\Models\admin\Mst_branch_product_varient;
use App\Models\admin\Trn_Dispute;
use App\Models\admin\Trn_Order;
use App\Models\admin\Mst_ConfigurePoints;
use App\Models\admin\Mst_ItemCategory;
use App\Models\admin\Mst_OfferZone;
use App\Models\admin\Mst_Product;
use App\Models\admin\Mst_ProductVariant;
use App\Models\admin\Mst_attribute_group;
use App\Models\admin\Mst_attribute_value;
use App\Models\admin\Mst_Setting;
use App\Models\admin\Mst_WorkingDay;
use App\Models\admin\Trn_OrderItem;
use App\Models\admin\Trn_StoreTimeSlot;
use App\Models\admin\Trn_StoreDeliveryTimeSlot;
use App\Models\admin\Trn_ServiceAreaSplit;
use App\Models\admin\Trn_CustomerDeviceToken;
use App\Models\admin\Trn_store_order;
use App\Models\User;
use App\Models\Mst_branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\admin\Mst_Tax;
use App\Models\admin\Trn_TaxSplitUp;
use Illuminate\Support\Facades\DB;
use Hash;
use Auth;
use Crypt;
use Carbon\Carbon;

class AdminController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function listoffer(Request $request)
    {
        $pageTitle = "Offer Zone";
        $offers = Mst_OfferZone::orderBy('offer_id', 'DESC')->get();
        return view('admin.elements.offers.list', compact('offers', 'pageTitle'));
    }

    public function createoffer(Request $request)
    {
        $pageTitle = "Create Offer";
        $branches = Mst_branch::isActive()->get(['branch_name','branch_id']);
        $categories = Mst_ItemCategory::all();
        return view('admin.elements.offers.create', compact('pageTitle', 'categories','branches'));
    }

    public function editoffer(Request $request, $offer_id)
    {
        $pageTitle = "Edit Offer";
        $offer = Mst_OfferZone::find($offer_id);
        $branches = Mst_branch::isActive()->get(['branch_name','branch_id']);
        $categories = Mst_ItemCategory::all();
        return view('admin.elements.offers.edit', compact('pageTitle', 'offer','categories','branches'));
    }
    public function listRestoreTaxes(Request $request)
    {
        $pageTitle = "Restore Taxes";
        $taxes = Mst_Tax::where('is_removed', 1)->orderBy('tax_id', 'DESC')->get();
        $tax_splits = Trn_TaxSplitUp::orderBy('tax_split_up_id', 'DESC')->get();
        return view('admin.elements.taxes.restore', compact('tax_splits', 'pageTitle', 'taxes'));
    }

    public function restoreTax(Request $request, $tax_id)
    {
        $vehicle_type = Mst_Tax::where('tax_id', $tax_id)->update(['is_removed' => 0]);
        return redirect('admin/tax/list')->with('status', 'Tax restored successfully.');
    }
    
    public function storeoffer(Request $request, Mst_OfferZone $offer)
    {
         $data = $request->except('_token');
         
         
         $existing = Mst_OfferZone::where('product_variant_id',$request->product_variant_id)->get();
         
         foreach($existing as $ext)
         {
             if($ext->date_start >= $request->date_start && $ext->date_start <= $request->date_start)
             {
               
               throw ValidationException::withMessages(['product_variant_id' => 'Offer exists for current product between requested dates']);
                 
             }
             
              
         }
         
         $product = Mst_branch_product_varient::find($request->product_variant_id);

         $product_id = $product->product_id;

         $price = $product->product_varient_offer_price;


        $validator = Validator::make(
            $request->all(),
            [
                'product_variant_id'       => 'required',
                'offer_price'       =>   $request->offer_type == 1 ? "numeric" : "numeric|max:$price" ,
            ],
            [
                'product_variant_id.required'         => 'Product Variant required',
                'offer_price.required'        => 'Offer price required',
            ]
        );

        if (!$validator->fails()) {
           
            
              $offer_price = $request->offer_price;
            
            $data = $request->except('_token');
            $offer->product_variant_id = $request->product_variant_id;
            $offer->product_id         = $product_id;
            $offer->offer_type         = $request->offer;
            $offer->discount_type      = $request->offer_type;
            $offer->offer_price        = $offer_price;
            $offer->date_start         = $request->date_start;
            $offer->time_start         = $request->time_start;
            $offer->date_end           = $request->date_end;
            $offer->time_end           = $request->time_end;
            $offer->link               = $request->link;
            $offer->is_active          = $request->is_active;
 


            $offer->save();

            return redirect('/admin/offers/list')->with('status', 'Offer added successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    public function removeoffer(Request $request, $offer_id)
    {
        try {
            Mst_OfferZone::where('offer_id', $offer_id)->delete();
            return redirect()->route('admin.offers')->with('status', 'Offer deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
        }
    }



    public function updateoffer(Request $request,$offer_id)
    {
        $data = $request->except('_token');

        $product = Mst_branch_product_varient::find($request->product_variant_id);
        
        $existing = Mst_OfferZone::where('product_variant_id',$request->product_variant_id)->get();
       //dd($offer_id);
         
         foreach($existing as $ext)
         {
             if($ext->offer_id!=$offer_id)
             {
                  if($ext->date_start >= $request->date_start && $ext->date_start <= $request->date_start)
             {
                 
               
               throw ValidationException::withMessages(['product_variant_id' => 'Offer exists for current product between requested dates']);
                 
             }
                 
             }
            
             
              
         }

        $price = $product->product_varient_price;

        $validator = Validator::make(
            $request->all(),
            [
                'product_variant_id'       => 'required',
                'offer_price'       =>  $request->offer_type == 1 ? "numeric" : "numeric|max:$price" ,
            ],
            [
                'product_variant_id.required'         => 'Product Variant required',
                'offer_price.required'        => 'Offer price required',
            ]
        );

              $offer_price = $request->offer_price;
            


        if (!$validator->fails()) {

            $data = $request->except('_token');
            $offer = Mst_OfferZone::find($offer_id);
            $offer->product_variant_id = $request->product_variant_id;
            $offer->offer_price        = $offer_price;
            $offer->offer_type         = $request->offer;
            $offer->discount_type      = $request->offer_type;
            $offer->date_start         = $request->date_start;
            $offer->time_start         = $request->time_start;
            $offer->date_end           = $request->date_end;
            $offer->time_end           = $request->time_end;
            $offer->link               = $request->link;
            $offer->is_active          = 1;
            $offer->update();

            return redirect('/admin/offers/list')->with('status', 'Offer updated successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    public function GetItemByCategory($item_category_id,$branch)
    {

        $products  = Mst_branch_product_varient::join('mst_branch_products', 'mst_branch_products.branch_product_id', '=', 'mst_branch_product_varients.product_id')
        ->join('mst__item_categories', 'mst__item_categories.item_category_id', '=', 'mst_branch_products.product_cat_id')
        ->where('mst_branch_products.is_removed', 0)
        ->where('mst__item_categories.is_active', 1)
        ->where('mst__item_categories.item_category_id', $item_category_id)
        ->where('mst_branch_product_varients.branch_id', $branch)
        ->orderBy('mst_branch_product_varients.stock_count', 'ASC')
        ->where('mst_branch_product_varients.is_removed', 0)
        ->get(['varient_id','product_name','variant_name','is_base_variant']);
        
        //dd($products);
        
           $products->each(function ($item, $key) {
              if ($item->is_base_variant == 0) {
                 
                 $item->variant_name = $item->variant_name.' -- '.$item->product_name;
            }
          });
          
          
        return response($products);
    }


    public function editStatusoffer(Request $request)
    {
        $offer_id = $request->offer_id;
        if ($c = Mst_OfferZone::findOrFail($offer_id)) {
            if ($c->is_active == 0) {
                Mst_OfferZone::where('offer_id', $offer_id)->update(['is_active' => 1]);
                echo "active";
            } else {
                Mst_OfferZone::where('offer_id', $offer_id)->update(['is_active' => 0]);
                echo "inactive";
            }
        }
    }



    public function workingDaysUpdate(Request $request)
    {
        // dd($request->all());

        $start = $request->start;
        $end = $request->end;
        $day = $request->day;

        $s_count = Mst_WorkingDay::count();

        if ($s_count > 1) {
            Mst_WorkingDay::all()->delete();
        }


        $i = 0;
        foreach ($request->day as $s) {
            $info = [
                'day' => $day[$i],
                'time_start' =>  $start[$i],
                'time_end' => $end[$i],
            ];

            //print_r($info);die;

            Mst_WorkingDay::insert($info);
            $i++;
        }
        return  redirect()->back()->with('status', 'Working days updated successfully.');
    }



    public function workingDays(Request $request)
    {
        $pageTitle = "Working Days";
        $time_slots_count = Mst_WorkingDay::count();
        $time_slots = Mst_WorkingDay::all();
        return view('admin.elements.settings.working_days', compact('time_slots', 'time_slots_count', 'pageTitle'));
    }
    public function Profile(Request $request)
    {
        $pageTitle = "Profile";
        $admin = User::find(auth()->user()->id);
        return view('admin.elements.profile.profile', compact('admin','pageTitle'));
    }

    // public function editProfile(Request $request)
    // {
    //     $admin = User::find(auth()->user()->id);
    //     return view('admin.elements.profile.profile', compact('admin'));
    // }

    public function updateProfile(Request $request)
    {
        $pageTitle = "Profile";
        $id =  auth()->user()->id;

        try {

            $validator = Validator::make(
                $request->all(),
                [
                    'name'       => 'required',
                    'email'       => 'required|unique:users,email,' . $id . ',id',
                    'password' => 'confirmed',
                ],
                [
                    'name.required'         => 'Name required',
                    'email.required'         => 'Email required',
                    'email.unique'         => 'Email exists',
                    'password.required'         => 'Password required',
                    'password.confirmed'         => 'Passwords not matching',
                    'password.min'         => 'Password should have 6 character',
                ]
            );
            if (!$validator->fails()) {
              

                $user = User::find($id);
                
                $user->name = $request->name;
                $user->email = $request->email;
                if($request->filled('password'))
                {
                $user->password = Hash::make($request->password);
                $user->update();
                Auth::login($user);
                }
                else{
                    $user->update(); 
                }

                return redirect()->route('admin.profile')->with('status', 'Profile updated successfully');
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
        }
    }


    // public function time_slot(Request $request)
    // {
    //     $pageTitle = "Working Days";

    //     $time_slots_count = Mst_WorkingDay::count();
    //     $time_slots = Mst_WorkingDay::get();


    //     return view('admin.elements.time_slot.create', compact('time_slots_count', 'time_slots', 'store', 'pageTitle', 'store_id'));
    // }


    public function Settings(Request $request)
    {
        $pageTitle = 'Settings';
        $settings = Mst_Setting::first();
        $admin_settings = Trn_ServiceAreaSplit::orderBy('sas_id', 'DESC')->get();
        $settingcount = Trn_ServiceAreaSplit::count();

        return view('admin.elements.settings.create', compact('admin_settings', 'settingcount', 'pageTitle', 'settings'));
    }

    public function UpdateSettings(Request $request)
    {

        if (isset($request->start) < 1) {
            return redirect()->back();
        }

        $s_count = Trn_ServiceAreaSplit::count();

        if ($s_count >= 1) {
            Trn_ServiceAreaSplit::where('service_start', '!=', null)->delete();
        }

        $i = 0;
        $start = $request->start;
        $end = $request->end;
        $delivery_charge = $request->delivery_charge;
        $packing_charge = $request->packing_charge;

        $data = [

            'service_area' => $request->service_area,
            'order_number_prefix' => $request->order_number_prefix,
            'is_tax_included' => $request->is_tax_included,

        ];
        if (Mst_Setting::count() > 0)
            Mst_Setting::first()->update($data);
        else
            Mst_Setting::create($data);

        foreach ($request->start as $s) {
            $info = [
                'service_start' => $start[$i],
                'service_end' =>  $end[$i],
                'delivery_charge' => $delivery_charge[$i],
                'packing_charge' => $packing_charge[$i],

            ];
            Trn_ServiceAreaSplit::insert($info);
            $i++;
        }

        return redirect()->back()->with('status', 'Settings updated successfully.');
    }

    public function adminHome(Request $request)
    {
        $pageTitle = "Dashboard";
        return view('admin.home',compact('pageTitle'));
    }

    public function configurePoints(Request $request)
    {
        $pageTitle = "Configure Points";
        $configure_points = null;
        if (Mst_ConfigurePoints::find(1)) {
            $configure_points = Mst_ConfigurePoints::find(1);
        } else {
            $cp = new Mst_ConfigurePoints;
            $cp->registraion_points = null;
            $cp->first_order_points = null;
            $cp->referal_points = null;
            $cp->rupee = null;
            $cp->rupee_points = null;
            $cp->order_amount = null;
            $cp->order_points = null;
            $cp->redeem_percentage = null;
            $cp->max_redeem_amount = null;
            $cp->joiner_points = null;
            $cp->save();
            $configure_points = Mst_ConfigurePoints::find(1);
        }
        return view('admin.elements.customer_rewards.edit', compact('configure_points', 'pageTitle'));
    }

    public function updateConfigurePoints(Request $request)
    {
        //dd($request->all());
        $cp = Mst_ConfigurePoints::find(1);
        $cp->registraion_points = $request->registraion_points;
        $cp->first_order_points = $request->first_order_points;
        $cp->referal_points = $request->referal_points;
        $cp->rupee = $request->rupee;
        $cp->rupee_points = $request->rupee_points;
        $cp->order_amount = $request->order_amount;
        $cp->order_points = $request->order_points;
        $cp->redeem_percentage = $request->redeem_percentage;
        $cp->max_redeem_amount = $request->max_redeem_amount;
        $cp->joiner_points = $request->joiner_points;
        $cp->update();
        return redirect()->back()->with('status', 'Configure Points updated successfully.');
    }

    

    public function viewOrder(Request $request)
    {
        $order_id = $request->order_id;
        $data = Trn_Order::where('order_id', $order_id)->first();
        // $data2 = Trn_OrderItem::where('order_id', $order_id)->get();
        $data->customer_name = @$data->customerData->customer_name;
        $data->customer_mobile = @$data->customerData->customer_mobile;
        $data->order_number = @$data->order_number;
        $data->order_total_amount = @$data->order_total_amount;
        return json_encode($data);
    }


    public function listOrders(Request $request)
    {
        $pageTitle = "Order";
        $orders = Trn_Order::orderBy('order_id', 'DESC')->get();
        $orderStatus = Sys_OrderStatus::orderBy('order_status_id', 'ASC')->get();
        return view('admin.elements.order.list', compact('orders', 'orderStatus', 'pageTitle'));
    }

    public function findOrderStatus(Request $request)
    {
        $orderId = $request->order_id;
        $orderData = Trn_Order::where('order_id', $orderId)
            ->select('order_id', 'order_status_id', 'order_number')->first();
        $orderData->status = $orderData->orderStatusData->status;
        return json_encode($orderData);
    }

    public function updateOrderStatus(Request $request)
    {
        $orderId = $request->order_id;
        $orderStatusId = $request->order_status_id;

        $orderData = array();
        $orderData['order_status_id'] = $orderStatusId;

        if (Trn_Order::where('order_id', $orderId)->update(['order_status_id' => $orderStatusId])) {
            $orderDatas = Trn_Order::find($orderId);
            $orderDatas->status = $orderDatas->orderStatusData->status;
            return json_encode($orderDatas);
        } else {
            return false;
        }
    }

    public function listTaxes(Request $request)
    {
        $pageTitle = "List Taxes";
        $taxes = Mst_Tax::orderBy('tax_id', 'DESC')->where('is_removed', '!=', 1)->get();
        $tax_splits = Trn_TaxSplitUp::orderBy('tax_split_up_id', 'DESC')->get();
        return view('admin.elements.taxes.list', compact('tax_splits', 'pageTitle', 'taxes'));
    }

    public function addTaxes(Request $request)
    {
        $pageTitle = "Add Tax";
        return view('admin.elements.taxes.add', compact('pageTitle'));
    }
    public function createTax(Request $request, Mst_Tax $tax)
    {

        $tax->tax_value  = $request->tax_value;
        $tax->tax_name  = $request->tax_name;
        // dd($request->all());
        $tax->save();
        $last_id = DB::getPdo()->lastInsertId();
        $i = 0;
        foreach ($request->split_tax_name as $tax) {


            $data = [
                'tax_id'      => $last_id,
                'split_tax_name'      => $tax,
                'split_tax_value'      => $request->split_tax_value[$i],

            ];
            Trn_TaxSplitUp::create($data);

            $i++;
        }


        return redirect('admin/tax/list')->with('status', 'Tax added successfully.');
    }
    public function removeTax(Request $request, Mst_Tax $tax, $tax_id)
    {
        $tax = Mst_Tax::where('tax_id', $tax_id)->update(['is_removed' => 1]);

        return redirect()->back()->with('status', 'Tax removed successfully.');
    }

    public function editTax(Request $request, Mst_Tax $tax, $tax_id)
    {
        $pageTitle = "Edit Tax";
        $tax = Mst_Tax::find($tax_id);
        $tax_splits = Trn_TaxSplitUp::where('tax_id', $tax_id)->get();
        return view('admin.elements.taxes.edit', compact('tax_splits', 'pageTitle', 'tax'));
    }

    public function updateTax(Request $request, Mst_Tax $tax, $tax_id)
    {
        // dd($request->all());

        $tax = Mst_Tax::find($tax_id);
        $tax->tax_value  = $request->tax_value;
        $tax->tax_name  = $request->tax_name;
        $tax->update();

        Trn_TaxSplitUp::where('tax_id', $tax_id)->delete();

        $i = 0;
        foreach ($request->split_tax_name as $tax) {


            $data = [
                'tax_id'      => $tax_id,
                'split_tax_name'      => $tax,
                'split_tax_value'      => $request->split_tax_value[$i],

            ];
            Trn_TaxSplitUp::create($data);

            $i++;
        }




        return redirect('admin/tax/list')->with('status', 'Tax updated successfully.');
    }

    public function storeAttribute(Request $request, Mst_attribute_group $attr_group)
    {
  
      $validator = Validator::make(
        $request->all(),
        [
          'group_name'                 => 'required',
  
  
        ],
        [
          'group_name.required'                 => 'Group name required',
  
  
        ]
      );
  
      if (!$validator->fails()) {
        $data = $request->except('_token');
  
  
        $attr_group->group_name      = $request->group_name;
  
        $attr_group->save();
        return redirect()->back()->with('status', 'Attribute added successfully.');
      } else {
  
        return redirect()->back()->withErrors($validator)->withInput();
      }
    }
    public function listAttributeGroup()
    {
  
      $pageHeading = "attribute_group";
      $pageTitle = "Attribute Group List";
      $attributegroups = Mst_attribute_group::all();
  
      return view('admin.elements.attribute_group.list', compact('attributegroups', 'pageTitle', 'pageHeading'));
    }
  
  
  
    public function editAttributeGroup(Request $request, $id)
    {
  
      $decryptId = Crypt::decryptString($id);
  
  
      $pageTitle = "Edit Attribute Group";
      $attributegroup = Mst_attribute_group::Find($decryptId);
  
      return view('admin.elements.attribute_group.edit', compact('attributegroup', 'pageTitle'));
    }
  
    public function updateAtrGroup(
      Request $request,
      Mst_attribute_group $attributegroup,
      $attr_group_id
    ) {
  
      $GrpId = $request->attr_group_id;
      $attributegroup = Mst_attribute_group::Find($GrpId);
  
      $validator = Validator::make(
        $request->all(),
        [
          'group_name'   => 'required',
  
        ],
        [
          'group_name.required'        => 'Group name required',
  
  
        ]
      );
  
      if (!$validator->fails()) {
        $data = $request->except('_token');
  
        $attributegroup->group_name  = $request->group_name;
  
  
        $attributegroup->update();
  
        return redirect('store/attribute_group/list')->with('status', 'Attribute group updated successfully.');
      } else {
  
        return redirect()->back()->withErrors($validator)->withInput();
      }
    }
  
    public function listAttr_Value()
    {
  
      $pageTitle = "List Attribute Value";
      $attributevalues = Mst_attribute_value::all();
      $attributegroups = Mst_attribute_group::all();
  
      return view('admin.elements.attribute_value.list', compact('attributevalues', 'pageTitle', 'attributegroups'));
    }
  
    public function createAttr_Value(Request $request, Mst_attribute_value $attribute_value)
    {
  
  
      $pageTitle = "Create Attribute Value";
      $attributevalues = Mst_attribute_value::all();
      $attributegroups = Mst_attribute_group::all();
  
      //$attr_grps    = $request->$attribute_group_id;
      return view('admin.elements.attribute_value.create', compact('attributevalues', 'pageTitle', 'attributegroups'));
    }
  
    public function storeAttr_Value(Request $request, Mst_attribute_value $attribute_value)
    {
  
      $validator = Validator::make(
        $request->all(),
        [
          'group_value'       => 'required',
          'attribute_group_id' => 'required',
  
        ],
        [
          'group_value.required'          => 'Attribute value required',
          'attribute_group_id.required|nimeric' => 'Select group of attribute'
  
  
        ]
      );
      // $this->uploads($request);
      if (!$validator->fails()) {
        $data = $request->except('_token');
  
        $values = $request->group_value;
  
        //dd($values);
        $attr_grp_value = $request->attribute_group_id;
        $Hexvalue = $request->Hexvalue;
        $group_value = $request->group_value;
        $status = 1;
        $date =  Carbon::now();
        // dd($date);
        if ($attr_grp_value == 2) {
          if ($Hexvalue) {
            $count = count($Hexvalue);
            //dd($count);
  
            //$countvalue = 2;
            for ($i = 0; $i < $count; $i++) {
  
              $attribute_value = new Mst_attribute_value;
              $attribute_value->attribute_group_id = $attr_grp_value;
              $attribute_value->attr_value_status = $status;
              $attribute_value->group_value = $request->group_value[$i];
              $attribute_value->Hexvalue = $Hexvalue[$i];
              $attribute_value->created_at = $date;
              $attribute_value->updated_at = $date;
  
              $attribute_value->save();
            }
          }
        } else {
  
          foreach ($values as $value) {
  
            $data = [
              [
                'group_value' => $value,
                'attribute_group_id' => $request->attribute_group_id,
                'attr_value_status' => 1,
                'created_at' => $date,
                'updated_at' => $date,
  
  
              ],
            ];
            //dd($data);
  
            Mst_attribute_value::insert($data);
          }
        }
  
        return redirect('store/attribute_value/list')->with('status', 'Attribute added successfully.');
      } else {
        //return redirect('/')->withErrors($validator->errors());
        return redirect()->back()->withErrors($validator)->withInput();
      }
    }
    public function editAttr_Value(Request $request, $id)
    {
  
      $decryptId = Crypt::decryptString($id);
  
      $pageTitle = "Edit Attribute Value";
      $attributevalue = Mst_attribute_value::Find($decryptId);
      $attributegroups = Mst_attribute_group::all();
  
      return view('admin.elements.attribute_value.edit', compact('attributevalue', 'attributegroups', 'pageTitle'));
    }
  
    public function updateAttr_Value(
      Request $request,
      Mst_attribute_value $attributevalue,
      $attr_value_id
    ) {
  
      $GrpId = $request->attr_value_id;
      $attributevalue = Mst_attribute_value::Find($GrpId);
  
      $validator = Validator::make(
        $request->all(),
        [
          'group_value'   => 'required',
          'attribute_group_id' => 'required',
  
        ],
        [
          'group_value.required'        => 'Group value required',
          'attribute_group_id'          => 'Group name required'
  
  
        ]
      );
      // $this->uploads($request);
      if (!$validator->fails()) {
        $data = $request->except('_token');
  
        $attributevalue->group_value  = $request->group_value;
        $attributevalue->attribute_group_id  = $request->attribute_group_id;
        if ($request->attribute_group_id == 2) {
          $attributevalue->Hexvalue  = $request->Hexvalue;
        }
  
        $attributevalue->update();
        //dd($fetch);
        return redirect('store/attribute_value/list')->with('status', 'Attribute value updated successfully.');
      } else {
  
        return redirect()->back()->withErrors($validator)->withInput();
      }
    }
    public function destroyAttr_Value(Request $request, Mst_attribute_value $attribute_value)
    {
  
      $delete = $attribute_value->delete();
  
  
      return redirect('store/attribute_value/list')->with('status', 'Attribute value deleted successfully.');;
    }
    public function destroyAttr_Group(Request $request, Mst_attribute_group $attribute_group)
    {
  
      $delete = $attribute_group->delete();
  
  
      return redirect('store/attribute_group/list')->with('status', 'Attribute group deleted successfully.');;
    }
  
    public function listDisputes(Request $request)
    {
      $pageTitle = "Disputes";
      //$store_id  = Auth::guard('store')->user()->store_id;
      $store_id = 1;
      if ($_GET) {
  
        $datefrom = $request->date_from;
        $dateto = $request->date_to;
  
        $a1 = Carbon::parse($request->date_from)->startOfDay();
        $a2  = Carbon::parse($request->date_to)->endOfDay();
  
        $order_number  = $request->order_number;
  
        $query = \DB::table("mst_disputes")->where('store_id', $store_id)->select("*");
  
  
        if (isset($order_number)) {
          $query = $query->where('order_number', $order_number);
        }
        if (isset($request->date_from) && isset($request->date_to)) {
          // $query = $query->whereBetween('created_at',[$a1->format('Y-m-d')." 00:00:00",$a2->format('Y-m-d')." 00:00:00"]);
          //echo "die";die;
          $query = $query->whereDate('created_at', '>=', $a1->format('Y-m-d') . " 00:00:00");
          $query = $query->whereDate('created_at', '<=', $a2->format('Y-m-d') . " 00:00:00");
        }
  
        if (isset($request->date_from) && !isset($request->date_to)) {
          $query = $query->whereDate('created_at', '>=', $a1->format('Y-m-d') . " 00:00:00");
        }
        if (!isset($request->date_from) && isset($request->date_to)) {
          $query = $query->whereDate('created_at', '<=', $a2->format('Y-m-d') . " 00:00:00");
        }
        
        $query->orderBy('dispute_id', 'DESC');
        $disputes = $query->get();
        return view('admin.elements.disputes.list', compact('dateto', 'datefrom', 'disputes', 'pageTitle'));
      }
  
      $disputes = \DB::table("mst_disputes")->where('store_id', $store_id)->select("*")->orderBy('dispute_id', 'DESC')->get();
      return view('admin.elements.disputes.list', compact('disputes', 'pageTitle'));
    }
  
    public function statusDisputes(Request $request, $dispute_id)
    {
      $data['dispute_status']  = $request->dispute_status;
      $data['store_response']  = $request->store_response;
      $query = \DB::table("mst_disputes")->where('dispute_id', $dispute_id)->update($data);
      $dispData =  \DB::table("mst_disputes")->where('dispute_id', $dispute_id)->first();
      if ($request->dispute_status == 1) {
        $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $dispData->customer_id)->get();
        $orderData = Trn_store_order::find($dispData->order_id);
  
        foreach ($customerDevice as $cd) {
          $title = 'Dispute closed';
          //  $body = 'First order points credited successully..';
          $body =  'Your dispute with order number' . $orderData->order_number . ' is closed by store..';
          $clickAction = "OrderListFragment";
                          $type = "order";
          $data['response'] =  Helper::customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
        }
      }
  
      if ($request->dispute_status == 3) {
        $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $dispData->customer_id)->get();
        $orderData = Trn_store_order::find($dispData->order_id);
  
        foreach ($customerDevice as $cd) {
          $title = 'Dispute in progress';
          //  $body = 'First order points credited successully..';
          $body =  'Your dispute with order number' . $orderData->order_number . ' is in progress..';
          $clickAction = "OrderListFragment";
                          $type = "order";
          $data['response'] =  Helper::customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
        }
      }
  
      return redirect()->back()->with('status', 'Status updated successfully.');
    }
  
  
    public function storeResponseUpdate(Request $request, $dispute_id)
    {
      $data['store_response']  = $request->store_response;
      $query = \DB::table("mst_disputes")->where('dispute_id', $dispute_id)->update($data);
  
  
      return redirect()->back()->with('status', 'Store rensponse updated successfully.');
    }
  
  
  
    public function currentIssues(Request $request)
    {
      $pageTitle = "Current Disputes";
      //$store_id  = Auth::guard('store')->user()->store_id;
      $store_id =1;
      $disputes = \DB::table("mst_disputes")->where('store_id', $store_id)->select("*")
        ->orderBy('dispute_id', 'DESC')->get();
      return view('admin.elements.disputes.list', compact('disputes', 'pageTitle'));
    }
  
  
    public function newIssues(Request $request)
    {
      $pageTitle = "New Disputes";
     //$store_id  = Auth::guard('store')->user()->store_id;
     $store_id =1;
      $disputes = \DB::table("mst_disputes")->where('store_id', $store_id)->select("*")
        ->whereDate('created_at', Carbon::today())->orderBy('dispute_id', 'DESC')->get();
      return view('admin.elements.disputes.list', compact('disputes', 'pageTitle'));
    }
    public function viewDisputeOrder(Request $request, $id)
    {
      try {
        $pageTitle = "View Order";
        $decrId  = Crypt::decryptString($id);
        $order = Trn_store_order::Find($decrId);
        $order_items = Trn_store_order_item::where('order_id', $decrId)->get();
  
        $product = $order->product_id;
  
        //$subadmin_id = Auth()->guard('store')->user()->subadmin_id;
        //$store_id = Auth()->guard('store')->user()->store_id;
        $subadmin_id = 1;
        $store_id = 1;
  
  
        $delivery_boys = Mst_delivery_boy::join('mst_store_link_delivery_boys', 'mst_store_link_delivery_boys.delivery_boy_id', '=', 'mst_delivery_boys.delivery_boy_id')
          ->select("mst_delivery_boys.*")->where('mst_store_link_delivery_boys.store_id', $store_id)->get();
  
        $customer = Trn_store_customer::all();
        $status = Sys_store_order_status::all();
  
        return view('admin.elements.disputes.order_view', compact('delivery_boys', 'order_items', 'order', 'pageTitle', 'status', 'customer'));
      } catch (\Exception $e) {
  
        return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
      }
    }
  
    public function updateTerms(Request $request)
    {
      $pageTitle = "Edit Terms & Conditions";
      $tc = Trn_TermsAndCondition::where('role', 1)->first();
      return view('admin.elements.tc.list', compact('pageTitle', 'tc'));
    }
  
  
  
    public function updateCusTerms(Request $request)
    {
      $pageTitle = "Edit Privacy and Return Policy";
      $tc = Trn_TermsAndCondition::where('role', 2)->first();
      return view('admin.elements.tc.list_cus_tc', compact('pageTitle', 'tc'));
    }
  
    public function updateTC(Request $request)
    {
      $tcCount = Trn_TermsAndCondition::where('role', 1)->count();
      if ($tcCount > 0) {
        Trn_TermsAndCondition::where('role', 1)->update(['terms_and_condition' => $request->tc, 'role' => 1]);
      } else {
        Trn_TermsAndCondition::where('role', 1)->create(['terms_and_condition' => $request->tc, 'role' => 1]);
      }
  
      return redirect()->back()->with('status', 'Terms and conditions updated.');
    }
  
    public function updateCusTC(Request $request)
    {
  
      $tcCount = Trn_TermsAndCondition::where('role', 2)->count();
      if ($tcCount > 0) {
        Trn_TermsAndCondition::where('role', 2)->update(['terms_and_condition' => $request->tc, 'role' => 2]);
      } else {
        Trn_TermsAndCondition::where('role', 2)->create(['terms_and_condition' => $request->tc, 'role' => 2]);
      }
      return redirect()->back()->with('status', 'Terms and conditions updated.');
    }
  
    public function time_slot(Request $request)
    {
      $pageTitle = "Working Days";
      // $store_id =   Auth::guard('store')->user()->store_id;
      $store_id = 1;
      // $store = Mst_store::find($store_id);
      $store = 1;

      // $time_slots_count = Trn_StoreDeliveryTimeSlot::where('store_id', Auth::guard('store')->user()->store_id)->count();
      // $time_slots = Trn_StoreDeliveryTimeSlot::where('store_id', Auth::guard('store')->user()->store_id)->get();

  
      $time_slots_count = Trn_StoreTimeSlot::where('store_id', 1)->count();
      $time_slots = Trn_StoreTimeSlot::where('store_id', 1)->get();

  
      return view('admin.elements.time_slot.create', compact('time_slots_count', 'time_slots', 'store', 'pageTitle', 'store_id'));
    }
  
    public function delivery_time_slots(Request $request)
    {
      $pageTitle = "Time Slots";
      // $store_id =   Auth::guard('store')->user()->store_id;
      $store_id = 1;
      // $store = Mst_store::find($store_id);
      $store = 1;
  
      // $time_slots_count = Trn_StoreDeliveryTimeSlot::where('store_id', Auth::guard('store')->user()->store_id)->count();
      // $time_slots = Trn_StoreDeliveryTimeSlot::where('store_id', Auth::guard('store')->user()->store_id)->get();

      $time_slots_count = Trn_StoreDeliveryTimeSlot::where('store_id', 1)->count();
      $time_slots = Trn_StoreDeliveryTimeSlot::where('store_id', 1)->get();

      
  
      return view('admin.elements.time_slot.delivery_time_slot', compact('time_slots', 'time_slots_count', 'store', 'pageTitle', 'store_id'));
    }
  
    public function update_delivery_time_slots(Request $request)
    {
      
  
      //try {
        //Auth::guard('store')->user()->store_id =1;
        
        $start = $request->start;
        $end = $request->end;
        $days = $request->days;

  
        $s_count = Trn_StoreDeliveryTimeSlot::where('store_id', 1)->count();
  
        if ($s_count >= 1) {
          Trn_StoreDeliveryTimeSlot::where('store_id', 1)->delete();
        }
        $data['error'] = NULL;
        
        $i = 0;
        foreach ($request->start as $s) {

         try{ 

          $start1 = $start[$i];
          $end1   = $end[$i];
          $day1   = $days[$i];

         $s=  Trn_StoreDeliveryTimeSlot::where('day',$day1)->each(function ($record) use($start1,$end1,$day1)
          {
             if($record->time_start < $start1  && $record->time_end > $start1)
             {
                 
               return false;
               
             } 
             if($record->time_start < $end1  &&  $record->time_end > $end1)
             {
               return false;
              
             } 
          });

        
         $t=  Trn_StoreTimeSlot::where('day',$day1)->each(function ($record) use($start1,$end1,$day1)
            {
                  
                if($record->time_start > $start1 || $record->time_end < $start1)
                 {
                    return false;
                   
                 }

                 if($record->time_start > $end1 || $record->time_end < $end1)
                 {
                    return false;
                   
                 }

            });
            
            if($t == false || $s == false )
            {
              throw new \Exception('Time slots Should be between Working Hours and do not add between already existing time slot on the same day');
                    continue;
            }
            
            if($start[$i] > $end[$i])
                {
                    throw new \Exception('End Time must Be Greater Than Start Time');
                    continue;
                   
                }
            

          $info = [
            'store_id' => 1,
            'day' => $days[$i],
            'time_start' =>  $start[$i],
            'time_end' => $end[$i],
          ];
  
          
  
          Trn_StoreDeliveryTimeSlot::insert($info);
          $i++;
         
        }catch(\Exception $e) {

          $data['error'] = $e->getMessage();


           $i++;
          continue;
        }
      }
      if (empty($data['error'])) {
        return  redirect()->back()->with('status', 'Time Slots updated successfully.');
      }
      return redirect()->back()->withErrors($data)->withInput();
    }
  
  
    public function updateTimeSlot(Request $request)
    {
      // dd($request->all());
  
  
  
      $start = $request->start;
      $end = $request->end;
      $day = $request->day;
  
  
      $i = 0;
  
      foreach ($request->day as $s) {
        // echo $start[$i]."  : ". 
        if ($start[$i]  > $end[$i]) {
          return redirect()->back()->withErrors(['Starting time can\'t be greater than ending time.'])->withInput();
        }
        $i++;
      }
  
      $s_count = Trn_StoreTimeSlot::where('store_id',  1)->count();
  
      if ($s_count > 1) {
        Trn_StoreTimeSlot::where('store_id', 1)->delete();
      }
  
  
      $i = 0;
      foreach ($request->day as $s) {
        $info = [
          'store_id' => 1,
          'day' => $day[$i],
          'time_start' =>  $start[$i],
          'time_end' => $end[$i],
        ];
  
        //print_r($info);die;
  
        Trn_StoreTimeSlot::insert($info);
        $i++;
      }
      return  redirect()->back()->with('status', 'Working days updated successfully.');
    }
  
   
  
  



}
