@extends('layouts.inner')
@section('title', 'File Import')
@section('content')
<div class="content-wrapper">
    @include('elements.msg')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">File Import <button class="btn btn-warning pull-right">{{$total}}</button></h4>
                    <p class="card-description"></p>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="example">
                            <thead class="bg-primary text-white">
                                <tr style="text-align: center;">
                                    <th>ID</th>
                                    <th>File Name</th>
                                    <th>Total</th>
                                    <th>Success</th>
                                    <th>Failed</th>
                                    <th>MFST Total</th>
                                    <th>API Queue</th>
                                    <th>Created</th>
                                    <!--<th>Action</th>-->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $Count = get_data_sequence($data->currentpage(), $data->perpage());
                                ?>
                                @foreach($data as $val)
                                <tr>
                                    <td>{{$Count++}}.</td>
                                    <td>{{$val->original_filename}}</td>
                                    <td>{{$val->total}}</td>
                                    <td>{{$val->success}}</td>
                                    <td>{{$val->failed}}</td>
                                    <td>{{$val->mfst_total}}</td>
                                    <td class="queue-{{$val->id}}">
                                        <?php if($val->api_queue == 'added'){?>
                                        <span class="text-success">Added in Queue</span>
                                        <?php }elseif($val->api_queue == 'done'){?>
                                        <span class="text-success">Successfully POST</span>
                                        <?php }else{?>
                                        <a href="javascript:void(0);" class="btn btn-warning btn-xs add_in_queue" title="View" fileID="{{$val->id}}">ADD</a>
                                        <?php }?>
                                    </td>
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
