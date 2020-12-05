@extends('layouts.inner')
@section('title', 'Fields Listings')
@section('content')
<div class="content-wrapper">
    @include('elements.msg')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Fields Listings <a href="{{route('APIFieldsValidationAdd')}}" class="pull-right btn btn-primary">Add</a>  <button class="btn btn-warning pull-right">{{$total}}</button></h4>
                    <p class="card-description"></p>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="example">
                            <thead class="bg-primary text-white">
                                <tr style="text-align: center;">
                                    <th>ID</th>
                                    <th>Field Name</th>
                                    <th>Field Validation</th>
                                    <th>Description</th>
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
                                    <td>{{$val->field_name}}</td>
                                    <td>{{$val->field_validation}}</td>
                                    <td>{{$val->description}}</td>
                                    <td>{{$val->created_at}}</td>
                                    <td> 
                                       
                                    <a href= "{{route('APIFieldsValidationEdit',[$val->id])}}"class="btn btn-primary btn-xs" title="Edit"><i class="fa fa-edit"></i></a> 
                                        <a href="{{route('APIFieldsValidationView',[$val->id])}}"class="btn btn-success btn-xs" title="View"><i class="fa fa-list"></i></a>
                                        <a href="{{route('APIFieldsValidationRemove',[$val->id])}}" onclick="return confirm('Are you sure you want to remove this?')" class="btn btn-danger btn-xs" title="Remove"><i class="fa fa-times"></i></a>
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
