@extends('layouts.inner')
@section('title', 'Cron Details')
@section('content')
<div class="content-wrapper">
    @include('elements.msg')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Email Address Details<a href="{{route('EmailManage')}}" class="pull-right btn btn-success">manage</a></h4>
                    <p class="card-description"></p>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody class="">
                                <tr>
                                    <td>Name</td>
                                    <td>{{@$data->name}}</td>
                                </tr>
                                <tr>
                                  <td>Email</td>
                                  <td>{{@$data->email}}</td>
                                </tr>
                                <tr>
                                    <td>Status</td>
                                    <td>{!!get_status($data->status)!!}</td>
                                </tr>
                                <tr>
                                    <td>Created</td>
                                    <td>{{@$data->created_at}}</td>
                                </tr>
                                <tr>
                                    <td>Updated</td>
                                    <td>{{@$data->updated_at}}</td>
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
