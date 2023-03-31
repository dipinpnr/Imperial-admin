@extends('admin.layouts.app')
@section('content')

<style>
input[type="file"] {
  display: block;
}
.imageThumb {
  max-height: 75px;
  border: 2px solid;
  padding: 1px;
  cursor: pointer;
}
.pip {
  display: inline-block;
  margin: 10px 10px 0 0;
}
.remove {
  display: block;
  background: #444;
  border: 1px solid black;
  color: white;
  text-align: center;
  cursor: pointer;
}
.remove:hover {
  background: white;
  color: black;
}
</style>

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
               
               
               @if ($messageq = Session::get('status-error'))
               <div class="alert alert-danger">
                  <p>{{ $messageq }}</p>
               </div>
               @endif


               <form action="{{route('store.store_product')}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
                  @php $user_id = Auth::user()->store_id; @endphp
                  <input type = "hidden" id="store_id" name="store_id" value="{{$user_id}}">
                  <div class="row">
                     <div class="col-md-12">
                        <div class="form-group">
                           <label class="form-label">Product Name *</label>
                           <input type="text" class="form-control" required
                              name="product_name" id="product_name" value="{{old('product_name')}}" placeholder="Product Name">
                        </div>
                     </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label" >Product Description *</label>
                            <!--<textarea class="form-control" id="product_description" required name="product_description" rows="2" cols="3" placeholder="Product Description">{{old('product_description')}}</textarea>-->
                            <textarea class="form-control" required name="product_description" rows="4"  placeholder="Product Description">{{old('product_description')}}</textarea>
                        </div>
                     </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">SKU *</label>
                            <input step="0.01" type="number" class="form-control" required 
                             name="sku"   id="regular_price" value="{{old('regular_price')}}" onkeypress="preventNonNumericalInput(event)" placeholder="SKU"  {{--oninput="regularPriceChange()" --}} >
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Sale Price *</label>
                            <input step="0.01"  type="number" class="form-control" required  onkeypress="preventNonNumericalInput(event)" name="sale_price"  id="sale_price" value="{{old('sale_price')}}" oninput="salePriceChange()"  placeholder="Sale Price">
                        <span style="color:red" id="sale_priceMsg"> </span>
                                </div>
                    </div>

                    <!--<div class="col-md-6">-->
                    <!--    <div class="form-group">-->
                    <!--      <label class="form-label">Tax *</label>-->
                    <!--       <select required  name="tax_id" id="tax_id" class="form-control"  >-->
                    <!--             <option value="">Tax</option>-->
                    <!--            @foreach($tax as $key)-->
                    <!--            <option {{old('tax_id') == $key->tax_id ? 'selected':''}} value="{{$key->tax_id }}"> {{$key->tax_name }} ( {{$key->tax_value}} ) </option>-->
                    <!--            @endforeach-->
                    <!--          </select>-->
                    <!--    </div>-->
                    <!--</div>-->
                     <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Min Stock *</label>
                          <input type="number" required class="form-control" onkeypress="preventNonNumericalInput(event)" name="min_stock" id="min_stock" value="{{old('min_stock' )}}" placeholder="Min Stock">               
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Product Code *</label>
                            <input type="text" required class="form-control" name="product_code" id="product_code" oninput="isCodeAvailable(this.value)" value="{{old('product_code')}}" placeholder="Product Code">
                        <p id="productCodeMsg" style="color:red"></p>
                        </div>
                    </div>
                     <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label" >Food Type * </label>
                            <select name="food_type" required id="foodType" class="form-control"  >
                                 <option value="">Food Type</option>
                                 <option value="1" {{old('food_type') == 1 ? 'selected':''}}>Veg</option>
                                <option value="2" {{old('food_type') == 2 ? 'selected':''}} >Non-Veg</option>
                                 <option value="3" {{old('food_type') == 3 ? 'selected':''}}>Egg</option>
                              {{--   @foreach($category as $key)
                                 <option {{old('product_cat_id') == $key->item_category_id ? 'selected':''}} value="{{ @$key->item_category_id }}">{{ @$key->category_name }}</option>
                                 @endforeach
                                 --}}
                                     

                            </select>
                        </div>
                     </div>
                     <div class="col-md-6">
                                <label class="custom-switch">
                                    <input type="hidden" name="is_must_try" value="0" />
                                    <input type="checkbox" name="is_must_try"  checked value=1 class="custom-switch-input">
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description">Is Must try?</span>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="custom-switch">
                                    <input type="hidden" name="is_must_recommended" value="0" />
                                    <input type="checkbox" name="is_must_recommended"  checked value=1 class="custom-switch-input">
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description">Is Recommended?</span>
                                </label>
                            </div>

                     <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label" >Product Category * </label>
                            <select name="product_cat_id" required id="category" class="form-control"  >
                                 <option value="">Product Category</option>
                                 @foreach($category as $key)
                                 <option {{old('product_cat_id') == $key->item_category_id ? 'selected':''}} value="{{ @$key->item_category_id }}">{{ @$key->category_name }}</option>
                                 @endforeach
                                     

                            </select>
                        </div>
                     </div>

                     <div class="col-md-6">
                      <div class="form-group">
                          <label class="form-label" >Product Sub Category </label>
                          <select name="sub_category_id" id="subcategory" class="form-control"  >
                               <option value="">Product Sub Category</option>
                        
                          </select>
                      </div>
                   </div>

                  <div class="col-md-6">
                      <div class="form-group">
                          <label class="form-label" >Sub Category Level 2 </label>
                          <select name="sub_category_id_lvltwo" id="sub_sub_category" class="form-control"  >
                               <option value="">Sub Category Level 2</option>
                              

                          </select>
                      </div>
                   </div>
                   
                   
                    <div id="attHalfRow500a" class="container"> 
                          <div  id="attHalfSec500a" class="section">
                            <div  class=" row">

                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label class="form-label">Attribute* </label>
                                    <select name="attr_group_id[500][]" onchange="findValue('500a0')"  id="attr_group500a0" class="attr_group form-control attrGroup500 proVariant" >
                                      <option value="">Attribute</option>
                                      @foreach($attr_groups as $key)
                                        <option value="{{$key->attr_group_id}}"> {{$key->group_name}} </option>
                                      @endforeach
                                    </select>
                                  </div> 
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Value* </label>
                                        <select name="attr_value_id[500][]"   id="attr_value500a0" class="attr_value form-control proVariant" >
                                          <option value="">Value</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                          </div>

                          <div class="col-md-2">
                            <div class="form-group">
                                <a  id="addVariantAttr500" onclick="addAttributes('500a',500)" class="text-white mt-2 btn btn-sm btn-secondary">Add More</a>
                            </div>
                          </div>
                        </div>

                  {{--   <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Vendor </label>
                           <select  name="vendor_id" id="vendor_id" class="form-control"  >
                                 <option value="">Vendor</option>
                                @foreach($agencies as $key)
                                <option {{old('vendor_id') == $key->agency_id ? 'selected':''}} value="{{$key->agency_id }}"> {{$key->agency_name }} </option>
                                @endforeach
                              </select>
                        </div>
                     </div> --}}

                     <div class="col-md-6">
                      <div class="form-group">
                          <label class="form-label">Product Brand </label>
                          <select  name="product_brand" id="product_brand" class="form-control"  >
                                 <option value="">Product Brand</option>
                                @foreach($brands as $brand)
                                <option {{old('product_brand') == $brand->brand_id ? 'selected':''}} value="{{$brand->brand_id }}"> {{$brand->brand_name }} </option>
                                @endforeach
                              </select>

                      </div>
                    </div>

                {{--  <div class="col-md-12">
                        <div class="form-group">
                          <label class="form-label">Global Product </label>
                           <select  name="global_product_id" id="global_product_id" class="form-control select2-show-search" data-placeholder="Global Product"  >
                                 <option value="">Global Product</option>
                                @foreach($global_product as $key)
                                <option {{old('global_product_id') == $key->global_product_id ? 'selected':''}} value="{{$key->global_product_id }}"> {{$key->product_name }} </option>
                                @endforeach
                              </select>
                        </div>
                     </div> --}}

                <div class="col-md-6">
                   <div class="form-group">
                    <div class="BaseFeatureArea"> 
                        {{-- <label class="form-label">Upload Images(1000*800) *</label> --}}
                         <label class="form-label">Upload Images *</label>
                        <input type="file" accept="image/png, image/jpeg, image/jpg" required class="form-control imgValidation" name="product_image[]" multiple="" value="{{old('product_image')}}" placeholder="Product Feature Image">
                        <br>
                     </div>
                     </div>
                    </div>
                  </div>
                  
                    <section style="display:none;" id="varClass"> 
                  <div id="attSec" class="container variantFulClass"> 
                    <p class="h4 ml-2">Add Product Variants </p>
                    <div  id="attRow" class="section">


                      <div style="border: 1px solid #0008ff42;"  class="mt-2 row">

                        <div class="col-md-12">
                          <div class="form-group">
                              <label class="form-label">Variant Name* </label>
                              <input  type="text" class="form-control proVariant"  name="variant_name[]"   id="variant_name0" placeholder="Variant Name">
                          </div>
                      </div>

                        <div id="attHalfRow0a" class="container"> 
                          <div  id="attHalfSec0a" class="section">
                            <div  class=" row">

                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label class="form-label">Attribute* </label>
                                    <select name="attr_group_id[0][]" onchange="findValue('0a0')"  id="attr_group0a0" class="attr_group form-control attrGroup0 proVariant" >
                                      <option value="">Attribute</option>
                                      @foreach($attr_groups as $key)
                                      <option value="{{$key->attr_group_id}}"> {{$key->group_name}} </option>
                                            @endforeach
                                    </select>
                                  </div> 
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Value* </label>
                                        <select name="attr_value_id[0][]"   id="attr_value0a0" class="attr_value form-control proVariant" >
                                          <option value="">Value</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                          </div>

                          <div class="col-md-2">
                            <div class="form-group">
                                <a  id="addVariantAttr0" onclick="addAttributes('0a',0)" class="text-white mt-2 btn btn-sm btn-secondary">Add More</a>
                            </div>
                          </div>
                        </div>



                      <!--  <div class="col-md-6">-->
                      <!--    <div class="form-group">-->
                      <!--        <label class="form-label">MRP* </label>-->
                      <!--        <input step="0.01" type="number" class="form-control proVariant " onkeypress="preventNonNumericalInput(event)"   oninput="regularPriceChangeVar(0)"  -->
                      <!--        name="var_regular_price[]"   id="var_regular_price0" value="" placeholder="MRP">-->
                      <!--    </div>-->
                      <!--</div>-->
                      <input step="0.01" type="hidden" class="form-control proVariant"  onkeypress="preventNonNumericalInput(event)"  
                        name="var_regular_price[]"   id="var_regular_price0" value="0" placeholder="MRP">
                 
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Sale Price* </label>
                                <input step="0.01"  type="number"  class="form-control proVariant" onkeypress="preventNonNumericalInput(event)"  oninput="salePriceChangeVar(0)"
                                name="var_sale_price[]"  id="var_sale_price0" value="" placeholder="Sale Price">
                                <span style="color:red" id="sale_priceMsg0"> </span>
                            </div>
                        </div>
                        <input type="hidden" id="cval0" value="0">

                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="form-label">Upload Images*</label>
                            <input type="file" id="fileInput" accept="image/png, image/jpeg, image/jpg"  multiple class="form-control proVariant imgValidation" name="var_images[0][]" >
                          </div>
                      </div>
                   </div>

                   
                 </div>
                 <div class="col-md-2">
                  <div class="form-group">
                      <a  id="addVariant" class="text-white mt-2 btn btn-raised btn-success">Add More</a>
                  </div>
                  </div>
                </div>

                 </section>

                  <a class="btn btn-primary btn-raised text-white mt-2" id="btnAddVar" onclick="showVariant()"> Add Variant </a>


                  </div>
                <br>
              <div class="row">
                      <div class="col-md-12">
                     <div class="form-group">
                     <center>
                            <button type="submit" onclick="submitForm()" id="submit" class="btn btn-raised btn-info">
                           Submit</button>
                           {{-- <button type="reset" class="btn btn-raised btn-success">
                           Reset</button> --}}
                           <a class="btn btn-danger" href="{{ route('store.list_product') }}">Cancel</a>
                       </center>
                     </div>
                  </div>
                </div>
       </form>
            <script src="{{ asset('vendor\unisharp\laravel-ckeditor/ckeditor.js')}}"></script>
            <script> 
            // CKEDITOR.replace('product_description');
            
         
            </script>
            <script>//CKEDITOR.replace('product_specification');</script>
             <script>//CKEDITOR.replace('product_delivery_info');</script>
             <script>//CKEDITOR.replace('product_delivery_info');</script>
            <script>//CKEDITOR.replace('product_shipping_info');</script>


      </div>
   </div>
