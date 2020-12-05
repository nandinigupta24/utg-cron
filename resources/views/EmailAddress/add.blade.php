@extends('layouts.inner')
@section('title', 'Add Email Address')
@section('content')
<div class="content-wrapper">
    @include('elements.msg')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Add Email Address</h4>
                    <p class="card-description"></p>
                    <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form class="forms-sample" method="post" action="{{route('EmailStore')}}">
                            {{csrf_field()}}
                        <div class="form-group">
                          <label for="exampleInputEmail1">Name</label>
                          <input type="text" class="form-control" name="name"  placeholder="Enter Name" required="" value="{{old('name')}}">
                        </div>
                        <div class="form-group">
                          <label for="exampleInputPassword1">Email Address</label>
                          <input type="text" class="form-control" name="email" placeholder="Enter Email Address" required="" value="{{old('email')}}">
                        </div>
                        <div class="form-group">
                          <label for="exampleInputPassword1">Status</label>
                          <select class="form-control" name="status" required="">
                              <option value="">Select Option</option>
                              <option value="published">Published</option>
                              <option value="unpublished">UnPublished</option>
                          </select>
                        </div>

                        <button type="submit" class="btn btn-success mr-2">Submit</button>
                        <button type="button" class="btn btn-danger">Cancel</button>
                      </form>
                    </div>
                  </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
