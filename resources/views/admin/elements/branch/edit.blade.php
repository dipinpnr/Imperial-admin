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
               @if ($message = Session::get('status'))
               <div class="alert alert-success">
                  <p>{{ $message }}</p>
               </div>
               @endif
            </div>
                <div class="col-lg-12">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form action="{{route('admin.update_branch',$branch->branch_id)}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                            <label class="form-label">Branch Name(*)</label>
                            <input type="text" class="form-control" name="name" value="{{old('name',$branch->branch_name)}}" placeholder="Branch Name">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                            <label class="form-label">Branch Code(*)</label>
                            <input type="text" class="form-control" name="code" value="{{old('code',$branch->branch_code)}}" placeholder="Branch Code">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                            <label class="form-label">Contact Person(*)</label>
                            <input type="text" class="form-control" name="branch_contact_person" value="{{old('branch_contact_person',$branch->branch_contact_person)}}" placeholder="Contact Person">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                            <label class="form-label">Contact Number(*)</label>
                            <input type="text" onkeypress='validate(event)' class="form-control" name="branch_contact_number" value="{{old('branch_contact_number',$branch->branch_contact_number)}}" placeholder="Contact Number">
                            </div>
                        </div>
                         <div class="col-md-6">
                            <div class="form-group">
                            <label class="form-label">Whatsapp Number(*)</label>
                            <input type="text" onkeypress='validate(event)' class="form-control" name="whatsapp_number" value="{{old('whatsapp_number',$branch->whatsapp_number)}}" placeholder="Whatsapp Number">
                            </div>
                        </div>
        
                        <div class="col-md-6">
                            <div class="form-group">
                            <label class="form-label">E-mail(*)</label>
                            <input type="text" class="form-control" name="email" value="{{old('email',$branch->branch_email)}}" placeholder="E-mail">
                            </div>
                        </div>
                        
                         <div class="col-md-6">
                            <div class="form-group">
                            <label class="form-label">Working Hours- Opening(*)</label>
                            <input type="time" class="form-control" name="working_hours_from" value="{{old('working_hours_from',$branch->working_hours_from)}}" placeholder="Working Hours">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                            <label class="form-label">Working Hours- Closing(*)</label>
                            <input type="time" class="form-control" name="working_hours_to" value="{{old('working_hours_to',$branch->working_hours_to)}}" placeholder="Working Hours">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                            <label class="form-label">Location(*)</label>
                            <select class="form-control" name="location" onchange="getAreas(this.value,{{$branch->branch_id}})">
                                <option value="">Select Location</option>
                                @foreach($cities as $city)
                                <option value="{{$city->city_id}}" {{$branch->city_id == $city->city_id ? 'selected' : '' }}>{{$city->city_name}}</option>
                                @endforeach
                            </select>
                            </div>
                        </div>
                       
                       
                        <div class="col-md-6">
                            <div class="form-group">
                            <label class="form-label">Address(*)</label>
                            <textarea value="{{old('address',$branch->address)}}" class="form-control" name="address">{{$branch->branch_address}}</textarea>
                            </div>
                        </div>
                       
                       
                        
                        <div class="col-md-6">
                            <div class="form-group">
                            <label class="form-label">Delivery Areas(*)</label>
                          <select class="area" id="area" name="deliveryareas[]" style="width: 100%" multiple placeholder="Select Delivery Areas" autocomplete="off">
                                @foreach($areas as $area)
                                <option value="{{$area->area_id}}" selected="selected">{{$area->area_name}}</option>
                                @endforeach
                                 </select>
                        </div>
                        </div>
                        

                            <div class="col-md-2">
                                <label class="custom-switch">
                                    <input type="hidden" name="is_active" value=0 />
                                    <input type="checkbox" name="is_active" {{$branch->branch_status == "1" ?  "checked" : ""}} value=1 class="custom-switch-input">
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description">Status(*)</span>
                                </label> 
                            </div> 
                        </div>
                          
                        <div class="form-group">
                            <center>
                            <button type="submit" class="btn btn-raised btn-primary">
                            <i class="fa fa-check-square-o"></i> Update</button>
                            <button type="reset" class="btn btn-raised btn-success">Reset</button>
                            <a class="btn btn-danger" href="{{ route('admin.branch') }}">Cancel</a>
                            </center>
                        </div>

                        </div>

                        
                    </div>
                    {{-- <script src="{{ asset('vendor\unisharp\laravel-ckeditor/ckeditor.js')}}"></script>
                    <script>CKEDITOR.replace('category_description');</script> --}}
                </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function validate(evt) {
                        var theEvent = evt || window.event;
                        var key = theEvent.keyCode || theEvent.which;
                        key = String.fromCharCode( key );
                        var regex = /[0-9]|\./;
                        if( !regex.test(key) ) {
                           theEvent.returnValue = false;
                           if(theEvent.preventDefault) theEvent.preventDefault();
                        }
                        }
function getAreas(value,branch)
{
$('.area').select2({
  ajax: {
    url: 'get-delivery-areas/'+value+'/'+branch,
    processResults: function (data) {
      return {
        results: $.map(data, function (item) {
                    return {
                        text: item.area_name,
                        id: item.area_id
                    }
             })
             
      };
    }
  },
  allowClear: true,
  placeholder: "Select an Area"
});
}
</script>    

@endsection
