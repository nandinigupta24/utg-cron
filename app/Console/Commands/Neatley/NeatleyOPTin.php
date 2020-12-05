<?php

namespace App\Console\Commands\Neatley;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Model\UTGAPI\NeatleyOPTins;
use Mail;

class NeatleyOPTin extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:NeatleyOPTin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to create leads';

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
//        $data = NeatleyOPTins::where('EnergyProceedCheck','yes')->orderBy('saleid','desc')->limit(1)->get();
        $start = Carbon::now()->startOfDay();
        $end = Carbon::now()->endOfDay();
        
        $listId = 1009;
        $user = 'Intelling-OmniChannel';
        $pass = '2j4VHhYYHqkTnBjJ';
        $token = '0HeIOWyak5UIdeXKmD4twR7kty030UWXQ4fiU6e6kr7cuPgTm2';
              
        $Count = 0;
        $data = DB::connection('118FordRelaunch')
                ->table('Sales')
                ->where('created_at', '>=', $start)
                ->where('created_at', '<=', $end)
                ->where('EnergyProceedCheck', 'yes')
                ->get();


        if (!empty($data) && count($data)) {
            $postData = [];
            $Count = 0;
            foreach ($data as $key => $value) {

                $dataExistDialer = DB::connection('OmniDialer')
                        ->table('list')
                        ->where('list_id', $listId)
                        ->where('phone_number', $value->phone_number)
                        ->count();
                
                $Optins = new NeatleyOPTins();
                if ($dataExistDialer > 0) {
                     $Optins->duplicate_status = 'yes';
                } else {
                    $Optins->duplicate_status = 'no';
                }
                
                $Optins->source_id = $value->source_id;
                $Optins->lead_id = $value->lead_id;
                $Optins->sale_id = $value->saleid;
                $Optins->first_name = $value->First_Name;
                $Optins->last_name = $value->Last_Name;
                $Optins->add1 = $value->Address1;
                $Optins->add2 = $value->Address2;
                $Optins->add3 = $value->Address3;
                $Optins->city = $value->City;
                $Optins->postal_code = $value->Postcode;
                $Optins->phone_number = $value->phone_number;
                $Optins->email = (!empty($value->email)) ? $value->email : 'test@gmail.com';
                $Optins->agent_id = $value->agentid;
                $Optins->datasource = $value->source_id;
                $Optins->campaign_name = $value->campaign;
                if ($Optins->save()) {
                    if($dataExistDialer == 0){
                        $Count++;
                        $postData[$Count]['import_id'] = $Optins->id;
                        $postData[$Count]['data_list'] = $listId;
                        $postData[$Count]['main_phone'] = @$value->phone_number;
                        $postData[$Count]['title'] = get_empty(@$value->title, '');
                        $postData[$Count]['first_name'] = get_empty(@$value->First_Name, '');
                        $postData[$Count]['last_name'] = get_empty(@$value->Last_Name, '');
                        $postData[$Count]['postcode'] = get_empty(@$value->Postcode, '');
                        $postData[$Count]['address1'] = get_empty(@$value->Address1, '');
                        $postData[$Count]['address2'] = get_empty(@$value->Address2, '');
                        $postData[$Count]['address3'] = get_empty(@$value->Address3, '');
                        $postData[$Count]['city'] = get_empty(@$value->City, '');
                        $postData[$Count]['email'] = get_empty(@$value->email, 'test@gmail.com');
                        $postData[$Count]['source_code'] = 'FORDOptin-' . date('Ymd') . '-' .$Optins->id;
                        $postData[$Count]['source'] = $value->source_id;
                        $postData[$Count]['custom_fields'] = ['optindate' => $value->created_at, 'Agent ID' => $value->agentid, 'Datasource' => $value->source_id];
                    }
                }
            }
        }

        
        /* END */
        $postData1 = [];
        $postData1['token'] = $token;
        $postData1['customers'] = $postData;
       
        $dataResponse = get_omni_api_curl_test($user, $pass, $token, $postData1);

        get_omni_response_update_NEATLEY($dataResponse);
        
        
        
        
        $records = [];
        $data1 = NeatleyOPTins::where('created_at', '>=', $start)
                ->where('created_at', '<=', $end)
                ->where('duplicate_status','yes')
                ->count();
        $data2 = NeatleyOPTins::where('created_at', '>=', $start)
                ->where('created_at', '<=', $end)
                ->where('duplicate_status','no')
                ->count();
        $data3 = NeatleyOPTins::where('created_at', '>=', $start)
                ->where('created_at', '<=', $end)
                ->count();
        
        $records['Duplicate'] = $data1;
        $records['Loaded'] = $data2;
        $records['Total'] = $data3;
        
//        $arrayMailTo = ['diallersupport@intelling.co.uk'];
        $arrayMailTo = [env('DIALER_TEAM_EMAIL')];
//        $arrayMailTo = ['apanwar@usethegeeks.co.uk'];
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.logs.neatley_optin';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com', 'Kelly.McNeill@intelling.co.uk', 'George.Eastham@switchexperts.co.uk', 'Mike.Oxton@intelling.co.uk', 'Anthony.Monks@intelling.co.uk','Colm.Corran@intelling.co.uk','apanwar@usethegeeks.co.uk'];
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'Neatley OPTin - FORD';
        $mail_data['data'] = @$records;
        

        $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                    $m->from($mail_data['from'], 'Intelling');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                });
        /* END */
    }

}
