<?php

namespace App\Http\Controllers\Admin\Fields;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Artisan;
use App\Model\UTGAPI\api_field_validations;
use App\Model\UTGAPI\EmailListing;

class HomeController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {

        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function manage() {
        $data = api_field_validations::paginate(10);
        
        return view('Fields.manage', compact(['data']));
    }

//    public function add() {
//        $emailListings = EmailListing::get();
//        
//        return view('Fields.add',compact(['emailListings']));
//    }
//
//    public function store(Request $request) {
//
//        $api_field_validations = new api_field_validations();
//        $api_field_validations->name = $request->name;
//        $api_field_validations->syntax = $request->syntax;
//        $api_field_validations->description = $request->description;
//        $api_field_validations->status = $request->status;
//        if ($api_field_validations->save()) {
//            return redirect()->back()->with("success", $request->syntax . " Fields added successfully  !");
//        }
//    }
//
//    public function edit(Request $request, $id) {
//        $data = api_field_validations::find($id);
//        $emailListings = EmailListing::get();
////        pr($data);
////        exit;
//        return view('Fields.edit', compact(['data','emailListings']));
//    }
//
//    public function update(Request $request, $id) {
//        $api_field_validations = api_field_validations::find($id);
//        $api_field_validations->name = $request->name;
//        $api_field_validations->syntax = $request->syntax;
//        $api_field_validations->description = $request->description;
//        $api_field_validations->status = $request->status;
//        $api_field_validations->email_notification = @$request->email_notification;
//        $api_field_validations->email_to = get_email_listing_import(@$request->email_to);
//        $api_field_validations->email_cc = get_email_listing_import(@$request->email_cc);
//        if ($api_field_validations->save()) {
//            return redirect()->back()->with("success", $request->syntax . " Fields updated successfully !!");
//        }
//    }
//
//    public function remove(Request $request, $id){
//        $api_field_validations = api_field_validations::find($id);
//        if($api_field_validations->delete()){
//            return redirect()->back()->with("success", $api_field_validations->syntax . " Fields removed successfully !!");
//        }
//    }
//    
//    public function view(Request $request, $id) {
//        $data = api_field_validations::find($id);
//        return view('Fields.view', compact(['data']));
//    }
}
