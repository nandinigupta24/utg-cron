<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Artisan;
use App\Model\UTGAPI\EmailListing;

class EmailAddressController extends Controller {

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
        $data = EmailListing::paginate(10);
        return view('EmailAddress.manage', compact(['data']));
    }

    public function add() {

        return view('EmailAddress.add');
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
                                'name' => 'required',
                                'email' => 'required|unique:email_listings',
                                ]);
        $EmailListing = new EmailListing();
        $EmailListing->name = $request->name;
        $EmailListing->email = $request->email;
        $EmailListing->status = $request->status;
        if ($EmailListing->save()) {
            return redirect()->back()->with("success", $request->email . " added successfully  !");
        }
    }

    public function edit(Request $request, $id) {
        $data = EmailListing::find($id);
        return view('EmailAddress.edit', compact(['data']));
    }

    public function update(Request $request, $id) {
        $EmailListing = EmailListing::find($id);
        $EmailListing->name = $request->name;
        $EmailListing->email = $request->email;
        $EmailListing->status = $request->status;
        if ($EmailListing->save()) {
            return redirect()->back()->with("success", $request->email . " Cron updated successfully !!");
        }
    }

    public function remove(Request $request, $id) {
        $EmailListings = EmailListing::find($id);
        if ($EmailListings->delete()) {
            return redirect()->back()->with("success", $EmailListings->email . " removed successfully !!");
        }
    }

    public function view(Request $request, $id) {
        $data = EmailListing::find($id);
        return view('EmailAddress.view', compact(['data']));
    }

}
