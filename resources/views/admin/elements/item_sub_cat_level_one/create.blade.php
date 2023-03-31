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
               <form action="{{route('admin.store_item_sub_category')}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
                  <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Parent Category *</label>
                           <select required class="form-control" name="category_id" id="category_id">
                              <option value="">Parent Category</option>
                                 @foreach ($categories as $key)
                                 <option {{old('category_id') == $key->item_category_id ? 'selected':''}} value=" {{ $key->item_category_id}} "> {{ $key->category_name }}</option>
                                 @endforeach
                           </select>
                        </div>
                     </div> 
                     <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Parent Sub Category *</label>
                           <select  class="form-control" name="subcategory_id" id="subcategory">
                              <option value="">Parent Sub Category</option>
                           </select>
                        </div>
                     </div> 

                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Sub Category *</label>
                           <input required type="text" class="form-control" name="sub_category_name" value="{{old('sub_category_name')}}" placeholder="Sub Category">
                        </div>
                     </div>

                    
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Sub Category Icon [150*150] *</label>
                           <input  type="file" required class="form-control" accept="image/x-png,image/jpg,image/jpeg" 
                           name="sub_category_icon" value="{{old('sub_category_icon')}}" placeholder="Sub Category Icon">
                        </div>
                     </div>

                     <div class="col-md-12">
                        <div class="form-group">
                           <label class="form-label">Sub Category Description *</label>
                           <textarea  required class="form-control" id="sub_category_description" name="sub_category_description" rows="4" placeholder="Sub Category Description">{{old('sub_category_description')}}</textarea>
                        </div>
                        <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Add</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-danger" href="{{ route('admin.item_sub_category') }}">Cancel</a>
                           </center>
                        </div>
                     </div>
                  </div>
                  {{-- <script src="{{ asset('vendor\unisharp\laravel-ckeditor/ckeditor.js')}}"></script> --}}
                  {{-- <script>CKEDITOR.replace('sub_category_description');</script> --}}
               </form>

      </div>
   </div>
</div>
</div>
</div>
<script type="text/javascript">
$.ajaxSetup({
headers: {
'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
}
});
$(document).ready(function () {
$('#category_id').on('change',function(e) {
var cat_id = e.target.value;
$.ajax({
url:"{{ route('subcat') }}",
type:"POST",
data: {
_token : "{{ csrf_token() }}",   
cat_id: cat_id
},
success:function (data) {   
$('#subcategory').empty();
$('#subcategory').append('<option value="">Select Subcategory</option>');
$.each(data.subcategories,function(index,subcategory){
$('#subcategory').append('<option value="'+subcategory.item_sub_category_id+'">'+subcategory.sub_category_name+'</option>');
})
}
})
});
});
</script>
@endsection
