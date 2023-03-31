@extends('admin.layouts.app')
@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-12 col-lg-12">
      <div class="card">
        <div class="row">
          <div class="col-12">
            
            
            @if ($message = Session::get('status'))
            <div class="alert alert-success">
              <p>{{ $message }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
            </div>
            @endif
             @if ($message = Session::get('err_status'))
            <div class="alert alert-danger">
              <p>{{ $message }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
            </div>
            @endif
            <div class="col-lg-12">
              @if ($errors->any())
              <div class="alert alert-danger">
                <strong>Whoops!</strong>
                <ul>
                  @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
              @endif

             <div class="card-header">
                        <h3 class="mb-0 card-title">{{$pageTitle}}</h3>
                     </div>
                    <div class="card-body border">
                <form action="{{route('store.list_product')}}" method="GET" 
                         enctype="multipart/form-data">
                   @csrf
            <div class="row">
               <div class="col-md-4">
                  <div class="form-group">
                     <label class="form-label">Product Name</label>
                       <input type="text" class="form-control" 
                       name="product_name"  value="{{ request()->input('product_name') }}" placeholder="Product Name">

                  </div>
               </div>
                <div class="col-md-4">
                  <div class="form-group">
                     <label class="form-label">Product Code</label>
                       <input type="text" class="form-control" 
                       name="product_code"  value="{{ request()->input('product_code') }}" placeholder="Product Code">

                  </div>
               </div>
                <div class="col-md-4">
                  <div class="form-group">
                     <label class="form-label">Product Status</label>
                       <select name="product_status" id="product_statusproduct_status"  class="form-control" >
                 <option value="" >Select Status</option>
                 <option {{request()->input('product_status') == '1' ? 'selected':''}} value="1" >Active</option>
                 <option {{request()->input('product_status') == '0' ? 'selected':''}} value="0" >Inactive</option>  
                 </select>
                  </div>
               </div>
            
             </div>
             
             <div class="row">
                    {{-- <div class="col-md-4">
                 <div class="form-group">
                    <label class="form-label">From Date</label>
                     <input type="date" class="form-control" name="From_date"  value="{{ request()->input('From_date') }}" placeholder=" Date">

                  </div> 
               </div>  --}}
                 {{-- <div class="col-md-4">
                 <div class="form-group">
                    <label class="form-label">To Date</label>
                     <input type="date" class="form-control" name="To_date"  value="{{ request()->input('To_date') }}" placeholder="Date">

                  </div> 
               </div>  --}}

               <div class="col-md-6">
                <div class="form-group">
                   <label class="form-label">Price Range From</label>
                     <input type="number" class="form-control" 
                     name="start_price"  value="{{ request()->input('start_price') }}" placeholder="Price Range From">

                </div>
             </div>
              <div class="col-md-6">
                <div class="form-group">
                   <label class="form-label">Price Range To</label>
                     <input type="number" class="form-control" 
                     name="end_price"  value="{{ request()->input('end_price') }}" placeholder="Price Range To">

                </div>
             </div>

               <!--  <div class="col-md-4">-->
               <!-- <div class="form-group">-->
               <!--     <label class="form-label">Stock Status</label>-->
               <!--      <select name="stock_status" id="stock_status_"  class="form-control" >-->
               <!--  <option value="" >Select Status</option>-->
               <!--  <option {{request()->input('stock_status') == '1' ? 'selected':''}} value="1" >Instock</option>-->
               <!--  <option {{request()->input('stock_status') == '0' ? 'selected':''}} value="0" >OutStock</option>  -->
               <!--  </select>-->
               <!--   </div>-->
               <!--</div> -->
             </div>
                <div class="row">
              </div>
                     <div class="col-md-12">
                     <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Filter</button>
                           {{-- <button type="reset" class="btn btn-raised btn-success">Reset</button> --}}
                          <a href="{{route('store.list_product')}}"  class="btn btn-info">Cancel</a>
                           </center>
                        </div>
                  </div>
                </div>
                   </form>
                </div>
             
               <div class="card-body">
                         <a href="{{route('store.create_product')}}" class="btn btn-block btn-info">
                           <i class="fa fa-plus"></i>
                           Create Product
                        </a> <br/>
                        <div class="row">
                            <div class="col-sm-4">
                        <a href="{{route('store.restore-products')}}" class=" text-white btn btn-block btn-danger">
                           <i class="fa fa-recycle"></i>
                          Restore Products
                        </a></div>
                        <div class="col-sm-4">
                        <a href="  {{route('admin.import_global_product')}} " class="btn btn-block btn-green">
                           <i class="fa fa-file-excel-o"></i>
                           Import  Products
                        </a></div>
                        <div class="col-sm-4">
                        <a href="javascriprt:void(0);" data-toggle="modal" data-target="#branchModal" id="branch-button" class=" text-white btn btn-block btn-success">
                           <i class="fa fa-arrows-h"></i>
                           Assign to Branch
                        </a></div>
                       </div>
                       <br/>
                       <label class="custom-control custom-checkbox">
                           <input type="checkbox" onclick="checkAllBoxes(this.id)" id="checkAll"  class="custom-control-input" name="example-checkbox4" >
                           <span class="custom-control-label">Select All</span>
                        </label>
                        
                <div class="table-responsive">
                  <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                      <tr>
                        <th></th>  
                        <th class="wd-15p">SL.No</th>
                        <th class="wd-15p">{{ __('Product') }}</th>
                        <th class="wd-15p">{{ __('Code') }}</th>
                        <th class="wd-15p">{{ __('Price') }}</th>
                        <th class="wd-15p">{{__('Image')}}</th>
                        <th class="wd-15p">{{__('Status')}}</th>
                        <!--<th class="wd-15p">{{__('Stock')}}</th>-->
                       <th class="wd-15p">{{__('Action')}}</th>
                       
                      </tr>
                    </thead>
                    <tbody>
                      
                      @foreach ($products as $product)
                      <tr>
                        <td><input type="checkbox" class="gProClass" id="product-ids" name="product_idz" value="{{$product->product_id}}"></td>  
                        <td>{{ $loop->iteration}}</td>
                        <td>{{$product->product_name}}</td>
                        <td>{{$product->product_code}}</td> 
                        <td>{{$product->product_price_offer}}</td>
                        <td>
                        @empty($product->product_base_image)
                        <img src="{{URL::to('/public/assets/images/product_default.jpeg')}}" width="50" >
                        @else
                        <img data-toggle="modal" data-target="#viewModal{{$product->product_id}}"  src="{{URL::to('/assets/uploads/products/base_product/base_image/'.$product->product_base_image)}}"  width="50" >&nbsp;</td>
                        @endEmpty
                        @php
                         $branch_products_count = \DB::table('mst_branch_products')->where('is_removed', 0)->
                            where('global_product_id',$product->product_id)->
                            sum('stock_count');
                         $branch_products_ids = \DB::table('mst_branch_products')->where('is_removed', 0)->
                            where('global_product_id',$product->product_id)->get()->pluck('branch_product_id')->all();
                            
                         $branch_product_varients_count = \DB::table('mst_branch_product_varients')->where('is_removed', 0)->
                            whereIn('product_id',$branch_products_ids)->
                            sum('stock_count');
                             
                            $stock_count_sum =  $branch_product_varients_count;
                          @endphp
                       
                        <td>
                            

                              @php

                                $productStatus = $product->product_status;
                              @endphp
                          <a style="color:white;" id="statusBtn{{$product->product_id}}" 
                                                    onclick="changeStatus({{$product->product_id}})"  
                                                    class="btn btn-sm @if($productStatus == 0) btn-danger @else btn-success @endif"
                                                > 
                                                    @if($productStatus == 0)
                                                        Inactive
                                                    @else
                                                        Active
                                                    @endif
                                                </a>
          
                        </td>
                        <!--<td>-->
                        <!--    <button type="button"  data-toggle="modal" data-target="#________StockModal{{$product->product_id}}"  class="btn btn-sm @if(@$stock_count_sum == 0) btn-danger @else btn-success @endif"> -->
                           
                          
                        <!--    @if(  @$stock_count_sum == 0)-->
                        <!--    Out of stock-->
                        <!--    @else-->
                        <!--    In stock-->
                        <!--    @endif </button>-->
                          
                        <!--  </form>-->

                        <!--</td>-->
                     
                   <td>
                     <form action="{{route('store.destroy_product',$product->product_id)}}" method="POST">
                       
                    @csrf
                      @method('POST')

                       <a class="btn btn-sm btn-cyan" href="{{url('store/product/edit/'.$product->product_id)}}">Edit</a> 
                       <a class="btn btn-sm btn-cyan"
                       href="{{url('store/product/view/'.$product->product_id)}}">View</a> 
                        <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
                      @php
                        $varCount = \DB::table('mst_store_product_varients')->where('product_id',$product->product_id)->where('is_removed','!=',1)->count();

                      @endphp
                       @if($varCount >= 2)
                       <br> <a class="mt-2 btn btn-sm btn-orange" href="{{url('store/product/variant/list/'.$product->product_id)}}">Product Variant</a> 
                        @endif
                      </form> 

                        </td> 
                        
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                  
               
             
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @foreach($products as $product)
  <div class="modal fade" id="viewModal{{$product->product_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
     <div class="modal-dialog" role="document">
        <div class="modal-content">
           <div class="modal-header">
              <h5 class="modal-title" id="example-Modal3">{{$pageTitle}}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
           </div>
           <div class="modal-body">

            <img  src="{{URL::to('/assets/uploads/products/base_product/base_image/'.$product->product_base_image)}}"  width="600" >

           </div>
           <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
           </div>
        </div>
     </div>
  </div>
@endforeach


 @foreach($products as $product)
            <div class="modal fade" id="StockModal{{$product->product_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">{{$pageTitle}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>
                           
                 <form action="{{ route('store.stock_update_product',$product->product_id) }} " method="POST" enctype="multipart/form-data" >
                 @csrf
                  <div class="modal-body">
           
                    <label class="form-label">Stock</label>
                    <input type="hidden" class="form-control" name="product_id" value="{{$product->product_id}}" >
                    
                  <input type="number" class="form-control" name="stock_count" value="{{old('stock_count',$product->stock_count)}}" placeholder="Stock">
                  
                </div>
              
                     <div class="modal-footer">
                       <button type="submit" class="btn btn-raised btn-primary">
                    <i class="fa fa-check-square-o"></i> Update</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                     </div>
                      </form>
                  </div>
               </div>
            </div>
            @endforeach
    <div class="modal fade" id="branchModal" tabindex="-1" role="dialog"  aria-hidden="true">
     <div class="modal-dialog" role="document">
        <div class="modal-content">
           <div class="modal-header">
              <h5 class="modal-title" id="example-Modal3">Assign Selected Products To Branches</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
           </div>
           <div class="modal-body">
            @if ($errors->any())
              <div class="alert alert-danger">
                <strong>Whoops!</strong>
                <ul>
                  @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
              @endif
   
            <form action="{{ route('assign_products') }} " method="POST" enctype="multipart/form-data" >
            @csrf
            <input type="hidden" name="assign_product[]" id ="assign_product">
                        
                    <label class="form-label"> Branches </label>
					 <select id="area" name="branches[]"  multiple="multiple" class="multiselect" >
					    @foreach($branches as $branch)
                        <option value="{{$branch->branch_id}}">{{$branch->branch_name}}</option>
                        @endforeach
                      </select>
             
           </div>
           <div class="modal-footer">
               <button type="submit" class="btn btn-secondary">Assign</button>
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
               </form>
           </div>
        </div>
     </div>
  </div>
  <script>

</script>    
<script>
     $("#branch-button").click(function(){
     let values = new Array();         
     $("input:checkbox[name=product_idz]:checked").each(function(){
      values.push($(this).val());
     });
     
     $("#assign_product").val(values);
     
});
</script>
<script>
   function checkAllBoxes(id){
     // alert(id);
      if ($('#'+id).is(':checked')) {
         $('.gProClass').prop('checked', true);
      }else{
         $('.gProClass').prop('checked', false);
      }
   }
</script>


            <script>

              $(function(e) {
                  $('#exampletable').DataTable( {
                      dom: 'Bfrtip',
                      buttons: [
                          {
                              extend: 'pdf',
                              title: 'Product List',
                              // orientation:'landscape',
                              footer: true,
                              exportOptions: {
                                   columns: [1,2,3,5,6,7],
                                   alignment: 'right',
                               },
                                customize: function(doc) {
                                    doc.content[1].margin = [ 100, 0, 100, 0 ]; //left, top, right, bottom
                             doc.content.forEach(function(item) {
                             if (item.table) {
                                item.table.widths = [40, '*','*','*','*','*']
                              }
                             })
                           }
                          },
                          {
                              extend: 'excel',
                              title: 'Product List',
                              footer: true,
                              exportOptions: {
                                   columns: [1,2,3,5,6,7]
                               }
                          }
                       ]
                  } );
              
              } );
              
              
function changeStatus(item_row_id)
{
   // $('#loaderCard').show();
  //  $('#example_tbody').hide();
  var stat = 0;
    var _token= $('input[name="_token"]').val();
    $.ajax({
        type:"GET",
        url:"{{ url('admin/ajax/change-status/product') }}?product_id="+item_row_id ,
        success:function(res){
            if(res == "active"){
                stat = 0;
                $("#statusBtn"+item_row_id).removeClass("btn-danger");
                $("#statusBtn"+item_row_id).addClass("btn-success");
                $( "#statusBtn"+item_row_id ).empty();
                $( "#statusBtn"+item_row_id ).text('Active');
            }
            else
            {
                stat = 1;
                $("#statusBtn"+item_row_id).removeClass("btn-success");
                $("#statusBtn"+item_row_id).addClass("btn-danger");
                $( "#statusBtn"+item_row_id ).empty();
                $( "#statusBtn"+item_row_id ).text('Inactive');
            }
        },
        complete: function(){
            $('#loaderCard').hide();
            $('#example_tbody').show();
            if(stat == 0)
            {
                return $.growl.notice({
                message: "Status updated"
                });
            }
            else
            {
                return $.growl.warning({
                title: "Notice!",
                message: "Status updated"
                });
            }
        },
        fail: function(){

            return $.growl.error({
                title: "Oops!",
                message: "Something wen't wrong"
            }); 

            $('#loaderCard').hide();
            $('#example_tbody').show();
            $('#example_tbody').html('<tr>No data found.</tr>');
        },
    });
}
</script>
              
                          </script>
            
  @endsection
 