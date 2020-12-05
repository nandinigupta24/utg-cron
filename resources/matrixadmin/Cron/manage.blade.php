@extends('layouts.inner')
@section('title', 'Cron Listings')
@section('content')
<div class="content-wrapper">
    @include('elements.msg')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Cron Listings</h4>
                    <p class="card-description"></p>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-primary text-white">
                                <tr style="text-align: center;">
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Syntax</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $Count = get_data_sequence($data->currentpage(), $data->perpage());
                                ?>
                                @foreach($data as $val)
                                <tr>
                                    <td>{{$Count++}}.</td>
                                    <td>{{$val->name}}</td>
                                    <td>{{$val->syntax}}</td>
                                    <td>{{$val->description}}</td>
                                    <td>{!!get_status($val->status)!!}</td>
                                    <td>{{$val->created_at}}</td>
                                    <td>
                                        <a href="{{route('CronEdit',[$val->id])}}" class="text-primary">Edit</a> 
                                        <a href="{{route('CronView',[$val->id])}}" class="text-success">View</a>
                                        <a href="{{route('CronRemove',[$val->id])}}" onclick="return confirm('Are you sure you want to remove this?')" class="text-danger">Delete</a>
                                        </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="pagination" style="margin-top:10px;">
                            {{$data->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
