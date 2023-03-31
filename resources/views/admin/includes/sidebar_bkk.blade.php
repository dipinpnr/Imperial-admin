<div class="app-sidebar__overlay" data-toggle="sidebar"></div>

   <!--/APP-SIDEBAR-->
   <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
   <aside class="app-sidebar">
      <div class="side-header">
         <a class="header-brand1 " href="#">
            <img src="{{URL::to('/assets/uploads/Frame.png')}}" class="header-brand-img desktop-logo" alt="logo">
           
            <a aria-label="Hide Sidebar" class="app-sidebar__toggle ml-auto" data-toggle="sidebar" href="#"></a><!-- sidebar-toggle-->
         </div>
         <div class="app-sidebar__user">
            <div class="dropdown user-pro-body text-center">
               <div class="user-pic">
                  <img src="{{URL::to('/assets/uploads/admin.png')}}" alt="user-img" class="avatar-xl rounded-circle">
               </div>
               <div class="user-info">
                 {{--  <h6 class=" mb-0 text-dark">{{Auth::user()->name}}</h6> --}}
                  <span class="text-muted app-sidebar__user-name text-sm">{{  auth()->user()->name }}</span>
               </div>
            </div>
         </div>
       
         
         <ul class="side-menu">
            <li><h3>Main</h3></li>
            <li class="slide">
               <a class="side-menu__item" href="{{ route('admin.home') }}"><i class="side-menu__icon ti-shield"></i><span class="side-menu__label">Dashboard</span></a>
            </li>
            
            <li><h3>Components</h3></li>
            
            <li class="slide">
               <a class="side-menu__item"  data-toggle="slide" href="#"><i class="side-menu__icon ti-home"></i><span class="side-menu__label">{{ __('Masters') }}</span><i class="angle fa fa-angle-right"></i></a>
               <ul class="slide-menu">
               {{-- <li><a class="slide-item" href="{{ route('admin.units') }}">{{ __('Units') }}</a></li> --}}
               <li><a class="slide-item" href="{{ route('admin.item_category') }}">{{ __('Category') }}</a></li>
                <li><a class="slide-item" href="{{ route('admin.item_sub_category') }}">{{ __('Sub Category') }}</a></li>
               <li><a class="slide-item" href="{{ route('admin.brands') }}">{{ __('Brands') }}</a></li>
               <!--<li><a class="slide-item" href="{{route('admin.list_taxes')}}">{{ __('Tax') }}</a></li>-->
               <li><a class="slide-item" href="{{route('admin.list_attribute_group')}}">{{ __('Attribute Group') }}</a></li>
               <li><a class="slide-item" href="{{route('admin.list_attribute_value')}}">{{ __('Attribute Value') }}</a></li>
               {{--<li><a class="slide-item" href="{{ route('admin.issues') }}">{{ __('Issues') }}</a></li>--}}
                <li><a class="slide-item" href="{{ route('admin.area') }}">{{ __('Delivery Areas') }}</a></li>
                <li><a class="slide-item" href="{{ route('admin.branch') }}">{{ __('Branches') }}</a></li>
              
              
               </ul>
            </li>

           
            

            <li class="slide">
               <a class="side-menu__item" href="{{ route('admin.customers') }}">
                  <i class="side-menu__icon ti-face-smile"></i>
                  <span class="side-menu__label">Customers</span></a>
            </li>


            <li class="slide">
               <a class="side-menu__item"  data-toggle="slide" href="#">
                  <i class="side-menu__icon fe fe-users"></i>
                  <span class="side-menu__label">{{ __('Customer Group') }}</span>
                  <i class="angle fa fa-angle-right"></i>
               </a>
               <ul class="slide-menu">
                  <li><a class="slide-item" href="{{ route('admin.customer_groups') }}">{{ __('Customers Groups') }}</a></li>
                  <li><a class="slide-item" href="{{ route('admin.customer_group_customers') }}">{{ __('Grouped Customers') }}</a></li>
               </ul>
            </li>

            <li class="slide">
               <a class="side-menu__item"   href="{{route('store.list_product')}}">
               <i class="side-menu__icon ti-package"></i></i>
                  <span class="side-menu__label">Products</span></a>
            </li>
            
            <li class="slide">
               <a class="side-menu__item"   href="{{route('admin.offers')}}">
               <i class="side-menu__icon ti-bell"></i></i>
                  <span class="side-menu__label">Offers</span></a>
            </li>

                     <li class="slide">
               <a class="side-menu__item" href="{{route('store.list_inventory')}}">
                  <i class="side-menu__icon ti-pencil-alt"></i>
                  <span class="side-menu__label"> {{ __('Inventory Management') }}</span>
               </a>
               </li>
       
               <li class="slide">
               <a class="side-menu__item" href="{{route('store.list_order')}}">
                  <i class="side-menu__icon ti-layers"></i>
                  <span class="side-menu__label"> {{ __('Orders') }}</span>
               </a>
               </li>

               <li class="slide">
               <a class="side-menu__item" href="{{ route('admin.customer_banners') }}">
                  <i class="side-menu__icon fe fe-airplay"></i>
                  <span class="side-menu__label">Customer Banner</span></a>
            </li>

                  <li class="slide">
            <a class="side-menu__item" href="{{route('store.list_coupon')}}">
               <i class="side-menu__icon ti-gift"></i>
               <span class="side-menu__label"> {{ __('Coupon') }}</span>
            </a>
            </li>

            <li class="slide">
       <a class="side-menu__item"  data-toggle="slide" href="#"><i class="side-menu__icon ti ti-file"></i><span class="side-menu__label">{{ __('Reports') }}</span><i class="angle fa fa-angle-right"></i></a>
        <ul class="slide-menu">
              <!--<li><a class="slide-item" href="{{route('store.show_reports')}}">{{ __('Product Wise Reports') }}</a></li>-->
              <!-- <li><a class="slide-item" href="{{route('store.store_visit_reports')}}">{{ __('Store Visit Reports') }}</a></li> -->
              <li><a class="slide-item" href="{{route('store.sales_reports')}}">{{ __('Sales Reports') }}</a></li>
              <li><a class="slide-item" href="{{route('store.inventory_reports')}}">{{ __('Inventory Reports') }}</a></li>
              <li><a class="slide-item" href="{{route('store.out_of_stock_reports')}}">{{ __('Out of Stock Reports') }}</a></li>

              <!-- <li><a class="slide-item" href="{{route('store.online_sales_reports')}}">{{ __('Online Sales Reports') }}</a></li>
              <li><a class="slide-item" href="{{route('store.offline_sales_reports')}}">{{ __('Offline Sales Reports') }}</a></li>

              <li><a class="slide-item" href="{{route('store.payment_reports')}}">{{ __('Payment Reports') }}</a></li>
              <li><a class="slide-item" href="{{route('store.delivery_reports')}}">{{ __('Delivery Reports') }}</a></li>
              <li><a class="slide-item" href="{{url('store/incoming-payments')}}">{{ __('Incoming Payments Reports') }}</a></li>
              <li><a class="slide-item" href="{{url('store/refund-reports')}}">{{ __('Refund Reports') }}</a></li> -->

            </ul>
        </li>
     
             
           <!--<li class="slide">-->
           <!-- <a class="side-menu__item"  data-toggle="slide" href="#"><i class="side-menu__icon fa fa-heart"></i><span class="side-menu__label">{{ __('Loyalty Programs') }}</span><i class="angle fa fa-angle-right"></i></a>-->
           <!-- <ul class="slide-menu">-->
           <!--    <li><a class="slide-item" href="{{route('admin.list_configure_points')}}">{{ __('Configure Points') }}</a></li>-->
           <!--    <li><a class="slide-item" href="{{route('admin.list_customer_reward')}}">{{ __('Customer Rewards') }}</a></li>-->
           <!--    <li><a class="slide-item" href="{{route('admin.list_points_to_customer')}}">Reward Points of<br> Non-existing Customer</a></li>-->
           <!-- </ul>-->
           <!-- </li>-->

               
 
        {{--<li class="slide">
        <a class="side-menu__item" href="{{route('store.list_disputes')}}">
          <i class="side-menu__icon ti-comments"></i>
          <span class="side-menu__label"> {{ __('Disputes') }}</span>
        </a>
      </li>--}}
      
      <li class="slide">
        <a class="side-menu__item" href="{{route('admin.reviews')}}">
          <i class="side-menu__icon ti-star"></i>
          <span class="side-menu__label"> {{ __('Product Reviews') }}</span>
        </a>
      </li>

      <li class="slide">
               <a class="side-menu__item"  data-toggle="slide" href="#">
                  <i class="side-menu__icon ti-settings"></i>
                  <span class="side-menu__label">{{ __('Settings') }}</span>
                  <i class="angle fa fa-angle-right"></i>
               </a>
               <ul class="slide-menu">
                  <li><a class="slide-item" href="{{ route('admin.settings') }}">{{ __('Admin settings') }}</a></li>
                  <li><a class="slide-item" href="{{route('store.time_slots')}}">{{ __('Working Days') }}</a></li>
                   <li><a class="slide-item" href="{{route('store.delivery_time_slots')}}">{{ __('Time Slots') }}</a></li>
                  <li><a class="slide-item" href="{{route('admin.edit_terms')}}">{{ __('Store Terms & Conditions') }}</a></li>
                 <li><a class="slide-item" href="{{route('admin.edit_terms_customer')}}">{{ __('Customer Terms & Conditions') }}</a></li>

               </ul>
            </li>


         </ul>
         
      </aside>
      <!--/APP-SIDEBAR-->