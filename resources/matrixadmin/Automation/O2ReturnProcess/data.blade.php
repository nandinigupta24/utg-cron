@extends('layouts.inner')
@section('title', 'Data Logs')
@section('content')
<div class="content-wrapper">
    @include('elements.msg')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">O2ReturnProcess Data</h4>
                    <p class="card-description"></p>
                    <div class="table-responsive">
                        <table class="table table-condensed">
                            <thead style="background-color: khaki; color:blue;">
                                <tr style="text-align: center;">
                                    <!--<th>ID</th>-->
                                    <th>List ID</th>
                                    <th>File Name</th>
                                    <th>Customer Id</th>
                                    <th>Subscribe ID</th>
                                    <th>Campaign Code</th>
                                    <th>Cell Code</th>
                                    <th>Treatment Code</th>
                                    <th>Response Date Time</th>
                                    <th>Response Reason Code</th>
                                    <th>Response Channel</th>
                                    <th>Custom Field 1</th>
                                    <th>Custom Field 2</th>
                                    <th>Custom Field 3</th>
                                    <th>Custom Field 4</th>
                                    <th>Custom Field 5</th>
                                    <th>status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $val)
                                <tr style="background-color: ">
                                    <!--<td>{{$val->id}}</td>-->
                                    <td>{{$val->list_id}}</td>
                                    <td>{{$val->File_name}}</td>
                                    <td>{{$val->Customer_Id}}</td>
                                    <td>{{$val->Subscriber_Id}}</td>
                                    <td>{{$val->Campaign_Code}}</td>
                                    <td>{{$val->Cell_Code}}</td>
                                    <td>{{$val->Treatment_Code}}</td>
                                    <td>{{$val->Response_Date_Time}}</td>
                                    <td>{{$val->Response_Reason_Code}}</td>
                                    <td>{{$val->Response_Channel}}</td>
                                    <td>{{$val->Custom_Field_1}}</td>
                                    <td>{{$val->Custom_Field_2}}</td>
                                    <td>{{$val->Custom_Field_3}}</td>
                                    <td>{{$val->Custom_Field_4}}</td>
                                    <td>{{$val->Custom_Field_5}}</td>
                                    <td>{{$val->status}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{$data->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