</div>
</div>
@endsection

{{-- .varient {
  background-color: #EDEDFD;
  border: 1px  grey;
} --}}

 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
</script>

<script>



function preventNonNumericalInput(e) {
  e = e || window.event;
  var charCode = (typeof e.which == "undefined") ? e.keyCode : e.which;
  var charStr = String.fromCharCode(charCode);

  if (!charStr.match(/^[0-9]+$/))
    e.preventDefault();
}



function submitForm(){
  // alert("form subm");
  var fd = new FormData();
  var files = $('#fileInput')[0].files;

//   console.log(fd);
//   console.log(files);
  // return false;

  // var _token = $('input[name="_token"]').val();
  //   $.ajax({
  //       url:"{{ route('store.stock_reset') }}",
  //       method:"POST",
  //       data:{product_varient_id:product_varient_id, _token:_token},
  //       success:function(result)
  //       {
  //         //alert(result);
  //         if(result == 0)
  //         { 
  //           $('#td'+product_varient_id).html('Empty');
  //         $("#stock_id"+product_varient_id).val('');
  //              var $el = $("#td"+product_varient_id),
  //                   x = 400,
  //                   originalColor = $el.css("background-color");

  //               $el.css("background", "#4871cc9c");
  //               setTimeout(function(){
  //                 $el.css("background-color", originalColor);
  //               }, x);
  //         }
  //       }
  //   });
    
}


