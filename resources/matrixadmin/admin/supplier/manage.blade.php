@extends('layouts.inner')
@section('title', 'Lead Listings')
@section('content')
<div class="content-wrapper">
    @include('elements.msg')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Lead Listings<button class="btn btn-warning pull-right">{{$data->total()}}</button></h4>
                    <p class="card-description"></p>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="example">
                            <thead class="bg-primary text-white">
                                <tr style="text-align: center;">
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>DOB</th>
                                    <th>Street 1</th>
                                    <th>Street 2</th>
                                    <th>City</th>
                                    <th>County</th>
                                    <th>Postcode</th>
                                    <th>Phone 1</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $Count = get_data_sequence($data->currentpage(), $data->perpage());
                                ?>
                                @foreach($data as $val)
                                <tr>
                                    <td>{{$Count++}}.</td>
                                    <td>{{$val->Title}}</td>
                                    <td>{{$val->First_Name}}</td>
                                    <td>{{$val->Last_Name}}</td>
                                    <td>{{$val->DOB}}</td>
                                    <td>{{$val->Street_1}}</td>
                                    <td>{{$val->Street_2}}</td>
                                    <td>{{$val->City}}</td>
                                    <td>{{$val->County}}</td>
                                    <td>{{$val->Postcode}}</td>
                                    <td>{{$val->Phone_1}}</td>
                                    <td>{!!get_duplicate_status($val->duplicate_status)!!}</td>
                                    <td>{{$val->created_at}}</td>
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
@section('script')
<script>
    $(document).ready(function(){
       $('.add_in_queue').click(function(){
          var fileID = $(this).attr('fileID');
          $.ajax({
            type: 'GET',
            url: "{{route('O2UNICAFileQueueAdd')}}",
            data: {fileID:fileID},
            success: function (data) {
              alert(data);
              $('.queue-'+fileID).html('<span class="text-success">Added in Queue</span>');
            }
          });
       }); 
    });
    </script>
@endsection
