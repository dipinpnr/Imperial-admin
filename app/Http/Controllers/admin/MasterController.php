<?php

namespace App\Http\Controllers\admin;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Image;
use Hash;
use DB;
use Carbon\Carbon;
use Crypt;

use App\Http\Controllers\Controller;
use App\Models\admin\Mst_AttributeGroup;
use App\Models\admin\Mst_AttributeValue;
use App\Models\admin\Mst_Brand;
use App\Models\admin\Mst_Coupon;
use App\Models\admin\Mst_CustomerBanner;
use App\Models\admin\Mst_DeliveryBoy;
use App\Models\admin\Mst_Issue;
use App\Models\Mst_delivery_area;
use App\Models\Mst_branch;
use App\Models\Sys_city;
use App\Models\admin\Mst_ItemCategory;
use App\Models\admin\Mst_ItemLevelTwoSubCategory;
use App\Models\admin\Mst_ItemSubCategory;
use App\Models\admin\Mst_Product;
use App\Models\admin\Mst_Tax;
use App\Models\admin\Mst_Unit;
use App\Models\admin\Sys_IssueType;
use App\Models\admin\Trn_TaxSplit;
use App\Models\admin\Mst_branch_product;
use App\Models\admin\Mst_Brandsubcat;
use App\Models\admin\Mst_Attributecategory;
use Illuminate\Http\Request;

