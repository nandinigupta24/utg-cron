@extends('layouts.inner')
@section('title', 'User Listings')
@section('content')
<div class="content-wrapper">
    @include('elements.msg')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">User Listings</h4>
                    <p class="card-description"></p>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="example">
                            <thead class="bg-primary text-white">
                                <tr style="text-align: center;">
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Password</th>
                                    <th>Status</th>
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
                                    <td>{{@$val->name}}</td>
                                    <td>{{@$val->email}}</td>
                                    <td>{{@$val->role}}</td>
                                    <td><button class="btn btn-warning reset-password" data-toggle="modal" data-target="#myModal"  user_id="{{@$val->id}}">RESET</button></td>
                                    <td>{!!get_status(@$val->status)!!}</td>
                                    <td>{{$val->created_at}}</td>
                                    <td>
                                      
                                        <a href= "{{route('UserEdit',[$val->id])}}"class="btn btn-primary btn-xs" title="Edit"><i class="fa fa-edit"></i></a> 
                                        <a href="{{route('UserView',[$val->id])}}"class="btn btn-success btn-xs" title="View"><i class="fa fa-list"></i></a>
                                        <a href="{{route('UserRemove',[$val->id])}}" onclick="return confirm('Are you sure you want to remove this?')" class="btn btn-danger btn-xs" title="Remove"><i class="fa fa-times"></i></a>
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
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        
        <h4 class="modal-title" id="myModalLabel">Reset Password</h4>
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
      </div>
      <div class="modal-body">
       <form class="forms-sample"name="forms-sample" method="post" action="{{route('UpdatePassword')}}">
                            {{csrf_field()}}
                            <input type="hidden" name="user_id" id="user-id">
                        <div class="form-group">
                          <label for="exampleInputPassword">New Password</label>
                          <input type="password" class="form-control" name="password" placeholder="Enter New Password" required="">
                        </div>
            
                        <button type="submit" class="btn btn-success mr-2" >Save changes</button>
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
 
       </form>
          
      </div>
    </div>
  </div>
</div>

@endsection
