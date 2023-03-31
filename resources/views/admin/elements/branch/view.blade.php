@extends('admin.layouts.app')
@section('content')
<div class="row" id="user-profile">
   <div class="col-lg-12">
      <div class="card">
         <div class="card-body">
            <div class="wideget-user">
          <h4>{{$pageTitle}}</h4>
                     <div class="row">
                  <div class="col-lg-6 col-md-12">
                     <div class="wideget-user-desc d-sm-flex">
                        <div class="wideget-user-img">
                          


                        </div>

                     </div>
                  </div>
               </div>
            </div>
         </div>

         <div class="border-top">
            <div class="wideget-user-tab">
               <div class="tab-menu-heading">
                  <div class="tabs-menu1">
                     <ul class="nav">
                        <li class=""><a href="#tab-51" class="active show"
                           data-toggle="tab">Profile</a></li>
                        <li><a href="#tab-71" data-toggle="tab" class="">Products</a></li>

                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="card">
         <div class="card-body">
            <div class="border-0">
               <div class="tab-content">
                  <div class="tab-pane active show" id="tab-51">
                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Product Information</strong></h5>
                        </div>
                        <div class="table-responsive ">
                           <table class="table row table-borderless">
                              <tbody class="col-lg-12 col-xl-6 p-0">
                                 <tr>
                                    <td><strong>Branch Name:</strong> {{ $branch->branch_name}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Branch Code:</strong> {{ $branch->branch_code}}</td>
                                 </tr>
                                  <tr>
                                    <td><strong>Contact Person:</strong> {{@$branch->branch_contact_person}}</td>
                                 </tr>
                                 
                                  <tr>
                                    <td><strong> Contact Number:</strong> {{@$branch->branch_contact_number}}</td>
                                 </tr>
                                 
                                <!-- <tr>-->
                                <!--    <td><strong> Type:</strong> -->
                                       
                                <!--       @if(@$product->product_type == 1)-->
                                <!--       Product-->
                                <!--       @else-->
                                <!--       Service-->
                                <!--       @endif-->

                                <!--    </td>-->
                                <!-- </tr>-->
                                <!--@if(@$product->product_type == 2)-->
                                <!--   <tr>-->
                                <!--    <td><strong>Service Type:</strong> -->
                                       
                                <!--       @if(@$product->service_type == 1)-->
                                <!--       Booking Only-->
                                <!--       @else-->
                                <!--       Purchase-->
                                <!--       @endif-->

                                <!--    </td>-->
                                <!-- </tr>-->
                                <!--@endif-->

                                 
                                 <tr>
                                    <td><strong>Address:</strong> {{ @$branch->branch_address}}</td>
                                 </tr>

                                 {{-- <tr>
                                    <td><strong>Offer From Date :</strong> {{ $product->product_offer_from_date}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Offer To Date :</strong> {{ $product->product_offer_to_date}}</td>
                                 </tr> --}}
                               
                                {{-- <tr>
                                     <td><strong>Commision Rate :</strong>{{ $product->product_commision_rate}}</td>
                                </tr> --}}
                                
                                <!--<tr>-->
                                <!--  <td><strong>Store:</strong> {{ @$product->store['store_name']}}</td>-->
                                <!--</tr>-->
                               


                              </tbody>
                              <tbody class="col-lg-12 col-xl-6 p-0">
                                  
                                   <tr>
                                    <td><strong>Whatsapp Number:</strong> {{ $branch->whatsapp_number}}</td>
                                 </tr>
                               <tr>
                                    <td><strong>Email:</strong> {{ $branch->branch_email }}</td>
                                </tr>
                                
                                  <tr>
                                    <td><strong>Opening:</strong> {{ $branch->working_hours_from}}</td>
                                </tr>

                                 <tr>
                                     <td><strong>Closing:</strong> {{ $branch->working_hours_to}}</td>
                                 </tr>
                               
                                 
                              </tbody>
                           </table>


                          
                        </div>
                        
                      <center>
                       <a class="btn btn-cyan" href="{{route('admin.branch') }}">Cancel</a>
                           </center>
                           
                     </div>
                 </div>
                  
                  
                    <div class="tab-pane" id="tab-71">

                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Products</strong></h5>
                        </div><br>
                           <div class="table-responsive ">
                           <table  id="example5" class="table table-striped table-bordered">
                              <thead>
                                 <tr>
                                   <th class="wd-15p">SL.No</th>
                                    <th class="wd-15p">{{ __('Product') }}</th>
                                    <th class="wd-15p">{{ __('Code') }}</th>
                                    <th class="wd-15p">{{ __('Price') }}</th>
                                    <th class="wd-15p">{{__('Image')}}</th>
                                 </tr>
                              </thead>
                             <tbody class="col-lg-12 col-xl-6 p-0">
                                 
                                  @foreach(@$products as $product)
                                 <tr>
                                    
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$product->product_name}}</td>
                                    <td>{{$product->product_code}}</td> 
                                   <td>{{$product->product_price_offer}}</td>
                                    <td>
                                    @empty($product->product_base_image)
                                    <img src="{{URL::to('/public/assets/images/product_default.jpeg')}}" width="50" >
                                    @else
                                    <img data-toggle="modal" data-target="#viewModal{{$product->product_id}}"  src="{{URL::to('/assets/uploads/products/base_product/base_image/'.$product->product_base_image)}}"  width="50" >&nbsp;</td>
                                    @endEmpty
                                    
                                    
                                 </tr>
                                  @endforeach
                                
                              </tbody>
                           </table>
                           {{$products->links()}}
                        <center>
                           <a class="btn btn-cyan" href="{{ route('admin.branch') }}">Cancel</a>
                           </center>
                        </div>
                     </div>
                  </div>


                  </div>

             </div>
             
             

</div>
</div>
</div>
</div>


@endsection
