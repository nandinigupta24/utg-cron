<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;
use App\Model\UTGAPI\OctopusAPIO2LGSE;
use App\Model\IntellingScriptDB\SDSales;

class OctopusLeadsAPI2 extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:OctopusLeadsAPI2';

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
//        $start = $end = date('Y-m-d');
        $start = $end = '2019-04-18';
        $user = 'Intelling-OmniChannel';
        $pass = '2j4VHhYYHqkTnBjJ';
        $token = '0HeIOWyak5UIdeXKmD4twR7kty030UWXQ4fiU6e6kr7cuPgTm2';
        $listId = 30083;
        $data = OctopusAPIO2LGSE::where('created_at', '>=', $start . ' 00:00:00')
                ->where('created_at', '<=', $end . ' 23:59:59')
                ->whereNULL('duplicate_status')
                ->get();
      
        foreach ($data as $value) {
            

            

                $lastInsertId = $value->id;
            

            $OctopusAPIO2LGSEUpdate = OctopusAPIO2LGSE::find($lastInsertId);
            $vendorLeadCode = $value->inbound_group . '-' . $lastInsertId;

            $dataExist = DB::connection('OmniDialer')
                    ->table('list')
                    ->where('phone_number', $value->phone_number)
                    ->where('list_id', $listId)
                    ->count();

            if ($dataExist > 0) {
                $OctopusAPIO2LGSEUpdate->duplicate_status = 'yes';
            } else {
                $OctopusAPIO2LGSEUpdate->duplicate_status = 'no';

                $postData = [];
                $postData['token'] = $token;
                $postData['data_list'] = $listId;
                $postData['main_phone'] = @$value->phone_number;
                $postData['first_name'] = @$value->first_name;
                $postData['last_name'] = @$value->last_name;
                $postData['address1'] = @$value->add1;
                $postData['address2'] = @$value->add2;
                $postData['address3'] = @$value->add3;
                $postData['city'] = @$value->city;
                $postData['state'] = @$value->state;
                $postData['postal_code'] = @$value->postal_code;
                $postData['province'] = '';
                $postData['source_code'] = $vendorLeadCode;
                $postData['source'] = 'LIVE_OPTIN';
                $postData['email'] = (!empty($value->email)) ? $value->email : 'test@gmail.com';
                $postData['custom_fields'] = ['optindate' => date('Y-m-d H:i:s'), 'agentid' => @$value->agent_id, 'datasource' => @$value->source_id];
                $dataPostAPI = get_omni_api_curl($user, $pass, $postData);
                
                $OctopusAPIO2LGSEUpdate->api_response = serialize($dataPostAPI);
            }
            if ($OctopusAPIO2LGSEUpdate->save()) {
//                
            }
        }
    }

}
