@extends('layouts.inner')
@section('title', 'User Details')
@section('content')
<div class="content-wrapper">
    @include('elements.msg')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Users Details<a href="{{route('UserListings')}}" class="pull-right btn btn-success">manage</a></h4>
                    <p class="card-description"></p>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody class="">
                                <tr>
                                    <td>Name</td>
                                    <td>{{@$data->name}}</td>
                                </tr>
                                <tr>
                                  <td>email</td>
                                  <td>{{@$data->email}}</td>
                                </tr>
                                <tr>
                                    <td>role</td>
                                    <td>{{@$data->role}}</td>
                                </tr>
<!--                                <tr>
                                    <td>Email Notification</td>
                                    <td>{{@$data->email_notification}}</td>
                                </tr>-->
                                <tr>
                                    <td>Status</td>
                                    <td>{{@$data->status}}</td>
                                </tr>
                                <tr>
                                    <td>Created</td>
                                    <td>{{@$data->created_at}}</td>
                                </tr>
                            </tbody>
                        </table>
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
