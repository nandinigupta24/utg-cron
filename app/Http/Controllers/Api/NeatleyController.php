<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use App\Model\UTGAPI\OctopusAPIO2LGSE;
use App\Model\IntellingScriptDB\SDSales;

class NeatleyController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {

//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $user = 'Intelling-OmniChannel';
        $pass = '2j4VHhYYHqkTnBjJ';
        $token = '0HeIOWyak5UIdeXKmD4twR7kty030UWXQ4fiU6e6kr7cuPgTm2';
        $listId = 30083;
        $case = $request->script_type;
        $saleid = $request->saleid;


        switch ($case) {
            case 'SwitchExpert':
                $data = SDSales::where('saleid', $saleid)->first();
                $campaign = $data->campaign_name;
                break;
            case 'TalkingPeople':
                $data = SDSales::where('saleid', $saleid)->first();
                $campaign = $data->security_phrase;
                break;
            case 'OilGenco':
                $data = SDSales::where('saleid', $saleid)->first();
                $campaign = '';
                break;
            case 'RightDeal':
                $data = SDSales::where('saleid', $saleid)->first();
                $campaign = '';
                break;
            case 'O2LeadGen':
                $data = DB::connection('IntellingScriptDB')->table('O2_leadgen_3006_O')->where('saleid', $saleid)->first();
                $campaign = '';
                break;
            case 'TalkingCust':
                $data = DB::connection('O2Inbound')->table('inboundSales')->where('saleid', $saleid)->first();
                $campaign = '';
                break;
            default:
        }
            
        $OctopusAPIO2LGSE = new OctopusAPIO2LGSE();
        $OctopusAPIO2LGSE->source_id = 'LIVE_OPTIN';
        $OctopusAPIO2LGSE->lead_id = $data->lead_id;
        $OctopusAPIO2LGSE->sale_id = $saleid;
        $OctopusAPIO2LGSE->first_name = $data->first_name;
        $OctopusAPIO2LGSE->last_name = $data->last_name;
        $OctopusAPIO2LGSE->add1 = $data->address1;
        $OctopusAPIO2LGSE->add2 = $data->address2;
        $OctopusAPIO2LGSE->add3 = $data->address3;
        $OctopusAPIO2LGSE->city = $data->city;
        $OctopusAPIO2LGSE->state = $data->state;
        $OctopusAPIO2LGSE->postal_code = $data->postal_code;
        $OctopusAPIO2LGSE->phone_number = $data->phone_number;
        $OctopusAPIO2LGSE->email = (!empty($data->email)) ? $data->email : 'test@gmail.com';
        $OctopusAPIO2LGSE->agent_id = $data->agentid;
        $OctopusAPIO2LGSE->datasource = $data->source_id;
        $OctopusAPIO2LGSE->campaign_name = $campaign;
        $OctopusAPIO2LGSE->inbound_group = $case;

        if (empty($data->phone_number)) {
            $OctopusAPIO2LGSE->api_status = 'failed';
        }
        if ($OctopusAPIO2LGSE->save()) {
            if (empty($data->phone_number)) {
                die('Finish');
            }

            $lastInsertId = $OctopusAPIO2LGSE->id;
        }

        $OctopusAPIO2LGSEUpdate = OctopusAPIO2LGSE::find($lastInsertId);
        $vendorLeadCode = $case.'-' . $lastInsertId;

        $dataExist = DB::connection('OmniDialer')
                ->table('list')
                ->where('phone_number', $data->phone_number)
                ->where('list_id', $listId)
                ->count();

        if ($dataExist > 0) {
            $OctopusAPIO2LGSEUpdate->duplicate_status = 'yes';
        } else {
            $OctopusAPIO2LGSEUpdate->duplicate_status = 'no';

            $postData = [];
            $postData['data_list'] = $listId;
            $postData['main_phone'] = @$data->phone_number;
            $postData['first_name'] = @$data->first_name;
            $postData['last_name'] = @$data->last_name;
            $postData['address1'] = @$data->address1;
            $postData['address2'] = @$data->address2;
            $postData['address3'] = @$data->address3;
            $postData['city'] = @$data->city;
            $postData['state'] = @$data->state;
            $postData['postal_code'] = @$data->postal_code;
            $postData['province'] = @$data->province;
            $postData['source_code'] = $vendorLeadCode;
            $postData['source'] = 'LIVE_OPTIN';
            $postData['email'] = (!empty($data->email)) ? $data->email : 'test@gmail.com';
            $postData['custom_fields'] = ['optindate' => date('Y-m-d H:i:s'), 'agentid' => @$data->agentid, 'datasource' => @$data->source_id];
            $dataPostAPI = get_omni_api_curl($user, $pass, $postData);

            $OctopusAPIO2LGSEUpdate->api_response = serialize($dataPostAPI);
        }
        if ($OctopusAPIO2LGSEUpdate->save()) {
            
        }
    }

}
