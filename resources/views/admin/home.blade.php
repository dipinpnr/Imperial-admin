@extends('admin.layouts.app')
@section('content')
 
   <div class="col-lg-12 col-md-12 col-sm-12 col-xl-12">
      <div class="row">
          
          
          
          <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
                    <div class="card">
                      <div class="card-body text-center statistics-info">
                        <div class="counter-icon bg-primary mb-0 box-primary-shadow">
                           <i class="fa fa-cart-plus" aria-hidden="true"></i>
                        </div>
                        <h6 class="mt-4 mb-1">{{ __('Products') }}</h6>
                                                <h2 class="mb-2 number-font counter-count">
                                                  {{  (new \App\Helpers\Helper)->totalProductCount() }}</h2>
                                                <p class="text-muted">{{ __('Total Products ') }}</p>
                                  </div>
                    </div>
                  </div>
                  
                            <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
                    <div class="card">
                      <div class="card-body text-center statistics-info">
                        <div class="counter-icon bg-primary mb-0 box-primary-shadow">
                           <i class="fa fa-pie-chart" aria-hidden="true"></i>
                        </div>
                          <h6 class="mt-4 mb-1">{{ __(' Categories') }}</h6>
                                                    <h2 class="mb-2 number-font counter-count">
                                                       {{ (new \App\Helpers\Helper)->totalCategories() }} 
                                                    </h2>
                                                    <p class="text-muted">{{ __('Total Categories Count') }}</p>
                                                </div>
                    </div>
                  </div>
                

                                  <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
                    <div class="card">
                      <div class="card-body text-center statistics-info">
                        <div class="counter-icon bg-success mb-0 box-primary-shadow">
                           <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                        </div>
                        <h6 class="mt-4 mb-1">{{__('Today\'s Sales')  }}</h6>
                                                  <h2 class="mb-2  "><i class="fa fa-rupee counter-count number-font">
                                                      {{  (new \App\Helpers\Helper)->todaySales() }}
                                                  </i>
                                                  </h2>
                                                <p class="text-muted">{{  __('Today\'s Sales Amount ') }}</p>
                                  </div>
                    </div>
                    </div>
                      <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
                    <div class="card">
                      <div class="card-body text-center statistics-info">
                        <div class="counter-icon bg-success mb-0 box-primary-shadow">
                           <i class="fa fa-inr" aria-hidden="true"></i>
                        </div>
                        <h6 class="mt-4 mb-1">{{__('Total Sales')  }}</h6>
                                                  <h2 class="mb-2  "><i class="fa fa-rupee counter-count number-font">
                                                      {{  (new \App\Helpers\Helper)->totalSales() }}
                                                  </i>
                                                  </h2>
                                                <p class="text-muted">{{  __('Total Sales Amount ') }}</p>
                                  </div>
                    </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
                    <div class="card">
                      <div class="card-body text-center statistics-info">
                        <div class="counter-icon bg-secondary mb-0 box-primary-shadow">
                           <i class="fa fa-plus" aria-hidden="true"></i>
                        </div>
                         <h6 class="mt-4 mb-1">{{ __('Today\'s Orders') }}</h6>
                                                   <h2 class="mb-2 number-font counter-count">
                                                      {{ (new \App\Helpers\Helper)->todayOrder() }} 
                                                   </h2>
                                                   <p class="text-muted">{{ __('Today\'s Orders Count') }}</p>
                                                </div>
                    </div>
                    </div>
                                  
                  
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
                    <div class="card">
                      <div class="card-body text-center statistics-info">
                        <div class="counter-icon bg-secondary mb-0 box-primary-shadow">
                           <i class="fa fa-bar-chart" aria-hidden="true"></i>
                        </div>
                         <h6 class="mt-4 mb-1">{{ __('Orders') }}</h6>
                                                   <h2 class="mb-2 number-font counter-count">
                                                      {{ (new \App\Helpers\Helper)->totalOrder() }} 
                                                   </h2>
                                                   <p class="text-muted">{{ __('Total Orders') }}</p>
                                                </div>
                    </div>
                    </div>
                                   {{-- <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
                    <div class="card">
                      <div class="card-body text-center statistics-info">
                        <div class="counter-icon bg-danger mb-0 box-primary-shadow">
                           <i class="fa fa-times-circle-o" aria-hidden="true"></i>
                        </div>
                         <h6 class="mt-4 mb-1">{{ __('Issues') }}</h6>
                                             <h2 class="mb-2 number-font counter-count">
                                                {{ (new \App\Helpers\Helper)->totalIssues() }} 
                                             </h2>
                                             <p class="text-muted">{{ __('Total Issues ') }}</p>
                                                </div>
                    </div>
                    </div>
                     <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
                    <div class="card">
                      <div class="card-body text-center statistics-info">
                        <div class="counter-icon bg-danger mb-0 box-primary-shadow">
                           <i class="fa fa-times-circle" aria-hidden="true"></i>
                        </div>
                        <h6 class="mt-4 mb-1">{{ __('New Issues') }}</h6>
                                                <h2 class="mb-2 number-font counter-count">
                                                   {{ (new \App\Helpers\Helper)->newIssues() }} 
                                                </h2>
                                                <p class="text-muted">{{ __('New Issues Count') }}</p>
                                                </div>
                    </div>
                    </div>--}}          
        

    
    </div>

  </div> 

    {{-- </div> --}}





