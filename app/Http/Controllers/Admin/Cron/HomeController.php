<?php

namespace App\Http\Controllers\Admin\Cron;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Artisan;
use App\Model\UTGAPI\CronDetail;
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
    public function index() {
        $data = CronDetail::paginate(10);
        return view('Cron.manage', compact(['data']));
    }

    public function add() {
        $emailListings = EmailListing::get();
        
        return view('Cron.add',compact(['emailListings']));
    }

    public function store(Request $request) {

        $CronDetail = new CronDetail();
        $CronDetail->name = $request->name;
        $CronDetail->syntax = $request->syntax;
        $CronDetail->description = $request->description;
        $CronDetail->status = $request->status;
        if ($CronDetail->save()) {
            return redirect()->back()->with("success", $request->syntax . " Cron added successfully  !");
        }
    }

    public function edit(Request $request, $id) {
        $data = CronDetail::find($id);
        $emailListings = EmailListing::get();
//        pr($data);
//        exit;
        return view('Cron.edit', compact(['data','emailListings']));
    }

    public function update(Request $request, $id) {
        $CronDetail = CronDetail::find($id);
        $CronDetail->name = $request->name;
        $CronDetail->syntax = $request->syntax;
        $CronDetail->description = $request->description;
        $CronDetail->status = $request->status;
        $CronDetail->email_notification = @$request->email_notification;
        $CronDetail->email_to = get_email_listing_import(@$request->email_to);
        $CronDetail->email_cc = get_email_listing_import(@$request->email_cc);
        if ($CronDetail->save()) {
            return redirect()->back()->with("success", $request->syntax . " Cron updated successfully !!");
        }
    }

    public function remove(Request $request, $id){
        $CronDetail = CronDetail::find($id);
        if($CronDetail->delete()){
            return redirect()->back()->with("success", $CronDetail->syntax . " Cron removed successfully !!");
        }
    }
    
    public function view(Request $request, $id) {
        $data = CronDetail::find($id);
        return view('Cron.view', compact(['data']));
    }
}
