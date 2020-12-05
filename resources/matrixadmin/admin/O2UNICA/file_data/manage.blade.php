@extends('layouts.inner')
@section('title', 'File Data')
@section('content')
<div class="content-wrapper">
    @include('elements.msg')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Data Listings<button class="btn btn-warning pull-right">{{$data->total()}}</button></h4>
                    <p class="card-description"></p>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="example">
                            <thead class="bg-primary text-white">
                                <tr style="text-align: center;">
                                    <th>ID</th>
                                    <th>Contact No</th>
                                    <th>Customer No</th>
                                    <th>Customer Id</th>
                                    <th>Account Id</th>
                                    <th>Subscriber Id</th>
                                    <th>Campaign Code</th>
                                    <th>Cell Code</th>
                                    <th>Channel Identifier</th>
                                    <th>Treatment Code</th>
                                    <th>Email</th>
                                    <th>Title</th>
                                    <th>Forename</th>
                                    <th>Surname</th>
                                    <th>Address Line1</th>
                                    <th>Address Line2</th>
                                    <th>Address Line3</th>
                                    <th>Address Line4</th>
                                    <th>Address Line5</th>
                                    <th>Postcode</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $Count = get_data_sequence($data->currentpage(), $data->perpage());
                                ?>
                                @foreach($data as $val)
                                <tr>
                                    <td>{{$Count++}}.</td>
                                    <td>{{$val->contact_No}}</td>
                                    <td>{{$val->customer_No}}</td>
                                    <td>{{$val->Customer_Id}}</td>
                                    <td>{{$val->Account_Id}}</td>
                                    <td>{{$val->Subscriber_Id}}</td>
                                    <td>{{$val->Campaign_Code}}</td>
                                    <td>{{$val->Cell_Code}}</td>
                                    <td>{{$val->Channel_Identifier}}</td>
                                    <td>{{$val->Treatment_Code}}</td>
                                    <td>{{$val->Email}}</td>
                                    <td>{{$val->Title}}</td>
                                    <td>{{$val->Forename}}</td>
                                    <td>{{$val->Surname}}</td>
                                    <td>{{$val->Address_Line1}}</td>
                                    <td>{{$val->Address_Line2}}</td>
                                    <td>{{$val->Address_Line3}}</td>
                                    <td>{{$val->Address_Line4}}</td>
                                    <td>{{$val->Address_Line5}}</td>
                                    <td>{{$val->Postcode}}</td>
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
