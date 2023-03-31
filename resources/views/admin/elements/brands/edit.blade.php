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

                    
                    <form action="{{route('admin.update_brand',$brand->brand_id)}}" method="POST" enctype="multipart/form-data">
                    @csrf

                    @if(isset($brand->brand_icon))
                    <img class="m-2" src="{{URL::to('assets/uploads/brand_icon/'.$brand->brand_icon)}}"  width="50" >
                    @endif
                    <div class="row">
                       

                        <div class="col-md-6">
                            <div class="form-group">
                            <label class="form-label">Brand Name(*)</label>
                            <input type="text" required class="form-control" name="brand_name" value="{{old('brand_name',$brand->brand_name)}}" placeholder="brand">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                            <label class="form-label">Brand Icon(*)</label>
                            <input type="file" class="form-control" accept="image/x-png,image/jpg,image/jpeg" 
                            name="brand_icon" value="{{old('brand_icon')}}" placeholder="brand Icon">
                            </div>
                        </div>
                        

                            

                              <div class="col-md-6">
                            <div class="form-group">
                            <label class="form-label">Select Category(*)</label>
                            <div id="Category">
                            <select name="category[]" required="" class="form-control" >
                            <option value=""> Select Category</option>
                             @foreach($category as $key)

                             <option  value="{{$key->item_category_id}}"> {{$key->category_name }} </option>
                             @endforeach
                            </select>
                            </div>
                            </div>
                            </div>
                            <div class="col-md-2">
                            <div class="form-group">
                            <label class="form-label">Add more</label>
                            <button type="button" id="addCategory" class="btn btn-raised btn-success"> Add More</button>
                            </div>
                            </div>
                       

                            <div class="col-md-2">
                                <label class="custom-switch">
                                    <input type="hidden" name="is_active" value=0 />
                                    <input type="checkbox" name="is_active" @if ($brand->is_active == 1)
                                    checked
                                    @endif   value=1 class="custom-switch-input">
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description">Status</span>
                                </label>
                            </div>
                            @php 
                             $i = 0;
                             $k = 0;
                             $usedAttr = array();
                         @endphp
                       @if(!empty($brand_cats))

                            <div class="col-md-12">

                                    <div class="row">
                                    <div class="table-responsive ">
                                    <table id="attrTable"   class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                            <th class="wd-15p">SL.No</th>
                                            <th class="wd-15p">{{ __('Categories') }}</th>
                                            
                                                <th  class="wd-20p">{{__('Action')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="col-lg-12 col-xl-12 p-1">
                                            
                                                @foreach ($brand_cats as $val)
                                                @php
                                                $k++;
                                                @endphp
                                                @endforeach
                                        </tbody>

                                                @foreach ($brand_cats as $val)
                                                @php
                                                $i++;
                                                $category_name = \DB::table('mst__item_categories')->where('item_category_id',$val->item_category_id)->pluck('category_name');
                                                $usedAttr[] = $val->item_category_id;
                                                @endphp
                                                <tr id="trId{{$val->id}}">
                                                    <td>{{$i}}</td>
                                                    <td>{{@$category_name[0]}}
                                                        <input type="hidden" class="attrGroup500" value="{{$val->id}}" />
                                                    </td>
                                                    <td>
                                                        <!--<form id="removeVarAttr" action="{{route('store.destroy_product_var_attr',$val->id)}}" method="POST">-->
                                                            @csrf
                                                            @method('POST')
                                                            <a type="submit" onclick="return confirm('Are you sure?')" href="{{route('admin.delete_brand_category', $val->id)}}"
                                                                class="btn btn-sm btn-danger text-white">Delete</a>
                                                            <!--@if(($i == 1) && ($k == 1))-->
                                                            <!--@else-->
                                                            <!--    onclick="removeTableRowTr({{$val->variant_attribute_id}})"  -->
                                                            <!--@endif-->
                                                            
                                                        <!--</form>-->
                                                    </td>
                                                </tr>
                                                @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                    </div>
                                    </div>
                                    @endif


                                                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <center>
                                    <button type="submit" class="btn btn-raised btn-primary">
                                    <i class="fa fa-check-square-o"></i> Update</button>
                                    <button type="reset" class="btn btn-raised btn-success">Reset</button>
                                    <a class="btn btn-danger" href="{{ route('admin.brands') }}">Cancel</a>
                                    </center>
                                </div>
                            </div>
                    </div>
                    {{-- <script src="{{ asset('vendor\unisharp\laravel-ckeditor/ckeditor.js')}}"></script>
                    <script>CKEDITOR.replace('brand_description');</script> --}}
                </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function() {
    var wrapper      = $("#Category"); //Fields wrapper
    var add_button   = $("#addCategory"); //Add button ID

    var x = 1; //initlal text box count


    $(add_button).click(function(e){ //on add input button click
    e.preventDefault();
    //max input box allowed
    x++; //text box increment
    $(wrapper).append('<div> <br> <label class="form-label"> Select Category</label><select name="category[]" required="" class="form-control" >@foreach($category as $key)<option  value="{{$key->item_category_id}}"> {{$key->category_name }} </option>@endforeach</select> <a href="#" class="remove_field btn btn-info btn btn-sm">Remove</a></div>'); //add input box

    });



    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
    e.preventDefault(); $(this).parent('div').remove(); x--;
    })
    });


</script>
@endsection