</div>

</div>

<!-- ROW-1 END -->
</div>

</div>
<!-- CONTAINER END -->
</div>



<script src="https://www.gstatic.com/firebasejs/8.3.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.3.0/firebase-messaging.js"></script>
<script>
  // var firebaseConfig = {
  //    apiKey: "AIzaSyABJjLKVYHKL020Zdi8pbHsNS2ZLQ1Ka4Q",
  //   authDomain: "yellowstore-web-application.firebaseapp.com",
  //   projectId: "yellowstore-web-application",
  //   storageBucket: "yellowstore-web-application.appspot.com",
  //   messagingSenderId: "444886856017",
  //   appId: "1:444886856017:web:935481722416346323e370",
  //   measurementId: "G-VX5SKTNN3F"
  // };
      
  //   firebase.initializeApp(firebaseConfig);
  //   const messaging = firebase.messaging();
  
  // document.addEventListener("DOMContentLoaded", function(){

  //           messaging
  //           .requestPermission()
  //           .then(function () {
  //             //  console.log("working");
  //               return messaging.getToken({ vapidKey: 'BA6V328NpU3KBKusQbV067G1jKrBpypf1KmnNd21d5wt8gYmHDJIOFUvs0UeYGE1KvTrntnSTkBy3Otg0VQUFmc' });
  //           })
  //           .then(function(token) {
  //              // console.log(token);
  //    var _token = $('input[name="_token"]').val();

  //     $.ajax({
  //           url:"{{ url('store.saveBrowserToken') }}",
  //           method:"POST",
  //           data:{token:token, _token:_token},
  //           success:function(result)
  //           {
  //              // console.log(result);
  //           }
  //      })         
  
  //           }).catch(function (err) {
  //               console.log('User Chat Token Error'+ err);
  //           });
  // });      
  //   messaging.onMessage(function(payload) {
  //       const noteTitle = payload.notification.title;
  //       const noteOptions = {
  //           body: payload.notification.body,
  //           icon: payload.notification.icon,
  //       };
  //       new Notification(noteTitle, noteOptions);
  //   });
  $('.counter-count').each(function () {
        $(this).prop('Counter',0).animate({
            Counter: $(this).text()
        }, {
            duration: 5000,
            easing: 'swing',
            step: function (now) {
                $(this).text(Math.ceil(now));
            }
        });
    });
   
</script>

@endsection
