@extends('layouts.inner')
@section('title', 'Add Fields')
@section('content')
<div class="content-wrapper">
    @include('elements.msg')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Add Fields</h4>
                    <p class="card-description"></p>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Fields Validation <a href="{{route('APIFieldValidationManage')}}" class="pull-right btn btn-primary">Manage</a></h4>
                                <form class="forms-sample" method="post" action="{{route('APIFieldsValidationStore')}}">
                                    {{csrf_field()}}
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Field Name</label>
                                        <input type="text" class="form-control" name="field_name"  placeholder="Enter Field Name" required="" value="{{old('field_name')}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Field Validation</label>
                                        <input type="text" class="form-control" name="field_validation" placeholder="Enter Field Validation" required="" value="{{old('field_validation')}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Description</label>
                                        <input type="text" class="form-control" name="description" placeholder="Enter Description" required="" value="{{old('description')}}">
                                    </div>
                                   

                                    <button type="submit" class="btn btn-success mr-2">Submit</button>
                                    <button type="button" class="btn btn-light">Cancel</button>
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
