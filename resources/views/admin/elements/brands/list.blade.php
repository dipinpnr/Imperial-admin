@extends('admin.layouts.app')
@section('content')

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
                       
                    
                        <div class="card-body">
                            <a href=" {{route('admin.create_brand')}}" class="btn btn-block btn-info">
                            <i class="fa fa-plus"></i> Create Brand </a>
                            </br>
                            {{ (new \App\Helpers\Helper)->ajaxLoader() }}
                            <div class="card-body"> 

                            <div id="example_tbody" class="table-responsive">
                                <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th class="wd-15p">SL.No</th>
                                            <th class="wd-15p">{{ __('Item Brand') }}</th>
                                            <th class="wd-15p">{{ __('Image') }}</th>
                                            <th class="wd-15p">{{ __('Category') }}</th>
                                            <th class="wd-20p">{{__('Status')}}</th>
                                            <th class="wd-15p">{{__('Action')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody >
                                        @php
                                        $i = 0;
                                        @endphp
                                        @foreach ($brands as $row)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $row->brand_name}}</td>
                                            <td>
                                            @if(isset($row->brand_icon))
                                                <img src="{{URL::to('assets/uploads/brand_icon/'.$row->brand_icon)}}"  width="50" >
                                            @else
                                            <img src="{{ (new \App\Helpers\Helper)->brandIcon() }}"  width="50" >
                                            @endif
                                            </td>
                                            <td>
                                                @foreach($row->categories as $cat)
                                                @php
                                                $category_name = \DB::table('mst__item_categories')->where('item_category_id',$cat->item_category_id)->pluck('category_name')->first();
                                                 echo  $category_name;
                                                @endphp
                                                 <br/>
                                                @endforeach
                                            </td>
                                            
                                            <td>
                                                <a style="color:white;" id="statusBtn{{$row->brand_id}}" 
                                                    onclick="changeStatus({{$row->brand_id}})"  
                                                    class="btn btn-sm @if($row->is_active == 0) btn-danger @else btn-success @endif"
                                                > 
                                                    @if($row->is_active == 0)
                                                        Inactive
                                                    @else
                                                        Active
                                                    @endif
                                                </a>
                                            </td>
                                            <td>
                                            <form action="{{route('admin.destroy_brand',$row->brand_id)}}" method="POST">
                                                <a class="btn btn-sm btn-cyan" href="{{url('admin/brand/edit/'.$row->brand_id)}}">Edit</a>
                                                <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#viewModal{{$row->brand_id}}" > View</button>
                                                @csrf
                                                @method('POST')
                                                <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
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
    </div>
</div>

            @foreach($brands as $row)
            <div class="modal fade" id="viewModal{{$row->brand_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">{{$pageTitle}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>
                     <div class="modal-body">

                        <div class="table-responsive ">
                           <table class="table row table-borderless">
                              <tbody class="col-lg-12 col-xl-12 p-0">
                              
                                 <tr>
                                    <td><h6>Brand Image: </td>
                                    <td>  
                                        @if(isset($row->brand_icon))
                                        <img src="{{URL::to('assets/uploads/brand_icon/'.$row->brand_icon)}}"  width="50" >
                                    @else
                                    <img src="{{ (new \App\Helpers\Helper)->brandIcon() }}"  width="50" >
                                    @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td><h6>Name: </td><td> {{ $row->brand_name }}</h6></td>
                                 </tr>
                                                               
                                  <tr>
                                    <td>
                                    <h6>Status: 
                                        </td><td>  
                                        @if($row->is_active == 0)
                                            Inactive
                                        @else
                                            Active
                                        @endif
                                    </h6></td>
                                 </tr>
                              </tbody>
                              <tr>
                                    <td><h6>Category: </td><td> @foreach($row->categories as $cat)
                                 @php

                                                $category_name = \DB::table('mst__item_categories')->where('item_category_id',$cat->item_category_id)->pluck('category_name')->first();
                                                 echo  $category_name;
                                                @endphp
                                                <br/>
                                 @endforeach </h6></td>
                                 </tr>
                           </table>
                        </div>

                     </div>
                     <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                     </div>
                  </div>
               </div>
            </div>
            @endforeach


<script>
function changeStatus(item_row_id)
{
   // $('#loaderCard').show();
  //  $('#example_tbody').hide();
  var stat = 0;
    var _token= $('input[name="_token"]').val();
    $.ajax({
        type:"GET",
        url:"{{ url('admin/ajax/change-status/brand') }}?brand_id="+item_row_id ,
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
@endsection
