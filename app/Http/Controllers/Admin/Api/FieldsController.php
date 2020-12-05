<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Artisan;
use App\User;
use Hash;
use Auth;
use Mail;
use App\Model\UTGAPI\ApiFieldValidations;

class FieldsController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {

        $this->middleware('auth');
    }

    public function manage(Request $request) {
        $total = ApiFieldValidations::count();
        $data = ApiFieldValidations::paginate(10);
        
        return view('admin.api.Fields.manage', compact(['data','total']));
    }

    public function add() {

        return view('admin.api.Fields.add');
    }

    public function update(Request $request, $id) {
        $ApiFieldValidations = ApiFieldValidations::find($id);
        $ApiFieldValidations->field_name = $request->field_name;
        $ApiFieldValidations->field_validation = @$request->field_validation;
        $ApiFieldValidations->description = @$request->description;
        
        if ($ApiFieldValidations->save()) {
            return redirect()->back()->with("success", $ApiFieldValidations->syntax . " Fields Validation updated successfully !!");
        }
    }

    public function edit(Request $request, $id) {
        $data = ApiFieldValidations::find($id);
        return view('admin.api.Fields.edit', compact(['data']));
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
                                'field_name' => 'required',
                                'field_validation' => 'required',
                                'description' => 'required',
                                
                                ]);
        $ApiFieldValidations = new ApiFieldValidations();
        $ApiFieldValidations->field_name = $request->field_name;
        $ApiFieldValidations->field_validation = @$request->field_validation;
        $ApiFieldValidations->description = @$request->description;
        
        if ($ApiFieldValidations->save()) {
            return redirect()->back()->with("success", $ApiFieldValidations->name . " Fields Validation updated successfully !!");
        }
    }

    public function view(Request $request, $id) {
        $data = ApiFieldValidations::find($id);
        return view('admin.api.Fields.view', compact(['data']));
    }

    public function remove(Request $request, $id) {
        $ApiFieldValidations = ApiFieldValidations::find($id);
        if ($ApiFieldValidations->delete()) {
            return redirect()->back()->with("success", $ApiFieldValidations->syntax . " Fields Validation removed successfully !!");
        }
    }

    public function change_password(Request $request) {

        return view('Fields.change_password', compact('data'));
    }

    public function changePassword(Request $request) {

        if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
            // The passwords matches
            return redirect()->back()->with("error", "Your current password does not matches with the password you provided. Please try again.");
        }

        if (strcmp($request->get('current-password'), $request->get('new-password')) == 0) {
            //Current password and new password are same
            return redirect()->back()->with("error", "New Password cannot be same as your current password. Please choose a different password.");
        }

        $validatedData = $request->validate([
            'current-password' => 'required',
            'new-password' => 'required',
        ]);


        //Change Password
        $user = Auth::user();
        $user->password = Hash::make($request->get('new-password'));
        $user->save();

        return redirect()->back()->with("success", "Password changed successfully !");
    }

    public function update_password(Request $request) {

        $user = User::find($request->get('user_id'));
        $user->password = Hash::make($request->get('password'));
        $user->save();

        $arrayMailTo = ['pkumar@usethegeeks.co.uk', 'apanwar@usethegeeks.co.uk'];
        $mail_data = array();

        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'pkumar@usethegeeks.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'User.email';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['pkumar@usethegeeks.com'];
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'Reset Password ';
        $mail_data['email'] = $request->get('email');
        $mail_data['password'] = $request->get('password');
        $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use($mail_data) {

                    $m->from($mail_data['from'], 'Intelling');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('pkumar@usethegeeks.co.uk', 'Intelling');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
//                    $m->attach(storage_path('') . $fileName . '.xls');
                });

        return redirect()->back()->withwith("success", "Password updated successfully !");
    }

}
