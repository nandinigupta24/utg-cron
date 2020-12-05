<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use App\User;
use App\Model\UTGAPI\Lead;

class CustomerController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request) {

//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        /*START TOKEN Matching Process*/
        if (empty($request->token)) {
            return response()->json(['success' => FALSE, 'error' => TRUE, 'message' => 'Your authentication is not active!!', 'data' => TRUE]);
        } else {
            $token = $request->token;
            $UserDetail = User::where('remember_token', $token)->first();
            if (empty($UserDetail->name)) {
                return response()->json(['success' => FALSE, 'error' => TRUE, 'message' => 'Your token is not active!!', 'data' => NULL]);
            }
        }
        /*END*/
        
        /*Fields Matching*/
        $ArrayInsertFields = array_keys($request->all());
        $ArrayTableFields = ['token','title','first_name','middle_initial','last_name','address1','address2','address3','city','state','province','postal_code','country_code','gender','date_of_birth','phone_code','phone_number','alt_phone','email','comments'];
        $result = array_diff($ArrayInsertFields,$ArrayTableFields);
        if(!empty($result) && count($result)){
            return response()->json(['success' => FALSE, 'error' => TRUE, 'message' => 'Some fields are wrong entered!!Please fix !!', 'data' => array_shift($result)]);
        }
        /*END*/
        
        $request->request->remove('token');
        
        /*Fields Validation*/
        $validator = Validator::make($request->all(), [
                    'first_name' => 'required|string|max:50',
                    'last_name' => 'required|string|max:50',
                    'phone_number' => 'required|numeric|regex:/(0)[0-9]{10}/|unique:leads',
                    'alt_phone' => 'nullable|numeric|regex:/(0)[0-9]{10}/',
                    'date_of_birth' => 'nullable|date_format:Y-m-d',
                    'email' => 'nullable|email',
                    'gender' => 'nullable|regex:/^[a-zA-Z]+$/u',
        ]);
        /*END*/
        
        if ($validator->fails()) {
            return response()->json(['success' => FALSE, 'error' => TRUE, 'message' => 'Some fields validation has been failed!!', 'data' => $validator->messages()->all()]);
        } else {
            /*DATA insert here*/
            $Lead = new Lead();
            $Lead->user_id = @$UserDetail->id;
            $Lead->title = @$request->title;
            $Lead->first_name = @$request->first_name;
            $Lead->middle_initial = @$request->middle_initial;
            $Lead->last_name = @$request->last_name;
            $Lead->address1 = @$request->address1;
            $Lead->address2 = @$request->address2;
            $Lead->address3 = @$request->address3;
            $Lead->city = @$request->city;
            $Lead->state = @$request->state;
            $Lead->province = @$request->province;
            $Lead->postal_code = @$request->postal_code;
            $Lead->country_code = @$request->country_code;
            $Lead->gender = @$request->gender;
            $Lead->date_of_birth = @$request->date_of_birth;
            $Lead->phone_code = @$request->phone_code;
            $Lead->phone_number = @$request->phone_number;
            $Lead->alt_phone = @$request->alt_phone;
            $Lead->email = @$request->email;
            $Lead->comments = @$request->comments;
            if ($Lead->save()) {
                return response()->json(['success' => TRUE, 'error' => FALSE, 'message' => 'Successfully Saved!!', 'data' => ['LeadId' => $Lead->id]]);
            }
        }
    }

    public function bulk_store(Request $request) {
        if (empty($request->token)) {
            return response()->json(['success' => FALSE, 'error' => TRUE, 'message' => 'Your authentication is not active!!', 'data' => TRUE]);
        } else {
            $token = $request->token;
            $UserDetail = User::where('remember_token', $token)->first();
            if (empty($UserDetail->name)) {
                return response()->json(['success' => FALSE, 'error' => TRUE, 'message' => 'Your token is not active!!', 'data' => NULL]);
            }
        }
        $request->request->remove('token');
        $validator = Validator::make($request->all(), [
                    'first_name' => 'required|string|max:50',
                    'last_name' => 'required|string|max:50',
                    'phone_number' => 'required|numeric|regex:/(0)[0-9]{10}/|unique:leads',
                    'alt_phone' => 'nullable|numeric|regex:/(0)[0-9]{10}/',
                    'date_of_birth' => 'nullable|date_format:Y-m-d',
                    'email' => 'nullable|email',
                    'gender' => 'nullable|regex:/^[a-zA-Z]+$/u',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => FALSE, 'error' => TRUE, 'message' => 'Some fields validation has been failed!!', 'data' => $validator->messages()->all()]);
        } else {

            $Lead = new Lead();
            $Lead->user_id = @$UserDetail->id;
            $Lead->title = @$request->title;
            $Lead->first_name = @$request->first_name;
            $Lead->middle_initial = @$request->middle_initial;
            $Lead->last_name = @$request->last_name;
            $Lead->address1 = @$request->address1;
            $Lead->address2 = @$request->address2;
            $Lead->address3 = @$request->address3;
            $Lead->city = @$request->city;
            $Lead->state = @$request->state;
            $Lead->province = @$request->province;
            $Lead->postal_code = @$request->postal_code;
            $Lead->country_code = @$request->country_code;
            $Lead->gender = @$request->gender;
            $Lead->date_of_birth = @$request->date_of_birth;
            $Lead->phone_code = @$request->phone_code;
            $Lead->phone_number = @$request->phone_number;
            $Lead->alt_phone = @$request->alt_phone;
            $Lead->email = @$request->email;
            $Lead->comments = @$request->comments;
            if ($Lead->save()) {
                return response()->json(['success' => TRUE, 'error' => FALSE, 'message' => 'Successfully Saved!!', 'data' => ['LeadId' => $Lead->id]]);
            }
        }
    }
    
}
