@extends('layouts.inner')
@section('title', 'Edit Cron')
@section('content')
<div class="content-wrapper">
    @include('elements.msg')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit Cron <a href="{{route('CronAdd')}}" class="pull-right btn btn-primary">Add</a></h4>
                    <p class="card-description"></p>
                    <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form class="forms-sample" method="post" action="{{route('CronUpdate',[$data->id])}}">
                            {{csrf_field()}}
                        <div class="form-group">
                          <label for="exampleInputEmail1">Name</label>
                          <input type="text" class="form-control" name="name"  placeholder="Enter Name" required="" value="{{@$data->name}}">
                        </div>
                        <div class="form-group">
                          <label for="exampleInputPassword1">Syntax</label>
                          <input type="text" class="form-control" name="syntax" placeholder="Enter Syntax" required="" value="{{@$data->syntax}}">
                        </div>
                        <div class="form-group">
                          <label for="exampleInputPassword1">Description</label>
                          <textarea class="form-control" name="description" rows="5" placeholder="Enter Description" required="" >{{@$data->description}}</textarea>
                        </div>
                            <div class="form-group">
                          <div class="form-check form-check-flat">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="email_notification" value="1"> Email Notification
                            <i class="input-helper"></i></label>
                          </div>
                        </div>
                            <div class="form-group">
                                <?php $arrayEmailTo = get_email_listing_export($data->email_to);?>
                                        <label for="exampleInputPassword1">Email To</label>
                                        <select class="form-control select2" multiple="" name="email_to[]" data-placeholder="Select Email Address" value="">
                                            @foreach($emailListings as $val)
                                            <option value="{{$val->id}}" {{(in_array($val->id,$arrayEmailTo)) ? 'selected="selected"' : ''}}>{{$val->name}} - {{$val->email}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <?php $arrayEmailCC = get_email_listing_export($data->email_cc);?>
                                        <label for="exampleInputPassword1">Email CC</label>
                                        <select class="form-control select2" multiple="" name="email_cc[]" data-placeholder="Select Email Address">
                                            @foreach($emailListings as $val)
                                            <option value="{{$val->id}}" {{(in_array($val->id,$arrayEmailCC)) ? 'selected="selected"' : ''}}>{{$val->name}} - {{$val->email}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                        <div class="form-group">
                          <label for="exampleInputPassword1">Status</label>
                          <select class="form-control" name="status" required="">
                              <option value="">Select Option</option>
                              <option value="published" {{(!empty($data->status) &&  $data->status == 'published') ? 'selected="selected"': ''}}>Published</option>
                              <option value="unpublished" {{(!empty($data->status) &&  $data->status == 'unpublished') ? 'selected="selected"': ''}}>UnPublished</option>
                          </select>
                        </div>

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
