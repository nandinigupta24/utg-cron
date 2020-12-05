@extends('layouts.inner')
@section('title', 'Change Password')
@section('content')
<div class="content-wrapper">
    @include('elements.msg')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Change Password</h4>
                    <p class="card-description"></p>
                    <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form class="forms-sample" method="post" action="{{route('PostChangePassword')}}">
                            {{csrf_field()}}
                        <div class="form-group">
                          <label for="exampleInputEmail1">Current Password</label>
                          <input type="password" class="form-control" name="current-password"  placeholder="Enter current password">
                        </div>
                        <div class="form-group">
                          <label for="exampleInputPassword1">New Password</label>
                          <input type="password" class="form-control" name="new-password" placeholder="Enter New Password">
                        </div>
<!--                        <div class="form-group">
                          <label for="exampleInputPassword1">Confirm Password</label>
                          <input type="password" class="form-control"  name="confirm-password" placeholder="Enter Confirm Password">
                        </div>-->
                        <button type="submit" class="btn btn-success mr-2">Submit</button>
                        <button class="btn btn-light">Cancel</button>
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
