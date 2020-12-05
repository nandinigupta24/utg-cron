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
                    <p class="card-description">
                        <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#exampleModal">Search</button>
                        <button type="button" class="btn btn-outline-info">Export</button>
                    </p>
                    <div class="table-responsive">
                        <table class="table table-condensed example" id="example">
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
                                 <?php
                                $Count = get_data_sequence($data->currentpage(), $data->perpage());
                                ?>
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
                        <!--{{$data->links()}}-->
                          <script type="text/javascript" charset="utf8" src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.8.2.min.js"></script>
  <script type="text/javascript" charset="utf8" src="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.min.js"></script>
  <script>
  $(function(){
    $("#example").dataTable();
  })
  </script>
                    </div>
                     <div class="pagination" style="margin-top:10px;">
                        {{$data->appends(['start'=>app('request')->input('start'),'end'=>app('request')->input('end'),'dupe_status'=>app('request')->input('dupe_status')])->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <h4 class="modal-title" id="exampleModalLabel">Search Here</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        
      </div>
         <form method="GET" action="{{route('AutomationO2ReturnProcessData')}}">
      <div class="modal-body">
          <div class="form-group">
            <label for="recipient-name" class="control-label">Start:</label>
            <input type="text" class="form-control" id="start" name="start" value="{{app('request')->input('start')}}">
          </div>
          <div class="form-group">
            <label for="recipient-name" class="control-label">End:</label>
            <input type="text" class="form-control" id="end" name="end" value="{{app('request')->input('end')}}">
          </div>
          <div class="form-group">
            <label for="message-text" class="control-label">Status:</label>
            <select class="form-control" name="dupe_status">
                <option value="">Select Status</option>
                <option value="yes" {{(app('request')->input('dupe_status') == 'yes') ? 'selected="selected"' : ''}}>Duplicate</option>
                <option value="no" {{(app('request')->input('dupe_status') == 'no') ? 'selected="selected"' : ''}}>Loaded</option>
            </select>
          </div>
      
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" name="type" value="export" class="btn btn-info">Export</button>
        <button type="submit" class="btn btn-primary">Search</button>
      </div>
               </form>
    </div>
  </div>
</div>
@endsection
