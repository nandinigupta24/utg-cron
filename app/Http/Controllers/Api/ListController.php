<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use App\User;
use App\Model\Intelling\LeadSupplier;
use Mail;
class ListController extends Controller {

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
        /* START TOKEN Matching Process */
//        if (empty($request->token)) {
//            return response()->json(['success' => FALSE, 'error' => TRUE, 'message' => 'Your authentication is not active!!', 'data' => TRUE]);
//        } else {
//            $token = $request->token;
//            $UserDetail = User::where('remember_token', $token)->first();
//            if (empty($UserDetail->name)) {
//                return response()->json(['success' => FALSE, 'error' => TRUE, 'message' => 'Your token is not active!!', 'data' => NULL]);
//            }
//        }
        /* END */

        /* Fields Matching */
//        $ArrayInsertFields = array_keys($request->all());
//        $ArrayTableFields = ['token','title','first_name','middle_initial','last_name','address1','address2','address3','city','state','province','postal_code','country_code','gender','date_of_birth','phone_code','phone_number','alt_phone','email','comments'];
//        $result = array_diff($ArrayInsertFields,$ArrayTableFields);
//        if(!empty($result) && count($result)){
//            return response()->json(['success' => FALSE, 'error' => TRUE, 'message' => 'Some fields are wrong entered!!Please fix !!', 'data' => array_shift($result)]);
//        }
        /* END */

        $request->request->remove('token');

        /* Fields Validation */
        $validator = Validator::make($request->all(), [
//                    'first_name' => 'required|string|max:50',
//                    'last_name' => 'required|string|max:50',
                    'Phone_1' => 'required|numeric|regex:/(0)[0-9]{10}/',
//                    'alt_phone' => 'nullable|numeric|regex:/(0)[0-9]{10}/',
//                    'date_of_birth' => 'nullable|date_format:Y-m-d',
//                    'email' => 'nullable|email',
//                    'gender' => 'nullable|regex:/^[a-zA-Z]+$/u',
        ]);
        /* END */
        
