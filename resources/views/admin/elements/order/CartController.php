<?php

namespace App\Http\Controllers\Customer\Api;

use App\Http\Controllers\Controller;
use App\Models\admin\Mst_Coupon;
use App\Models\admin\Trn_Cart;
use App\Models\admin\Trn_CustomerCoupon;
use App\Models\admin\Trn_WishList;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function viewCart(Request $request)
    {
       $data = array();
       try
       {
       //$summary=array();
       $sub_total_amount=0;
       $branch_id=$request->branch_id;
       $total_amount=0;
        $data['cart_items']=Trn_Cart::with('productVariantData')
        ->whereHas('productVariantData', function (Builder $query) use($branch_id) {
          return $query->where('stock_count', '>',0)->where('branch_id',$branch_id);
        })->where('customer_id',Auth::guard('api-customer')->user()->customer_id)->get();
        foreach($data['cart_items'] as $item)
        {
          if($item->productVariantData->branch_id==$branch_id)
          {
            $item->productVariantData->product_varient_offer_price=getFinalOfferPrice($item->productVariantData->varient_id);
            
            $sub_total_amount+=getFinalOfferPrice($item->productVariantData->varient_id)*$item->quantity;
          }
        }
        $total_amount=$sub_total_amount;  
        ////Coupon calculation
       /* if($total_amount<=0)
        {
            $data['status']=0;
            $data['message']="No items to checkout";
            return response($data);
        }
        else
        {
            $coupon=Mst_Coupon::where('coupon_code',$request->coupon_code);
            // dd($coupon->first());
             
             if(is_null($coupon->first()))
             {
              return response()->json(['status'=>0,'message'=>'Invalid coupon code','total_rate'=>$total_amount]);
             }
             else
             {
               if(date('Y-m-d')>$coupon->first()->valid_to)
                   return response()->json(['status'=>0,'message'=>'Coupon Expired','total_rate'=>$total_amount]);
                 //return response()->json(['status'=>5,'message'=>'Coupon Expired','total_rate'=>$total_amount]);
               }
               if($coupon->first()->min_purchase_amt>=$total_amount)
               {
                 return response()->json(['status'=>0,'message'=>'Not Applicable to current amount','total_rate'=>$total_amount]);
               }
           
             $customer_coupon=Trn_CustomerCoupon::where('customer_id',Auth::guard('api-customer')->user()->customer_id)->where('coupon_id',$coupon->first()->coupon_id);
             if($customer_coupon->exists())
             {
               if($customer_coupon->first()->is_applied==1)
               {
                 if($coupon->first()->coupon_type==1)
                 {
                   return response()->json(['status'=>0,'message'=>'You can not apply this coupon code.This coupon already applied before','total_rate'=>$total_amount]);
       
                 }
                 else
                 {
                   if($coupon->first()->discount_type==1)
                   {
                       $total_amount=$total_amount-$coupon->first()->discount;
                   }
                   if($coupon->first()->discount_type==2)
                   {
                     $total_amount=$this->total_amount-($total_amount*$coupon->first()->discount)/100;
                   }
                   return response()->json(['status'=>1,'message'=>'Coupon code applied successfully','total_rate'=>$total_amount]);
       
                 }
                 
       
               }
               else
               {
                 if($coupon->first()->discount_type==1)
                 {
                    $total_amount=$total_amount-$coupon->first()->discount;
                 }
                 if($coupon->first()->discount_type==2)
                 {
                    $total_amount=$total_amount-($total_amount*$coupon->first()->discount)/100;
                 }
                 return response()->json(['status'=>1,'message'=>'Coupon code applied successfully','total_rate'=>$total_amount]);
                 
               }
               
               //return response()->json(['status'=>1,'message'=>'You can not apply this coupon code.This coupon already applied before','total_rate'=>$total_amount]);
             }
             else
             {
              
             
                   $customer_coupon=new Trn_CustomerCoupon();
                   $customer_coupon->coupon_id=@$coupon->first()->coupon_id;
                   $customer_coupon->customer_id=Auth::guard('api-customer')->user()->customer_id;
                   $customer_coupon->is_applied=0;
                   $customer_coupon->save();
                   if($coupon->first()->discount_type==1)
                   {
                       $total_amount=$total_amount-$coupon->first()->discount;
                   }
                   if($coupon->first()->discount_type==2)
                   {
                       $total_amount=$total_amount-($total_amount*$coupon->first()->discount)/100;
                   }
                   return response()->json(['status'=>1,'message'=>'Coupon code applied successfully','total_rate'=>$total_amount]);
       
               }
             
               //return response()->json(['status'=>2,'message'=>'Coupon code applied successfully','total_rate'=>number_format($total_amount_applied,2),]);
             }

    

        //return $total_amount;
*/
        ///Coupon calculation end
        
        $data['sub_total_amount']=number_format($sub_total_amount,2);
        $data['delvery_charge']=0;
        $data['coupon_discount']=0;  
        $data['coupon_code']='No Coupon Applied';

        if($request->coupon_code)
        {
            $total_amount=number_format($request->amount_after_deduction,2);
            $data['coupon_code']=$request->coupon_code;
            $data['coupon_discount']=number_format($sub_total_amount-$total_amount,2); 
        }
        $data['total_amount']=$total_amount;
        $data['status'] = 1;
        $data['message'] = "Cart Fetched";
        $data['product_image_path']=getImagePath('product');
        $data['default_image']=getDefaultImage();
        return response($data);
    }
    catch (\Exception $e) {
        $response = ['status' => '0', 'message' => $e->getMessage()];
        return response($response);
    } 
    catch (\Throwable $e) {
        $response = ['status' => '0', 'message' => $e->getMessage()];
        return response($response);
    }
       
    }
    public function addTocart(Request $request)
    {
         /*if(!Auth::check()){
            return redirect()->route('test');
        }*/
        //dd($product_id);
        $data=array();
        $product_varient_id=$request->varient_id;
        $product_id=$request->product_id;
        try{
        $stock_count=getVarientStock($product_varient_id);
        if($stock_count<=0)
        {
           $data['status']=0;
           $data['message']="Out of Stock.Cannot add to cart!";
           return response($data);

        }
        else
        {
        $exist=Trn_Cart::where('product_varient_id',$product_varient_id)->where('customer_id',Auth::guard('api-customer')->user()->customer_id);
        if($exist->exists())
        {
            $cart=$exist->first();
            //dd('exists',$cart->quantity+1);
            $cart->quantity=$cart->quantity+1;
        }
        else
        {
            $cart=new Trn_Cart();
            $cart->quantity=1;

        }
        $cart->product_id=$product_id;
        $cart->product_varient_id=$product_varient_id;
        $cart->customer_id=Auth::guard('api-customer')->user()->customer_id;
        $cart->store_id=1;
        $cart->save();
        $data['message']='Product added to cart!';
        $data['status']=1;
        return response($data);
       
        //return redirect(request()->header('Referer'));

        }
    }
    catch (\Exception $e) {
        $response = ['status' => '0', 'message' => $e->getMessage()];
        return response($response);
    } 
    catch (\Throwable $e) {
        $response = ['status' => '0', 'message' => $e->getMessage()];
        return response($response);
    }

        }


    public function manageCart(Request $request)
    {
        $data=array();
        $product_varient_id=$request->varient_id;
        $product_id=$request->product_id;
        $type=$request->type;
        $quantity_count=$request->qty_count;
        try
        {
        $stock_count=getVarientStock($product_varient_id);
        if($stock_count<=0)
        {
           $data['status']=0;
           $data['message']="Out of Stock.Cannot add to cart!";
           return response($data);

        }
        else
        {
            if($type=='increment')
            {
        
                $exist=Trn_Cart::where('product_varient_id',$product_varient_id)->where('customer_id',Auth::guard('api-customer')->user()->customer_id);
                if($exist->exists())
                {
                    $cart=$exist->first();
                    //dd('exists',$cart->quantity+1);
                    
                    if($request->qty_count)
                    {
                        $cart->quantity=$cart->quantity+$request->qty_count;

                    }
                    else
                    {
                        $cart->quantity=$cart->quantity+1;

                    }
                    
                }
                else
                {
                    $cart=new Trn_Cart();
                    if($request->qty_count)
                    {
                        $cart->quantity=$cart->quantity+$request->qty_count;

                    }
                    else
                    {
                        $cart->quantity=1;

                    }
                    

                }
                $cart->product_id=$product_id;
                $cart->product_varient_id=$product_varient_id;
                $cart->customer_id=Auth::guard('api-customer')->user()->customer_id;
                $cart->store_id=1;
                $cart->save();
                $data['message']='Product added to cart!';
                $data['status']=1;
                return response($data);
            }
            if($type=='decrement')
            {
                $cart=Trn_Cart::where('customer_id',Auth::guard('api-customer')->user()->customer_id)->where('product_varient_id',$product_varient_id)->first();
                if($cart->quantity!=1)
                {   
                    $cart->quantity= $cart->quantity-1;
                    $cart->update();
                    $data['status']=1;
                    $data['message']="Quantity  decreased by one";
                    return response($data);
                }
                else{
                    $cart->delete();
                    $data['status']=1;
                    $data['message']="item has been removed from cart";

                    return response($data);
                }

            }
            if($type=='remove')
            {
                $cart=Trn_Cart::where('customer_id',Auth::guard('api-customer')->user()->customer_id)->where('product_varient_id',$product_varient_id)->first();
                $cart->delete();
                $data['status']=1;
                $data['message']="item has been removed from cart";

                return response($data);

            }
       
        //return redirect(request()->header('Referer'));

        }
    }
    catch (\Exception $e) {
        $response = ['status' => '0', 'message' => $e->getMessage()];
        return response($response);
    } 
    catch (\Throwable $e) {
        $response = ['status' => '0', 'message' => $e->getMessage()];
        return response($response);
    }
            
    }
    public function removeFromCart(Request $request)
    {
        $data=array();
        $cart_id=$request->cart_id;
        try{
            Trn_cart::findOrFail($cart_id)->delete();
            $data['status']=1;
            $data['message']="Product has been removed from cart";
            return response($data);
        }
        catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } 
        catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
       

    }
    
    public function updateQuantity(Request $request)
    {
      //dd('qwerty');
      $data=array();
      $cart_id=$request->cart_id;
      $updateType=$request->update_type;
      try
      {
      $cart=Trn_Cart::findOrFail($cart_id);
     // $pro_var_detail=Mst_branch_product_varient::findOrFail($cart->product_varient_id);
        if($updateType=='decrement')
        {   
          if($cart->quantity!=1)
          {   
            $cart->quantity= $cart->quantity-1;
          }
          else{
            $data['status']=0;
            $data['message']="Quantity cannot be decreased";
            return response($data);
          }
        }
        if($updateType=='increment')
        {
          /*if($pro_var_detail->stock_count<=$cart->quantity)
          {
            $this->quantity[$cart_id]=$cart->quantity;
            //sleep(3);
            $this->dispatchBrowserEvent('out-of-stock',['message'=>'Stock is unavailable..Try later!']); 
           
            return redirect(request()->header('Referer'));
           // $this->emit('stock_exceeded')
          }
          else
          {*/
            $cart->quantity= $cart->quantity+1;
          //} 
        }
        //$cart=Trn_Cart::findOrFail($cart_id);
        $cart->update();
        $data['status']=1;
        $data['message']="Quantity updated";
        return response($data);
    }
    catch (\Exception $e) {
        $response = ['status' => '0', 'message' => $e->getMessage()];
        return response($response);
    } 
    catch (\Throwable $e) {
        $response = ['status' => '0', 'message' => $e->getMessage()];
        return response($response);
    }
    }
    public function didMissProducts(Request $request)
    {
        $branch_id=$request->branch_id;
        $data=array();
        try
        {
            $did_miss_products=didYouMissAppProducts($branch_id);
            foreach($did_miss_products as $product)
            {
                $product->product_varient_offer_price=getFinalOfferPrice($product->varient_id);
            }
            $data['did_miss_products']=$did_miss_products;
            foreach($data['did_miss_products'] as $pdct)
            {
               
                    
                        //$pdct['in_cart']=0;
                        //$pdct['cart_item_quantity']=0;
            
                   
                    if(Trn_WishList::where('product_variant_id',$pdct['varient_id'])->where('customer_id',Auth::guard('api-customer')->user()->customer_id)->first()!=NULL)
                    {
                        $pdct['in_wishlist']=1;
            
                    }
                    else
                    {
                        $pdct['in_wishlist']=0;
            
                    }
        
               

            }

            $data['product_image_path']=getImagePath('product');
            $data['default_image']=getDefaultImage();
            $data['status']=1;
            $data['message']="Products Fetched";
            return response($data);
        }
        catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } 
        catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }
    public function getCartWishCount(Request $request)
    {
        $data=array();
        $branch_id=$request->branch_id;
        $customer_id=Auth::guard('api-customer')->user()->customer_id;
        $cart_count=Trn_Cart::where('customer_id',$customer_id)
        ->leftjoin('mst_branch_product_varients','mst_branch_product_varients.varient_id','=','trn__carts.product_varient_id')
        ->where('mst_branch_product_varients.stock_count','>',0)
        ->where('mst_branch_product_varients.branch_id',$branch_id)
        ->count();
        $wish_count=Trn_WishList::where('customer_id',$customer_id)
        ->leftjoin('mst_branch_product_varients','mst_branch_product_varients.varient_id','=','trn__wish_lists.product_variant_id')
        ->where('mst_branch_product_varients.stock_count','>',0)
        ->where('mst_branch_product_varients.branch_id',$branch_id)
        ->count();
        $data['status']=1;
        $data['cart_count']=$cart_count;
        $data['wishlist_count']=$wish_count;
        return response($data);
    }
    public function removeAllFromCart()
    {
        $customer_id=Auth::guard('api-customer')->user()->customer_id;
        Trn_cart::where('customer_id',$customer_id)->delete();
        $data['status']=1;
        $data['message']='All items Removed';
        return response($data);

    }

}
