<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\MasterController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\CustomerController;
use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\admin\CouponController;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/products/sample', function () {
    
    $filepath = public_path('assets/uploads/sample_products.xlsx');

    return Response::download($filepath); 

})->name('download.products.sample');


Route::get('/clear-cache', function () {
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('config:cache');
    return 'DONE'; //Return anything
});

Route::group(['namespace' => 'admin'], function ()
{

    Route::get('admin-login', [HomeController::class, 'index'])
        ->name('admin.login');
    Route::get('/admin-home', [AdminController::class,'adminHome'])
        ->name('admin.home');

     //Delivery Area
    // list and manage
    Route::get('/admin/area/list', [MasterController::class,'listArea'])
        ->name('admin.area');
    //  add
    Route::get('/admin/area/create', [MasterController::class,'createarea'])
        ->name('admin.create_area');
    // store 
    Route::post('/admin/area/store', [MasterController::class,'storearea'])
        ->name('admin.store_area');
    //change status
    Route::get('admin/ajax/change-status/area', [MasterController::class,'editStatusArea']);
    // remove area
    Route::post('/admin/area/remove/{area_id}', [MasterController::class,'removeArea'])
        ->name('admin.destroy_area');
    // update area
    Route::post('/admin/area/update/{area_id}', [MasterController::class,'updateArea'])
        ->name('admin.update_area');
    // edit area view
    Route::get('/admin/area/edit/{area_id}', [MasterController::class,'editArea'])
        ->name('admin.edit_area');

    //Branch
    // list and manage
    Route::get('/admin/branch/list', [MasterController::class,'listBranch'])
        ->name('admin.branch');
    //  add
    Route::get('/admin/branch/create', [MasterController::class,'createBranch'])
        ->name('admin.create_branch');
    // store 
    Route::post('/admin/branch/store', [MasterController::class,'storeBranch'])
        ->name('admin.store_branch');
    //change status
    Route::get('admin/ajax/change-status/branch', [MasterController::class,'editStatusBranch']);
     //change status
     Route::get('admin/ajax/change-feature-status/branch', [MasterController::class,'editFeatureStatusBranch']);
    // remove branch
    Route::post('/admin/branch/remove/{branch_id}', [MasterController::class,'removeBranch'])
        ->name('admin.destroy_branch');
    // update branch
    Route::post('/admin/branch/update/{branch_id}', [MasterController::class,'updateBranch'])
        ->name('admin.update_branch');
    // edit branch view
    Route::get('/admin/branch/edit/{branch_id}', [MasterController::class,'editBranch'])
        ->name('admin.edit_branch');

     Route::get('admin/branch/view/{id}', [MasterController::class,'viewBranch'])->name('admin.view_branch');
   
    
    //units
    // list and manage
    Route::get('/admin/units/list', [MasterController::class,'listUnit'])
        ->name('admin.units');
    // add unit view
    Route::get('/admin/unit/create', [MasterController::class,'createUnit'])
        ->name('admin.create_unit');
    // store unit
    Route::post('/admin/unit/store', [MasterController::class,'storeUnit'])
        ->name('admin.store_unit');
    // remove unit
    Route::post('/admin/unit/remove/{unit_id}', [MasterController::class,'removeUnit'])
        ->name('admin.destroy_unit');
    // update unit
    Route::post('/admin/unit/update/{unit_id}', [MasterController::class,'updateUnit'])
        ->name('admin.update_unit');
    // edit unit view
    Route::get('/admin/unit/edit/{unit_id}', [MasterController::class,'editUnit'])
        ->name('admin.edit_unit');
    //change status
    Route::get('admin/ajax/change-status/unit', [MasterController::class,'editStatusUnit']);

    //item category
    // list and manage
    Route::get('/admin/item-category/list', [MasterController::class,'listItemCategory'])
        ->name('admin.item_category');
    // add category view
    Route::get('/admin/item-category/create', [MasterController::class,'createItemCategory'])
        ->name('admin.create_item_category');
    // store category
    Route::post('/admin/item-category/store', [MasterController::class,'storeItemCategory'])
        ->name('admin.store_item_category');
    // remove category
    Route::post('/admin/item-category/remove/{item_category_id}', [MasterController::class,'removeItemCategory'])
        ->name('admin.destroy_item_category');
    // update category
    Route::post('/admin/item-category/update/{item_category_id}', [MasterController::class,'updateItemCategory'])
        ->name('admin.update_item_category');
    // edit category view
    Route::get('/admin/item-category/edit/{category_name_slug}', [MasterController::class,'editItemCategory'])
        ->name('admin.edit_item_category');
    //change status
    Route::get('admin/ajax/change-status/item-category', [MasterController::class,'editStatusItemCategory']);

     //item sub category
    // list and manage
    Route::get('/admin/item-sub-category/list', [MasterController::class,'listItemSubCategory'])
        ->name('admin.item_sub_category');
    // add sub category view
    Route::get('/admin/item-sub-category/create', [MasterController::class,'createItemSubCategory'])
        ->name('admin.create_item_sub_category');
    // store sub category
    Route::post('/admin/item-sub-category/store', [MasterController::class,'storeItemSubCategory'])
        ->name('admin.store_item_sub_category');
    // remove sub  category
    Route::post('/admin/item-sub-category/remove/{item_sub_category_id}', [MasterController::class,'removeItemSubCategory'])
        ->name('admin.destroy_item_sub_category');
    // update  sub category
    Route::post('/admin/item-sub-category/update/{item_sub_category_id}', [MasterController::class,'updateItemSubCategory'])
        ->name('admin.update_item_sub_category');
    // edit sub category view
    Route::get('/admin/item-sub-category/edit/{item_sub_category_id}', [MasterController::class,'editItemSubCategory'])
        ->name('admin.edit_item_sub_category');
    //change status
    Route::get('admin/ajax/change-status/item-sub-category', [MasterController::class,'editStatusItemSubCategory']);

    Route::post('subcat', [MasterController::class, 'subCat'])->name('subcat');
    
    Route::post('subsubcat', [MasterController::class, 'subCatLvlTwo'])->name('subsubcat');

    //brands
    // list and manage
    Route::get('/admin/brands/list', [MasterController::class,'listBrand'])
        ->name('admin.brands');
    // add brand view
    Route::get('/admin/brand/create', [MasterController::class,'createBrand'])
        ->name('admin.create_brand');
    // store brand
    Route::post('/admin/brand/store', [MasterController::class,'storeBrand'])
        ->name('admin.store_brand');
    // remove brand
    Route::post('/admin/brand/remove/{brand_id}', [MasterController::class,'removeBrand'])
        ->name('admin.destroy_brand');
    Route::get('/admin/brand/delete_brand_category/{category_id_id}', [MasterController::class,'removeBrandCat'])
        ->name('admin.delete_brand_category');    
    // update brand
    Route::post('/admin/brand/update/{brand_id}', [MasterController::class,'updateBrand'])
        ->name('admin.update_brand');
    // edit brand view
    Route::get('/admin/brand/edit/{brand_id}', [MasterController::class,'editBrand'])
        ->name('admin.edit_brand');
    //change status
    Route::get('admin/ajax/change-status/brand', [MasterController::class,'editStatusBrand']);

    //customer
     Route::get('admin/customers/list', 
     [CustomerController::class,'listCustomers'])
        ->name('admin.customers');
    // add customer view
    Route::get('/admin/customer/create', 
    [CustomerController::class,'createCustomer'])
        ->name('admin.create_customer');
    // store customer
    Route::post('/admin/customer/store', 
    [CustomerController::class,'storeCustomer'])
        ->name('admin.store_customer');
    // remove customer
    Route::post('/admin/customer/remove/{customer_id}', 
    [CustomerController::class,'removeCustomer'])
        ->name('admin.destroy_customer');
    // update customer
    Route::post('/admin/customer/update/{customer_id}', 
    [CustomerController::class,'updateCustomer'])
        ->name('admin.update_customer');
    // edit customer view
    Route::get('/admin/customer/edit/{customer_id}', 
    [CustomerController::class,'editCustomer'])
        ->name('admin.edit_customer');
    //change status
    Route::get('admin/ajax/change-status/customer', 
    [CustomerController::class,'editStatusCustomer']);



    //customer_group
    // list and manage
    Route::get('/admin/customer-group/list',[CustomerController::class,'listCustomerGroup'])
        ->name('admin.customer_groups');
    // add customer_group view
    Route::get('/admin/customer-group/create',[CustomerController::class,'createCustomerGroup'])
        ->name('admin.create_customer_group');
    // store customer_group
    Route::post('/admin/customer-group/store',[CustomerController::class,'storeCustomerGroup'])
        ->name('admin.store_customer_group');
    // remove customer_group
    Route::post('/admin/customer-group/remove/{customer_group_id}',[CustomerController::class,'removeCustomerGroup'])
        ->name('admin.destroy_customer_group');
    // update customer_group
    Route::post('/admin/customer-group/update/{customer_group_id}',[CustomerController::class,'updateCustomerGroup'])
        ->name('admin.update_customer_group');
    // edit customer_group view
    Route::get('/admin/customer-group/edit/{customer_group_id}',[CustomerController::class,'editCustomerGroup'])
        ->name('admin.edit_customer_group');
    //change status
    Route::get('admin/ajax/change-status/customer-group',[CustomerController::class,'editStatusCustomerGroup']);

    //customer_group customers
    Route::get('/admin/customer-group-customers/list',[CustomerController::class,'listCustomerGroupCustomers'])
        ->name('admin.customer_group_customers');

    Route::post('/admin/customer-group-customers/remove',[CustomerController::class,'removeCGC'])
        ->name('admin.destroy_cgc');
    Route::get('/admin/customer-group-customers/assign',[CustomerController::class,'assignCGC'])
        ->name('admin.assign_customer_to_cg');
    Route::post('/admin/customer-group-customers/store',[CustomerController::class,'storeCGC'])
        ->name('admin.store_cgc_assign');

    //issues
    Route::get('admin/issues/list',[MasterController::class,'listIssues'])
        ->name('admin.issues');
    // add issue view
    Route::get('/admin/issue/create',[MasterController::class,'createissue'])
        ->name('admin.create_issue');
    // store issue
    Route::post('/admin/issue/store',[MasterController::class,'storeissue'])
        ->name('admin.store_issue');
    // remove issue
    Route::post('/admin/issue/remove/{issue_id}',[MasterController::class,'removeissue'])
        ->name('admin.destroy_issue');
    // update issue
    Route::post('/admin/issue/update/{issue_id}',[MasterController::class,'updateissue'])
        ->name('admin.update_issue');
    // edit issue view
    Route::get('/admin/issue/edit/{issue_id}',[MasterController::class,'editissue'])
        ->name('admin.edit_issue');
    //change status
    Route::get('admin/ajax/change-status/issue',[MasterController::class,'editStatusissue']);
    

        Route::get('store/disputes/list', [AdminController::class,'listDisputes'])->name('store.list_disputes');
    Route::post('store/disputes/status/{dispute_id}', [AdminController::class,'statusDisputes'])->name('store.dispute_status');
    Route::post('store/disputes/store-response/{dispute_id}', [AdminController::class,'storeResponseUpdate'])->name('store.dispute_store_response');

    Route::get('store/dispute-order/view/{id}', [AdminController::class,'viewDisputeOrder'])->name('store.view_dispute_order');

    Route::get('store/current-issues', [AdminController::class,'currentIssues'])->name('store.current_issues');
    Route::get('store/new-issues', [AdminController::class,'newIssues'])->name('store.new_issues');

    //customer_banners
    // list and manage
    Route::get('/admin/customer-banners/list', [MasterController::class,'listCustomerBanner'])
        ->name('admin.customer_banners');
    // add customer_banner view
    Route::get('/admin/customer-banner/create', [MasterController::class,'createCustomerBanner'])
        ->name('admin.create_customer_banner');
    // store customer_banner
    Route::post('/admin/customer-banner/store', [MasterController::class,'storeCustomerBanner'])
        ->name('admin.store_customer_banner');
    // update customer_banner
    Route::post('/admin/customer-banner/update/{customer_banner_id}', [MasterController::class,'updateCustomerBanner'])
        ->name('admin.update_customer_banner');
    // remove customer_banner
    Route::post('/admin/customer-banner/remove/{customer_banner_id}', [MasterController::class,'removeCustomerBanner'])
        ->name('admin.destroy_customer_banner');
    // edit customer_banner view
    Route::get('/admin/customer-banner/edit/{customer_banner_id}', [MasterController::class,'editCustomerBanner'])
        ->name('admin.edit_customer_banner');
    //change status
    Route::get('admin/ajax/change-status/customer-banner', [MasterController::class,'editStatusCustomerBanner']);
    
    //add/remove from homepge
    Route::get('admin/ajax/add_to_home/item-category', [MasterController::class,'addToHomeItemCategory']);

    Route::get('admin/profile', [AdminController::class,'Profile'])
        ->name('admin.profile');
    Route::get('admin/edit-profile/{id}', [AdminController::class,'editProfile'])
        ->name('admin.edit_profile');
    Route::post('admin/update-profile/{id}', [AdminController::class,'updateProfile'])
        ->name('admin.update_profile');

    //Route::get('admin/settings', [AdminController::class,'Settings']) ->name('admin.settings');
    //Route::post('admin/settings', [AdminController::class,'UpdateSettings']) ->name('admin.update_sas');

     //offers
    // list and manage
    Route::get('/admin/offers/list', [AdminController::class,'listoffer'])
        ->name('admin.offers');
    // add offer view
    Route::get('/admin/offer/create', [AdminController::class,'createoffer'])
        ->name('admin.create_offer');
    // store offer
    Route::post('/admin/offer/store', [AdminController::class,'storeoffer'])
        ->name('admin.store_offer');
    // remove offer
    Route::post('/admin/offer/remove/{offer_id}', [AdminController::class,'removeoffer'])
        ->name('admin.destroy_offer');
    // update offer
    Route::post('/admin/offer/update/{offer_id}', [AdminController::class,'updateoffer'])
        ->name('admin.update_offer');
    // edit offer view
    Route::get('/admin/offer/edit/{offer_id}', [AdminController::class,'editoffer'])
        ->name('admin.edit_offer');
    //change status
    Route::get('admin/ajax/change-status/offer', [AdminController::class,'editStatusoffer']);
    Route::get('admin/offer/get-product-by-category/{category}/{branch}', [AdminController::class,'GetItemByCategory']);


    
     //tax master
     Route::get('admin/tax/list', [AdminController::class,'listTaxes'])->name('admin.list_taxes');
     Route::post('admin/tax/create', [AdminController::class,'createTax'])->name('admin.create_tax');
     Route::post('admin/tax/remove/{tax_id}', [AdminController::class,'removeTax'])->name('admin.destroy_tax');
     Route::post('admin/tax/update/{tax_id}', [AdminController::class,'updateTax'])->name('admin.update_tax');
     Route::get('admin/tax/edit/{tax_id}', [AdminController::class,'editTax'])->name('admin.edit_tax');
     Route::get('admin/tax/add', [AdminController::class,'addTaxes'])->name('admin.add_taxes');

     Route::get('admin/tax/restore-list', [AdminController::class,'listRestoreTaxes'])->name('admin.restore_list_taxes');
     Route::post('admin/tax/restore/{tax_id}', [AdminController::class,'restoreTax'])->name('admin.restore_tax');

     //attribute group

     Route::get('store/attribute_group/list', [AdminController::class,'listAttributeGroup'])->name('admin.list_attribute_group');

     Route::post('store/attribute_group/store_attr', [AdminController::class,'storeAttribute'])->name('save_attribute_group');

     Route::get('store/attribute_group/edit/{id}', [AdminController::class,'editAttributeGroup'])->name('store.edit_attribute_group');
     Route::post('store/attribute_group/update/{attr_group_id}', [AdminController::class,'updateAtrGroup'])->name('store.update_attribute_group');
     Route::post('store/attribute_group/destroy/{attribute_group}', [AdminController::class,'destroyAttr_Group'])->name('store.destroy_attribute_group');

     //attribute value

     Route::get('store/attribute_value/list', [AdminController::class,'listAttr_Value'])->name('admin.list_attribute_value');

     Route::get('store/attribute_value/create', [AdminController::class,'createAttr_Value'])->name('store.create_attribute_value');

     Route::post('store/attribute_value/store', [AdminController::class,'storeAttr_Value'])->name('store.store_attribute_value');

     Route::get('store/attribute_value/edit/{id}', [AdminController::class,'editAttr_Value'])->name('store.edit_attribute_value');

     Route::post('store/attribute_value/update/{attr_value_id}', [AdminController::class,'updateAttr_Value'])->name('store.update_attribute_value');

     Route::post('store/attribute_value/destroy/{attribute_value}', [AdminController::class,'destroyAttr_Value'])->name('store.destroy_attribute_value');


      // product
      Route::get('store/product/list', [ProductController::class,'listProduct'])->name('store.list_product');

      Route::get('store/product/create', [ProductController::class,'createProduct'])->name('store.create_product');

      Route::post('store/product/store', [ProductController::class,'storeProduct'])->name('store.store_product');
      
      Route::get('admin/ajax/change-status/product', [ProductController::class,'editStatusProduct']);
      
      Route::get('admin/ajax/add-to-missme/product', [ProductController::class,'addToMissMeProduct']);

      Route::get('store/product/edit/{id}', [ProductController::class,'editProduct'])->name('store.edit_product');
      Route::get('store/ajax/product/set_default_image', [ProductController::class,'setDefaultImage']);
      // Route::get('product/ajax/is-code-available', 'CouponController@isPCodeAvailable');


      Route::get('admin/change-img-status/{store_id}', [ProductController::class,'statusStoreIMG'])->name('admin.status_storeIMG');

      Route::post('admin/assign_products', [ProductController::class,'assignProducts'])->name('assign_products');
      
      Route::post('store/product/update/{product_id}', [ProductController::class,'updateProduct'])->name('store.update_product');
      Route::post('store/product/update/images/{product_id}', [ProductController::class,'updateProductImages'])->name('store.update_product_images');

      Route::get('store/product/view/{id}', [ProductController::class,'viewProduct'])->name('store.view_product');

      Route::post('store/product/destroy/{product}', [ProductController::class,'destroyProduct'])->name('store.destroy_product');
      Route::post('store/product/destroy/image/{product_image_id}', [ProductController::class,'destroyProductImage'])->name('store.destroy_product_image');

      Route::post('store/product/status/{product_id}', [ProductController::class,'statusProduct'])->name('store.status_product');
      Route::post('store/product/stock/update/{product_id}', [ProductController::class,'stockUpdate'])->name('store.stock_update_product');

      Route::post('store/product/attribute/destroy/{attr_groups}', [ProductController::class,'destroyAttribute'])->name('store.destroy_attribute');
      Route::post('store/product/attribute/store', [ProductController::class,'storeAttribute'])->name('store.store_attribute');


      Route::post('store/product/variant/destroy/{product_varient_id}', [ProductController::class,'destroyProductVariant'])->name('store.destroy_product_variant');
      Route::post('store/product/variant/attr/destroy/{variant_attribute_id}', [ProductController::class,'destroyProductVariantAttr'])->name('store.destroy_product_var_attr');
      Route::post('store/product/variant/attr/add', [ProductController::class,'addProductVariantAttr'])->name('store.add_attr_to_variant');
      Route::get('store/product/variant/list/{product_id}', [ProductController::class,'listProductVariant']);


      Route::get('ajax/product-variant/attr-remove', [ProductController::class,'GetVarAttr_Remove']);
      Route::get('ajax/product-variant/attr-count', [ProductController::class,'GetVarAttr_Count']);


      Route::get('store/product/variant/edit/{product_varient_id}', [ProductController::class,'editProductVariant']);
      Route::post('store/product/variant/update/{product_varient_id}', [ProductController::class,'updateProductVariant'])->name('store.update_product_variant');

      Route::get('store/product/restore', [ProductController::class,'restoreProduct'])->name('store.restore-products');
      
      Route::get('store/restore-product/{id}',[ProductController::class,'restoreProductSave'])->name('store.restore-products-save');

      // get parent cat and sub cat by ajax

      Route::get('store/product/ajax/get_category', [ProductController::class,'GetCategory']);

      Route::get('store/product/ajax/get_subcategory', [ProductController::class,'GetSubCategory']);
      Route::get('store/product/ajax/get_attr_value', [ProductController::class,'GetAttr_Value']);
      Route::get('store/ajax/find-availble-dboys', [ProductController::class,'GetAvailableDBoy']);
      
       // inventory management
       Route::post('store/product-video-remove/{product_video_id}', [ProductController::class,'removeProductVideo'])->name('store.destroy_product_video');

       Route::get('store/inventory/list', [ProductController::class,'listInventory'])->name('store.list_inventory');
       Route::post('store/stock/update/ajax', [ProductController::class,'UpdateStock'])->name('store.stock_update');
       Route::post('store/stock/reset/ajax', [ProductController::class,'resetStock'])->name('store.stock_reset');
              
        // order Managemnet

        Route::get('store/order/list',[OrderController::class,'listOrder'])->name('store.list_order');
        Route::get('store/today-order/list',[OrderController::class,'listTodaysOrder'])->name('store.list_todays_order');
        Route::get('store/order/view/{id}',[OrderController::class,'viewOrder'])->name('store.view_order');
        Route::post('store/order/update/{id}',[OrderController::class,'updateOrder'])->name('store.update_order');
        Route::get('store/order/invoice/{id}',[OrderController::class,'viewInvoice'])->name('store.invoice_order');
        Route::post('store/order/status/{order_id}',[OrderController::class,'OrderStatus'])->name('store.order_status');

        Route::get('store/share-items',[OrderController::class,'ShareItems'])->name('store.share_item_list');

        //invoice 

        Route::get('store/product_invoice/pdf/{id}',[OrderController::class,'generatePdf'])->name('store.generate_invoice_pdf');
        Route::get('store/product_invoice/whatsup/send/{id}',[OrderController::class,'SendInvoice'])->name('store.send_invoice');

        Route::get('store/assign_order/delivery_boy/{id}', [OrderController::class,'AssignOrder'])->name('store.assign_order');
        Route::post('store/assign_order/delivery_boy/{order_id}', [OrderController::class,'storeAssignedOrder'])->name('store.assign_store_order');
        
        Route::get('admin/customer_reward/list', [CustomerController::class,'listCustomerReward'])->name('admin.list_customer_reward');
        Route::get('admin/add/reward-to-customer', [CustomerController::class,'addRewardToCustomer'])->name('admin.add_reward_to_customer');
        Route::get('admin/add/reward-to-existing-customer', [CustomerController::class,'addReward'])->name('admin.add_rew_exis_cus');
        Route::post('admin/store/reward-to-customer', [CustomerController::class,'storeReward'])->name('admin.store_po_exis_cus');
        Route::post('admin/store/reward-to-existing-customer', [CustomerController::class,'storeRewardToCustomer'])->name('admin.store_points_to_customer');
        Route::get('admin/list/reward-to-customer', [CustomerController::class,'listRewardToCustomer'])->name('admin.list_points_to_customer');

        Route::post('admin/remove/reward-to-customer/{reward_to_customer_id}', [CustomerController::class,'removeRewardToCustomer'])->name('admin.remove_points_to_customer');
        Route::post('admin/remove/temp/reward-to-customer/{reward_to_customer_temp_id}', [CustomerController::class,'removeTempRewardToCustomer'])->name('admin.remove_temp__points_to_customer');

        Route::get('admin/edit/reward-to-customer/{reward_to_customer_id}', [CustomerController::class,'editRewardToCustomer'])->name('admin.edit_points_to_customer');
        Route::get('admin/edit/temp/reward-to-customer/{reward_to_customer_temp_id}', [CustomerController::class,'editTempRewardToCustomer'])->name('admin.edit_temp_points_to_customer');

        Route::post('admin/update/reward-to-customer/{reward_to_customer_id}', [CustomerController::class,'updateRewardToCustomer'])->name('admin.update_points_to_customer');
        Route::post('admin/update/temp/reward-to-customer/{reward_to_customer_temp_id}', [CustomerController::class,'updateTempRewardToCustomer'])->name('admin.update_temp_points_to_customer');

        Route::get('admin/configure_points/list', [CustomerController::class,'listConfigurePoints'])->name('admin.list_configure_points');

        Route::post('admin/configure_points/store/{cf_id}', [CustomerController::class,'storeConfigurePoints'])->name('admin.store_configure_points');
        
        // store Report

        Route::get('store/product-wise-report', [CouponController::class,'showReport'])->name('store.show_reports');
        Route::get('store/store-visit-report', [CouponController::class,'showStoreVisitReport'])->name('store.store_visit_reports');
        Route::get('store/town-name-list', [CouponController::class,'listTownNames']);
        Route::get('store/sales-report', [CouponController::class,'showSalesReport'])->name('store.sales_reports');
        Route::get('store/inventory-report', [CouponController::class,'showInventoryReport'])->name('store.inventory_reports');
        Route::get('store/out-of-stock-report', [CouponController::class,'showOutofStockReport'])->name('store.out_of_stock_reports');

        Route::get('store/delivery-report', [CouponController::class,'deliveryReport'])->name('store.delivery_reports');
        Route::get('store/payment-report', [CouponController::class,'paymentReport'])->name('store.payment_reports');


        Route::get('store/online-sales-report', [CouponController::class,'showOnlineSalesReport'])->name('store.online_sales_reports');
        Route::get('store/offline-sales-report', [CouponController::class,'showOfflineSalesReport'])->name('store.offline_sales_reports');

        Route::post('store/browser-token/save', [CouponController::class,'saveBrowserToken'])->name('store.saveBrowserToken');

        Route::get('store/incoming-payments', [CouponController::class,'storeIncomingPayments']);

        Route::get('store/refund-reports', [CouponController::class,'storeRefundReports'])->name('store.refund-reports');

        // coupon

        Route::get('store/coupon/list',[CouponController::class, 'listCoupon'])->name('store.list_coupon');
        Route::get('store/coupon/create',[CouponController::class, 'createCoupon'])->name('store.create_coupon');
        Route::post('store/coupon/store',[CouponController::class, 'storecoupon'])->name('store.store_coupon');
        Route::post('store/coupon/remove/{coupon_id}',[CouponController::class, 'removecoupon'])->name('store.destroy_coupon');
        Route::post('store/coupon/update/{coupon_id}',[CouponController::class, 'updatecoupon'])->name('store.update_coupon');
        Route::get('store/coupon/edit/{coupon_id}',[CouponController::class, 'editcoupon'])->name('store.edit_coupon');
        
        Route::get('admin/update-terms',[AdminController::class,'updateTerms'])->name('admin.edit_terms');
        Route::get('admin/update-privacy',[AdminController::class,'updateCusTerms'])->name('admin.edit_terms_customer');
        Route::post('admin/update-tc',[AdminController::class,'updateTC'])->name('admin.update_tc');
        Route::post('admin/customer/update-tc',[AdminController::class,'updateCusTC'])->name('admin.update_cus_tc');

        // time_slots
        Route::get('store/time/slot', [AdminController::class,'time_slot'])->name('store.time_slots');
        Route::post('store/time/slot/update', [AdminController::class,'updateTimeSlot'])->name('store.update_time_slot');

        Route::get('store/delivery/time/slot',[AdminController::class,'delivery_time_slots'])->name('store.delivery_time_slots');
        Route::post('store/delivery/time/slot/update',[AdminController::class,'update_delivery_time_slots'])->name('store.update_delivery_time_slots');

        Route::get('admin/product-name-list', [ProductController::class,'listProductNames']);
        
        Route::get('admin/branch/get-delivery-areas/{id}', [MasterController::class,'listDeliveryAreas']);
        
        Route::get('admin/branch/edit/get-delivery-areas/{id}/{branch}', [MasterController::class,'updateDeliveryAreas']);

        Route::get('/admin/global/product/import', [ProductController::class,'importGlobalProduct'])->name('admin.import_global_product');
        
        Route::post('/admin/global/product/post-import', [ProductController::class,'postImportGlobalProduct'])->name('admin.store_imported_global_products');
        
        //ratings
        
        Route::get('/admin/product/reviews', [ProductController::class,'listReviews'])->name('admin.reviews');
        
        //change status
       Route::get('admin/ajax/change-status/review', [ProductController::class,'editStatusReviews']);


   
});


Auth::routes(['register' => false]);

// Route::get('/home', 'HomeController@index')
//     ->name('home');

// //logout from other devices
// Route::get('logoutOthers', function ()
// {
//     auth()
//         ->logoutOtherDevices('password');
//     return redirect('/');
// });