        if ($validator->fails()) {
            return response()->json(['success' => FALSE, 'error' => TRUE, 'message' => 'Some fields validation has been failed!!', 'data' => $validator->messages()->all()]);
        } else {
            $VendorLeadCodePrefix = 'Supplier-' . date('YmdHis');
            if(trim($request->SSID) == 'Mr Savvy'){
                $listId = 30034;
            }elseif(strpos(trim($request->SSID), 'TFLI') !== false) { 
                $listId = 30035;
            }else{
                 $listId = 30036;
            }
            /* DATA insert here */
            $Lead = new LeadSupplier();
            $Lead->list_id = @$listId;
            $Lead->Lead_ID = @$request->Lead_ID;
            $Lead->Email = @$request->Email;
            $Lead->Title = @$request->Title;
            $Lead->First_Name = @$request->First_Name;
            $Lead->Last_Name = @$request->Last_Name;
            $Lead->DOB = @$request->DOB;
            $Lead->Street_1 = @$request->Street_1;
            $Lead->Street_2 = @$request->Street_2;
            $Lead->City = @$request->City;
            $Lead->County = @$request->County;
            $Lead->Postcode = @$request->Postcode;
            $Lead->Phone_1 = @$request->Phone_1;
            $Lead->IP_Address = @$request->IP_Address;
            $Lead->Source = @$request->Source;
            $Lead->C1 = @$request->C1;
            $Lead->SID = @$request->SID;
            $Lead->SSID = @$request->SSID;
            $Lead->Optin_Url = @$request->Optin_Url;
            $Lead->Optin_Date = (!empty($request->Optin_Date)) ? date('Y-m-d', strtotime(@$request->Optin_Date)) : NULL;
            $Lead->UniqueCode = @$request->UniqueCode;
            $Lead->AboutCust1a = 'OPTINDATE';
            $Lead->AboutCust1b = @$request->Optin_Date;
            $Lead->AboutCust2a = 'Website';
            $Lead->AboutCust2b = @$request->Optin_Url;
            $Lead->AboutCust3a = 'DATASOURCE';
            $Lead->AboutCust3b = @$request->Source;
            if ($Lead->save()) {
                $VendorLeadCode = $VendorLeadCodePrefix . '-' . str_pad($Lead->id, 8, '0', STR_PAD_LEFT);
                $LeadSupplier = LeadSupplier::find($Lead->id);
                $LeadSupplier->vendor_lead_code = $VendorLeadCode;
                $ValidationDate = date('Y-m-d', strtotime("-60 days"));
                $listIdSrch = DB::connection('MainDialer')
                ->table('lists')
                ->where('campaign_id', 3003)
                ->pluck('list_id')
                ->toArray();
                
                $dataExistDialer = DB::connection('MainDialer')
                        ->table('list')
                        ->where('entry_date', '>=', $ValidationDate.' 00:00:00')
                        ->whereIn('list_id', $listIdSrch)
                        ->where('phone_number', @$request->Phone_1)
                        ->count();
                
                if ($dataExistDialer == 0) {
                    $LeadSupplier->duplicate_status = 'no';
                    $dataArray = [];
                    $dataArray['list_id'] = $listId;
                    $dataArray['phone_number'] = get_phone_check(@$request->Phone_1);
                    $dataArray['title'] = @$request->Title;
                    $dataArray['first_name'] = @$request->First_Name;
                    $dataArray['last_name'] = @$request->Last_Name;
                    $dataArray['postal_code'] = @$request->Postcode;
                    $dataArray['email'] = @$request->Email;
                    $dataArray['address1'] = @$request->Street_1;
                    $dataArray['address2'] = @$request->Street_2;
                    $dataArray['city'] = @$request->City;
                    $dataArray['country'] = @$request->County;
                    $dataArray['vendor_lead_code'] = $VendorLeadCode;
                    $dataArray['source_id'] = @$request->Source;
                    $dataArray['custom_fields'] = 'Y';
                    $dataArray['C1'] = @$request->C1;
                    $dataArray['SID'] = @$request->SID;
                    $dataArray['SSID'] = @$request->SSID;
                    $dataArray['Optin_Url'] = @$request->Optin_Url;
                    $dataArray['Optin_Date'] = (!empty($request->Optin_Date)) ? date('Y-m-d', strtotime(@$request->Optin_Date)) : NULL;;
                    $dataArray['UniqueCode'] = @$request->UniqueCode;
                    $dataArray['AboutCust1a'] = 'OPTINDATE';
                    $dataArray['AboutCust1b'] = @$request->Optin_Date;
                    $dataArray['AboutCust2a'] = 'Website';
                    $dataArray['AboutCust2b'] = @$request->Optin_Url;
                    $dataArray['AboutCust3a'] = 'DATASOURCE';
                    $dataArray['AboutCust3b'] = @$request->Source;
                    $queryString = http_build_query($dataArray);

                $data = @file_get_contents('http://10.29.104.7/Admin/.functions/non_agent_api.php?source=Test&user=APIUSER&pass=APIUSER&function=add_lead&' . $queryString);
                $LeadSupplier->api_response = serialize($data);
                    if(strpos($data, 'Error:') !== false){
                        $arrayMailTo = ['diallersupport@intelling.co.uk'];
                        $mail_data = array();
                        $mail_data['to'] = $arrayMailTo;
                        $mail_data['from'] = 'intellingreports@intelling.co.uk';
                        $mail_data['msg'] = '';
                        $mail_data['view'] = 'emails.error_main_api';
                        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
                        $mail_data['subject'] = 'Error on Supplier Lead';
                        $mail_data['data'] = @$data;

                        $result = Mail::send($mail_data['view'], ['data' => $data], function ($m) use ($mail_data) {
                                    $m->from($mail_data['from'], 'Intelling');
                                    if (!empty($mail_data['cc'])) {
                                        $m->cc($mail_data['cc']);
                                    }
                                    $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                                });
                    }
                } else {
                    $LeadSupplier->duplicate_status = 'yes';
                    $dataListIds = DB::connection('MainDialer')
                                            ->table('list')
                                            ->where('entry_date', '>=', $ValidationDate.' 00:00:00')
                                            ->whereIn('list_id', $listIdSrch)
                                            ->where('phone_number',get_phone_check(@$request->Phone_1))
                                            ->pluck('list_id')->toArray();
                    
                    $LeadSupplier->duplicate_list_id = implode(',', $dataListIds);
                }

                if ($LeadSupplier->save()) {
                    
                }
                /* POST on API MAIN DIALER */


                return response()->json(['success' => TRUE, 'error' => FALSE, 'message' => 'Successfully Saved!!', 'data' => ['LeadId' => $Lead->id]]);
            }
        }
    }

    public function detail(Request $request) {
        /* Fields Validation */
        $validator = Validator::make($request->all(), [
                    'lead_id' => 'required',
        ]);
        /* END */

        if ($validator->fails()) {
            return response()->json(['success' => FALSE, 'error' => TRUE, 'message' => 'Some fields validation has been failed!!', 'data' => $validator->messages()->all()]);
        } else {

            /* DATA insert here */
            $Lead = LeadSupplier::find($request->lead_id);
            if (!empty($Lead->id)) {
                $Lead = $Lead->toArray();
                unset($Lead['id']);
                unset($Lead['Lead_ID']);
                return response()->json(['success' => TRUE, 'error' => FALSE, 'message' => 'Successfully Found!!', 'data' => $Lead]);
            } else {
                return response()->json(['success' => FALSE, 'error' => TRUE, 'message' => 'Lead Id does not exist!!', 'data' => NULL]);
            }
        }
    }    

    public function manual_post(Request $request){
//        $data = LeadSupplier::where('created_at','>=',date('Y-m-d').' 00:00:00')->where('duplicate_status','yes')->get()->toArray();
//        $data = LeadSupplier::where('created_at','>=',date('Y-m-d').' 00:00:00')->where('duplicate_status','no')->first()->toArray(); 
            
//        pr(unserialize($data['api_response']));
        $dataArray = [];
        $dataArray['list_id'] = 30036;
//        $dataArray['phone_number'] = get_phone_check(@$value->Phone_1);
        $data = @file_get_contents('http://10.29.104.7/Admin/.functions/non_agent_api.php?source=Test&user=APIUSER&pass=APIUSER&function=add_lead&' . $queryString);
        
        if(strpos($data, 'ERROR:') !== false){
            $arrayMailTo = ['apanwar@usethegeeks.co.uk'];
                        $mail_data = array();
                        $mail_data['to'] = $arrayMailTo;
                        $mail_data['from'] = 'intellingreports@intelling.co.uk';
                        $mail_data['msg'] = '';
                        $mail_data['view'] = 'emails.error_main_api';
//                        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
                        $mail_data['subject'] = 'Error on Supplier Lead';
                        $mail_data['data'] = @$data;

                        $result = Mail::send($mail_data['view'], ['data' => $data], function ($m) use ($mail_data) {
                                    $m->from($mail_data['from'], 'Intelling');
                                    if (!empty($mail_data['cc'])) {
                                        $m->cc($mail_data['cc']);
                                    }
                                    $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                                });
        }
        
        exit;
        foreach($data as $value){
            if($value->SSID == 'Mr Savvy'){
                 $listId = 30034;
            }elseif(strpos($value->SSID, 'TFLI') !== false) { 
                $listId = 30035;
            }else{
                 $listId = 30036;
            }
            $dataArray = [];
            $dataArray['list_id'] = $listId;
            $dataArray['phone_number'] = get_phone_check(@$value->Phone_1);
            $dataArray['title'] = @$value->Title;
            $dataArray['first_name'] = @$value->First_Name;
            $dataArray['last_name'] = @$value->Last_Name;
            $dataArray['postal_code'] = @$value->Postcode;
            $dataArray['email'] = @$value->Email;
            $dataArray['address1'] = @$value->Street_1;
            $dataArray['address2'] = @$value->Street_2;
            $dataArray['city'] = @$value->City;
            $dataArray['country'] = @$value->County;
            $dataArray['vendor_lead_code'] = @$value->vendor_lead_code;
            $dataArray['source_id'] = @$value->Source;
            $dataArray['custom_fields'] = 'Y';
            $dataArray['C1'] = @$value->C1;
            $dataArray['SID'] = @$value->SID;
            $dataArray['SSID'] = @$value->SSID;
            $dataArray['Optin_Url'] = @$value->Optin_Url;
            $dataArray['Optin_Date'] = (!empty($value->Optin_Date)) ? date('Y-m-d', strtotime(@$value->Optin_Date)) : NULL;;
            $dataArray['UniqueCode'] = @$value->UniqueCode;
            $dataArray['AboutCust1a'] = 'OPTINDATE';
            $dataArray['AboutCust1b'] = @$value->Optin_Date;
            $dataArray['AboutCust2a'] = 'Website';
            $dataArray['AboutCust2b'] = @$value->Optin_Url;
            $dataArray['AboutCust3a'] = 'DATASOURCE';
            $dataArray['AboutCust3b'] = @$value->Source;
            $queryString = http_build_query($dataArray);
           
            $ValidationDate = date('Y-m-d', strtotime("-60 days"));
            $listIdSrch = DB::connection('MainDialer')
                                        ->table('lists')
                                        ->where('campaign_id', 3003)
                                        ->pluck('list_id')
                                        ->toArray();
                
            $dataExistDialer = DB::connection('MainDialer')
                                        ->table('list')
                                        ->where('entry_date', '>=', $ValidationDate.' 00:00:00')
                                        ->whereIn('list_id', $listIdSrch)
                                        ->where('phone_number', get_phone_check(@$value->Phone_1))
                                        ->count();
                $LeadSupplier = LeadSupplier::find($value->id);
                if ($dataExistDialer == 0) {
                $data = @file_get_contents('http://10.29.104.7/Admin/.functions/non_agent_api.php?source=Test&user=APIUSER&pass=APIUSER&function=add_lead&' . $queryString);
                $LeadSupplier->api_response = serialize($data);
                $LeadSupplier->duplicate_status = 'no';
                }else{
                    $LeadSupplier->duplicate_status = 'yes';
                   $dataListIds = DB::connection('MainDialer')
                                            ->table('list')
                                            ->where('entry_date', '>=', $ValidationDate.' 00:00:00')
                                            ->whereIn('list_id', $listIdSrch)
                                            ->where('phone_number',get_phone_check(@$value->Phone_1))
                                            ->pluck('list_id')->toArray();
                    
                    $LeadSupplier->duplicate_list_id = implode(',', $dataListIds);
                }

                if ($LeadSupplier->save()) {
                    
                }
        }
    }
}
