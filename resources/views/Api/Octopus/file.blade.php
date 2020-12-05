@extends('layouts.inner')
@section('title', 'Octopus Lead Listings')
@section('content')
<div class="content-wrapper">
    @include('elements.msg')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Octopus Leads</h4>
                    <p class="card-description"></p>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-primary text-white">
                                <tr style="text-align: center;">
                                    <th>S NO.</th>
                                    <th>Lead ID</th>
                                    <th>Inbound Group</th>
                                    <th>Campaign</th>
                                    <th>OPTIN Lead ID</th>
                                    <th>Sale ID</th>
                                    <th>Agent ID</th>
                                    <th>Phone Number</th>
                                    <th>Datasource</th>
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
                                    <td>{{get_customerid_api_response($val->api_response)}}</td>
                                    <td>{{$val->inbound_group}}</td>
                                    <td>{{$val->campaign_name}}</td>
                                    <td>{{$val->lead_id}}</td>
                                    <td>{{$val->sale_id}}</td>
                                    <td>{{$val->agent_id}}</td>
                                    <td>{{$val->phone_number}}</td>
                                    <td>{{$val->datasource}}</td>
                                    <td>{!!get_duplicate_status($val->duplicate_status)!!}</td>
                                    <td>{{$val->created_at}}</td>
                                    <td>
                                        <a href=""><i class="fa fa-list"></i></a>
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
