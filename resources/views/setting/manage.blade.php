@extends('layouts.inner')
@section('title', 'DB Connections')
@section('content')
<div class="content-wrapper">
    @include('elements.msg')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Database Connections</h4>
                    <p class="card-description"></p>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="bg-primary text-white">
                                <tr style="text-align: center;">
                                    <th>S No</th>
                                    <th>Connection Name</th>
                                    <th>Driver</th>
                                    <th>Host</th>
                                    <th>Port</th>
                                    <th>Database Name</th>
                                    <th>Database Username</th>
                                    <th>Database Password</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $Count = 0;
                                foreach ($data as $key => $value) {
                                    $Count++;
                                    ?>
                                    <tr>
                                        <td>{{$Count}}.</td>
                                        <td>{{$key}}</td>
                                        <td>{{@($value['driver']) ? $value['driver'] : 'N/A'}}</td>
                                        <td>{{@($value['host']) ? $value['host'] : 'N/A'}}</td>
                                        <td>{{@($value['port']) ? $value['port'] : 'N/A'}}</td>
                                        <td>{{@($value['database']) ? $value['database'] : 'N/A'}}</td>
                                        <td>{{@($value['username']) ? $value['username'] : 'N/A'}}</td>
                                        <td>{{@($value['password']) ? $value['password'] : 'N/A'}}</td>

                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
