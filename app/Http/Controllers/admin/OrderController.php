<?php

namespace App\Http\Controllers\admin;

use Notification;
use App\Notifications\EmailNotification;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;
use Auth;
use App\Models\admin\Mst_categories;
use App\Models\admin\Mst_business_types;
use App\Models\admin\Mst_store;
use App\Models\admin\Country;
use App\Models\admin\State;
use App\Models\admin\District;
use App\Models\admin\Town;
use App\Models\admin\Trn_OrderItem;
use App\Models\admin\Mst_store_documents;
use App\Models\admin\Mst_store_images;
use App\Models\admin\Mst_store_agencies;
use App\Models\admin\Trn_CustomerDeviceToken;
use App\Models\admin\Trn_StoreDeviceToken;
use App\Models\admin\Mst_store_link_agency;
use App\Models\admin\Mst_store_companies;
use App\Models\admin\Trn_store_customer;
use App\Models\admin\Mst_store_product;
use App\Models\admin\Mst_attribute_group;
use App\Models\admin\Mst_attribute_value;
use App\Models\admin\Mst_product_image;
use App\Models\admin\Trn_store_customer_otp_verify;
use App\Models\admin\Mst_delivery_boy;
use App\Models\admin\Sys_delivery_boy_availability;
use App\Models\admin\Mst_store_link_delivery_boy;
use App\Models\admin\Trn_delivery_boy_order;
use App\Models\admin\Sys_vehicle_type;
use App\Models\admin\Sys_store_order_status;
use App\Models\admin\Trn_store_order_item;
use App\Models\admin\Trn_customer_reward;
use App\Models\admin\Trn_customer_reward_transaction_type;
use App\Models\admin\Trn_store_order;
use App\Models\admin\Trn_store_payment;
use App\Models\admin\Mst_store_link_subadmin;
use App\Models\admin\Mst_store_product_varient;
use App\Models\admin\Sys_payment_type;
use App\Models\admin\Trn_store_payment_settlment;
use App\Models\admin\Trn_delivery_boy_payment_settlment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Image;
use Hash;
use DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Crypt;


use App\Models\admin\Mst_Subadmin_Detail;
use App\Models\admin\Trn_delivery_boy_payment;
use App\Models\admin\Trn_store_payments_tracker;
use App\Models\admin\Trn_sub_admin_payment_settlment;
use App\Models\admin\Trn_subadmin_payments_tracker;
use App\Models\admin\Trn_configure_points;
use App\Models\admin\Trn_registration_point;
use App\Models\admin\Trn_first_order_point;
use App\Models\admin\Trn_referal_point;
use App\Models\admin\Trn_points_to_rupee;
use App\Models\admin\Trn_points_redeemed;
use App\Models\admin\Mst_SubCategory;
use App\Models\admin\Trn_CategoryBusinessType;
use App\Models\admin\Trn_StoreAdmin;
use App\Models\admin\Trn_TermsAndCondition;
use App\Models\admin\Trn_customerAddress;

