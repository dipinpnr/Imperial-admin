<?php

namespace App\Http\Controllers\admin;


use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Image;
use DB;
use Carbon\Carbon;
use Crypt;

use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\Controller;
use App\Models\admin\Trn_store_customer;
use App\Models\admin\Mst_CustomerGroup;
use App\Models\admin\Trn_configure_points;
use App\Models\admin\Trn_CustomerGroupCustomers;
use App\Models\admin\Trn_customer_reward;
use App\Models\admin\Mst_RewardToCustomer;
use App\Models\admin\Trn_RewardToCustomerTemp;
use Illuminate\Http\Request;

class CustomerController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function addRewardToCustomer()
    {
        $pageTitle = "Add Reward To Non-Existing Customer";
       
        return view('admin.elements.customer_reward.add_customer_rewards.add',compact('pageTitle'));

    
       // return redirect()->back()->with('status','Reward added successfully.');
    }
    public function storeRewardToCustomer(Request $request,Mst_RewardToCustomer $reward,Trn_RewardToCustomerTemp $temp_reward)
    {
      //  dd($request->all());
      try{ 

            if (Trn_store_customer::where('customer_mobile_number', '=', $request->customer_mobile_number)->exists()) 
            {
                return redirect()->back()->withErrors(['Customer exists'])->withInput();
             }  
            else
            {
                 $reward->user_id       = auth()->user()->id;
                $reward->customer_mobile_number     = $request->customer_mobile_number;
                $reward->reward_discription = $request->reward_discription;
                $reward->reward_points = $request->reward_points;
                $reward->added_date         =  Carbon::now()->format('Y-m-d');
                $reward->save(); 
            }
          

        } catch (\Exception $e) {
             //return redirect()->back()->withErrors([  $e->getMessage() ])->withInput();
        
            return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
        }

        
        return redirect('admin/list/reward-to-customer')->with('status','Customer reward added successfully');

    }
    

    public function listCustomeRewards(Request $request)
    {
        $pageTitle = "Customer Rewards";
        $customer_rewards = Trn_CustomerReward::orderBy('customer_reward_id', 'DESC')->get();
        // dd($customers);

        if ($_GET) {

            $datefrom = $request->date_from;
            $dateto = $request->date_to;

            $a1 = Carbon::parse($request->date_from)->startOfDay();
            $a2  = Carbon::parse($request->date_to)->endOfDay();
            $customer_name = $request->customer_name;
            $query = Trn_CustomerReward::join('trn_store_customers', 'trn_store_customers.customer_id', 'trn_customer_rewards.customer_id');

            if (isset($request->date_from) && isset($request->date_to)) {
                $query = $query->whereBetween('trn__customer_rewards.created_at', [$a1, $a2]);
            }

            if (isset($request->customer_name)) {
    
                $query = $query->where("trn_store_customers.customer_first_name like '%$customer_name%' ");
            }

            $customer_rewards = $query->get();
            

            return view('admin.masters.customer_rewards.list', compact('dateto', 'datefrom', 'customer_rewards', 'pageTitle'));
        }


        return view('admin.elements.customer_rewards.list', compact('customer_rewards', 'pageTitle'));
    }

    public function listCustomerGroupCustomers(Request $request)
    {
        $pageTitle = "Customer Group Customers";
        $customers = Trn_CustomerGroupCustomers::orderBy('cgc_id', 'DESC')->get();
        // dd($customers);
        return view('admin.elements.customer_group.cgc_list', compact('customers', 'pageTitle'));
    }

    public function removeCGC(Request $request)
    {
        Trn_CustomerGroupCustomers::where('cgc_id', '=', $request->cgc_id)->delete();
        return redirect('admin/customer-group-customers/list')->with('status', 'Customer removed from customer group successfully.');
    }

    public function assignCGC(Request $request)
    {
        $pageTitle = "Assign Customer to Customer Group";
        $existing_customers = Trn_CustomerGroupCustomers::get()->pluck('customer_id')->all();
        $customers = Trn_store_customer::where('is_active', 1)
            ->select('customer_id', 'customer_first_name', 'customer_mobile_number')
            ->orderBy('customer_first_name')->whereNotIn('customer_id',$existing_customers)->get();
        $customerGroups = Mst_CustomerGroup::where('is_active', 1)->orderBy('customer_group_name')->get();
        return view('admin.elements.customer_group.cgc_assign', compact('customerGroups', 'customers', 'pageTitle'));
    }

    public function storeCGC(Request $request, Trn_CustomerGroupCustomers $cgc)
    {
        $data = $request->except('_token');

        $validator = Validator::make(
            $request->all(),
            [
                'customer_group_id'       => 'required',
                'customer_id'       => 'required',
            ],
            [
                'customer_group_id.required'         => 'Customer group required',
                'customer_id.required'         => 'Customer required',
            ]
        );

        if (!$validator->fails()) {

            $data = $request->except('_token');

            $cgc->customer_group_id = $request->customer_group_id;
            $cgc->customer_id = $request->customer_id;
            $cgc->is_active = 1;
            $cgc->save();

            return redirect('/admin/customer-group-customers/list')->with('status', 'Customer added to Customer group  successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    public function listCustomers(Request $request)
    {
        $pageTitle = "Customers";
        
        if ($_GET) {
  
          $product_name = $request->product_name;
          $product_code = $request->product_code;
          
          $query = Trn_store_customer::orderBy('customer_id', 'DESC');
          
          if (isset($request->customer_name)) {
              
            $name =   $request->customer_name;
            
            $query = $query->where('customer_first_name', 'LIKE', '%' . $name . '%');

          }
          
          if (isset($request->customer_mobile)) {
              
            $query = $query->where('customer_mobile_number', 'LIKE', '%' . $request->customer_mobile . '%');

          }
          if (isset($request->status)) {
              
            $query = $query->where('is_active',$request->status);

          }
          
        $customers = $query->get();
        
        return view('admin.elements.customers.list', compact('customers', 'pageTitle'));
          
        }
        $customers = Trn_store_customer::orderBy('customer_id', 'DESC')->get();
        return view('admin.elements.customers.list', compact('customers', 'pageTitle'));
    }

    public function createCustomer(Request $request)
    {
        $pageTitle = "Create Customer";
        return view('admin.elements.customers.create', compact('pageTitle'));
    }

    public function editCustomer(Request $request, $customer_id)
    {
        $pageTitle = "Edit Customer";
        $customer = Trn_store_customer::find($customer_id);
        return view('admin.elements.customers.edit', compact('customer', 'pageTitle'));
    }

    public function storeCustomer(Request $request, Trn_store_customer $customer)
    {
        $data = $request->except('_token');

        $validator = Validator::make(
            $request->all(),
            [
                'customer_name'       => 'required',
                'customer_mobile'       => 'required|unique:trn_store_customers,customer_mobile_number,NULL,customer_id',
                'customer_email'       => 'required|email|unique:trn_store_customers,customer_email,NULL,customer_id',
                'password' => 'required|confirmed|min:6',


            ],
            [
                'customer_name.required'         => 'Customer name required',
                'customer_mobile.required'         => 'Customer mobile required',
                'password.required'         => 'Password required',
                'password.confirmed'         => 'Passwords not matching',
                'password.min'         => 'Password should have 6 character',

            ]
        );

        if (!$validator->fails()) {

            $data = $request->except('_token');

            $customer->customer_first_name = $request->customer_name;
            $customer->customer_mobile_number = $request->customer_mobile;
            $customer->customer_email = $request->customer_email;
            $customer->dob = $request->customer_dob;
            $customer->gender = $request->customer_gender;
            $customer->password = Hash::make($request->password);;
            $customer->is_active = $request->is_active;
            $customer->save();

            return redirect('/admin/customers/list')->with('status', 'Customer added successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    public function updateCustomer(Request $request, $customer_id)
    {
        $data = $request->except('_token');

        $validator = Validator::make(
            $request->all(),
            [
                'customer_name'       => 'required',
                'customer_mobile'       => 'required|unique:trn_store_customers,customer_mobile_number,' . $customer_id . ',customer_id',
                'customer_email'       => 'required|email|unique:trn_store_customers,customer_email,' . $customer_id . ',customer_id',
                'password' => 'confirmed',


            ],
            [
                'customer_name.required'         => 'Customer name required',
                'customer_mobile.required'         => 'Customer mobile required',
                'password.required'         => 'Password required',
                'password.confirmed'         => 'Passwords not matching',
                'password.min'         => 'Password should have 6 character',

            ]
        );

        if (!$validator->fails()) {

            $data = $request->except('_token');
            $customer = Trn_store_customer::find($customer_id);
            $customer->customer_first_name = $request->customer_name;
            $customer->customer_mobile_number = $request->customer_mobile;
            $customer->customer_email = $request->customer_email;
            $customer->dob = $request->customer_dob;
            $customer->gender = $request->customer_gender;
             if (isset($request->password))
                $customer->password = Hash::make($request->password);

            $customer->is_active = $request->is_active;
            $customer->update();

            return redirect('/admin/customers/list')->with('status', 'Customer updated successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    public function removeCustomer(Request $request, $customer_id)
    {
        Trn_store_customer::where('customer_id', '=', $customer_id)->delete();
        return redirect('admin/customers/list')->with('status', 'Customer deleted successfully.');
    }

    public function editStatusCustomer(Request $request)
    {
        $customer_id = $request->customer_id;
        if ($c = Trn_store_customer::findOrFail($customer_id)) {
            if ($c->is_active == 0) {
                Trn_store_customer::where('customer_id', $customer_id)->update(['is_active' => 1]);
                echo "active";
            } else {
                Trn_store_customer::where('customer_id', $customer_id)->update(['is_active' => 0]);
                echo "inactive";
            }
        }
    }

    public function listCustomerGroup(Request $request)
    {
        $pageTitle = "Customer Group";
        $customerGroups = Mst_CustomerGroup::orderBy('customer_group_id', 'DESC')->get();
        return view('admin.elements.customer_group.list', compact('customerGroups', 'pageTitle'));
    }

    public function createCustomerGroup(Request $request)
    {
        $pageTitle = "Create Customer Group";
        return view('admin.elements.customer_group.create', compact('pageTitle'));
    }

    public function editCustomerGroup(Request $request, $customer_group_id)
    {
        $pageTitle = "Edit Customer Group";
        $customer_group = Mst_CustomerGroup::find($customer_group_id);
        return view('admin.elements.customer_group.edit', compact('customer_group', 'pageTitle'));
    }

    public function editStatusCustomerGroup(Request $request)
    {
        $customer_group_id = $request->customer_group_id;
        if ($c = Mst_CustomerGroup::findOrFail($customer_group_id)) {
            if ($c->is_active == 0) {
                Mst_CustomerGroup::where('customer_group_id', $customer_group_id)->update(['is_active' => 1]);
                echo "active";
            } else {
                Mst_CustomerGroup::where('customer_group_id', $customer_group_id)->update(['is_active' => 0]);
                echo "inactive";
            }
        }
    }

    public function removeCustomerGroup(Request $request, $customer_group_id)
    {
        Mst_CustomerGroup::where('customer_group_id', '=', $customer_group_id)->delete();
        return redirect('/admin/customer-group/list')->with('status', 'Customer group deleted successfully.');
    }



    public function storeCustomerGroup(Request $request, Mst_CustomerGroup $customer_group)
    {
        $data = $request->except('_token');

        $validator = Validator::make(
            $request->all(),
            [
                'customer_group_name'       => 'required',
            ],
            [
                'customer_group_name.required'         => 'Customer group name required',
            ]
        );

        if (!$validator->fails()) {

            $data = $request->except('_token');

            $customer_group->customer_group_name = $request->customer_group_name;
            $customer_group->customer_group_description = $request->customer_group_description;
            $customer_group->is_active = $request->is_active;
            $customer_group->save();

            return redirect('/admin/customer-group/list')->with('status', 'Customer group added successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    public function updateCustomerGroup(Request $request, $customer_group_id)
    {
        $data = $request->except('_token');

        $validator = Validator::make(
            $request->all(),
            [
                'customer_group_name'       => 'required',
            ],
            [
                'customer_group_name.required'         => 'Customer group name required',
            ]
        );

        if (!$validator->fails()) {

            $data = $request->except('_token');
            $customer_group = Mst_CustomerGroup::find($customer_group_id);

            $customer_group->customer_group_name = $request->customer_group_name;
            $customer_group->customer_group_description = $request->customer_group_description;
            $customer_group->is_active = $request->is_active;
            $customer_group->update();

            return redirect('/admin/customer-group/list')->with('status', 'Customer group updated successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }
    public function listCustomerReward(Request $request)
    {

        $pageTitle = "Customer Reward";
        $customer_rewards = Trn_customer_reward::orderBy('reward_id', 'DESC')->get();
        if ($_GET) {
                
            $datefrom = $request->date_from;
            $dateto = $request->date_to;


            $a1 = Carbon::parse($request->date_from)->startOfDay();
            $a2  = Carbon::parse($request->date_to)->endOfDay();
            $customer_first_name = $request->customer_name;
            $query = Trn_customer_reward::with('customer');

            if (isset($request->date_from) && isset($request->date_to)) {
                $query = $query->whereBetween('created_at', [$a1, $a2]);
            }

            if (isset($request->customer_name)) {
                $query->whereHas('customer', fn($query) => $query->where('customer_first_name', $request->customer_name));
            }

            $customer_rewards = $query->orderBy('reward_id', 'DESC')->get();


            return view('admin.elements.customer_reward.list', compact('dateto', 'datefrom', 'customer_rewards', 'pageTitle'));
        }



        return view('admin.elements.customer_reward.list', compact('customer_rewards', 'pageTitle'));
    }
            //configure points

            public function listConfigurePoints(Request $request)
            {

                $pageTitle = "Configure Points";
                $configure_points = Trn_configure_points::first();
                if (isset($configure_points)) {
                    $configure_points_id = $configure_points->configure_points_id;
                } else {
                    $configure_points_id = 1;
                }

                return view('admin.elements.configure_points.create', compact('configure_points_id', 'configure_points', 'pageTitle'));
            }

            public function createConfigurePoints(Request $request)
            {
                $pageTitle = "Add Configure Points";

                return view('admin.elements.configure_points.create', compact('pageTitle'));
            }


            public function storeConfigurePoints(Request $request, Trn_configure_points $points, $cf_id)
            {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'registraion_points'          => 'required',
                        'first_order_points'          => 'required',
                        'referal_points'          => 'required',
                        'rupee_points'          => 'required',
                        'rupee'          => 'required',
                        'order_points'          => 'required',
                        'order_amount'          => 'required',
                        //'points'          => 'required',
                    ],
                    [
                        'order_points.required'          => 'Rupee required',
                        'order_points.required'          => 'Order points required',
                        //  'points.required'          => 'Points required',
                        'first_order_points.required'          => 'First order points required',
                        'referal_points.required'          => 'Referal required',
                        'registraion_points.required'          => 'Registration required',
                        'rupee_points.required'          => 'Ruppes to points required',
                        'order_amount.required'          => 'Order amount required',
                    ]
                );

                if (!$validator->fails()) {


                    $points = Trn_configure_points::find($cf_id);
                    if (isset($points)) {
                        // $points->points = $request->points;
                        $points->first_order_points = $request->first_order_points;
                        $points->referal_points = $request->referal_points;
                        $points->registraion_points = $request->registraion_points;
                        $points->rupee = $request->rupee;
                        $points->rupee_points = $request->rupee_points;
                        $points->order_amount = $request->order_amount;
                        $points->order_points = $request->order_points;
                        $points->redeem_percentage = $request->redeem_percentage;
                        $points->max_redeem_amount = $request->max_redeem_amount;
                        $points->joiner_points = $request->joiner_points;

                        $points->update();
                    } else {
                        $points = new Trn_configure_points;
                        $points->first_order_points = $request->first_order_points;
                        $points->referal_points = $request->referal_points;
                        $points->registraion_points = $request->registraion_points;
                        $points->rupee = $request->rupee;
                        $points->rupee_points = $request->rupee_points;
                        $points->order_amount = $request->order_amount;
                        $points->order_points = $request->order_points;
                        $points->redeem_percentage = $request->redeem_percentage;
                        $points->max_redeem_amount = $request->max_redeem_amount;
                        $points->joiner_points = $request->joiner_points;

                        $points->save();
                    }
                    return redirect('admin/configure_points/list')->with('status', 'Configure points updated successfully.');
                } else {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
            }

            public function removeConfigurePoints(Request $request, $cp_id, Trn_configure_points $points)
            {
                $points = Trn_configure_points::find($cp_id);

                $points->delete();

                return redirect()->back()->with('status', 'Configure points added successfully.');
            }

            public function statusConfigurePoints(Request $request, $cp_id, Trn_configure_points $points)
            {
                $points = Trn_configure_points::find($cp_id);

                $points->isActive = $request->isActive;

                $points->update();

                return redirect()->back()->with('status', 'Status updated successfully.');
            }
            public function editConfigurePoints(Request $request, $cp_id, Trn_configure_points $points)
            {
                $pageTitle = "Edit Configure Points";
                $configure_point = Trn_configure_points::find($cp_id);

                return view('admin.elements.configure_points.edit', compact('pageTitle', 'configure_point'));
            }
            public function updateConfigurePoints(Request $request, $cp_id, Trn_configure_points $points)
            {
                $points = Trn_configure_points::find($cp_id);
                $validator = Validator::make(
                    $request->all(),
                    [
                        'points'          => 'required',
                        'order_amount'          => 'required',
                        'valid_from'          => 'required',
                    ],
                    [
                        'points.required'          => 'Points required',
                        'order_amount.required'          => 'Order amount required',
                        'valid_from.required'          => 'Valid from required',
                    ]
                );

                if (!$validator->fails()) {
                    $points->points = $request->points;
                    $points->order_amount = $request->order_amount;
                    $points->valid_from = $request->valid_from;
                    $points->isActive = $request->isActive;
                    $points->update();
                    return redirect('admin/configure_points/list')->with('status', 'Configure points updated successfully.');
                } else {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
            }
            public function addReward()
            {
                $pageTitle = "Add Reward To Existing Customer";
               $customers = Trn_store_customer::select('customer_id','customer_first_name','customer_last_name','customer_mobile_number')->get();
                return view('admin.elements.customer_reward.add',compact('pageTitle','customers'));
        
            
               // return redirect()->back()->with('status','Reward added successfully.');
            }
            
            
            public function storeReward(Request $request)
            {
              try{ 
                  if(isset($request->customer_id))
                  {
                    $reward = new Trn_customer_reward;
                    $reward->transaction_type_id    = 0;
                    $reward->reward_points_earned   = $request->reward_points;
                    $reward->customer_id    = $request->customer_id;
                    $reward->reward_approved_date       =  Carbon::now()->format('Y-m-d');
                    $reward->reward_point_expire_date       =  Carbon::now()->format('Y-m-d');
                    $reward->reward_point_status    = 1;
                    $reward->discription    = $request->reward_discription;
                    $reward->save(); 
                  }else
                  {
                    return redirect()->back()->withErrors(['Customer not exist!'])->withInput();
                  }
                  
                } catch (\Exception $e) {
                     //return redirect()->back()->withErrors([  $e->getMessage() ])->withInput();
                
                    return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
                }
        
                return redirect('admin/customer_reward/list')->with('status','Customer reward added successfully');
        
            }
          
            public function listRewardToCustomer(Request $request)
            {
                $pageTitle = "List Rewards To Customers";
        
                $rewards = Mst_RewardToCustomer::orderBy('reward_to_customer_id','DESC')->get();
                $dummy_rewards = Trn_RewardToCustomerTemp::orderBy('reward_to_customer_temp_id','DESC')->get();
                 
               
                return view('admin.elements.customer_reward.add_customer_rewards.list',compact('dummy_rewards','rewards','pageTitle'));
        
            }
        
            public function editRewardToCustomer(Request $request,$reward_to_customer_id)
            {
                $pageTitle = "Edit Reward To Customer";
                $reward_to_customer_id  = Crypt::decryptString($reward_to_customer_id);
                $reward = Mst_RewardToCustomer::find($reward_to_customer_id);
                return view('admin.elements.customer_reward.add_customer_rewards.edit',compact('reward','pageTitle'));
        
            }
        
            public function editTempRewardToCustomer(Request $request,$reward_to_customer_temp_id)
            {
                $pageTitle = "Edit Reward To Customer";
                $reward_to_customer_temp_id  = Crypt::decryptString($reward_to_customer_temp_id);
                $dummy_reward = Trn_RewardToCustomerTemp::find($reward_to_customer_temp_id);
        
                return view('admin.elements.customer_reward.add_customer_rewards.edit_temp',compact('dummy_reward','pageTitle'));
        
            }
        
            public function updateRewardToCustomer(Request $request,$reward_to_customer_id)
            {
              try{ 
                  
                   if (Trn_store_customer::where('customer_mobile_number', '=', $request->customer_mobile_number)->exists()) 
                    {
                        return redirect()->back()->withErrors(['Customer exists'])->withInput();
                     }  
                    else
                    {
        
                        $reward['user_id'] = auth()->user()->id;
                        $reward['customer_mobile_number'] = $request->customer_mobile_number;
                        $reward['reward_discription'] = $request->reward_discription;
                        $reward['reward_points'] = $request->reward_points;
                        $reward['added_date'] = Carbon::now()->format('Y-m-d');
                    }
        
                Mst_RewardToCustomer::where('reward_to_customer_id',$reward_to_customer_id)->update($reward);
        
        
                return redirect('admin/list/reward-to-customer')->with('status','Customer reward updated successfully');
        
        
                } catch (\Exception $e) {
                     //return redirect()->back()->withErrors([  $e->getMessage() ])->withInput();
                
                    return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
                }
        
        
            }
        
            public function updateTempRewardToCustomer(Request $request,$reward_to_customer_temp_id)
            {
              try{ 
        
                $reward['customer_mobile_number'] = $request->customer_mobile_number;
                $reward['reward_discription'] = $request->reward_discription;
                $reward['reward_points'] = $request->reward_points;
                $reward['added_date'] = Carbon::now()->format('Y-m-d');
        
                Trn_RewardToCustomerTemp::where('reward_to_customer_temp_id',$reward_to_customer_temp_id)->update($reward);
        
                return redirect('admin/list/reward-to-customer')->with('status','Customer reward updated successfully');
        
        
                } catch (\Exception $e) {
                     return redirect()->back()->withErrors([  $e->getMessage() ])->withInput();
                
                  //  return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
                }
        
        
            }
        
            public function removeRewardToCustomer(Request $request,$reward_to_customer_id)
            {
                Mst_RewardToCustomer::where('reward_to_customer_id',$reward_to_customer_id)->delete();
        
                return redirect()->back()->with('status','Deleted successfully.');
            
            }
        
            public function removeTempRewardToCustomer(Request $request,$reward_to_customer_temp_id)
            {
                Trn_RewardToCustomerTemp::where('reward_to_customer_temp_id',$reward_to_customer_temp_id)->delete();
        
                return redirect()->back()->with('status','Deleted successfully.');
            
            }
        
            
         
           
   
}
