<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;
use App\Model\O2Inbound\InboundSale;
use App\Model\UTGAPI\O2ReturnProcessData;
use App\Model\UTGAPI\OctopusAPIO2LGSE;
use App\Model\IntellingScriptDB\SDSales;

class EnergySaveMoney extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:EnergySaveMoney';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        ini_set('max_execution_time', 6000);
        $user = 'IntellingTwo';
        $pass = 'Eg926bD5GfbGEJwG';
        $token = 'brwh890FraGrLy1VpvwU9KwDhAwT0EdB4dlWc8IqpzBIjh894L';
        $listId = 30083;



        $data = SDSales::where('createddate', '>=', '2019-01-01 00:00:00')
                ->where('createddate', '<=', '2019-02-03 23:59:59')
                ->where('energy_savemoney', 'yes')
                ->where('script_query', 'TalkingPeople')
                ->whereNotIn('agentid', [1777])
                ->get();

        foreach ($data as $value) {
            $case = 'TalkingPeople';
            $saleid = $value->saleid;
            $exist = OctopusAPIO2LGSE::where('phone_number',$value->phone_number)->count();
            if($exist > 0){
                continue;
            }

            switch ($case) {
                case 'SwitchExpert':
                    $data = DB::connection('SwitchExpert')->table('SDSales')->where('saleid', $saleid)->first();
                    $campaign = $data->campaign_name;
                    break;
                case 'TalkingPeople':
                    $data = DB::connection('SE_RightDea')->table('RDSales')->where('saleid', $saleid)->first();
                    $campaign = $data->security_phrase;
                    break;
                case 'OilGenco':
                    $data = DB::connection('SE_RightDea')->table('OilSales')->where('saleid', $saleid)->first();
                    $campaign = '';
                    break;
                case 'RightDeal':
                    $data = DB::connection('SE_RightDea')->table('RDSales')->where('saleid', $saleid)->first();
                    $campaign = '';
                    break;
                case 'O2LeadGen':
                    $data = DB::connection('IntellingScriptDB')->table('O2_leadgen_3006_O')->where('saleid', $saleid)->first();
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
                    continue;
                }

                $lastInsertId = $OctopusAPIO2LGSE->id;
            }

            $OctopusAPIO2LGSEUpdate = OctopusAPIO2LGSE::find($lastInsertId);
            $vendorLeadCode = $case . '-' . $lastInsertId;

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
                $postData['token'] = $token;
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
                $dataPostAPI = get_omni_api_curl($user, $pass,$postData);

                $OctopusAPIO2LGSEUpdate->api_response = serialize($dataPostAPI);
            }
            if ($OctopusAPIO2LGSEUpdate->save()) {
                
            }
        }
    }

}