use App\Models\admin\Trn_OrderPaymentTransaction;
use App\Models\admin\Trn_OrderSplitPayments;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function listOrder(Request $request)
  {

    $pageTitle = "List Orders";
    //$store_id =   Auth::guard('store')->user()->store_id;
    $customer = Trn_store_customer::all();

    $orders = Trn_store_order::with('customer')->orderBy('order_id', 'DESC')->get();
    $status = Sys_store_order_status::all();
    $store = Mst_store::all();
    $product = Mst_store_product::get();

    

    if ($_GET) {

      $delivery_boy_id = $request->delivery_boy_id;
      $status_id = $request->status_id;
      $customer_id = $request->customer_id;


      $a1 = Carbon::parse($request->date_from)->startOfDay();
      $a2  = Carbon::parse($request->date_to)->endOfDay();
      DB::enableQueryLog();

      $query = Trn_store_order::query();

      if (isset($request->status_id)) {
        $query->where('status_id', $status_id);
        //  $query->orWhere('payment_status', $status_id);
      }
      if (isset($request->delivery_boy_id)) {
        $query->where('delivery_boy_id', $delivery_boy_id);
      }
      if (isset($request->customer_id)) {
        $query->where('customer_id', $customer_id);
      }
      if (isset($request->date_from) && isset($request->date_to)) {
        $query->whereDate('created_at', '>=', $a1)->whereDate('created_at', '<=', $a2);
      }
      $orders = $query->orderBy('order_id', 'DESC')->get();
      // dd(DB::getQueryLog());
      return view('admin.elements.order.list', compact('customer', 'orders', 'pageTitle', 'status', 'store', 'status', 'product'));
    }
    return view('admin.elements.order.list', compact('customer', 'orders', 'pageTitle', 'status', 'store', 'status', 'product'));
  }


  public function listTodaysOrder(Request $request)
  {

    $pageTitle = "List Orders";
   //$store_id =   Auth::guard('store')->user()->store_id;
    $store = 1 ;
    $customer = Trn_store_customer::all();

    $date_from = Carbon::now()->toDateString();
    $date_to = Carbon::now()->toDateString();
    $a1 = Carbon::parse($date_from)->startOfDay();
    $a2  = Carbon::parse($date_to)->endOfDay();

    $orderC = Trn_store_order::where('store_id', '=', $store_id)
      ->whereDate('created_at', '>=', $a1)->whereDate('created_at', '<=', $a2)
      ->orderBy('order_id', 'DESC')->count();

    $orders = Trn_store_order::where('store_id', '=', $store_id)
      ->whereDate('created_at', '>=', $a1)->whereDate('created_at', '<=', $a2)
      ->orderBy('order_id', 'DESC')->paginate($orderC);

    $status = Sys_store_order_status::all();
    $store = Mst_store::all();
    $product = Mst_store_product::where('store_id', '=', $store_id)->get();

    $delivery_boys = Mst_delivery_boy::join('mst_store_link_delivery_boys', 'mst_store_link_delivery_boys.delivery_boy_id', '=', 'mst_delivery_boys.delivery_boy_id')
      ->select("mst_delivery_boys.*")->where('mst_store_link_delivery_boys.store_id', $store_id)->get();

    $assign_delivery_boys = Mst_delivery_boy::join('mst_store_link_delivery_boys', 'mst_store_link_delivery_boys.delivery_boy_id', '=', 'mst_delivery_boys.delivery_boy_id')
      ->select("mst_delivery_boys.*")
      ->where('mst_delivery_boys.availability_status', 1)
      ->where('mst_delivery_boys.delivery_boy_status', 1)
      ->where('mst_store_link_delivery_boys.store_id', $store_id)->get();

    return view('store.elements.order.list', compact('assign_delivery_boys', 'date_to', 'date_from', 'customer', 'orders', 'pageTitle', 'status', 'store', 'status', 'product', 'delivery_boys'));
  }




  public function viewOrder(Request $request, $id)
  {
    try {
      $pageTitle = "View Order";
      $decrId  = Crypt::decryptString($id);
      $order = Trn_store_order::Find($decrId);
      $order_items = Trn_OrderItem::where('order_id', $decrId)->get();
      

      $product = $order->product_id;

        //$subadmin_id = Auth()->guard('store')->user()->subadmin_id;
        // $store_id = Auth()->guard('store')->user()->store_id;
       
        $subadmin_id =1;
        $store_id =1;

      $payments = Trn_OrderPaymentTransaction::join('trn__order_split_payments', 'trn__order_split_payments.opt_id', '=', 'trn__order_payment_transactions.opt_id')
        ->join('trn_store_orders', 'trn_store_orders.order_id', '=', 'trn__order_payment_transactions.order_id')
        ->where('trn__order_split_payments.paymentRole', '=', 1)
        ->where('trn_store_orders.store_id', '=', $store_id)
        ->where('trn_store_orders.order_id', '=', $decrId)
        ->where('trn__order_payment_transactions.order_id', '=', $decrId)
        ->first();


      $delivery_boys = Mst_delivery_boy::join('mst_store_link_delivery_boys', 'mst_store_link_delivery_boys.delivery_boy_id', '=', 'mst_delivery_boys.delivery_boy_id')
        ->select("mst_delivery_boys.*")->where('mst_store_link_delivery_boys.store_id', $store_id)->get();

      $customer = Trn_store_customer::all();
      $status = Sys_store_order_status::all();

      return view('admin.elements.order.view', compact('delivery_boys', 'payments', 'order_items', 'order', 'pageTitle', 'status', 'customer'));
    } catch (\Exception $e) {
      
      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }


  public function viewDisputeOrder(Request $request, $id)
  {
    try {
      $pageTitle = "View Order";
      $decrId  = Crypt::decryptString($id);
      $order = Trn_store_order::Find($decrId);
      $order_items = Trn_store_order_item::where('order_id', $decrId)->get();

      $product = $order->product_id;

    //   $subadmin_id = Auth()->guard('store')->user()->subadmin_id;
    //   $store_id = Auth()->guard('store')->user()->store_id;


    $subadmin_id =1;
    $store_id = 1;

      $delivery_boys = Mst_delivery_boy::join('mst_store_link_delivery_boys', 'mst_store_link_delivery_boys.delivery_boy_id', '=', 'mst_delivery_boys.delivery_boy_id')
        ->select("mst_delivery_boys.*")->where('mst_store_link_delivery_boys.store_id', $store_id)->get();

      $customer = Trn_store_customer::all();
      $status = Sys_store_order_status::all();

      return view('store.elements.disputes.order_view', compact('delivery_boys', 'order_items', 'order', 'pageTitle', 'status', 'customer'));
    } catch (\Exception $e) {

      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }





  public function updateOrder(Request $request, $id)
  {
    try {

      //dd($request->all());

      foreach ($request->order_item_id as $key => $value) {
        Trn_store_order_item::where('order_item_id', $value)->update(['tick_status' => $request->product[$key]]);
      }

      $data['delivery_boy_id']  = $request->delivery_boy_id;
      $data['status_id']  = $request->status_id;

      if ($request->status_id == 7) {
        $data['delivery_status_id'] = 1;
      } else if ($request->status_id == 8) {
        $data['delivery_status_id'] = 2;
      } else if ($request->status_id == 9) {
        $data['delivery_status_id'] = 3;
      } else {
        $data['delivery_status_id'] = null;
      }



      $data['order_note']  = $request->order_note;

      $query = Trn_store_order::where('order_id', $id)->update($data);

      return redirect()->back()->with('status', 'Order updated successfully.');
    } catch (\Exception $e) {
      // echo $e->getMessage();die;
      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }


  public function viewInvoice(Request $request, $id)
  {
    
    $pageTitle = "View Invoice";
    $decrId  = Crypt::decryptString($id);
    $order_id = $decrId;
    $order = Trn_store_order::Find($decrId);
    $customer = Trn_store_customer::all();
    $status = Sys_store_order_status::all();
    $order_items = Trn_store_order_item::where('order_id', $decrId)->get();
    //$store_id = Auth::guard('store')->user()->store_id;
    $store_id = 1;
    $store_data = Mst_store::where('store_id', $store_id)->first();
    // dd($order_items);


    return view('admin.elements.order.invoice', compact('store_data', 'order_id', 'order_items', 'order', 'pageTitle', 'status', 'customer'));
  }
  public function OrderStatus(Request $request, Trn_store_order $order, $order_id)
  {

    //try {


    $order_id = $request->order_id;
    $order = Trn_store_order::Find($order_id);
    $order_number = $order->order_number;
    $store_id = $order->store_id;
    $customer_id = $order->customer_id;

    $validator = Validator::make(
      $request->all(),
      [

        'status_id'   => 'required',

      ],
      [
        'status_id.required' => 'Status required',


      ]
    );

    if (!$validator->fails()) {
      $data = $request->except('_token');


      $order->status_id = $request->status_id;

      if ($request->status_id == 8) {
        if ($order->order_type == 'APP') {
          if (($order->delivery_boy_id == 0) || !isset($order->delivery_boy_id)) {
            return redirect()->back()->withErrors(['delivery boy not assigned']);
          }
        }
      }
      if (($request->status_id == 9) && ($order->status_id != 9)) {

        $order->delivery_date = Carbon::now()->format('Y-m-d');
        $order->delivery_time = Carbon::now()->format('H:i');
        if ($order->order_type == 'APP') {
          if (($order->delivery_boy_id == 0) || !isset($order->delivery_boy_id)) {
            return redirect()->back()->withErrors(['delivery boy not assigned']);
          }
        }

        $configPoint = Trn_configure_points::find(1);
        $orderAmount  = $configPoint->order_amount;
        $orderPoint  = $configPoint->order_points;

        $orderAmounttoPointPercentage =  $orderAmount / $orderPoint;
        $orderPointAmount = ($order->product_total_amount * $orderAmounttoPointPercentage) / 100;


        if (Trn_store_order::where('customer_id', $customer_id)->count() == 1) {
          $configPoint = Trn_configure_points::find(1);

          $cr = new Trn_customer_reward;
          $cr->transaction_type_id = 0;
          $cr->reward_points_earned = $configPoint->first_order_points;
          $cr->customer_id = $customer_id;
          $cr->order_id = $order_id;
          $cr->reward_approved_date = Carbon::now()->format('Y-m-d');
          $cr->reward_point_expire_date = Carbon::now()->format('Y-m-d');
          $cr->reward_point_status = 1;
          $cr->discription = "First order points";
          $cr->save();

          $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $customer_id)->get();

          foreach ($customerDevice as $cd) {
            $title = 'First order points credited';
            //  $body = 'First order points credited successully..';
            $body = $configPoint->first_order_points . ' points credited to your wallet..';
            $clickAction = "OrderListFragment";
            $type = "order";
            $data['response'] =  $this->customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
          }


          // referal - point
          $refCusData = Trn_store_customer::find($order->customer_id);
          if ($refCusData->referred_by) {
            $crRef = new Trn_customer_reward;
            $crRef->transaction_type_id = 0;
            $crRef->reward_points_earned = $configPoint->referal_points;
            $crRef->customer_id = $refCusData->referred_by;
            $crRef->order_id = null;
            $crRef->reward_approved_date = Carbon::now()->format('Y-m-d');
            $crRef->reward_point_expire_date = Carbon::now()->format('Y-m-d');
            $crRef->reward_point_status = 1;
            $crRef->discription = "Referal points";
            $crRef->save();

            $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $refCusData->referred_by)->get();

            foreach ($customerDevice as $cd) {
              $title = 'Referal points credited';
              //$body = 'Referal points credited successully..';
              $body = $configPoint->referal_points . ' points credited to your wallet..';
              $clickAction = "OrderListFragment";
              $type = "order";
              $data['response'] =  $this->customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
            }



            // joiner - point
            $crJoin = new Trn_customer_reward;
            $crJoin->transaction_type_id = 0;
            $crJoin->reward_points_earned = $configPoint->joiner_points;
            $crJoin->customer_id = $order->customer_id;
            $crJoin->order_id = $order->order_id;
            $crJoin->reward_approved_date = Carbon::now()->format('Y-m-d');
            $crJoin->reward_point_expire_date = Carbon::now()->format('Y-m-d');
            $crJoin->reward_point_status = 1;
            $crJoin->discription = "Referal joiner points";
            if ($crJoin->save()) {
              $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $order->customer_id)->get();

              foreach ($customerDevice as $cd) {
                $title = 'Referal joiner points credited';
                //$body = 'Referal joiner points credited successully..';
                $body = $configPoint->joiner_points . ' points credited to your wallet..';
                $clickAction = "OrderListFragment";
                $type = "order";
                $data['response'] =  $this->customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
              }
            }
          }
        }

        if (Trn_customer_reward::where('order_id', $order_id)->count() < 1) {

          if ((Trn_customer_reward::where('order_id', $order_id)->count() < 1) || (Trn_store_order::where('customer_id', $customer_id)->count() == 1)) {
            $cr = new Trn_customer_reward;
            $cr->transaction_type_id = 0;
            $cr->reward_points_earned = $orderPointAmount;
            $cr->customer_id = $customer_id;
            $cr->order_id = $order_id;
            $cr->reward_approved_date = Carbon::now()->format('Y-m-d');
            $cr->reward_point_expire_date = Carbon::now()->format('Y-m-d');
            $cr->reward_point_status = 1;
            $cr->discription = null;
            $cr->save();

            $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $customer_id)->get();

            foreach ($customerDevice as $cd) {
              $title = 'Order points credited';
              $body = $orderPointAmount . ' points credited to your wallet..';
              $clickAction = "OrderListFragment";
              $type = "order";
              $data['response'] =  Helper::customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
            }
          }
        }
      }

      if ($request->status_id == 8) {
        $order->delivery_status_id = 2;
      } else if ($request->status_id == 7) {
        $order->delivery_status_id = 1;
      } else if ($request->status_id == 9) {
        $order->delivery_status_id = 3;
      } else {
        $order->delivery_status_id = null;
      }





     $cus = Trn_store_customer::find($customer_id);


      $status_id = $request->status_id;
      if ($status_id == 1) {
        $order_status = "Pending";

        
        $status = 'Your order with order id ' . $order_number . ' is pending..';

        
        Notification::send($cus, new EmailNotification($cus,$status));

          
        
      } elseif ($status_id == 2) {
        $order_status = "PaymentSuccess";
      } elseif ($status_id == 3) {
        $order_status = "Payment Cancelled";
      } elseif ($status_id == 4) {
        $order_status = "Confirmed";
        
        $status = 'Your order with order id ' . $order_number . ' is Confirmed..';

        
        Notification::send($cus, new EmailNotification($cus,$status));

        
      } elseif ($status_id == 5) {
        $order_status = "Cancelled";

        $status = 'Your order with order id ' . $order_number . ' is cancelled..';

        
        Notification::send($cus, new EmailNotification($cus,$status));
          
       
      } elseif ($status_id == 4) {

        $order_status = "Confirmed";

        $status = 'Your order with order id ' . $order_number . ' is Confirmed..';

       
        Notification::send($cus, new EmailNotification($cus,$status));
          
       
      } elseif ($status_id == 6) {
         $order_status = "Completed";

          $status = 'Your order with order id ' . $order_number . ' is completed..';
          
          
        Notification::send($cus, new EmailNotification($cus,$status));

       
      } elseif ($status_id == 7) {
        $order_status = "Ready for Delivery";

        
          $status = 'Your order with order id ' . $order_number . ' is packed and ready for delivery..';

          
          Notification::send($cus, new EmailNotification($cus,$status));
          
        
      } elseif ($status_id == 8) {
        $order_status = "Out for Delivery";

          $status = 'Your order with order id ' . $order_number . ' is out for delivery..';
          Notification::send($cus, new EmailNotification($cus,$status));

          
      } else {
        if ($order->status_id == 9) {

            $status = 'Your order with order id ' . $order_number . ' is deliverd..';
            Notification::send($cus, new EmailNotification($cus,$status));

           }
        
      }

      $cus_id = $order->customer_id;

      $customer = Trn_store_customer::Find($cus_id);
      $customer_email = $customer->customer_email;
      //dd($customer_email);

      if ($request->status_id == 5) {
        // if (isset($order->referenceId) && ($order->isRefunded < 2)) {


        //   $curl = curl_init();

        //   curl_setopt_array($curl, array(
        //     CURLOPT_URL => 'https://api.cashfree.com/api/v1/order/refund',
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => '',
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => true,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => 'POST',
        //     CURLOPT_POSTFIELDS => array(
        //       'appId' => '165253d13ce80549d879dba25b352561',
        //       'secretKey' => 'bab0967cdc3e5559bded656346423baf0b1d38c4',
        //       'ContentType' => 'application/json',
        //       'referenceId' => $order->referenceId, 'refundAmount' => $order->product_total_amount, 'refundNote' => 'full refund'
        //     ),
        //     CURLOPT_HTTPHEADER => array(
        //       'Accept' => 'application/json',
        //       'x-api-version' => '2021-05-21',
        //       'x-client-id' => '165253d13ce80549d879dba25b352561',
        //       'x-client-secret' => 'bab0967cdc3e5559bded656346423baf0b1d38c4'
        //     ),
        //   ));

        //   $response = curl_exec($curl);
        //   // dd($response);
        //   curl_close($curl);
        //   $dataString = json_decode($response);
        //   if ($dataString->status == "OK") {
        //     $data['message'] = $dataString->message;
        //     $data['refundId'] = $dataString->refundId;
        //   } else {
        //     $data['message'] = $dataString->message;
        //     //  $data['message'] = "Refund failed! Please contact store";
        //   }

        //   if ($dataString->status == "OK") {
        //     $order->refundId = $dataString->refundId;
        //     $order->refundStatus = "Inprogress";
        //     $order->isRefunded = 1;
        //   }
        // }

        $orderData = Trn_store_order_item::where('order_id', $order_id)->get();


        // dd($orderData);
        foreach ($orderData as $o) {

          $productVarOlddata = Mst_store_product_varient::find($o->product_varient_id);

          $sd = new Mst_StockDetail;
          $sd->store_id = $store_id;
          $sd->product_id = $o->product_id;
          $sd->stock = $o->quantity;
          $sd->product_varient_id = $o->product_varient_id;
          $sd->prev_stock = $productVarOlddata->stock_count;
          $sd->save();


          DB::table('mst_store_product_varients')->where('product_varient_id', $o->product_varient_id)->increment('stock_count', $o->quantity);
        }
      }


      $order->update();


      $data = array('order_number' => $order_number, 'order_status' => $request->status_id, 'to_mail' => $customer_email);


      // Mail::send('store/mail-template/order-status-mail-template', $data, function($message) use ($data){
      //         $message->to($data['to_mail'], 'Yellowstore - Order Status')->subject
      //             ('ORDER-STATUS-UPDATION');
      //         $message->from('anumadathinakath@gmail.com','Customer-Order-Status');
      //     });

      return redirect()->back()->with('status', 'Status updated successfully.');
    } else {
      return redirect()->back()->withErrors($validator)->withInput();
    }
    // } catch (\Exception $e) {

    //   return redirect()->back()->withErrors(['Something went wrong!'])->withInput();

    // }
  }
  public function AssignOrder(Request $request, $id)
  {


    $pageTitle = "Assign Order to Delivery Boy";
   // $store_id = Auth()->guard('store')->user()->store_id;
    $store_id = 1;
    $decrId  = Crypt::decryptString($id);
    $order = Trn_store_order::Find($decrId);
    $delivery_boys = Mst_delivery_boy::where('store_id', '=', $store_id)->get();

    return view('admin.elements.order.assign_order', compact('order', 'pageTitle', 'delivery_boys'));
  }

  public function storeAssignedOrder(Request $request, Mst_order_link_delivery_boy $link_delivery_boy)
  {


    $order_id = $request->order_id;
    $validator = Validator::make(
      $request->all(),
      [
        'delivery_boy_id'             => 'required',

      ],
      [
        'delivery_boy_id.required'       => 'Delivery boy required',



      ]
    );

    if (!$validator->fails()) {

      $data = $request->except('_token');

      $link_delivery_boy->order_id = $request->order_id;
      $link_delivery_boy->delivery_boy_id = $request->delivery_boy_id;
      $link_delivery_boy->save();

      $order = Trn_store_order::Find($request->order_id);
      $order->delivery_boy_id =  $request->delivery_boy_id;
      $order->delivery_accept =  null;
      $order->save();

      $dBoyDevices = Trn_DeliveryBoyDeviceToken::where('delivery_boy_id', $request->delivery_boy_id)->get();

      foreach ($dBoyDevices as $cd) {
        $title = 'Order Assigned';
        $body = 'New order(' . $order->order_number . ') arrived';
        $clickAction = "AssignedOrderFragment";
                        $type = "order-assigned";
        $data =  Helper::deliveryBoyNotification($cd->dboy_device_token, $title, $body,$clickAction,$type);
      }


      return redirect('store/order/list')->with('status', 'Order assigned successfully.');
    } else {

      return redirect()->back()->withErrors($validator)->withInput();
    }
  }

  public function generatePdf(Request $request, $id)
  {


    $decrId  = Crypt::decryptString($id);
    $order = Trn_store_order::Find($decrId);
    $order_no = $order->order_number;
    $pageTitle = "Invoice";
    $order_id =   $decrId;
    $order_items = Trn_store_order_item::where('order_id', $decrId)->get();

    // $store_id = Auth::guard('store')->user()->store_id;
    $store_id = 1;
    $store_data = Mst_store::where('store_id', $store_id)->first();

    // dd($order_no);

    $pdf = pdf::loadView('admin.elements.order.bill', compact('store_data', 'order_id', 'order_items', 'order', 'pageTitle'));

    //return view('store.elements.order.bill',compact('order_items','pageTitle','order'));


    $content =  $pdf->download()->getOriginalContent();

    Storage::put('uploads\order_invoice\Invoice_' . $order_no . '.pdf', $content);

    return $pdf->download('Invoice_' . $order_no . '.pdf');
  }
  
 

 


}
