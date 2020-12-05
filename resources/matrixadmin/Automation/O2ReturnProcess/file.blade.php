@extends('layouts.inner')
@section('title', 'File Logs')
@section('content')
<div class="content-wrapper">
    @include('elements.msg')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">O2ReturnProcess File</h4>
                    <p class="card-description"></p>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead style="background-color: khaki; color:blue;">
                                <tr style="text-align: center;">
                                    <th>ID</th>
                                    <th>List ID</th>
                                    <th>File Name</th>
                                    <th>MFST File Name</th>
                                    <th>Records</th>
                                    <th>Business DT</th>
                                    <th>Extract DT</th>
                                    <!--<th>File Sent</th>-->
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $val)
                                <tr style="background-color: ">
                                    <td>{{$val->id}}</td>
                                    <td>{{$val->list_id}}</td>
                                    <td>{{$val->File_name}}</td>
                                    <td>{{$val->mfst_file_name}}</td>
                                    <td>{{$val->records}}</td>
                                    <td>{{$val->bussinessdate}}</td>
                                    <td>{{$val->extractdate}}</td>
                                    <!--<td>{{$val->file_sent}}</td>-->
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
