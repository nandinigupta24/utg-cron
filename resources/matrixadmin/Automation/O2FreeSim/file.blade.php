@extends('layouts.inner')
@section('title', 'File Logs')
@section('content')
<div class="content-wrapper">
    @include('elements.msg')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">O2 Free Sim File</h4>
                    <p class="card-description"></p>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-primary text-white">
                                <tr style="text-align: center;">
                                    <th>ID</th>
                                    <th>File Name</th>
                                    <th>Number Of Records</th>
                                    <th>Loaded Records</th>
                                    <th>Duplicate on Loaded</th>
                                    <th>Count of PACKDESC</th>
                                    <th>Count of FILECODE</th>
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
                                    <td>{{$val->file_name}}</td>
                                    <td>{{$val->number_of_records}}</td>
                                    <td>{{$val->loaded_records}}</td>
                                    <td>{{$val->duplicate_on_loaded}}</td>
                                    <td>{{$val->count_of_PACKDESC}}</td>
                                    <td>{{$val->count_of_FILECODE}}</td>
                                    <td>{{$val->created_at}}</td>
                                    <td>
                                        <button type="button" class="btn btn-outline-warning btn-fw" onclick="location.href='https://api.usethegeeks.com/storage/Automation/O2FreeSim/In/{{$val->save_file_name}}';">
                                            <i class="fa fa-download"></i>Download</button>
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
