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

                            <div id="example_tbody" class="table-responsive">
                                <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th class="wd-15p">SL.No</th>
                                            <th class="wd-15p">{{ __('Product Name') }}</th>
                                            <th class="wd-15p">{{ __('Customer Name') }}</th>
                                            <th class="wd-15p">{{ __('Ratings') }}</th>
                                            <th class="wd-15p">{{ __('Review') }}</th>
                                            <th class="wd-15p">{{ __('Review Date') }}</th>
                                            <th class="wd-20p">{{__('Status')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody >
                                        @foreach ($ratings as $row)
                                         <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ @$row->productVariantData->variant_name}}</td>
                                            <td>{{ @$row->customerData->customer_first_name}}</td>
                                            <td>{{ @$row->rating}}</td>
                                            <td>{{ @$row->review}}</td>
                                            <td>{{ @$row->created_at->diffForHumans()}}</td>
                                            <td>
                                                <a style="color:white;" id="statusBtn{{$row->reviews_id}}" 
                                                    onclick="changeStatus({{$row->reviews_id}})"  
                                                    class="btn btn-sm @if($row->isVisible == 1) btn-danger @else btn-success @endif"
                                                > 
                                                    @if($row->isVisible == 0)
                                                        Approve
                                                    @else
                                                        Disapprove
                                                    @endif
                                                </a>
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

           
<script>
function changeStatus(item_row_id)
{
   // $('#loaderCard').show();
  //  $('#example_tbody').hide();
  var stat = 0;
    var _token= $('input[name="_token"]').val();
    $.ajax({
        type:"GET",
        url:"{{ url('admin/ajax/change-status/review') }}?review_id="+item_row_id ,
        success:function(res){
            if(res == "active"){
                stat = 0;
                $("#statusBtn"+item_row_id).removeClass("btn-success");
                $("#statusBtn"+item_row_id).addClass("btn-danger");
                $( "#statusBtn"+item_row_id ).empty();
                $( "#statusBtn"+item_row_id ).text('Disapprove');
            }
            else
            {
                stat = 1;
                $("#statusBtn"+item_row_id).removeClass("btn-danger");
                $("#statusBtn"+item_row_id).addClass("btn-success");
                $( "#statusBtn"+item_row_id ).empty();
                $( "#statusBtn"+item_row_id ).text('Approve');
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
