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
                            <a href=" {{route('admin.create_branch')}}" class="btn btn-block btn-info">
                            <i class="fa fa-plus"></i> Add Branch </a>
                            </br>
                            {{ (new \App\Helpers\Helper)->ajaxLoader() }}    
                         <div class="card-body"> 

                            <div id="example_tbody" class="table-responsive">
                                <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th class="wd-15p">SL.No</th>
                                            <th class="wd-15p">{{ __('Branch Name') }}</th>
                                            <th class="wd-15p">{{ __('Branch Code') }}</th>
                                            <th class="wd-15p">{{ __('Contact Person') }}</th>
                                            <th class="wd-15p">{{ __('Contact Number') }}</th>
                                            <th class="wd-20p">{{__('Status')}}</th>
                                             <th class="wd-20p">{{__('Featured')}}</th>
                                            <th class="wd-15p">{{__('Action')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody >
                                        @php
                                        $i = 0;
                                        @endphp
                                        @foreach ($branches as $row)
                                         <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $row->branch_name}}</td>
                                            <td>{{ $row->branch_code}}</td>
                                            <td>{{ $row->branch_contact_person}}</td>
                                            <td>{{ $row->branch_contact_number}}</td>
                                           
                                            <td>
                                                <a style="color:white;" id="statusBtn{{$row->branch_id}}" 
                                                    onclick="changeStatus({{$row->branch_id}})"  
                                                    class="btn btn-sm @if($row->branch_status == 0) btn-danger @else btn-success @endif"
                                                > 
                                                    @if($row->branch_status == 0)
                                                        Inactive
                                                    @else
                                                        Active
                                                    @endif
                                                </a>
                                            </td>
                                            <td>
                                             <a style="color:white;" id="statusFeatureBtn{{$row->branch_id}}" 
                                                    onclick="changeFeatureStatus({{$row->branch_id}})"  
                                                    class="btn btn-sm @if($row->feature_status == 0) btn-outline-danger @else btn-outline-success @endif"
                                                > 
                                                    @if($row->feature_status == 0)
                                                        Non featured
                                                    @else
                                                        Featured
                                                    @endif
                                                </a>
                                            </td>
                                            <td>
                                            <form action="{{route('admin.destroy_branch',$row->branch_id)}}" method="POST">
                                                <a class="btn btn-sm btn-cyan" href="{{url('admin/branch/edit/'.$row->branch_id)}}">Edit</a>
                                                <a class="btn btn-sm btn-cyan" href="{{url('admin/branch/view/'.$row->branch_id)}}">View</a> 
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
        {{-- @foreach($branches as $branch)
            <div class="modal fade" id="viewModal{{$branch->branch_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
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
                                    <input type="hidden" class="form-control" name="branch_id" value="{{$branch->branch_id}}" >
                                 </tr>
                                 <tr>
                                    <td><h6>Branch Name: </td><td> {{ $branch->branch_name }}</h6></td>
                                 </tr>
                                
                                 <tr>
                                    <td><h6>Branch Code: </td><td> {!! $branch->branch_code !!}</h6></td>
                                 </tr>
                                 <tr>
                                    <td><h6>Contact Person: </td><td> {!! $branch->branch_contact_person !!}</h6></td>
                                 </tr>
                                 <tr>
                                    <td><h6>Contact Number: </td><td> {!! $branch->branch_contact_number !!}</h6></td>
                                 </tr>
                                  <tr>
                                    <td>
                                    <h6>Status: 
                                        </td><td>  
                                        @if($branch->branch_status == 0)
                                            Inactive
                                        @else
                                            Active
                                        @endif
                                    </h6></td>
                                 </tr>
                                 <tr>
                                    <td><h6>Delivery Areas: </td><td> {!! $branch->areas()->pluck('area_name')->implode(",") !!}</h6></td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>

                     </div>
                     <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                     </div>
                  </div>
               </div>
            </div>
            @endforeach --}}

          

<script>
function changeStatus(item_row_id)
{
   // $('#loaderCard').show();
  //  $('#example_tbody').hide();
  var stat = 0;
    var _token= $('input[name="_token"]').val();
    $.ajax({
        type:"GET",
        url:"{{ url('admin/ajax/change-status/branch') }}?branch_id="+item_row_id ,
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
function changeFeatureStatus(item_row_id)
{
   // $('#loaderCard').show();
  //  $('#example_tbody').hide();
  var stat = 0;
    var _token= $('input[name="_token"]').val();
    $.ajax({
        type:"GET",
        url:"{{ url('admin/ajax/change-feature-status/branch') }}?branch_id="+item_row_id ,
        success:function(res){
            if(res == "featured"){
                stat = 0;
                $("#statusFeatureBtn"+item_row_id).removeClass("btn-danger");
                $("#statusFeatureBtn"+item_row_id).addClass("btn-outline-success");
                $( "#statusFeatureBtn"+item_row_id ).empty();
                $( "#statusFeatureBtn"+item_row_id ).text('Featured');
            }
            else
            {
                stat = 1;
                $("#statusFeatureBtn"+item_row_id).removeClass("btn-success");
                $("#statusFeatureBtn"+item_row_id).addClass("btn-outline-danger");
                $( "#statusFeatureBtn"+item_row_id ).empty();
                $( "#statusFeatureBtn"+item_row_id ).text('Non featured');
            }
        },
        complete: function(){
            $('#loaderCard').hide();
            $('#example_tbody').show();
            if(stat == 0)
            {
                return $.growl.notice({
                message: "Feature Status updated"
                });
            }
            else
            {
                return $.growl.warning({
                title: "Notice!",
                message: "Feature Status updated"
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
