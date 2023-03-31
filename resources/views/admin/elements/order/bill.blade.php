


   <table  width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="49%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><table width="100%" border="0" style="margin-bottom: 10px;" cellspacing="0" cellpadding="0">
            <tr>
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:15px;">Invoice From</td>
            </tr>
             @php
             $branch = \DB::table('mst_branches')->where('branch_id',$order->branch_id)->first();
             $invoice_data = \DB::table('trn_order_invoices')->where('order_id',$order->order_id)->first();
             $oredrAddr = \DB::table('trn_customer_addresses')->where('customer_address_id',$order->delivery_address)->first();
             $da = \DB::table('mst_delivery_areas')->where('area_id',@$oredrAddr->area_id)->first();
             $ts = \DB::table('trn__store_delivery_time_slots')->where('store_delivery_time_slot_id',@$order->delivery_timeslot_id)->first();
             @endphp
             <tr>
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;"> {{ @$branch->branch_name }} </td>
            </tr>
            <tr>
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;"> {{ @$branch->branch_address }} </td>
            </tr>
            <tr>
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;">Order Number: {{@$order->order_number}} </td>
            </tr>
  
            <tr>
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;">Invoice Number: INV{{@$order->order_number}} <br> Invoice Date: {{$changeDate = date("d-m-Y", strtotime( @$order->created_at))  }}</td>
            </tr>
  
            <tr>
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;"> 
                <div style="margin-top:5px;">
                    Phone: {{ @$branch->branch_contact_number }} <br>
                                  
                   <br>
                  <!--Phone: {{ @$store_data->store_mobile }} <br>-->
  
                

               </div>
              </td>
            </tr>
             <tr>
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;"></td>
            </tr>
         
            </tr>
            </table>
          </td>
        
        
      <td width="51%" valign="top" style="padding-bottom:10px;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
       
        <tr>
          <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:15px;" align="right">Invoice To</td>
        </tr>
        <!--<tr>-->
        <!--  <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;" align="right">{{@$order->customer['customer_first_name']}} {{@$order->customer['customer_last_name']}}</td>-->
        <!--</tr>-->
        <tr>
          <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;" align="right"> 
              <div>
                  <br>
                  @if (isset($order->delivery_address))
                    @php 
                     $cAddr =  \DB::table('trn_customer_addresses')->where('customer_address_id',$order->delivery_address)->first();
                    @endphp
                    
                      {!!str_replace('*', ' ',@$order->customerAddress['name'])!!} <br>
  
                       <br>
  
                       Pincode: {{$order->customerAddress['pincode']}}<br>
                       Phone: {{@$order->customerAddress['phone']}}<br>
                       Delivery Area: {{ @$da->area_name}}<br>
                       Time slot : {{ @$ts->day }} {{ ' ' }} {{@$ts->time_start }}-{{ @$ts->time_end }}<br>
                    
                                       
                  @else
                {!! str_replace('*','',@$order->customerAddress['address'])!!} <br>
                Pincode: {{$order->customer['customer_pincode']}}<br>
                Phone: {{@$order->customer['customer_mobile_number']}}<br>
                  @endif
               
               
              </div>
          </td>
        </tr> 
       
      </table></td>
    </tr>
    <tr>
      <td  colspan="2"> </td>
    </tr>
      <tr>
          <td colspan="2">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-left:1px solid #333; border-right:1px solid #333;" height="32" align="center">
                Item<br>Name
              </td>
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;"  align="center">
                Qty
              </td>
               <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;"  align="center">
                Sale Price
              </td>
              {{-- <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;"  align="center">
                Subtotal
              </td> --}}
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;"  align="center">
                Discount<br>Amount		
              </td>
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;"  align="center">
                Sub Total
              </td>
              <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;"  align="center">
                Total
              </td>
            </tr>
           
              @foreach ($order_items as $order_item)
                <tr>
                  <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-left:1px solid #333; border-right:1px solid #333;" height="32" align="center"> 
                  @php
                                              $name = \DB::table('mst_branch_product_varients')->where('varient_id',$order_item->product_varient_id)->get()->pluck('variant_name');
                                              @endphp
                                             {{ @$name->first() }}

                     </td>
                  <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">
                    {{@$order_item->quantity}} 
                  </td>
                  <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">
                   @php
                                              $price = \DB::table('mst_branch_product_varients')->where('varient_id',$order_item->product_varient_id)->get()->pluck('product_varient_offer_price');
                                               
                                              @endphp
                                             
                    {{ @$price->first() }}
                  </td>
                  <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333; border-right:1px solid #333;" align="center">
                         {{ @$order_item->product_varient->product_varient_offer_price - @$order_item->unit_price }}             
                   </td>
 
  
                 <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">
                   {{ @$order_item->unit_price }}
                   
                  </td>
                  <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">
                    {{@$order_item->unit_price * $order_item->quantity }} 
                  </td>
                  
                </tr>
                
              @endforeach
  
          </table>
        </td>
    </tr>
    
    
  </table>
  <div style="font-family:Verdana, Geneva, sans-serif;border-top-style:solid;border-top-width:1px; position:absolute;
      bottom:0;
      right:0; " height="32" align="center">
    <table style="margin:10px;">
      <tr  >
        <td style="font-size: smaller;" >Sub Total &nbsp;</td>
        <td style="font-size: smaller;" >{{ @$order->product_total_amount }}  </td>
      </tr>
     <tr>
        <td style="font-size: smaller;">Applied Discount &nbsp;</td>
        <td style="font-size: smaller;">  {{@$order->amount_reduced_by_coupon}} </td>
      </tr>
  
      <tr>
        <td style="font-size: smaller;font-weight: 500;">Grand Total &nbsp;</td>
        <td style="font-size: smaller;font-weight: 500;"> {{ @$order->product_total_amount }}</td>
      </tr>
  
      
  
     
        @if(@$order->order_type == 'APP')
     

       <tr>
        <td style="font-size: smaller;">Redeemed Amount &nbsp;</td>
        <td style="font-size: smaller;">
             @if(isset($order->amount_reduced_by_rp))
       {{ @$order->amount_reduced_by_rp}} ({{ @$order->reward_points_used}} points )
       @else
       0.00
       @endif
                                                   
        </td>
      </tr>
      
       <tr>
        <td style="font-size: smaller;">Coupon Amount &nbsp;</td>
        <td style="font-size: smaller;">  {{ @$order->amount_reduced_by_coupon }}</td>
      </tr>
      
      @endif

      <tr>
        <td style="font-size: smaller;font-weight: 500;">&nbsp;</td>
        <td style="font-size: smaller;font-weight: 500;">&nbsp;</td>
      </tr>
   
     
    </table>
  </div>
  
  
  