function isCodeAvailable(value)
{
            var _token= $('input[name="_token"]').val();
            var store_id = $('#store_id').val();
        $.ajax({
          type:"GET",
          url:"{{ url('product/ajax/is-code-available') }}?product_code="+value,
          data: {store_id: store_id},


          success:function(res){
                if(res == 1)
                {
                   $('#productCodeMsg').text('Product code exists'); 
                   $('#submit').hide();
                }
                else{
                   $('#productCodeMsg').text(''); 
                   $('#submit').show();

                }
          }

        });
}

function showVariant(){

      if($("#varClass").is(":visible"))
      {
            $("#varClass").hide();
            $("#btnAddVar").text('Add Variant');
      }
      else
      {
        let firstAttrGrp = $('#attr_group500a0').val();
        let firstAttrVal = $('#attr_value500a0').val();

        if((firstAttrGrp != '' ) && (firstAttrVal != '')){
          $("#varClass").show();
          $("#btnAddVar").text('Hide Variant');
        }else{
          alert("Base attribute and value can't be empty! ");
        }
      }

}


function regularPriceChange(){
    salePriceChange();
}

function salePriceChange()
{
    let salePrice = $('#sale_price').val();
    let regularPrice = $('#regular_price').val();
    
    if(parseFloat(salePrice) < 0)
    {
            $('#sale_price').val(0);
    }
    
    if(parseFloat(regularPrice) < 0)
    {
            $('#regular_price').val(0);
    }
    
    
    if(salePrice !== "")
    {
        
       
    }
    else
    {
        $('#sale_priceMsg').html('');
        $("#submit").attr("disabled", false);

    }
}


