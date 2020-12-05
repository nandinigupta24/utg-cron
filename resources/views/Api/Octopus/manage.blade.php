@extends('layouts.inner')
@section('title', 'Octopus Leads')
@section('content')
<div class="content-wrapper">
    @include('elements.msg')
    <div class="row">
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-left">
                            <i class="mdi mdi-cube text-danger icon-lg"></i>
                        </div>
                        <div class="float-right">
                            <p class="mb-0 text-right">Total Leads</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0">{{$arrayCount['Total']}}</h3>
                            </div>
                        </div>
                    </div>
<!--                    <p class="text-muted mt-3 mb-0">
                        <i class="mdi mdi-alert-octagon mr-1" aria-hidden="true"></i> 65% lower growth
                    </p>-->
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-left">
                            <i class="mdi mdi-receipt text-warning icon-lg"></i>
                        </div>
                        <div class="float-right">
                            <p class="mb-0 text-right">Loaded</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0">{{$arrayCount['Loaded']}}</h3>
                            </div>
                        </div>
                    </div>
<!--                    <p class="text-muted mt-3 mb-0">
                        <i class="mdi mdi-bookmark-outline mr-1" aria-hidden="true"></i> Product-wise sales
                    </p>-->
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-left">
                            <i class="mdi mdi-poll-box text-success icon-lg"></i>
                        </div>
                        <div class="float-right">
                            <p class="mb-0 text-right">Duplicate</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0">{{$arrayCount['Duplicate']}}</h3>
                            </div>
                        </div>
                    </div>
<!--                    <p class="text-muted mt-3 mb-0">
                        <i class="mdi mdi-calendar mr-1" aria-hidden="true"></i> Weekly Sales
                    </p>-->
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-left">
                            <i class="mdi mdi-account-location text-info icon-lg"></i>
                        </div>
                        <div class="float-right">
                            <p class="mb-0 text-right">Employees</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0">246</h3>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted mt-3 mb-0">
                        <i class="mdi mdi-reload mr-1" aria-hidden="true"></i> Product-wise sales
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Inbound Group Wise</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>#</th>
                                    <th>Inbound Group</th>
                                    <th>Campaign</th>
                                    <th>Total</th>
                                    <th>Loaded</th>
                                    <th>Duplicate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $Count=1;
                                foreach($arrayCountGroup as $key=>$val){
                                foreach($val as $k=>$value){
                                    ?>
                                <tr>
                                    <td class="font-weight-medium">{{$Count++}}.</td>
                                    <td>{{@$key}}</td>
                                    <td>{{@$k}}</td>
                                    <td>{{@$value['Total']}}</td>
                                    <td>{{(!empty($value['Loaded'])) ? $value['Loaded'] : 0}}</td>
                                    <td>{{(!empty($value['Duplicate'])) ? $value['Duplicate'] : 0}}</td>
                                </tr>
                                <?php } } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
