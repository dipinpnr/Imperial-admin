@extends('admin.layouts.app')
@section('content')

@php
    use App\Models\admin\Trn_RecentlyVisitedStore;
    use App\Models\admin\Trn_store_order;

@endphp

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 col-lg-12">
            <div class="card">
                <div class="row">
                    <div class="col-12" >
    
                      @if ($message = Session::get('status'))
                      <div class="alert alert-success">
                         <p>{{ $message }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
                      </div>
                      @endif
                      <div class="col-lg-12">
                             @if ($errors->any())
                             <div class="alert alert-danger">
                                <h6>Whoops!</h6> There were some problems with your input.<br><br>
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
                                <form action="{{route('store.out_of_stock_reports')}}" method="GET" enctype="multipart/form-data">
                                   @csrf
                                    <div class="row">
                                           <div class="col-md-12">
                                              <div class="form-group">
                                                  <label class="form-label">Branch</label>
                                                      <select name="branch_id" id="branch_id" class="form-control select2-show-search" data-placeholder="Customer" >
                                                           <option value="" {{ request()->input('branch_id') == null ? 'selected':'' }} >Branch</option>
                                                           @foreach ($branches as $key)
                                                                <option value="{{ $key->branch_id }}" {{request()->input('branch_id') == $key->branch_id ? 'selected':''}} >{{ $key->branch_name }}  </option>
                                                           @endforeach
                                                      </select>
                                               </div>
                                          </div> 
                                      
                                           <div class="col-md-6">
                                              <div class="form-group">
                                                  <label class="form-label">Products</label>
                                                      <select name="product_id" id="product_id" class="form-control select2-show-search" data-placeholder="Products" >
                                                           <option value="" {{ request()->input('product_id') == null ? 'selected':'' }} >Products</option>
                                                           @foreach ($products as $key)
                                                                <option value="{{ $key->branch_product_id }}" {{request()->input('product_id') == $key->branch_product_id ? 'selected':''}} > {{ $key->product_name }} </option>
                                                           @endforeach
                                                      </select>
                                               </div>
                                          </div>
                                          
                                          
                                         
                                         <div class="col-md-6">
                                            <div class="form-group">
                                              <label class="form-label">Category </label>
                                               <select  name="category_id" id="categoryId" class="form-control select2-show-search" data-placeholder="Category"  >
                                                <option value="" {{ request()->input('category_id') == null ? 'selected':'' }}>Category</option>
                                                    @foreach($categories as $key)
                                                    <option {{request()->input('category_id') == $key->item_category_id ? 'selected':''}} value="{{$key->item_category_id }}"> {{$key->category_name }} </option>
                                                    @endforeach
                                                  </select>
                                            </div>
                                         </div>
                                         
                                         
                                         <div class="col-md-12">
                                            <div class="form-group">
                                                <center>
                                                   <button type="submit" class="btn btn-raised btn-primary"><i class="fa fa-check-square-o"></i> Filter</button>
                                                   {{-- <button type="reset" id="reset" class="btn btn-raised btn-success">Reset</button> --}}
                                                   <a href="{{route('store.out_of_stock_reports')}}"  class="btn btn-info">Cancel</a>
                                                </center>
                                            </div>
                                          </div>
                     
                     
                                    </div>
                                </form>
                            </div>
                        
                            <div class="card-body">
                                <div class="table-responsive">
                                   <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                                      <thead>
                                         <tr>
                                            <th class="wd-15p">SL.No</th>
                                            <th class="wd-15p">Date</th>
                                            <th class="wd-15p">Time</th>
                                            <th class="wd-15p">Product Name</th>
                                            <th class="wd-15p">Variant Price</th>
                                            {{-- <th class="wd-15p">Vendor</th> --}}
                                            <th class="wd-15p">Category</th>
                                            {{--<th class="wd-15p">Sub Category</th> --}}
                                            <th class="wd-15p">Branch</th> 
                                            <!--<th class="wd-15p">Product Status</th>-->
                                          
                                       
                                         </tr>
                                      </thead>
                                      <tbody>
                                          
                                        @php
                                        $i = 0;
                                        @endphp
                                        @foreach ($data as $d)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            @php
                                                if(!isset($d->updated_time)){
                                                    $dateU = $d->updated_time;
                                                }else{
                                                    $dateU = $d->created_at;
                                                }
                                            @endphp
                                            <td>{{ \Carbon\Carbon::parse($dateU)->format('d-m-Y')}}</td>
                                            <td>{{ \Carbon\Carbon::parse($dateU)->format('h:i:s')}}</td>

                                            <td>{{ $d->variant_name }}</td>
                                            <td>{{ $d->product_varient_offer_price }}</td>
                                            {{-- <td>
                                                @if(isset($d->agency_name))
                                                {{ $d->agency_name }}
                                                @else
                                                ---
                                                @endif
                                            </td> --}}
                                            <td>
                                                @if(isset($d->category_name))
                                                {{ $d->category_name }}
                                                @else
                                                ---
                                                @endif
                                            </td>
                                           {{-- <td>
                                                @if(isset($d->sub_category_name))
                                                {{ $d->sub_category_name }}
                                                @else
                                                ---
                                                @endif
                                            </td> --}} 
                                            <td>
                                                
                                                {{ @$d->branch->branch_name }}
                                                
                                            </td> 
                                            <!--<td> -->
                                            <!--Inactive-->
                                               
                                            <!--</td>-->
                                            

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
    </div>
</div>


<script>
    $(function(e) {
	 $('#exampletable').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'pdf',
                title: 'Out of stock report',
                footer: true,
                
                 orientation : 'landscape',
                pageSize : 'LEGAL',
            },
            {
                extend: 'excel',
                title: 'Out of stock report',
                footer: true,
               
            }
         ]
    } );

} );


$(document).ready(function() {
        
        $("#categoryId").on('change', function(){    
            
        let categoryId = $('#categoryId').val();
        
       // console.log(categoryId);

        var _token= $('input[name="_token"]').val();
        
            $.ajax({
              type:"GET",
              url:"{{ url('store/product/ajax/get_subcategory') }}?category_id="+categoryId,
    
              success:function(res){
                    if(res){
                       // console.log(res);
                        $('#subCategoryId').prop("diabled",false);
                        $('#subCategoryId').empty();
                        $('#subCategoryId').append('<option value="">Sub Category</option>');
                        $.each(res,function(sub_category_id,sub_category_name)
                        {
                          $('#subCategoryId').append('<option value="'+sub_category_id+'">'+sub_category_name+'</option>');
                        });
                        
                        let subCategoryId = getUrlParameter('sub_category_id');
                        if ( typeof subCategoryId !== "undefined" && subCategoryId) {
                            $("#subCategoryId option").each(function(){
                                if($(this).val()==subCategoryId){ 
                                    $(this).attr("selected","selected");    
                                }
                            });
                        } 
                    
                    
                    }else
                    {
                      $('#storeId').empty();
                    }
                }
    
            });
        });
    });
    
</script>

@endsection