function regularPriceChangeVar(p){
    salePriceChangeVar(p);
}

function salePriceChangeVar(p)
{
    let salePrice = $('#var_sale_price'+p).val();
    let regularPrice = $('#var_regular_price'+p).val();
    
    if(parseFloat(salePrice) < 0)
    {
            $('#var_sale_price'+p).val(0);
    }
    
    if(parseFloat(regularPrice) < 0)
    {
            $('#var_regular_price'+p).val(0);
    }
    
    
    if(salePrice !== "")
    {
        
        
    }
    else
    {
        $('#sale_priceMsg'+p).html('');
        $("#submit").attr("disabled", false);

    }
}


      var xx = 1; 

  function addAttributes(att_id_val,mainKey){
      
  // alert(mainKey);
  
  var ek = $('.attrGroup'+mainKey).map((_,el) => el.value).get()

// console.log(ek,att_id_val)
      var wrapper      = $("#attHalfSec"+att_id_val); 
      var add_button      = $("#addVariantAttr"+att_id_val); 
     // alert(wrapper);
           var attid = att_id_val + xx;
           var attid_2 = att_id_val + (xx -1);
      
// console.log('#attr_group'+attid_2);
console.log(att_id_val,mainKey,xx,attid,$('#attr_group'+attid_2).val());
console.log(mainKey+"a"+xx);

let prAttrValue = $('#attr_value'+attid_2).val();

  let prevAttrVal = $('#attr_group'+attid_2).val();
  if(prevAttrVal != "" && prAttrValue != ''){
      
   
        $(".attrGroup"+mainKey).attr('readonly',true);
        
        // $("#attr_group"+att_id_val+(xx - 1)).prop('disabled', true);

            var id_number = parseInt(att_id_val. replace(/[^0-9. ]/g, ""));
          
          $(wrapper).append('<div  class="row"><div class="col-md-6"><div class="form-group"><label class="form-label">Attribute * </label><select required name="attr_group_id['+mainKey+'][]" onchange="findValue(\''+attid+'\')"id="attr_group'+attid+'" class="attr_group attrGroup'+id_number+' form-control" ><option value="">Attribute</option>@foreach($attr_groups as $key)<option  value="{{$key->attr_group_id}}"> {{$key->group_name}} </option>@endforeach</select></div></div><div class="col-md-6"><div class="form-group"><label class="form-label">Value * </label><select required name="attr_value_id['+mainKey+'][]"   id="attr_value'+attid+'" class="attr_value form-control" ><option value="">Value</option></select></div></div><a href="#" onclick="removeAttrRow('+mainKey+','+xx+')" class="remove_field ml-5 btn btn-info btn btn-sm">Remove</a></div>'); //add input box
           ++xx;
 
      for(let i=0;i< ek.length; i++){
        $(".attrGroup"+mainKey+" option[value="+ek[i]+"]").hide();
        
      }
    }else{
        alert("Previous attribute empty");
    }
      
      $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); 
      });

  }

  function removeAttrRow(x,y){
      console.log(x,y);
    //   console.log(".attrGroup"+x+"a"+(y-1));
              $("#attr_group"+x+"a"+(y-1)).attr('readonly', false);

  }


  $(document).ready(function() {
      var wrapper      = $("#attRow"); //Fields wrapper
      var add_button      = $("#addVariant"); //Add button ID
      var x = 0; //initlal text box count
      $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        //max input box allowed
          x++; //text box increment
          var attr_id_div = x+'a0';
          $(wrapper).append('<div style="border: 1px solid #0008ff42;" class="mt-2 row"><div class="col-md-12"><div class="form-group"><label class="form-label">Variant Name </label><input  type="text" class="form-control"  name="variant_name[]"   id="variant_name'+x+'" placeholder="Variant Name"></div></div><div id="attHalfRow'+x+'a" class="container"> <div  id="attHalfSec'+x+'a" class="section"><div  class=" row"><div class="col-md-6"><div class="form-group"><label class="form-label">Attribute *</label><select required name="attr_group_id['+x+'][]" onchange="findValue(\''+attr_id_div+'\')"  id="attr_group'+attr_id_div+'" class="attr_group attrGroup'+x+' form-control" ><option value="">Attribute</option>@foreach($attr_groups as $key)<option value="{{$key->attr_group_id}}"> {{$key->group_name}} </option>@endforeach</select></div></div><div class="col-md-6"><div class="form-group"><label class="form-label">Value *</label><select required name="attr_value_id['+x+'][]"   id="attr_value'+attr_id_div+'" class="attr_value form-control" ><option value="">Value</option></select></div></div></div></div><div class="col-md-2"><div class="form-group"><a  id="addVariantAttr'+x+'" onclick="addAttributes(\''+x+'a\','+x+')" class="text-white mt-2 btn btn-sm btn-secondary">Add More</a></div></div></div> <div class="col-md-6"><div class="form-group"><label class="form-label">MRP </label><input step="0.01" type="number" onkeypress="preventNonNumericalInput(event)" class="form-control"  oninput="regularPriceChangeVar('+x+')"  name="var_regular_price[]"   id="var_regular_price'+x+'"   placeholder="MRP"></div></div><div class="col-md-6"><div class="form-group"><label class="form-label">Sale Price </label><input step="0.01"  onkeypress="preventNonNumericalInput(event)" type="number" class="form-control"  name="var_sale_price[]"  id="var_sale_price'+x+'" oninput="salePriceChangeVar('+x+')" placeholder="Sale Price"><span style="color:red" id="sale_priceMsg'+x+'"> </span></div></div><input type="hidden" id="cval'+x+'" value="'+x+'"><div class="col-md-12"><div class="form-group"><label class="form-label">Upload Images*</label><input multiple  type="file" class="form-control imgValidation" accept="image/png, image/jpeg, image/jpg" name="var_images['+x+'][]" ></div></div><a href="#" class="remove_field ml-4 mb-2 btn btn-warning btn btn-sm">Remove</a></div>'); //add input box
      });
      
      $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); x--;
      })
  });


