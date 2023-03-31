<?php

namespace App\Imports;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\admin\Mst_ItemCategory;
use App\Models\admin\Mst_ItemSubCategory;
use App\Models\admin\Mst_GlobalProducts;
use App\Models\admin\Mst_store_product;
use App\Models\admin\Mst_branch_product;
use App\Models\admin\Mst_branch_product_varient;
use App\Models\admin\Mst_store_product_varient;
use App\Models\admin\Mst_business_types;
use App\Models\admin\Mst_StockDetail;
use App\Models\admin\Trn_ProductVariantAttribute;
use App\Models\admin\Mst_Tax;
use App\Models\admin\Mst_Brand;
use App\Models\Mst_branch;
use App\Models\admin\Mst_attribute_value;
use App\Models\admin\Mst_attribute_group;
use Illuminate\Validation\Rule;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\ValidationException;
use Throwable;

class GlobalProductsImport implements ToCollection, WithHeadingRow, SkipsOnError, WithValidation
{
    use Importable, SkipsErrors;
    
    public function collection(Collection $rows)
    {

        foreach($rows as $row) {
            
            
            $branch = Mst_branch::where('branch_code',$row['branches'])->first();

            $category = Mst_ItemSubCategory::where('sub_category_name',$row['subcategory'])->first();
            
             $group = Mst_attribute_group::where('group_name',$row['group'])->first();
            
             $value = Mst_attribute_value::where('group_value',$row['value'])->first();
            
            
            
            
             if (is_null($branch)) {
                 
                    throw ValidationException::withMessages(['Branch '.$row['branches'].' does not exist']);
              
             }
             
               
               
                $dataz = [
                    'product_name' => $row['product_description'],
                    'SKU'=> $row['sku'],
                    'product_name_slug' => Str::of($row['product_description'])->slug('-'),
                    'product_description' => $row['product_description'],
                    'product_price' => $row['original_price_if_there_is_a_discount'],
                    'product_price_offer' => $row['rsp_retail_selling_price'] == null ?  $row['original_price_if_there_is_a_discount'] : $row['rsp_retail_selling_price'],
                    'tax_id' => 0,
                    'min_stock' => null,
                    'product_code' => $row['ean'],
                    'stock_count' => $row['stock_qty'],
                    'business_type_id' => 0 ,
                    'product_brand' => null,
                    'attr_group_id' => @$group->attr_group_id,
                    'attr_value_id' => @$value->attr_value_id,
                    'store_id' => 1 ,
                    'product_status' =>  1 ,
                    'product_type' =>  1 ,
                    'product_cat_id' => @$category->item_category_id,
                    'sub_category_id' => @$category->parent_sub_category,
                    'sub_category_leveltwo' => @$category->item_sub_category_id,
                    'product_base_image' => null,
                    'created_date' =>  Carbon::now()->format('Y-m-d'),
            ];
             
             
            $global_products = Mst_store_product::create($dataz);
            $dataz['global_product_id'] = $global_products->product_id;
            $dataz['branch_id']  = $branch->branch_id;
            $branch_product  = Mst_branch_product::create($dataz);
            
            $data3 = [
              'product_id' => $global_products->product_id,
              'SKU' => $row['sku'],
              'variant_name' => $row['product_description'],
              'product_varient_price' => $row['original_price_if_there_is_a_discount'],
              'product_varient_offer_price' => $row['rsp_retail_selling_price'] == null ?  $row['original_price_if_there_is_a_discount'] : $row['rsp_retail_selling_price'],
              'product_varient_base_image' => null,
              'stock_count' => $row['stock_qty'],
              'color_id' =>  0,
              'is_base_variant' =>  1,
              'store_id' => 1 ,
            ];
            
            $store_varient = Mst_store_product_varient::create($data3);
            $data3['branch_id']  = $branch->branch_id;
            $data3['product_id'] = $branch_product->branch_product_id;
            $branch_varient = Mst_branch_product_varient::create($data3);
            
            $sd = new Mst_StockDetail;
            $sd->store_id = $branch->branch_id;
            $sd->product_id = $branch_product->branch_product_id;
            $sd->stock = $row['stock_qty'];
            $sd->product_varient_id = $branch_varient->varient_id;
            $sd->prev_stock = 0;
            $sd->save();
            
            
            
            $v = new Trn_ProductVariantAttribute;
            $v->product_varient_id = $store_varient->product_varient_id;
            $v->attr_group_id = @$group->attr_group_id;
            $v->attr_value_id = @$value->attr_value_id;
            $v->save();  
            
        }

    }
    public function rules(): array
    {
        $branches = Mst_branch::pluck('branch_code');
        $cats = Mst_ItemSubCategory::pluck('sub_category_name');
        $groups = Mst_attribute_group::pluck('group_name');
        $values = Mst_attribute_value::pluck('group_value');
        
        return [
            '*.branches' => ['required',Rule::in($branches)],
            '*.ean' => ['required',Rule::unique('mst_store_products','product_code')],
            '*.sku' => ['required','numeric'],
            '*.product_description' => ['required'],
            '*.rsp_retail_selling_price' => ['nullable'],
            '*.original_price_if_there_is_a_discount' => ['required'],
            '*.stock_qty' => ['required'],
            '*.subcategory' => ['required',Rule::in($cats)],
            '*.group' => ['required',Rule::in($groups)],
            '*.value' => ['required',Rule::in($values)],
            
            
        ];
    }
        

    public function onFailure(Failure $failures)
    {
        throw ValidationException::withMessages([$failures->errors()]);
    }
}
