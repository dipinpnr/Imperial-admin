@extends('admin.layouts.app')
@section('content')
<div class="container">
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">
               <h3 class="mb-0 card-title">{{$pageTitle}}</h3>
            </div>
            <div class="card-body">
              
            </div>
            <div class="col-md-4">
            {{--  <a href="{{route('store.generate_invoice_pdf')}}" class="btn btn-info ">Generate Invoice</a> --}}
          </div>
            <div id="div_print"><br><br><br><br>
            <div class="col-lg-12">
              
               
                 <input type="hidden" class="form-control" name="order_id" value="{{$order->order_id}}">
                 
                 <div class="col-md-12">
                     <p><img width="250" height="90" src="{{url('/assets/uploads/Frame.png')}}" style="display: block;margin-left: auto;margin-right: auto;" /></p>
                 <div class="row">
                     
                                <div class="col-md-6 text-left">
                                    <p class="h3">Invoice From:</p>
                                      @php
                                        $branch = \DB::table('mst_branches')->where('branch_id',$order->branch_id)->first();
                                        $invoice_data = \DB::table('trn_order_invoices')->where('order_id',$order->order_id)->first();
                                        $oredrAddr = \DB::table('trn_customer_addresses')->where('customer_address_id',$order->delivery_address)->first();
                                        $da = \DB::table('mst_delivery_areas')->where('area_id',@$oredrAddr->area_id)->first();
                                        $ts = \DB::table('trn__store_delivery_time_slots')->where('store_delivery_time_slot_id',@$order->delivery_timeslot_id)->first();
                                       @endphp
                                      
                                    <p class="h5">{{ @$branch->branch_name }}</p>
                                    <p class="h5">{{ @$branch->branch_address }}</p>
                                    <address>
                                    <h6>Invoice Number : INV{{@$order->order_number}}</h6>
                                    <h6>Invoice Date : {{$changeDate = date("d-m-Y", strtotime( @$order->created_at))  }}</h6>
                                   
                                    <div>
                                       
                                     <br>
                                  

                                       Phone: {{ @$branch->branch_contact_number }} <br>
                                      

                                    </div>
                                    
                                    </address>
                                 </div>

                                 <div class="col-md-6 text-right">
                                    <p class="h3">Invoice To:</p>
                                    <address>
                                 
                                   
                                   @if(isset($order->delivery_address))
                                
                             <h5> {!!str_replace('*', ' ',@$order->customerAddress['name'])!!} </h5>
                             
                              <div>
                                    {!! str_replace('*','',@$order->customerAddress['address'])!!} <br>

                                     <br>

                                     Pincode: {{@$order->customerAddress['pincode']}}<br>
                                     Phone: {{@$order->customerAddress['phone']}}<br>
                                     Delivery Area: {{ @$da->area_name}}<br>
                                     Time slot : {{ @$ts->day }} {{ ' ' }} {{@$ts->time_start }}-{{ @$ts->time_end }}<br>
                                   </div>
                             
                             @else
                             
                                <h5> {{@$order->customer['customer_first_name']}} {{@$order->customer['customer_last_name']}}  </h5>
                                   <div>
                                    {{@$order->customer['customer_address']}} <br>
                                     Pincode: {{@$order->customer['customer_pincode']}}<br>
                                     Phone: {{@$order->customer['customer_mobile_number']}}<br>
                                     Delivery Area: {{ @$da->area_name}}<br>
                                     Time slot : {{ @$ts->day }} {{ ' ' }} {{@$ts->time_start }}-{{ @$ts->time_end }}<br>
                                   </div>
                             
                             @endif

                                   
                                   
                                   
                                    </address>
                                 </div>
                              </div>
                  </div>
              
            <br>
                  <div class="col-md-12">
                  <div class="table-responsive push">
                        <table class="table table-bordered table-hover mb-0 text-nowrap">
                          <thead>
                                    <tr>
                                        <td>SL.No</td>
                                       <td>Item<br>Name</td>
                                       <td>Qty</td>
                                       
                                       <td>Sale Price</td>
                                       <td>Discount<br>Amount</td>
                                       <!--<td class="text-center">Tax %</td>-->
                                       <!--<td class="text-center">Tax Details</td>-->
                                       <!--<td>Tax<br>Amount</td>-->
                                       <td>Subtotal</td>
                                       <td colspan="10" >Total</td>
                                    </tr>
                           </thead>
                           <tbody>  
                                    @php
                                       $dis_amt = 0;
                                       $subtotal = 0;
                                       $tax_amount  = 0;
                                       $gand_total = 0;
                                       $tval = 0;
                                       $t_val = 0;
                                       $c = 0;
                                    @endphp
                                    
                                    @foreach ($order_items as $order_item)
                                       <tr>
                                           <td>{{ ++$c }}</td>
                                          <td>
                                             {{ @$order_item->product_varient->variant_name }}
                                             
                                             
                                           </td>
                                          <td>{{@$order_item->quantity}} </td>
                                           
                                           <td> {{ @$order_item->product_varient->product_varient_offer_price }}</td>
                                 
                                           
                                              @php
                                                 $discountAmt = $order_item->quantity * (@$order_item->product_varient->product_varient_price - @$order_item->product_varient->product_varient_offer_price);
                                              @endphp
                                              

                                              @php
                                                $tax_info = \DB::table('mst_branch_products')
                                                ->join('mst__taxes','mst__taxes.tax_id','=','mst_branch_products.tax_id')
                                                ->where('mst_branch_products.branch_product_id', $order_item->product_id)
                                                ->select('mst__taxes.tax_id','mst__taxes.tax_name','mst__taxes.tax_value')
                                                ->first();  
                                                $tval  = $order_item->unit_price * @$order_item->quantity;
                                                $tTax = $order_item->quantity * (@$order_item->product_varient->product_varient_offer_price * @$tax_info->tax_value / (100 + @$tax_info->tax_value));
                                                $orgCost =  $order_item->quantity * (@$order_item->product_varient->product_varient_offer_price * 100 / (100 + @$tax_info->tax_value));
                                                $Tot = $tTax + $orgCost;
                                             @endphp

                                              
                                          @php
                                            
                                             @$t_val = ($tax_info->tax_value * $tval) * 0.01 ;
                                             $splitdata = \DB::table('trn__tax_split_ups')->get();
                                               // dd($splitdata);
                                          @endphp
                                          {{-- <td>
                                             {{ @$tax_info->tax_name }}
                                          </td>
                                          <td>
                                             {{@$tax_info->tax_value }}
                                          </td> --}}
                                          <td>
                                             {{ @$order_item->product_varient->product_varient_offer_price - @$order_item->unit_price }}
                                          </td>
                                          
                                          <td>
                                            {{ @$order_item->unit_price }}  
                                            
                                           </td>
                                           <td colspan="10" >
                                             {{@$order_item->unit_price * $order_item->quantity }}  

                                           </td>
                                          
                                       </tr>
                                       @php
                                          $dis_amt =  $dis_amt + $discountAmt;
                                          $single_subtotal = @$order_item->unit_price * @$order_item->quantity;
                                          $subtotal = $subtotal + $single_subtotal; 
                                          $tax_amount = $tax_amount + $tTax ; 
                                       @endphp
                                    @endforeach
                                    
                                    <tr>
                                       <td colspan="10" class=" text-right">Sub Total</td>
                                       <td class=" h4">   {{ number_format((float)$subtotal, 2, '.', '') }}   </td>
                                    </tr>
                            

                                   
                                  
                                    @php
                                    $pCharge = 0;
                                    $dCharge = 0;
                                      $dCharge =   @$order->delivery_charge;
                                      $pCharge =   @$order->packing_charge;
                                    @endphp

                                    @if(@$order->order_type == 'APP')

                                    <!--<tr>-->
                                    <!--   <td colspan="10" class=" text-right">Delivery Charge</td>-->
                                    <!--   <td class="  h4">{{ $dCharge }}</td>-->
                                    <!--</tr>-->

                                    <!-- <tr>-->
                                    <!--   <td colspan="10" class=" text-right">Packing Charge</td>-->
                                    <!--   <td class=" h4"> {{ $pCharge }}</td>-->
                                    <!--</tr>-->
                                    @else

                                    <!--<tr>-->
                                    <!--   <td colspan="10" class=" text-right">Delivery Charge</td>-->
                                    <!--   <td class="  h4">0.00</td>-->
                                    <!--</tr>-->

                                    <!-- <tr>-->
                                    <!--   <td colspan="10" class=" text-right">Packing Charge</td>-->
                                    <!--   <td class=" h4"> 0.00 </td>-->
                                    <!--</tr> -->

                                    @endif
                                    <tr>
                                       <td colspan="10" class=" text-right">Applied Discount</td>
                                       <td class=" h4"> {{@$order->amount_reduced_by_coupon}} </td>
                                    </tr>


                                    <tr>
                                       <td colspan="10" class="font-weight-bold text-uppercase text-right">Grand Total</td>
                                       <td class="font-weight-bold  h4"><i class="fa fa-inr"></i> {{ @$order->product_total_amount }}</td>
                                    </tr>

                                    
                                   

                                   
                                    @if(@$order->order_type == 'APP')

                                    

                                        <!--<tr>-->
                                        <!--   <td colspan="8" class=" text-right">Reward point used</td>-->
                                        <!--   <td class=" h4"> </td>-->
                                        <!--</tr>-->
                                        <tr>
                                           <td colspan="10" class=" text-right">Redeemed amount</td>
                                           <td class=" h4"> 
                                                   @if(isset($order->amount_reduced_by_rp))
                                                   {{ @$order->amount_reduced_by_rp}} ({{ @$order->reward_points_used}} points )
                                                   @else
                                                   0.00
                                                   @endif
                                                   
                                           </td>
                                        </tr>
                                        
                                    

                                   

                                 


                                    <tr>
                                       <td colspan="10" class=" text-right">Coupon Amount</td>
                                       <td class=" h4"> {{ @$order->amount_reduced_by_coupon }} </td>
                                    </tr>


                                    
                                    


                                   
                                    @endif
                                    
                                    <!-- <tr>-->
                                    <!--   <td colspan="8" class=" text-right">Sub Total</td>-->
                                    <!--   <td class=" h4"> {{ @$subtotal }} </td>-->
                                    <!--</tr>-->
                                    
                                    @php
                                        @$gand_total = @$subtotal;
                                    @endphp

                                  


                                 </tbody>
                        </table>
                     </div> 
                  </div>
                              {{-- <br>
                              <h5 class="mt-6 ml-4">Tax Split Ups</h5>
                              <div class="col-md-8">
                                 <div class="table-responsive push">
                                 <table class="table table-bordered table-hover mb-0 text-nowrap">
                                   <thead>
                                      
                                      @php
                                          $tax_d = \DB::table('mst_store_products')
                                             ->join('trn_order_items','trn_order_items.product_id','=','mst_store_products.product_id')
                                               ->join('mst__taxes','mst__taxes.tax_id','=','mst_store_products.tax_id')
                                               ->where('trn_order_items.order_id', $order_id)
                                               ->select('mst__taxes.tax_id','mst__taxes.tax_name','mst__taxes.tax_value')
                                               ->get()->unique('tax_id');
                                            //   dd($tax_d);
                                      @endphp
                                      @foreach ($tax_d as $tax_s)
                                      @php
                                         $splitdata = \DB::table('trn__tax_split_ups')->where('tax_id',$tax_s->tax_id)->get();
                                         $stax = 0;
                                        @endphp
                                         <tr>
                                            <th><b>{{ $tax_s->tax_name }} {{ $tax_s->tax_value }}% </b></th>
                                            @foreach ($splitdata as $item)
                                            @php
                                               $spliteddata = \DB::table('mst_store_products')
                                               ->join('trn_order_items','trn_order_items.product_id','=','mst_store_products.product_id')
                                               ->where('mst_store_products.tax_id', $tax_s->tax_id)
                                               ->where('trn_order_items.order_id', $order_id)
                                               ->sum('trn_order_items.tax_amount');
                                            @endphp
                                            <td>

                                                {{ $item->split_tax_name }} {{ $item->split_tax_value }}%
                                                <br>
                                                @php
                                                    $stax = ($item->split_tax_value * $spliteddata) / $tax_s->tax_value; 
                                                @endphp
                                                {{ number_format((float)$stax, 2, '.', '') }}
                                               
                                             </td>
                                            @endforeach
                                         </tr>
                                      @endforeach
                                   </thead>
                                   <tbody>
                                   </tbody>
                                 </table>
                              </div>
                           </div> --}}
                           
                        </div>
                     </div>

                        <div class="card-footer text-right">
                                <a href="{{route('store.list_order')}}" class="btn btn-cyan mb-1"  >Cancel</a>
                                

                              <button type="button" class="btn btn-info mb-1"  onClick="printdiv('div_print');"><i class="si si-printer"></i> Print Invoice</button>
                         
                     </div><!-- COL-END -->
                 
             
         </div>
      </div>
   </div>

   <script type="text/javascript">
        function printdiv(printpage) {
            var headstr = "<html><head><title></title></head><body>";
            var footstr = "</body>";
            var newstr = document.all.item(printpage).innerHTML;
            var oldstr = document.body.innerHTML;
            document.body.innerHTML = headstr + newstr + footstr;
            window.print();
            location.reload();
            return false;
        }
    </script>

   @endsection
  
   