$(document).ready(function () {
    $("div#service_type_id").hide();
   
});

    $(document).ready(function () {
        $('#variant_name0').on('input', function() {
            let attVal = $("#variant_name0").val();
            if(attVal == '')
            {
                $(".proVariant").prop('required',false); 
            }
            else
            {
                $(".proVariant").prop('required',true); 
            }
        });
    });


function proTypeChanged(val)
{
  if(val == 2){
    $("div#service_type_id").show();
    $(".proVariant").prop('required',false); 
    $("#service_type_input").prop('required',true);
    $("div#attSec").hide();
    $('#service_type_input').prop('selectedIndex',0);


  }
  else{
    $(".proVariant").prop('required',false); // edited after call
    $("#service_type_input").prop('required',false);
    $("div#service_type_id").hide();
    $("div#attSec").show();
    $("#btnAddVar").show();

 }
}
var vc = 1;
function servTypeChanged(v){
  if(vc != 1){
    if(v == 1){
      $("div#attSec").hide();
      $("#btnAddVar").hide();

      $(".proVariant").prop('required',false);
   

    }
    else{
      $("div#attSec").show();
      $(".proVariant").prop('required',false);  // edited after call
      $("#btnAddVar").show();

    }
  }
  vc++;
}



</script>


<script type="text/javascript">


