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
                {{-- $2y$10$uMQYs1ukF.C0eU2aiZ2d8un57xCNl7TPKzSg.2u0P3GFStR9iXK/y

                $2y$10$uMQYs1ukF.C0eU2aiZ2d8un57xCNl7TPKzSg.2u0P3GFStR9iXK/y

                $2y$10$uMQYs1ukF.C0eU2aiZ2d8un57xCNl7TPKzSg.2u0P3GFStR9iXK/y --}}
                
                <div class="card-body border">
                <form action="{{route('admin.customers')}}" method="GET" 
                         enctype="multipart/form-data">
                   @csrf
            <div class="row">
               <div class="col-md-4">
                  <div class="form-group">
                     <label class="form-label">Customer Name</label>
                       <input type="text" class="form-control" 
                       name="customer_name"  value="{{ request()->input('customer_name') }}" placeholder="Customer Name">

                  </div>
               </div>
                <div class="col-md-4">
                  <div class="form-group">
                     <label class="form-label">Customer Mobile</label>
                       <input type="text" class="form-control" 
                       name="customer_mobile"  value="{{ request()->input('customer_mobile') }}" placeholder="Customer Mobile">

                  </div>
               </div>
                 <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label"> Status</label>
                     <select name="status" id="status"  class="form-control" >
                 <option value="" >Select Status</option>
                 <option {{request()->input('status') == '1' ? 'selected':''}} value="1" >Active</option>
                 <option {{request()->input('status') == '0' ? 'selected':''}} value="0" >In Active</option>  
                 </select>
                  </div>
               </div> 
             </div>
                <div class="row">
              </div>
                     <div class="col-md-12">
                     <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Filter</button>
                           {{-- <button type="reset" class="btn btn-raised btn-success">Reset</button> --}}
                          <a href="{{route('admin.customers')}}"  class="btn btn-info">Cancel</a>
                           </center>
                        </div>
                  </div>
                </div>
                   </form>
                </div>
             
         
               <div class="card-body">
                   
                <a href=" {{route('admin.create_customer')}}" class="btn btn-block btn-info">
                    <i class="fa fa-plus"></i> Create Customer 
                </a> <br>   
                <div class="table-responsive">
                  <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                      <tr>
                        <th class="wd-15p">S.No</th>
                        <th class="wd-15p">Name</th>
                        <th class="wd-15p">Mobile</th>
                        <th class="wd-15p">Status</th>
                      
                       <th class="wd-15p">{{__('Action')}}</th>

                      </tr>
                    </thead>
                    <tbody>
                      @php
                      $i = 0;
                      @endphp
                      @foreach ($customers as $row)
                      <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{$row->customer_first_name}} </td>
                        <td>{{$row->customer_mobile_number}} </td>

                        <td>
                          <a style="color:white;" id="statusBtn{{$row->customer_id}}" 
                              onclick="changeStatus({{$row->customer_id}})"  
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
                            <form action="{{route('admin.destroy_customer',$row->customer_id)}}" method="POST">
                                <a class="btn btn-sm btn-cyan" href="{{url('admin/customer/edit/'.$row->customer_id)}}">Edit</a>
                                <a  class="text-white btn btn-sm btn-info" data-toggle="modal" data-target="#viewModal{{$row->customer_id}}" > View</a>
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



   @foreach($customers as $row)
            <div class="modal fade" id="viewModal{{$row->customer_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
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
                                    <td><h6>Customer name: </td><td> {{ $row->customer_first_name }}</h6></td>
                                 </tr>

                                 <tr>
                                  <td><h6>Customer mobile: </td><td> {{ $row->customer_mobile_number }}</h6></td>
                                  </tr>

                                  <tr>
                                    <td><h6>Customer email: </td><td> {{ $row->customer_email }}</h6></td>
                                  </tr>

                                  <tr>
                                    <td><h6>Customer gender: </td><td> {{ $row->gender }}</h6></td>
                                  </tr>

                                  <tr>
                                    <td><h6>Customer dob: </td><td> {{ $row->dob }}</h6></td>
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
        url:"{{ url('admin/ajax/change-status/customer') }}?customer_id="+item_row_id ,
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
