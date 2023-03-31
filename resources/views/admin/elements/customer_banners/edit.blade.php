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
                    <form action="{{route('admin.update_customer_banner',['customer_banner_id' => $customerbanner->customer_banner_id ])}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        
                        <div class="col-md-12">
                            <div class="form-group">
                            <label class="form-label">Heading</label>
                            <input type="text" class="form-control" value="{{old('heading',$customerbanner->heading)}}"  required  name="heading" >
                            </div>
                        </div>
                        
                       
                        
                        <div class="col-md-12">
                            <div class="form-group">
                            <label class="form-label">Customer Banner Image</label>
                            <input type="file" class="form-control" accept="image/x-png,image/jpg,image/jpeg"  multiple name="images[]" >
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="form-group">
                            <label class="form-label">Content</label>
                            <textarea name="content" class="form-control">{{$customerbanner->content}}</textarea>
                            </div>
                        </div>
                        
                           
                        
                            <div class="col-md-12">
                                <div class="form-group">
                                    <center>
                                    <button type="submit" class="btn btn-raised btn-primary">
                                    <i class="fa fa-check-square-o"></i> Add</button>
                                    <button type="reset" class="btn btn-raised btn-success">Reset</button>
                                    <a class="btn btn-danger" href="{{ route('admin.customer_banners') }}">Cancel</a>
                                    </center>
                                </div>
                            </div>

                        </div>

                        
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