$(document).ready(function() {
   var wrapper      = $(".BaseFeatureArea"); //Fields wrapper
  var add_button      = $(".addBaseFeatureImage"); //Add button ID

  var x = 1; //initlal text box count
  $(add_button).click(function(e){ //on add input button click
    e.preventDefault();
    //max input box allowed
      x++; //text box increment
      $(wrapper).append('<div>  <input type="file" class="form-control imgValidation" accept="image/png, image/jpeg, image/jpg" name="product_image[]"  placeholder="Base Product Feature Image" /> <a href="#" class="remove_field btn btn-small btn-danger">Remove</a></div>'); //add input box

  });

  $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
    e.preventDefault(); $(this).parent('div').remove(); x--;
  })
});

$(document).ready(function() {

var agsc = 0;

       $('#vendor_id').change(function(){
       if(agsc != 0)
       { 
        var vendor_id = $(this).val();

        var _token= $('input[name="_token"]').val();
        $.ajax({
          type:"GET",
          url:"{{ url('store/product/ajax/global_product') }}?vendor_id="+vendor_id,


          success:function(res){
            if(res){
            $('#global_product_id').prop("diabled",false);
            $('#global_product_id').empty();
            $('#global_product_id').append('<option value="">Global Product</option>');
            $.each(res,function(global_product_id,product_name)
            {
              $('#global_product_id').append('<option value="'+global_product_id+'">'+product_name+'</option>');
            });

            }else
            {
              $('#global_product_id').empty();

            }
            }

        });
       }
       else
       {
         agsc++;
       }
      });

    });


 //$(document).ready(function() {

   function findValue(c){

    //$('#attr_group'+c).change(function(){
      // alert(c);
       var attr_group_id = $('#attr_group'+c).val();

       var _token= $('input[name="_token"]').val();

       $.ajax({
         type:"GET",
         url:"{{ url('store/product/ajax/get_attr_value') }}?attr_group_id="+attr_group_id,

         success:function(res){
           console.log(res);
           if(res){
            $('#attr_value'+c).prop("diabled",false);
            $('#attr_value'+c).empty();
            $('#attr_value'+c).append('<option value="">Value</option>');
            $.each(res,function(attr_value_id,group_value)
            {
              $('#attr_value'+c).append('<option value="'+attr_value_id+'">'+group_value+'</option>');
            });

            }else
            {
              $('#attr_value'+c).empty();

            }
           }

       });
      
    // });


   }


    
    //});
</script>



<script type="text/javascript">
$.ajaxSetup({
headers: {
'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
}
});
$(document).ready(function () {
$('#category').on('change',function(e) {
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

<script type="text/javascript">
$.ajaxSetup({
headers: {
'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
}
});
$(document).ready(function () {
$('#subcategory').on('change',function(e) {
var subcat_id = e.target.value;
$.ajax({
url:"{{ route('subsubcat') }}",
type:"POST",
data: {
_token : "{{ csrf_token() }}",   
subcat_id: subcat_id
},
success:function (data) {   
$('#sub_sub_category').empty();
$('#sub_sub_category').append('<option value="">Select Subcategory</option>');
$.each(data.subsubcategories,function(index,subsubcategory){
$('#sub_sub_category').append('<option value="'+subsubcategory.item_sub_category_id+'">'+subsubcategory.sub_category_name+'</option>');
})
}
})
});
});
</script>

