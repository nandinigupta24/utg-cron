@extends('layouts.inner')
@section('title', 'Data Logs')
@section('content')
<div class="content-wrapper">
    @include('elements.msg')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">O2 Free Sim Data </h4>
                    <p class="card-description">
                        <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#exampleModal">Search</button>
                        <button type="button" class="btn btn-outline-info">Export</button>
                    </p>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered example" id="example">
                            <thead class="bg-primary text-white">
                                <tr style="text-align: center;">
                                    <th>ID</th>
                                    <th>Vendor</th>
                                    <th>Datasource</th>
                                    <th>NAME</th>
                                    <th>ADDR1</th>
                                    <th>MPN</th>
                                    <th>EMAIL</th>
                                    <th>TEMPLATE</th>
                                    <th>SIMTYPE</th>
                                    <th>OPTIN</th>
                                    <th>PACKDESC</th>
                                    <th>FILECODE</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $Count = get_data_sequence($data->currentpage(), $data->perpage());
                                ?>
                                @foreach($data as $val)
                                <tr style="background-color: ">
                                    <td>{{$Count++}}.</td>
                                    <td>{{$val->Vendor}}</td>
                                    <td>{{$val->Datasource}}</td>
                                    <td>{{$val->NAME}}</td>
                                    <td>{{$val->ADDR1}}</td>
                                    <td>{{$val->MPN}}</td>
                                    <td>{{$val->EMAIL}}</td>
                                    <td>{{$val->TEMPLATE}}</td>
                                    <td>{{$val->SIMTYPE}}</td>
                                    <td>{{$val->OPTIN}}</td>
                                    <td>{{$val->PACKDESC}}</td>
                                    <td>{{$val->FILECODE}}</td>
                                    <td>{!!get_duplicate_status($val->dupes_status)!!}</td>
                                    <td>{{$val->created_at}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
<!--                        <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
// JQuery Reference, If you have added jQuery reference in your master page then ignore, 
// else include this too with the below reference

<script src="https://cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.4/css/jquery.dataTables.min.css">
<script>
   $(document).ready(function () {
$('#example').DataTable();
$('.dataTables_length').addClass('bs-select');
} );
</script>-->
                        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">  
        <link  href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>  
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<!--// JQuery Reference, If you have added jQuery reference in your master page then ignore, 
// else include this too with the below reference-->

<script src="https://cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.4/css/jquery.dataTables.min.css">
<script>
    $(document).ready(function () {
               $('#example').DataTable({
               processing: true,
               serverSide: true,
               ajax: '{{url('Automation/O2FreeSim/Data') }}',
               columns: [
                        { data: 'Vendor', name: 'Vendor' },
                        { data: 'Datasource', name: 'Datasource' },
                        { data: 'OfficeNAME', name: 'NAME' }
                        { data: 'ADDR1', name: 'ADDR1' }
                     ]
            });
         });
         </script>
                    </div>
<!--                    <div class="pagination" style="margin-top:10px;">
                        {{$data->appends(['start'=>app('request')->input('start'),'end'=>app('request')->input('end'),'dupe_status'=>app('request')->input('dupe_status')])->links()}}
                    </div>-->
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
         <form method="GET" action="{{route('AutomationO2FreeSimProcessData')}}">
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
