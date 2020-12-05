<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Artisan;
use App\User;
use Hash;
use Auth;
use Mail;

class UserController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {

        $this->middleware('auth');
    }

    public function manage(Request $request) {
        $data = User::paginate(10);

        return view('User.manage', compact(['data']));
    }

    public function add() {

        return view('User.add');
    }

    public function update(Request $request, $id) {
        $User = User::find($id);
        $User->name = $request->name;
        $User->email = @$request->email;
        $User->role = @$request->role;
        $User->status = $request->status;
        if ($User->save()) {
            return redirect()->back()->with("success", $User->syntax . " User updated successfully !!");
        }
    }

    public function edit(Request $request, $id) {
        $data = User::find($id);
        return view('User.edit', compact(['data']));
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
                                'name' => 'required',
                                'email' => 'required|unique:users',
                                'role' => 'required',
                                'status' => 'required',
                                ]);
        $User = new User();
        $User->name = $request->name;
        $User->email = @$request->email;
        $User->role = @$request->role;
        $User->status = $request->status;
        $User->password = Hash::make(123456);
        if ($User->save()) {
            return redirect()->back()->with("success", $User->name . " User updated successfully !!");
        }
    }

    public function view(Request $request, $id) {
        $data = User::find($id);
        return view('User.view', compact(['data']));
    }

    public function remove(Request $request, $id) {
        $User = User::find($id);
        if ($User->delete()) {
            return redirect()->back()->with("success", $User->syntax . " User removed successfully !!");
        }
    }

    public function change_password(Request $request) {

        return view('User.change_password', compact('data'));
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
