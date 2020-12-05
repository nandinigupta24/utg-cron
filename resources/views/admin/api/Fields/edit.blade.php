@extends('layouts.inner')
@section('title', 'Edit Fields Validation')
@section('content')
<div class="content-wrapper">
    @include('elements.msg')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Fields Validation <a href="{{route('APIFieldValidationManage')}}" class="pull-right btn btn-primary">Manage</a></h4>
                    <p class="card-description"></p>
                    <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form class="forms-sample" method="post" action="{{route('APIFieldsValidationUpdate',[$data->id])}}">
                            {{csrf_field()}}
                         <div class="form-group">
                                        <label for="exampleInputPassword1">Field Name</label>
                                        <input type="text" class="form-control" name="field_name"  placeholder="Enter Field Name" required="" value="{{@$data->field_name}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Field Validation</label>
                                        <input type="text" class="form-control" name="field_validation" placeholder="Enter Field Validation" required="" value="{{@$data->field_validation}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Description</label>
                                        <input type="text" class="form-control" name="description" placeholder="Enter Description" required="" value="{{@$data->description}}">
                                    </div>
                        
<!--                            <div class="form-group">
                          <div class="form-check form-check-flat">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="email_notification" value="1"> Email Notification
                            <i class="input-helper"></i></label>
                          </div>
                        </div>-->
<!--                        <div class="form-group">
                          <label for="exampleInputPassword1">Status</label>
                          <select class="form-control" name="status" required="">
                              <option value="">Select Option</option>
                              <option value="active" {{(!empty($data->status) &&  $data->status == 'active') ? 'selected="selected"': ''}}>active</option>
                              <option value="deactive" {{(!empty($data->status) &&  $data->status == 'deactive') ? 'selected="selected"': ''}}>deactive</option>
                          </select>
                        </div>-->

                        <button type="submit" class="btn btn-success mr-2">Update</button>
                        <button type="button" class="btn btn-danger">Cancel</button>
                      </form>
                    </div>
                  </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