class MasterController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function listDeliveryAreas($id)
    {
        $existing_area = DB::table('trn_branch_deliveryarea')->get()->pluck('area')->all();
        $areas = Mst_delivery_area::active()->where('city_id',$id)->whereNotIn('area_id',$existing_area)->get(['area_id','area_name']);

        return response()->json($areas);
    }
    
    public function updateDeliveryAreas($id,$branch)
    {
        $branch = Mst_branch::where('branch_id', '=', $branch)->first();
        $existing_area = DB::table('trn_branch_deliveryarea')->get()->pluck('area')->all();
        $brancharea= $branch->areas()->pluck('area')->all();
        $data = array_diff($existing_area, $brancharea);
        $areas = Mst_delivery_area::active()->where('city_id',$id)->whereNotIn('area_id',$data)->get(['area_id','area_name']);

        return response()->json($areas);
    }
    
    
    
    
     public function listItemSubCategory(Request $request)
    {
        $pageTitle = "Item Sub Categories ";
        $sub_category = Mst_ItemSubCategory::orderBy('item_sub_category_id', 'DESC')->get();
        return view('admin.elements.item_sub_cat_level_one.list', compact('sub_category', 'pageTitle'));
    }



    public function createItemSubCategory(Request $request)
    {
        $pageTitle = "Create Item Sub Category";
        $categories = Mst_ItemCategory::where('is_active', '=', '1')->orderBy('category_name')->get();
        return view('admin.elements.item_sub_cat_level_one.create', compact('categories', 'pageTitle'));
    }

    public function subcat(Request $request)
    {
        $parent_id = $request->cat_id;
         
        $subcategories = Mst_ItemSubCategory::where('item_category_id',$parent_id)->whereNull('parent_sub_category')->get();
        
       
        return response()->json([
            'subcategories' => $subcategories
        ]);
    }
    public function subCatLvlTwo(Request $request)
    {
        $parent_id = $request->subcat_id;
         
        $subcategories = Mst_ItemSubCategory::where('parent_sub_category',$parent_id)->get();
        
        
        return response()->json([
            'subsubcategories' => $subcategories
        ]);
    }
    

    public function editItemSubCategory(Request $request, $item_sub_category_id)
    {
        $pageTitle = "Edit Item Sub Category";
        $sub_category = Mst_ItemSubCategory::where('item_sub_category_id', '=', $item_sub_category_id)->first();
        $categories = Mst_ItemCategory::where('is_active', '=', '1')->orderBy('category_name')->get();
        $subcategories = Mst_ItemSubCategory::where('is_active', '=', '1')->orderBy('sub_category_name')->get();
        return view('admin.elements.item_sub_cat_level_one.edit', compact('categories','subcategories','sub_category', 'pageTitle'));
    }


    public function addToHomeItemCategory(Request $request)
    {
        $item_category_id = $request->item_category_id;
    
        if ($c = Mst_ItemCategory::findOrFail($item_category_id)) {
            if ($c->added_to_home == 0) {
                Mst_ItemCategory::where('item_category_id', $item_category_id)->update(['added_to_home' => 1]);
                echo "added";
            } else {
                Mst_ItemCategory::where('item_category_id', $item_category_id)->update(['added_to_home' => 0]);
                echo "removed";
            }
        }
    }


    public function storeItemSubCategory(Request $request, Mst_ItemSubCategory $sub_category)
    {
        $data = $request->except('_token');

        $validator = Validator::make(
            $request->all(),
            [
                'category_id'       => 'required',
                'subcategory_id'     => 'nullable',
                'sub_category_name'       => 'required',
                //  'sub_category_icon'        => 'required',
                // 'sub_category_icon'        => 'dimensions:width=150,height=150|image|mimes:jpeg,png,jpg',
                'sub_category_description' => 'required',


            ],
            [
                'category_id.required'         => 'Parent category required',
                'sub_category_name.required'         => 'Sub category name required',
                'sub_category_icon.required'        => 'Sub category icon required',
                'sub_category_icon.dimensions'        => 'Sub category icon dimensions is invalid',
                'sub_category_description.required'     => 'Sub category description required',

            ]
        );

        if (!$validator->fails()) {

            $data = $request->except('_token');

            $sub_category->sub_category_name         = $request->sub_category_name;
            $sub_category->sub_category_name_slug      = Str::of($request->sub_category_name)->slug('-');
            $sub_category->sub_category_description = $request->sub_category_description;
            $sub_category->item_category_id         =  $request->category_id;

            $sub_category->parent_sub_category        =  $request->subcategory_id;
            

            if ($request->hasFile('sub_category_icon')) {
                $file = $request->file('sub_category_icon');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move('assets/uploads/category_icon', $filename);
                $sub_category->sub_category_icon = $filename;
            }


            $sub_category->is_active         = 1;

            $sub_category->save();

            return redirect('/admin/item-sub-category/list')->with('status', 'Sub category added successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    public function updateItemSubCategory(Request $request, $item_sub_category_id)
    {
        $data = $request->except('_token');

        $validator = Validator::make(
            $request->all(),
            [
                'category_id'       => 'required',
                'sub_category_name'       => 'required',
                //  'sub_category_icon'        => 'required',
                // 'sub_category_icon'        => 'dimensions:width=150,height=150|image|mimes:jpeg,png,jpg',
                'sub_category_description' => 'required',


            ],
            [
                'category_id.required'         => 'Parent category required',
                'sub_category_name.required'         => 'Sub category name required',
                'sub_category_icon.required'        => 'Sub category icon required',
                'sub_category_icon.dimensions'        => 'Sub category icon dimensions is invalid',
                'sub_category_description.required'     => 'Sub category description required',

            ]
        );

        if (!$validator->fails()) {

            $data = $request->except('_token');
            $sub_category = Mst_ItemSubCategory::find($item_sub_category_id);
            $sub_category->sub_category_name         = $request->sub_category_name;
            $sub_category->sub_category_name_slug      = Str::of($request->sub_category_name)->slug('-');
            $sub_category->sub_category_description = $request->sub_category_description;
            $sub_category->item_category_id         =  $request->category_id;

            $sub_category->parent_sub_category        =  $request->subcategory_id;
        


            if ($request->hasFile('sub_category_icon'))
             {
                $file = $request->file('sub_category_icon');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move('assets/uploads/category_icon', $filename);
                $sub_category->sub_category_icon = $filename;
            }

            $sub_category->update();

            return redirect('/admin/item-sub-category/list')->with('status', 'Sub category updated successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    public function removeItemSubCategory(Request $request, $item_sub_category_id)
    {
        Mst_ItemSubCategory::where('item_sub_category_id', '=', $item_sub_category_id)->delete();
        return redirect('/admin/item-sub-category/list')->with('status', 'Sub category removed successfully.');
    }


    public function editStatusItemSubCategory(Request $request)
    {
        $item_sub_category_id = $request->item_sub_category_id;
        if ($c = Mst_ItemSubCategory::findOrFail($item_sub_category_id)) {
            if ($c->is_active == 0) {
                Mst_ItemSubCategory::where('item_sub_category_id', $item_sub_category_id)->update(['is_active' => 1]);
                echo "active";
            } else {
                Mst_ItemSubCategory::where('item_sub_category_id', $item_sub_category_id)->update(['is_active' => 0]);
                echo "inactive";
            }
        }
    }

      public function listUnit(Request $request)
    {
        $pageTitle = " Units";
        $units = Mst_Unit::orderBy('unit_id', 'DESC')->get();
        return view('admin.elements.units.list', compact('units', 'pageTitle'));
    }

    public function listArea(Request $request)
    {
        $pageTitle = "Delivery Areas";
        $areas = Mst_delivery_area::orderBy('created_at', 'DESC')->get();
        return view('admin.elements.area.list', compact('areas','pageTitle'));
    }
        
    public function createArea(Request $request)
    {
        $pageTitle = "Add Delivery Area";
        $cities = Sys_city::get(); 
        return view('admin.elements.area.create', compact('cities','pageTitle'));
    }
     
    public function storeArea(Request $request, Mst_delivery_area $row)
    {
        //  dd($request->all());

        $validator = Validator::make(
            $request->all(),
            [
                'name'       => 'required|unique:mst_delivery_areas,area_name',
                'code'       => 'required|unique:mst_delivery_areas,area_code',
                'location'   => 'required'
            ],
            [
                'name.unique'       => "Area  Name already taken",
                'name.required'         => 'Area name required',
                'code.required'         => ' Area Code required',
                'code.unique'       => " Area Code  already taken"
            ]
        );

        if (!$validator->fails()) {
            $data = $request->except('_token');
            $row->area_name         = $request->name;
            $row->area_code         = $request->code;
            $row->city_id           = $request->location;
            $row->is_active = $request->is_active;

            $row->save();
            return redirect('admin/area/list')->with('status', 'Area added successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }
    public function editStatusArea(Request $request)
    {
        $area_id = $request->area_id;
        if ($c = Mst_delivery_area::where('area_id',$area_id)->first()) {
            if ($c->is_active == 0) {
                Mst_delivery_area::where('area_id', $area_id)->update(['is_active' => 1]);
                echo "active";
            } else {
                Mst_delivery_area::where('area_id', $area_id)->update(['is_active' => 0]);
                echo "inactive";
            }
        }
    }
    public function removeArea(Request $request, $area_id)
    {
        Mst_delivery_area::where('area_id', '=', $area_id)->delete();
        return redirect('admin/area/list')->with('status', 'Area deleted successfully.');
    }

    public function editArea(Request $request, $area_id)
    {
        $pageTitle = "Edit Area";
        $area = Mst_delivery_area::where('area_id', '=', $area_id)->first();
        $cities = Sys_city::get();
        return view('admin.elements.area.edit', compact('area','cities', 'pageTitle'));
    }

    public function updateArea(Request $request, $area_id)
    {
        //  dd($request->all());
        $validator = Validator::make(
            $request->all(),
            [
                'name'       => 'required|unique:mst_delivery_areas,area_name,'.$area_id.',area_id',
                'code'       => 'required|unique:mst_delivery_areas,area_code,'.$area_id.',area_id',
            ],
            [
                'name.unique'       => "Area  Name already taken",
                'name.required'         => 'Area name required',
                'code.required'         => ' Area Code required',
                'code.unique'       => " Area Code  already taken"
            ]
        );
         if (!$validator->fails()) {
            $data = $request->except('_token');
            $row = Mst_delivery_area::where('area_id',$area_id)->first();
            $data = $request->except('_token');
            $row->area_name         = $request->name;
            $row->area_code         = $request->code;
            $row->city_id           = $request->location;
            $row->is_active = $request->is_active;

            $row->update();
            return redirect('admin/area/list')->with('status', 'Delivery Area updated successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }
    public function listBranch(Request $request)
    {
        $pageTitle = "Branches";
        $branches = Mst_branch::orderBy('created_at', 'DESC')->get();
        return view('admin.elements.branch.list', compact('branches', 'pageTitle'));
    }
    
    public function viewBranch(Request $request, $id)
    {
      $pageTitle = "View Branch";
      
      $branch = Mst_Branch::find($id);
      
      $products = Mst_branch_product::where('branch_id',$id)->paginate(10);
  
      
     
      return view('admin.elements.branch.view', compact('branch','products','pageTitle'));
    }
  
        
    public function createBranch(Request $request)
    {
        $pageTitle = "Add Branch";
        $existing_area = DB::table('trn_branch_deliveryarea')->get()->pluck('area')->all();
        $areas = Mst_delivery_area::whereNotIn('area_id',$existing_area)->get();
        $cities = Sys_city::get();
        return view('admin.elements.branch.create', compact('pageTitle','areas','cities'));
    }
     
    public function storeBranch(Request $request, Mst_branch $row)
    {
        //  dd($request->all());

        $validator = Validator::make(
            $request->all(),
            [
                'name'       => 'required|unique:mst_branches,branch_name',
                'code'       => 'required|unique:mst_branches,branch_code',
                'branch_contact_person' => 'required',
                'branch_contact_number' => 'required',
                'deliveryareas' => 'required',
                'email' => 'required|email',
                'location' => 'required',
                'password' => 'required|min:6',
                'whatsapp_number' => 'required',
                'address' => 'required',
                'working_hours_from' => 'required',
                'working_hours_to' => 'required',
                
            ],
            [
                'name.unique'       => "Branch  Name already taken",
                'name.required'         => 'Branch Name required',
                'code.required'         => ' Branch Code required',
                'code.unique'       => " Branch Code  already taken",
                'branch_contact_person.required' => 'Contact Person Required',
                'branch_contact_number.required' => 'Contact Number Required',
                'deliveryareas.required' => 'Please Choose Delivery Areas',
                'whatsapp_number.required' => 'Whatsapp Number Required',
                'working_hours_to.required' => 'Please Enter Opening Time',
                'working_hours_from.required' => 'Please Enter Closing Time',
                
            ]
        );

        if (!$validator->fails()) {
            $data = $request->except('_token');
            $row->branch_name         = $request->name;
            $row->branch_code         = $request->code;
            $row->branch_contact_person         = $request->branch_contact_person;
            $row->branch_contact_number         = $request->branch_contact_number;
            $row->whatsapp_number         =       $request->whatsapp_number;
            $row->branch_email         =          $request->email;
            $row->city_id         =              $request->location;
            $row->working_hours_from  =           $request->working_hours_from;
            $row->working_hours_to  =           $request->working_hours_to;
            $row->branch_contact_number         = $request->branch_contact_number;
            $row->branch_address         =          $request->address;
            $row->password         =          $request->password;
            
            $row->branch_status = $request->is_active;

            $row->save();
            $row->areas()->sync($request->deliveryareas);
            return redirect('admin/branch/list')->with('status', 'Branch added successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }
    public function editStatusbranch(Request $request)
    {
        $branch_id = $request->branch_id;
        if ($c = Mst_branch::where('branch_id',$branch_id)->first()) {
            if ($c->branch_status == 0) {
                Mst_branch::where('branch_id', $branch_id)->update(['branch_status' => 1]);
                echo "active";
            } else {
                Mst_branch::where('branch_id', $branch_id)->update(['branch_status' => 0]);
                echo "inactive";
            }
        }
    }
    public function editFeatureStatusbranch(Request $request)
    {
        $branch_id = $request->branch_id;
        if ($c = Mst_branch::where('branch_id',$branch_id)->first()) {
            if ($c->feature_status == 0) {
                Mst_branch::where('branch_id', $branch_id)->update(['feature_status' => 1]);
                echo "featured";
            } else {
                Mst_branch::where('branch_id', $branch_id)->update(['feature_status' => 0]);
                echo "non featured";
            }
        }
    }
    public function removebranch(Request $request, $branch_id)
    {
        Mst_branch::where('branch_id', '=', $branch_id)->delete();
        return redirect('admin/branch/list')->with('status', 'Branch deleted successfully.');
    }

    public function editbranch(Request $request, $branch_id)
    {
        $pageTitle = "Edit branch";
        $branch = Mst_branch::where('branch_id', '=', $branch_id)->first();
        $brancharea= $branch->areas()->pluck('area')->all();
        $areas = Mst_delivery_area::whereIn('area_id',$brancharea)->get();
        $cities = Sys_city::get();
        return view('admin.elements.branch.edit', compact('branch', 'pageTitle','areas','cities'));
    }

    public function updatebranch(Request $request, $branch_id)
    {
        //  dd($request->all());
        $validator = Validator::make(
            $request->all(),
            [
                'name'       => 'required|unique:mst_branches,branch_name,'.$branch_id.',branch_id',
                'code'       => 'required|unique:mst_branches,branch_code,'.$branch_id.',branch_id',
                'branch_contact_person' => 'required',
                'branch_contact_number' => 'required',
                'deliveryareas' => 'required',
                'email' => 'required|email',
                'location' => 'required',
                'whatsapp_number' => 'required',
                'address' => 'required',
                'working_hours_from' => 'required',
                'working_hours_to' => 'required',
                
            ],
            [
                'name.unique'       => "Branch  Name already taken",
                'name.required'         => 'Branch Name required',
                'code.required'         => ' Branch Code required',
                'code.unique'       => " Branch Code  already taken",
                'branch_contact_person.required' => 'Contact Person Required',
                'branch_contact_number.required' => 'Contact Number Required',
                'deliveryareas.required' => 'Please Choose Delivery Areas',
                 'whatsapp_number.required' => 'Whatsapp Number Required',
                 'working_hours_to.required' => 'Please Enter Opening Time',
                 'working_hours_from.required' => 'Please Enter Closing Time',
                
            ]
        );
         if (!$validator->fails()) {
            $data = $request->except('_token');
            $row = Mst_branch::where('branch_id',$branch_id)->first();
            $data = $request->except('_token');
            $row->branch_name         = $request->name;
            $row->branch_code         = $request->code;
            $row->branch_contact_person         = $request->branch_contact_person;
            $row->branch_contact_number         = $request->branch_contact_number;
            $row->whatsapp_number         =       $request->whatsapp_number;
            $row->branch_email         =          $request->email;
            $row->city_id         =          $request->location;
            $row->working_hours_from  =           $request->working_hours_from;
            $row->working_hours_to  =           $request->working_hours_to;
            $row->branch_contact_number         = $request->branch_contact_number;
            $row->branch_address         =          $request->address;
            
            $row->branch_status = $request->is_active;
            

            $row->update();
            $row->areas()->sync($request->deliveryareas);
            
            return redirect('admin/branch/list')->with('status', 'Branch updated successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }



  
    public function listCustomerBanner(Request $request)
    {
        $pageTitle = "Item Customer Banner";
        $customer_banners = Mst_CustomerBanner::orderBy('customer_banner_id', 'DESC')->get();
        return view('admin.elements.customer_banners.list', compact('customer_banners', 'pageTitle'));
    }

    public function createCustomerBanner(Request $request)
    {
        $pageTitle = "Create Customer Banner";
        $customer_banners = Mst_CustomerBanner::all();
        return view('admin.elements.customer_banners.create', compact('pageTitle', 'customer_banners'));
    }
     public function editCustomerBanner(Request $request,$id)
    {
        $pageTitle = "Update Customer Banner";
        $customerbanner = Mst_CustomerBanner::find($id);
        return view('admin.elements.customer_banners.edit', compact('pageTitle', 'customerbanner'));
    }


     public function storeCustomerBanner(Request $request, Mst_Issue $issue)
    {
        $data = $request->except('_token');

        $validator = Validator::make(
            $request->all(),
            [
                'images'       => 'required',
                'heading'    => 'required',
                'content'    => 'required'

            ],
            [
                'images.required'         => 'Images required',

            ]
        );

        if (!$validator->fails()) {

            $data = $request->except('_token');

            if ($request->hasFile('images')) {
                $allowedfileExtension = ['jpg', 'png', 'jpeg',];
                $files = $request->file('images');
                $c = 1;
                foreach ($files as $file) {

                    $filename = time() . '_' . $file->getClientOriginalName();
                    if ($file->move('assets/uploads/customer_banners', $filename)) {

                        $itemImage = new Mst_CustomerBanner;
                        $itemImage->customer_banner = $filename;
                        $itemImage->heading = $request->heading;
                        $itemImage->content = $request->content;
                        $itemImage->is_default = 0;
                        $itemImage->is_active = 1;
                        $itemImage->save();

                        $c++;
                    }
                }
            }

            return redirect('/admin/customer-banners/list')->with('status', 'Customer banner added successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }
    
    public function updateCustomerBanner(Request $request, $id)
    {
        $data = $request->except('_token');

        $validator = Validator::make(
            $request->all(),
            [
                'images'       => 'nullable',
                'heading'    => 'required',
                'content'    => 'required'

            ],
            [
                'images.required'         => 'Images required',

            ]
        );

        if (!$validator->fails()) {

            $data = $request->except('_token');

            if ($request->hasFile('images')) {
                
                Mst_CustomerBanner::where('customer_banner_id',$id)->update(['customer_banner'=> NULL]);
                $allowedfileExtension = ['jpg', 'png', 'jpeg',];
                $files = $request->file('images');
                $c = 1;
                foreach ($files as $file) {

                    $filename = time() . '_' . $file->getClientOriginalName();
                    if ($file->move('assets/uploads/customer_banners', $filename)) {

                        $itemImage = Mst_CustomerBanner::find($id);
                        $itemImage->customer_banner = $filename;
                        $itemImage->heading = $request->heading;
                        $itemImage->content = $request->content;
                        $itemImage->save();

                        $c++;
                    }
                }
            }
            
            else
            {
                        $itemImage = Mst_CustomerBanner::find($id);
                        $itemImage->heading = $request->heading;
                        $itemImage->content = $request->content;
                        $itemImage->save();

            }

            return redirect('/admin/customer-banners/list')->with('status', 'Customer banner added successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }



    public function removeCustomerBanner(Request $request, $customer_banner_id)
    {
        try {
            Mst_CustomerBanner::where('customer_banner_id', $customer_banner_id)->delete();
            return redirect()->route('admin.customer_banners')->with('status', 'Customer Banner deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
        }
    }

    public function editStatusCustomerBanner(Request $request)
    {
        $customer_banner_id = $request->customer_banner_id;
        if ($c = Mst_CustomerBanner::findOrFail($customer_banner_id)) {
            if ($c->is_active == 0) {
                Mst_CustomerBanner::where('customer_banner_id', $customer_banner_id)->update(['is_active' => 1]);
                echo "active";
            } else {
                Mst_CustomerBanner::where('customer_banner_id', $customer_banner_id)->update(['is_active' => 0]);
                echo "inactive";
            }
        }
    }



    public function listIssues(Request $request)
    {
        $pageTitle = "Item Issues";
        $issues = Mst_Issue::orderBy('issue_id', 'DESC')->get();
        return view('admin.elements.issues.list', compact('issues', 'pageTitle'));
    }

    public function editStatusissue(Request $request)
    {
        $issue_id = $request->issue_id;
        if ($c = Mst_Issue::findOrFail($issue_id)) {
            if ($c->is_active == 0) {
                Mst_Issue::where('issue_id', $issue_id)->update(['is_active' => 1]);
                echo "active";
            } else {
                Mst_Issue::where('issue_id', $issue_id)->update(['is_active' => 0]);
                echo "inactive";
            }
        }
    }


    public function createissue(Request $request)
    {
        $pageTitle = "Create Issue";
        $issue_types = Sys_IssueType::all();
        return view('admin.elements.issues.create', compact('pageTitle', 'issue_types'));
    }

    public function editissue(Request $request, $issue_id)
    {
        $pageTitle = "Edit Issue";
        $issue_types = Sys_IssueType::all();
        $issue = Mst_Issue::where('issue_id', '=', $issue_id)->first();
        return view('admin.elements.issues.edit', compact('issue', 'issue_types', 'pageTitle'));
    }

    public function storeissue(Request $request, Mst_Issue $issue)
    {
        $data = $request->except('_token');

        $validator = Validator::make(
            $request->all(),
            [
                'issue_type_id'       => 'required',
                'issue'       => 'required',

            ],
            [
                'issue_type_id.required'         => 'Issue type required',
                'issue.required'         => 'Issue required',

            ]
        );

        if (!$validator->fails()) {

            $data = $request->except('_token');

            $issue->issue_type_id         = $request->issue_type_id;
            $issue->issue = $request->issue;
            $issue->is_active = $request->is_active;
            $issue->save();

            return redirect('/admin/issues/list')->with('status', 'Issue added successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    public function updateissue(Request $request, $issue_id)
    {
        $data = $request->except('_token');

        $validator = Validator::make(
            $request->all(),
            [
                'issue_type_id'       => 'required',
                'issue'       => 'required',
            ],
            [
                'issue_type_id.required'         => 'Issue type required',
                'issue.required'         => 'Issue required',
            ]
        );

        if (!$validator->fails()) {

            $data = $request->except('_token');
            $issue = Mst_Issue::find($issue_id);
            $issue->issue_type_id         = $request->issue_type_id;
            $issue->issue = $request->issue;
            $issue->is_active = $request->is_active;
            $issue->update();

            return redirect('/admin/issues/list')->with('status', 'Issue updated successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }


    public function removeissue(Request $request, $issue_id)
    {
        try {
            Mst_Issue::where('issue_id', $issue_id)->delete();
            return redirect()->route('admin.issues')->with('status', 'Issue deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
        }
    }





    public function listDeliveryBoy(Request $request)
    {
        $pageTitle = "Delivery Boy";
        $delivery_boys = Mst_DeliveryBoy::orderBy('delivery_boy_id', 'DESC')->get();
        return view('admin.elements.delivery_boy.list', compact('delivery_boys', 'pageTitle'));
    }

    public function createDeliveryBoy(Request $request)
    {
        $pageTitle = "Create Delivery Boy";
        return view('admin.elements.delivery_boy.create', compact('pageTitle'));
    }

    public function storeDeliveryBoy(Request $request, Mst_DeliveryBoy $db)
    {
        try {

            $validator = Validator::make(
                $request->all(),
                [
                    'delivery_boy_name'       => 'required',
                    'delivery_boy_phone'       => 'required|unique:mst__delivery_boys',
                    'delivery_boy_address'       => 'required',
                    'password' => 'required|confirmed|min:6',
                ],
                [
                    'delivery_boy_name.required'         => 'Customer name required',
                    'delivery_boy_phone.required'         => 'Customer mobile required',
                    'delivery_boy_address.required'         => 'Address required',
                    'password.required'         => 'Password required',
                    'password.confirmed'         => 'Passwords not matching',
                    'password.min'         => 'Password should have 6 character',
                ]
            );

            if (!$validator->fails()) {
                $db->delivery_boy_name = $request->delivery_boy_name;
                $db->delivery_boy_phone = $request->delivery_boy_phone;
                $db->delivery_boy_email = $request->delivery_boy_email;
                $db->delivery_boy_address = $request->delivery_boy_address;
                $db->password = Hash::make($request->password);;
                $db->is_active = $request->is_active;
                $db->is_online = 0;
                $db->save();

                return redirect()->route('admin.delivery_boys')->with('status', 'Delivery boy created successfully');
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
        }
    }

    public function updateDeliveryBoy(Request $request, $delivery_boy_id)
    {
        try {

            $validator = Validator::make(
                $request->all(),
                [
                    'delivery_boy_name'       => 'required',
                    'delivery_boy_phone'       => 'required|unique:mst__delivery_boys,delivery_boy_phone,' . $delivery_boy_id . ',delivery_boy_id',
                    'delivery_boy_address'       => 'required',
                    'password' => 'confirmed',
                ],
                [
                    'delivery_boy_name.required'         => 'Customer name required',
                    'delivery_boy_phone.required'         => 'Customer mobile required',
                    'delivery_boy_address.required'         => 'Address required',
                    'password.required'         => 'Password required',
                    'password.confirmed'         => 'Passwords not matching',
                    'password.min'         => 'Password should have 6 character',
                ]
            );
            if (!$validator->fails()) {

                $data = $request->except('_token');
                $db = Mst_DeliveryBoy::find($delivery_boy_id);
                $db->delivery_boy_name = $request->delivery_boy_name;
                $db->delivery_boy_phone = $request->delivery_boy_phone;
                $db->delivery_boy_email = $request->delivery_boy_email;
                $db->delivery_boy_address = $request->delivery_boy_address;
                $db->is_active = $request->is_active;

                if (isset($request->password))
                    $db->password = Hash::make($request->password);;

                $db->update();

                return redirect()->route('admin.delivery_boys')->with('status', 'Delivery boy updated successfully');
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
        }
    }

    public function removeDeliveryBoy(Request $request, $delivery_boy_id)
    {
        try {
            Mst_DeliveryBoy::where('delivery_boy_id', $delivery_boy_id)->delete();
            return redirect()->route('admin.delivery_boys')->with('status', 'Delivery boy deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
        }
    }


    public function editStatusDeliveryBoy(Request $request)
    {
        $delivery_boy_id = $request->delivery_boy_id;
        if ($c = Mst_DeliveryBoy::findOrFail($delivery_boy_id)) {
            if ($c->is_active == 0) {
                Mst_DeliveryBoy::where('delivery_boy_id', $delivery_boy_id)->update(['is_active' => 1]);
                echo "active";
            } else {
                Mst_DeliveryBoy::where('delivery_boy_id', $delivery_boy_id)->update(['is_active' => 0]);
                echo "inactive";
            }
        }
    }

    public function editDeliveryBoy(Request $request, $delivery_boy_id)
    {
        $pageTitle = "Edit Delivery Boy";
        $delivery_boy = Mst_DeliveryBoy::where('delivery_boy_id', '=', $delivery_boy_id)->first();
        return view('admin.elements.delivery_boy.edit', compact('delivery_boy', 'pageTitle'));
    }


    public function listCoupon(Request $request)
    {
        try {

            $pageTitle = "Coupons";
            $coupons = Mst_Coupon::orderBy('coupon_id', 'DESC')->get();
            if ($_GET) {
                $couponDetail =  Mst_Coupon::where('coupon_status', $request->status);
                if ($request->coupon_status == 0) {
                    $today = Carbon::now()->toDateTimeString();
                    $couponDetail = $couponDetail->whereDate('valid_to', '>=', $today);
                }
                $coupons = $couponDetail->orderBy('coupon_id', 'DESC')->get();
                return view('store.elements.coupon.list', compact('coupons',  'pageTitle'));
            }
            return view('admin.elements.coupon.list', compact('coupons', 'pageTitle'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
        }
    }

    public function createcoupon(Request $request)
    {
        try {
            $pageTitle = "Create Coupon";
            return view('admin.elements.coupon.create', compact('pageTitle'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
        }
    }


    public function storecoupon(Request $request, Mst_Coupon $coupon)
    {
        try {

            $validator = Validator::make(
                $request->all(),
                [
                    'coupon_code'          => 'required',
                    'coupon_type'          => 'required',
                    'discount_type'          => 'required',
                    'discount'          => 'required',
                    'valid_to'          => 'required',
                    'valid_from'          => 'required',
                    'min_purchase_amt'          => 'required',
                ],
                [
                    'coupon_code.required'             => 'Code required',
                    'coupon_type.required'             => 'Type required',
                    'discount.required'             => 'Discount required',
                    'discount_type.required'             => 'Discount type required',
                    'valid_to.required'             => 'Valid to required',
                    'valid_from.required'             => 'Valid from required',
                    'min_purchase_amt.required'             => 'Minimum purchase amount required',
                ]
            );

            if (!$validator->fails()) {
                $coupon->coupon_code = $request->coupon_code;
                $coupon->coupon_type = $request->coupon_type;
                $coupon->discount_type = $request->discount_type;
                $coupon->discount = $request->discount;
                $coupon->valid_to = $request->valid_to;
                $coupon->valid_from = $request->valid_from;
                $coupon->coupon_status = $request->coupon_status;
                $coupon->min_purchase_amt = $request->min_purchase_amt;
                $coupon->save();

                return redirect()->route('admin.list_coupon')->with('status', 'Coupon created successfully');
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
        }
    }


    public function editcoupon(Request $request, $coupon_id)
    {
        try {

            $pageTitle = "Edit Coupon";
            $coupon_id  = Crypt::decryptString($coupon_id);
            $coupon = Mst_Coupon::find($coupon_id);
            $coupon->valid_from = Carbon::parse($coupon->valid_from)->format('Y-m-d');
            $coupon->valid_to = Carbon::parse($coupon->valid_to)->format('Y-m-d');
            return view('admin.elements.coupon.edit', compact('coupon', 'pageTitle'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
        }
    }

    public function updatecoupon(Request $request, $coupon_id)
    {
        try {

            $validator = Validator::make(
                $request->all(),
                [
                    'coupon_code'          => 'required',
                    'coupon_type'          => 'required',
                    'discount'          => 'required',
                    'discount_type'          => 'required',
                    'valid_to'          => 'required',
                    'valid_from'          => 'required',
                    'min_purchase_amt'          => 'required',
                ],
                [
                    'coupon_code.required'             => 'Code required',
                    'discount.required'             => 'Discount required',
                    'coupon_type.required'             => 'Type required',
                    'discount_type.required'             => 'Discount type required',
                    'valid_to.required'             => 'Valid to required',
                    'valid_from.required'             => 'Valid from required',
                    'min_purchase_amt.required'             => 'Minimum purchase amount required',
                ]
            );
            //   $coupon_id  = Crypt::decryptString($coupon_id);
            if (!$validator->fails()) {
                $coupon['coupon_code'] = $request->coupon_code;
                $coupon['coupon_type'] = $request->coupon_type;
                $coupon['discount'] = $request->discount;
                $coupon['discount_type'] = $request->discount_type;
                $coupon['valid_to'] = $request->valid_to;
                $coupon['valid_from'] = $request->valid_from;
                $coupon['coupon_status'] = $request->coupon_status;
                $coupon['min_purchase_amt'] = $request->min_purchase_amt;

                Mst_Coupon::where('coupon_id', $coupon_id)->update($coupon);

                return redirect()->route('admin.list_coupon')->with('status', 'Coupon updated successfully');
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
        }
    }

    public function removecoupon(Request $request, $coupon_id)
    {
        try {
            //  $coupon_id  = Crypt::decryptString($coupon_id);
            Mst_Coupon::where('coupon_id', $coupon_id)->delete();
            return redirect()->route('store.list_coupon')->with('status', 'Coupon deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
        }
    }

    public function listIltsc(Request $request)
    {
        $pageTitle = "Item Sub Categories Level Two";
        $sub_category = Mst_ItemLevelTwoSubCategory::orderBy('iltsc_id', 'DESC')->get();
        return view('admin.elements.item_sub_cat_level_two.list', compact('sub_category', 'pageTitle'));
    }

    public function createIltsc(Request $request)
    {
        $pageTitle = "Create Item Sub Category Level Two";
        $sub_categories = Mst_ItemSubCategory::where('is_active', '=', '1')->orderBy('sub_category_name')->get();
        $categories = Mst_ItemCategory::where('is_active', '=', '1')->orderBy('category_name')->get();
        return view('admin.elements.item_sub_cat_level_two.create', compact('sub_categories', 'categories', 'pageTitle'));
    }

    public function storeIltsc(Request $request, Mst_ItemLevelTwoSubCategory $iltsc)
    {
        $data = $request->except('_token');
        $validator = Validator::make(
            $request->all(),
            [
                'item_sub_category_id'       => 'required',
                'iltsc_name'       => 'required',
                'iltsc_description' => 'required',
            ],
            [
                'iltsc_name.required'         => 'Sub category name required',
                'item_sub_category_id.required'        => 'Sub category level one required',
                'iltsc_icon.dimensions'        => 'Sub category icon dimensions is invalid',
                'iltsc_description.required'     => 'Sub category description required',
            ]
        );

        if (!$validator->fails()) {

            $data = $request->except('_token');
            $catData = Mst_ItemSubCategory::find($request->item_sub_category_id);
            $iltsc->item_category_id         = $catData->item_category_id;
            $iltsc->item_sub_category_id         = $request->item_sub_category_id;
            $iltsc->iltsc_name         = $request->iltsc_name;
            $iltsc->iltsc_name_slug      = Str::of($request->iltsc_name)->slug('-');
            $iltsc->iltsc_description = $request->iltsc_description;

            if ($request->hasFile('iltsc_icon')) {
                $file = $request->file('iltsc_icon');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move('assets/uploads/category_icon', $filename);
                $iltsc->iltsc_icon = $filename;
            }
            $iltsc->is_active         = 1;
            $iltsc->save();

            return redirect('/admin/item-sub-category-level-two/list')->with('status', 'Sub category level two added successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    public function updateIltsc(Request $request, $iltsc_id)
    {
        $data = $request->except('_token');

        $validator = Validator::make(
            $request->all(),
            [
                'item_sub_category_id'       => 'required',
                'iltsc_name'       => 'required',
                'iltsc_description' => 'required',
            ],
            [
                'iltsc_name.required'         => 'Sub category name required',
                'item_sub_category_id.required'        => 'Sub category level one required',
                'iltsc_icon.dimensions'        => 'Sub category icon dimensions is invalid',
                'iltsc_description.required'     => 'Sub category description required',
            ]
        );

        if (!$validator->fails()) {

            $data = $request->except('_token');
            $iltsc = Mst_ItemLevelTwoSubCategory::find($iltsc_id);
            $catData = Mst_ItemSubCategory::find($request->item_sub_category_id);
            $iltsc->item_category_id         = $catData->item_category_id;
            $iltsc->item_sub_category_id         = $request->item_sub_category_id;
            $iltsc->iltsc_name         = $request->iltsc_name;
            $iltsc->iltsc_name_slug      = Str::of($request->iltsc_name)->slug('-');
            $iltsc->iltsc_description = $request->iltsc_description;

            if ($request->hasFile('iltsc_icon')) {
                $file = $request->file('iltsc_icon');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move('assets/uploads/category_icon', $filename);
                $iltsc->iltsc_icon = $filename;
            }

            $iltsc->update();

            return redirect('/admin/item-sub-category-level-two/list')->with('status', 'Sub category updated successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }


    public function editStatusIltsc(Request $request)
    {
        $iltsc_id = $request->iltsc_id;
        if ($c = Mst_ItemLevelTwoSubCategory::findOrFail($iltsc_id)) {
            if ($c->is_active == 0) {
                Mst_ItemLevelTwoSubCategory::where('iltsc_id', $iltsc_id)->update(['is_active' => 1]);
                echo "active";
            } else {
                Mst_ItemLevelTwoSubCategory::where('iltsc_id', $iltsc_id)->update(['is_active' => 0]);
                echo "inactive";
            }
        }
    }

    public function editIltsc(Request $request, $iltsc_id)
    {
        $pageTitle = "Edit Item Sub Category Level Two";

        $sub_category_l_2 = Mst_ItemLevelTwoSubCategory::where('iltsc_id', '=', $iltsc_id)->first();
        $categories = Mst_ItemCategory::where('is_active', '=', '1')->orderBy('category_name')->get();
        $sub_categories = Mst_ItemSubCategory::where('is_active', '=', '1')
            ->where('item_category_id', $sub_category_l_2->item_category_id)
            ->orderBy('sub_category_name')->get();

        return view('admin.elements.item_sub_cat_level_two.edit', compact('sub_category_l_2', 'categories', 'sub_categories', 'pageTitle'));
    }


    public function removeIltsc(Request $request, $iltsc_id)
    {
        Mst_ItemLevelTwoSubCategory::where('iltsc_id', '=', $iltsc_id)->delete();
        return redirect('/admin/item-sub-category-level-two/list')->with('status', 'Sub category removed successfully.');
    }

    
    public function listItemCategory(Request $request)
    {
        $pageTitle = " Categories";
        $categories = Mst_ItemCategory::orderBy('item_category_id', 'DESC')->get();
        return view('admin.elements.item_categories.list', compact('categories', 'pageTitle'));
    }

    public function createItemCategory(Request $request)
    {
        $pageTitle = "Create  Category";
        return view('admin.elements.item_categories.create', compact('pageTitle'));
    }

    public function storeItemCategory(Request $request, Mst_ItemCategory $category)
    {
        //  dd($request->all());

        $validator = Validator::make(
            $request->all(),
            [
                'category_name'       => 'required|unique:mst__item_categories,category_name,NULL,unit_id,deleted_at,NULL',
                //'category_icon'        => 'dimensions:width=150,height=150|image|mimes:jpeg,png,jpg',
                'category_icon'        => 'required|image|mimes:jpeg,png,jpg',
                'category_description' => 'required',
            ],
            [
                'category_name.required'         => 'Category name required',
                'category_icon.required'        => 'Category icon required',
                'category_icon.dimensions'        => 'Category icon dimensions is invalid',
                'category_description.required'     => 'Category description required',
            ]
        );

        if (!$validator->fails()) {
            $data = $request->except('_token');
            $category->category_name         = $request->category_name;
            $category->category_name_slug      = Str::of($request->category_name)->slug('-');
            $category->category_description = $request->category_description;
            $category->is_active = $request->is_active;
            if ($request->hasFile('category_icon'))
             {
                $file = $request->file('category_icon');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move('assets/uploads/category_icon', $filename);
                $category->category_icon = $filename;
            }
            $category->save();
            return redirect('admin/item-category/list')->with('status', 'Category added successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    public function editStatusItemCategory(Request $request)
    {
        $item_category_id = $request->item_category_id;
        if ($c = Mst_ItemCategory::findOrFail($item_category_id)) {
            if ($c->is_active == 0) {
                Mst_ItemCategory::where('item_category_id', $item_category_id)->update(['is_active' => 1]);
                echo "active";
            } else {
                Mst_ItemCategory::where('item_category_id', $item_category_id)->update(['is_active' => 0]);
                echo "inactive";
            }
        }
    }

    public function editItemCategory(Request $request, $category_name_slug)
    {
        $pageTitle = "Edit  Category";
        $category = Mst_ItemCategory::where('category_name_slug', '=', $category_name_slug)->first();
        return view('admin.elements.item_categories.edit', compact('category', 'pageTitle'));
    }
    public function updateItemCategory(Request $request, $item_category_id)

    {
        $validator = Validator::make(
            $request->all(),
            [ 'category_name'       => 'unique:mst__item_categories,category_name,'.$item_category_id.',item_category_id,deleted_at,NULL',
            //'category_icon'        => 'dimensions:width=150,height=150|image|mimes:jpeg,png,jpg',
            // 'category_icon'        => 'required|image|mimes:jpeg,png,jpg',
            'category_description' => 'required',
        ],
        [
            'category_name.required'         => 'Category name required',
            'category_icon.required'        => 'Category icon required',
            'category_icon.dimensions'        => 'Category icon dimensions is invalid',
            'category_description.required'     => 'Category description required',
        ]
        );

        if (!$validator->fails()) {

           
            $data = $request->except('_token');
            $row = Mst_ItemCategory::find($item_category_id);
            $row->category_name         = $request->category_name;
            $row->category_name_slug      = Str::of($request->category_name)->slug('-');
            $row->category_description         = $request->category_description;
            $row->is_active = $request->is_active;

        
                if ($request->hasFile('category_icon') )
                {
                  $file = $request->file('category_icon');
                  $filename = time() . '_' . $file->getClientOriginalName();
                  $file->move('assets/uploads/category_icon', $filename);
                  $row->category_icon = $filename;
                  
                }
             
            $row->update();

            return redirect('admin/item-category/list')->with('status', 'Category updated successfully.');
        }
         else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }
   

    public function removeItemCategory(Request $request, $item_category_id)
    {
        Mst_ItemCategory::where('item_category_id', '=', $item_category_id)->delete();
        return redirect('admin/item-category/list')->with('status', 'Category deleted successfully.');
    }
    

    
    public function editStatusUnit(Request $request)
    {
        $unit_id = $request->unit_id;
        if ($c = Mst_Unit::findOrFail($unit_id)) {
            if ($c->is_active == 0) {
                Mst_Unit::where('unit_id', $unit_id)->update(['is_active' => 1]);
                echo "active";
            } else {
                Mst_Unit::where('unit_id', $unit_id)->update(['is_active' => 0]);
                echo "inactive";
            }
        }
    }


    public function createUnit(Request $request)
    {
        $pageTitle = "Create Unit";
        return view('admin.elements.units.create', compact('pageTitle'));
    }

    public function editUnit(Request $request, $unit_id)
    {
        $pageTitle = "Edit Unit";
        $unit = Mst_Unit::where('unit_id', '=', $unit_id)->first();
        return view('admin.elements.units.edit', compact('unit', 'pageTitle'));
    }


    public function storeUnit(Request $request, Mst_Unit $row)
    {
        //  dd($request->all());

        $validator = Validator::make(
            $request->all(),
            [
                'unit_name'       => 'required|unique:mst__units,unit_name,NULL,unit_id,deleted_at,NULL',
                'unit_sf'       => 'required|unique:mst__units,unit_sf,NULL,unit_id,deleted_at,NULL',
            ],
            [
                'unit_name.unique'       => "Unit  Name already taken",
                'unit_name.required'         => 'Unit name required',
                'unit_sf.required'         => ' shotform required',
                'unit_sf.unique'       => " shotform  already taken"
            ]
        );

        if (!$validator->fails()) {
            $data = $request->except('_token');
            $row->unit_name         = $request->unit_name;
            $row->unit_sf         = $request->unit_sf;
            $row->is_active = $request->is_active;

            $row->save();
            return redirect('admin/units/list')->with('status', 'Unit added successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    public function removeUnit(Request $request, $unit_id)
    {
        Mst_Unit::where('unit_id', '=', $unit_id)->delete();
        return redirect('admin/units/list')->with('status', 'Unit deleted successfully.');
    }

    public function updateUnit(Request $request, $unit_id)
    {
        //  dd($request->all());
        $validator = Validator::make(
            $request->all(),
            [
                 'unit_name'       => 'required|unique:mst__units,unit_name,'.$unit_id.',unit_id,deleted_at,NULL',
                   'unit_sf'       => 'required|unique:mst__units,unit_sf,'.$unit_id.',unit_id,deleted_at,NULL',
            ],
            [
                'unit_name.unique'       => "Unit  Name already taken",
                'unit_name.required'         => 'Unit name required',
                'unit_sf.required'         => 'Unit shotform required',
                'unit_sf.unique'       => "Unit shotform  already taken"
            ]
        );
        if (!$validator->fails()) {
            $data = $request->except('_token');
            $row = Mst_Unit::find($unit_id);
            $row->unit_name         = $request->unit_name;
            $row->unit_sf         = $request->unit_sf;
            $row->is_active = $request->is_active;

            $row->update();
            return redirect('admin/units/list')->with('status', 'Unit updated successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }


    public function listAttributeGroup(Request $request)
    {
        $pageTitle = "Item Attribute Groups";
        $attribute_groups = Mst_AttributeGroup::orderBy('attribute_group_id', 'DESC')->get();
        return view('admin.elements.attribute_groups.list', compact('attribute_groups', 'pageTitle'));
    }

    public function createAttributeGroup(Request $request)
    {
        $pageTitle = "Create Attribute Group";
        $category=Mst_ItemLevelTwoSubCategory::where('is_active',1)->get();

        return view('admin.elements.attribute_groups.create', compact('pageTitle','category'));
    }

    public function storeAttributeGroup(Request $request, Mst_AttributeGroup $row)
    {
        //  dd($request->all());

        $validator = Validator::make(
            $request->all(),
            [
                'attribute_group'       => 'required|unique:mst__attribute_groups',
            ],
            [
                'attribute_group.required'         => 'Attribute group name required',
                'attribute_group.unique'         => 'Attribute group exists',
            ]
        );
        if (!$validator->fails()) {
            $data = $request->except('_token');
            $row->attribute_group         = $request->attribute_group;
            $row->is_active = $request->is_active;
            if ($row->save()) {
                $lastCatid = DB::getPdo()->lastInsertId();
                // dd($records);
                foreach (array_unique($request->category) as  $row) {
                $records=Mst_ItemLevelTwoSubCategory::where('iltsc_id',$row)->first();

                    $cb = new Mst_Attributecategory;
                    $cb->attribute_group_id = $lastCatid;
                    $cb->item_category_id = $records->item_category_id;
                    $cb->item_sub_category_id = $records->item_sub_category_id;
                    $cb->iltsc_id = $row;
                    $cb->save();
                }
            }
            return redirect('admin/attribute-group/list')->with('status', 'Attribute group added successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    public function editStatusAttributeGroup(Request $request)
    {
        $attribute_group_id = $request->attribute_group_id;
        if ($c = Mst_AttributeGroup::findOrFail($attribute_group_id)) {
            if ($c->is_active == 0) {
                Mst_AttributeGroup::where('attribute_group_id', $attribute_group_id)->update(['is_active' => 1]);
                echo "active";
            } else {
                Mst_AttributeGroup::where('attribute_group_id', $attribute_group_id)->update(['is_active' => 0]);
                echo "inactive";
            }
        }
    }

    public function editAttributeGroup(Request $request, $attribute_group_id)
    {
        $pageTitle = "Edit Attribute Group";
        $attribute_group = Mst_AttributeGroup::where('attribute_group_id', '=', $attribute_group_id)->first();
        $category=Mst_ItemLevelTwoSubCategory::where('is_active',1)->get();

        return view('admin.elements.attribute_groups.edit', compact('attribute_group', 'pageTitle','category'));
    }

    public function removeAttributeGroup(Request $request, $attribute_group_id)
    {
        Mst_AttributeGroup::where('attribute_group_id', '=', $attribute_group_id)->delete();
        return redirect('admin/attribute-group/list')->with('status', 'Attribute group deleted successfully.');
    }

    public function updateAttributeGroup(Request $request, $attribute_group_id)
    {
        //  dd($request->all());
        $validator = Validator::make(
            $request->all(),
            [
                'attribute_group'       => 'required|unique:mst__attribute_groups,attribute_group,' . $attribute_group_id . ',attribute_group_id',
            ],
            [
                'attribute_group.required'         => 'Attribute group name required',
                'attribute_group.unique'         => 'Attribute group exists',
            ]
        );
        if (!$validator->fails()) {
            $data = $request->except('_token');
            $row = Mst_AttributeGroup::find($attribute_group_id);
            $row->attribute_group         = $request->attribute_group;
            $row->is_active = $request->is_active;
            if ($row->update()) {
                Mst_Attributecategory::where('attribute_group_id', $attribute_group_id)->delete();
    
                    foreach (array_unique($request->category) as  $row) {
                    $records=Mst_ItemLevelTwoSubCategory::where('iltsc_id',$row)->first();

                    $cb = new Mst_Attributecategory;
                    $cb->attribute_group_id = $attribute_group_id;
                    $cb->item_category_id = $records->item_category_id;
                    $cb->item_sub_category_id = $records->item_sub_category_id;
                    $cb->iltsc_id = $row;
                    $cb->save();

                }
            }
            return redirect('admin/attribute-group/list')->with('status', 'Attribute group updated successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    public function listAttributeValue(Request $request)
    {
        $pageTitle = "Item Attribute Values";
        $attribute_values = Mst_AttributeValue::orderBy('attribute_value_id', 'DESC')->get();
        return view('admin.elements.attribute_values.list', compact('attribute_values', 'pageTitle'));
    }

    public function createAttributeValue(Request $request)
    {
        $pageTitle = "Create Attribute Value";
        $attribute_groups = Mst_AttributeGroup::where('is_active', 1)->orderBy('attribute_group_id', 'DESC')->get();
        return view('admin.elements.attribute_values.create', compact('attribute_groups', 'pageTitle'));
    }

    public function storeAttributeValue(Request $request, Mst_AttributeValue $row)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'attribute_group_id'       => 'required',
                'attribute_value'       => 'required',
            ],
            [
                'attribute_group_id.required'         => 'Attribute group required',
                'attribute_value.required'         => 'Attribute value required',
            ]
        );
        if (!$validator->fails()) {
            $data = $request->except('_token');
            $row->attribute_group_id         = $request->attribute_group_id;
            $row->attribute_value         = $request->attribute_value;
            $row->is_active = $request->is_active;
            $row->save();
            return redirect('admin/attribute-value/list')->with('status', 'Attribute value added successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    public function editStatusAttributeValue(Request $request)
    {
        $attribute_value_id = $request->attribute_value_id;
        if ($c = Mst_AttributeValue::findOrFail($attribute_value_id)) {
            if ($c->is_active == 0) {
                Mst_AttributeValue::where('attribute_value_id', $attribute_value_id)->update(['is_active' => 1]);
                echo "active";
            } else {
                Mst_AttributeValue::where('attribute_value_id', $attribute_value_id)->update(['is_active' => 0]);
                echo "inactive";
            }
        }
    }

    public function editAttributeValue(Request $request, $attribute_value_id)
    {
        $pageTitle = "Edit Attribute Value";
        $attribute_value = Mst_AttributeValue::where('attribute_value_id', '=', $attribute_value_id)->first();
        $attribute_groups = Mst_AttributeGroup::where('is_active', 1)->orderBy('attribute_group_id', 'DESC')->get();
        return view('admin.elements.attribute_values.edit', compact('attribute_groups', 'attribute_value', 'pageTitle'));
    }

    public function updateAttributeValue(Request $request, $attribute_value_id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'attribute_group_id'       => 'required',
                'attribute_value'       => 'required',
            ],
            [
                'attribute_group_id.required'         => 'Attribute group required',
                'attribute_value.required'         => 'Attribute value required',
            ]
        );
        if (!$validator->fails()) {
            $data = $request->except('_token');
            $row = Mst_AttributeValue::find($attribute_value_id);
            $row->attribute_group_id         = $request->attribute_group_id;
            $row->attribute_value         = $request->attribute_value;
            $row->is_active = $request->is_active;
            $row->update();
            return redirect('admin/attribute-value/list')->with('status', 'Attribute value updated successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    public function removeAttributeValue(Request $request, $attribute_value_id)
    {
        Mst_AttributeValue::where('attribute_value_id', '=', $attribute_value_id)->delete();
        return redirect('admin/attribute-value/list')->with('status', 'Attribute value deleted successfully.');
    }


    public function listBrand(Request $request)
    {
        $pageTitle = "Item Brands";
        $brands = Mst_Brand::with('categories')->orderBy('brand_id', 'DESC')->get();
        return view('admin.elements.brands.list', compact('brands', 'pageTitle'));
    }

    public function createBrand(Request $request)
    {
        $pageTitle = "Create Brand";
        $category=Mst_ItemCategory::where('is_active',1)->get();
        return view('admin.elements.brands.create', compact('pageTitle','category'));
    }


    public function storeBrand(Request $request, Mst_Brand $row)
    {
        //  dd($request->all());

        $validator = Validator::make(
            $request->all(),
            [
                'brand_name'       => 'required|unique:mst__brands,brand_name,NULL,brand_id,deleted_at,NULL',
                //'brand_icon'        => 'dimensions:width=150,height=150|image|mimes:jpeg,png,jpg',
                'brand_icon'        => 'required|image|mimes:jpeg,png,jpg',
            ],
            [
                'brand_name.required'         => 'Brand name required',
                'brand_name.unique'         => 'Brand name exists',
                'brand_icon.required'        => 'Brand icon required',
                'brand_icon.dimensions'        => 'Brand icon dimensions is invalid',
            ]
        );

        if (!$validator->fails()) {
            $data = $request->except('_token');
            $row->brand_name         = $request->brand_name;
            $row->brand_name_slug      = Str::of($request->brand_name)->slug('-');
            $row->is_active = $request->is_active;
            if ($request->hasFile('brand_icon')) {
                $file = $request->file('brand_icon');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move('assets/uploads/brand_icon', $filename);
                $row->brand_icon = $filename;
            }
            if ($row->save()) {
                $lastCatid = DB::getPdo()->lastInsertId();
                // dd($records);
                foreach (array_unique($request->category) as  $row) {
                $records=Mst_ItemCategory::where('item_category_id',$row)->first();

                    $cb = new Mst_Brandsubcat;
                    $cb->brand_id = $lastCatid;
                    $cb->item_category_id = $records->item_category_id;
                    // $cb->item_sub_category_id = $records->item_sub_category_id;
                    // $cb->iltsc_id = $row;
                    $cb->save();
                }
            }

            return redirect('admin/brands/list')->with('status', 'Brand added successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }


    public function editStatusBrand(Request $request)
    {
        $brand_id = $request->brand_id;
        if ($c = Mst_Brand::findOrFail($brand_id)) {
            if ($c->is_active == 0) {
                Mst_Brand::where('brand_id', $brand_id)->update(['is_active' => 1]);
                echo "active";
            } else {
                Mst_Brand::where('brand_id', $brand_id)->update(['is_active' => 0]);
                echo "inactive";
            }
        }
    }

    public function editBrand(Request $request, $brand_id)
    {
        $pageTitle = "Edit Brand";
        $brand = Mst_Brand::where('brand_id', '=', $brand_id)->first();
        $brand_cats = Mst_brandsubcat::where('brand_id',$brand_id)->get();
        $category=Mst_ItemCategory::where('is_active',1)->get();

        return view('admin.elements.brands.edit', compact('brand', 'pageTitle','category','brand_cats'));
    }


    public function updateBrand(Request $request, $brand_id)
    {
        //  dd($request->all());

        $validator = Validator::make(
            $request->all(),
            [
                'brand_name'       => 'required|unique:mst__brands,brand_name,'.$brand_id.',brand_id,deleted_at,NULL',
                //'brand_name'       => 'required|unique:mst__brands',
                //'brand_icon'        => 'dimensions:width=150,height=150|image|mimes:jpeg,png,jpg',
                'brand_icon'        => 'image|mimes:jpeg,png,jpg',
            ],
            [
                'brand_name.required'         => 'Brand name required',
                'brand_name.unique'         => 'Brand name exists',
                'brand_icon.required'        => 'Brand icon required',
                'brand_icon.dimensions'        => 'Brand icon dimensions is invalid',
            ]
        );

        //  dd($request->all());

        if (!$validator->fails()) {
            $data = $request->except('_token');
            $row = Mst_Brand::find($brand_id);
            $row->brand_name         = $request->brand_name;
            $row->brand_name_slug      = Str::of($request->brand_name)->slug('-');
            $row->is_active = $request->is_active;
            if ($request->hasFile('brand_icon')) {
                $file = $request->file('brand_icon');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move('assets/uploads/brand_icon', $filename);
                $row->brand_icon = $filename;
            }
            
                if ($row->update()) {
                $cat = Mst_brandsubcat::where('brand_id', $brand_id)->pluck('item_category_id');
                    foreach (array_unique($request->category) as  $row) {
                    if(!$cat->contains($row)){
                    $records=Mst_ItemCategory::where('item_category_id',$row)->first();

                    $cb = new Mst_brandsubcat;
                    $cb->brand_id = $brand_id;
                    $cb->item_category_id = $records->item_category_id;
                    $cb->item_sub_category_id = $records->item_sub_category_id;
                    $cb->iltsc_id = $row;
                    $cb->save();
                    }
                }
            }
            return redirect('admin/brands/list')->with('status', 'Brand updated successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    public function removeBrand(Request $request, $brand_id)
    {
        Mst_Brand::where('brand_id', '=', $brand_id)->delete();
        return redirect('admin/brands/list')->with('status', 'Brand deleted successfully.');
    }
    public function removeBrandCat($category_id)
    {
        Mst_brandsubcat::find($category_id)->delete();
        return redirect()->back()->with('status', 'Category deleted successfully.');
    }
 



    public function listTaxes(Request $request)
    {
        $pageTitle = "Taxes";
        $taxes = Mst_Tax::orderBy('tax_id', 'DESC')->get();
        $tax_splits = Trn_TaxSplit::orderBy('tax_split_id', 'DESC')->get();
        return view('admin.elements.taxes.list', compact('tax_splits', 'pageTitle', 'taxes'));
    }

    public function addTaxes(Request $request)
    {
        $pageTitle = "Add Tax";
        return view('admin.elements.taxes.create', compact('pageTitle'));
    }

    public function createTax(Request $request, Mst_Tax $tax)
    {
        $tax->tax_value  = $request->tax_value;
        $tax->tax_name  = $request->tax_name;
        $tax->is_active  = $request->is_active;
        $tax->save();

        $last_id = DB::getPdo()->lastInsertId();
        $i = 0;
        foreach ($request->split_tax_name as $tax) {
            $taxSplit = new Trn_TaxSplit;
            $taxSplit->tax_id = $last_id;
            $taxSplit->tax_split_name  = $tax;
            $taxSplit->tax_split_value  =  $request->split_tax_value[$i];
            $taxSplit->save();
            $i++;
        }
        return redirect('admin/tax/list')->with('status', 'Tax added successfully.');
    }

    public function editTax(Request $request, Mst_Tax $tax, $tax_id)
    {
        $pageTitle = "Edit Tax";
        $tax = Mst_Tax::find($tax_id);
        $tax_splits = Trn_TaxSplit::where('tax_id', $tax_id)->get();
        return view('admin.elements.taxes.edit', compact('tax_splits', 'pageTitle', 'tax'));
    }

    public function editStatusTax(Request $request)
    {
        $tax_id = $request->tax_id;
        if ($c = Mst_Tax::findOrFail($tax_id)) {
            if ($c->is_active == 0) {
                Mst_Tax::where('tax_id', $tax_id)->update(['is_active' => 1]);
                echo "active";
            } else {
                Mst_Tax::where('tax_id', $tax_id)->update(['is_active' => 0]);
                echo "inactive";
            }
        }
    }

    public function updateTax(Request $request, Mst_Tax $tax, $tax_id)
    {
        $tax = Mst_Tax::find($tax_id);
        $tax->tax_value  = $request->tax_value;
        $tax->tax_name  = $request->tax_name;
        $tax->is_active  = $request->is_active;
        $tax->update();

        Trn_TaxSplit::where('tax_id', $tax_id)->delete();

        $i = 0;
        foreach ($request->split_tax_name as $tax) {
            $taxSplit = new Trn_TaxSplit;
            $taxSplit->tax_id = $tax_id;
            $taxSplit->tax_split_name  = $tax;
            $taxSplit->tax_split_value  =  $request->split_tax_value[$i];
            $taxSplit->save();
            $i++;
        }

        return redirect('admin/tax/list')->with('status', 'Tax updated successfully.');
    }
    public function removeTax(Request $request, $tax_id)
    {
        Mst_Tax::where('tax_id', '=', $tax_id)->delete();
        return redirect('admin/tax/list')->with('status', 'Tax deleted successfully.');
    }
}
