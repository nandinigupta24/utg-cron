<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Model\Intelling\SwitchExpertOPTins;
use Mail;

class OptinAllNew extends Command {   

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:OptinAllNew';

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
        
//        $start = $end = '2019-07-04';
        $start = $end = date('Y-m-d');
        
        $startMessage = date('Y-m-d',strtotime($start));
        $endMessage = date('Y-m-d',strtotime($end));
        
        if($startMessage == $endMessage){
            $subjectMesage = '('.$startMessage.')';
        }else{
            $subjectMesage = '('.$startMessage.' - '.$endMessage.')';
        }

        $listId = 4000;
        $user = 'Intelling-OmniChannel';
        $pass = '2j4VHhYYHqkTnBjJ';
        $token = '0HeIOWyak5UIdeXKmD4twR7kty030UWXQ4fiU6e6kr7cuPgTm2';
        $dataCheck = [];
        $postData = [];
        $Count = 0;
        
        $InboundCampaignID = ['EnitreMedia','Grosvenor','Ignition','IPTel','MTA_Leadgen','Neatley','Oil_Genco','OilGenco','OutworxIn','RightDealIN','Sandra','SEMobSwitch','Switch_Expe','Synergy','Synthesis','Topic'];
        
        /* Start O2 Inbound */
        $campaign_id = 'SWITCH-EXPERTS-OPTIN';
        $InboundData = DB::connection('MainDialer')
                ->table('O2Script.customers')
                ->join('custom_view.list','O2Script.customers.lead_id','custom_view.list.lead_id')
                ->join('custom_view.users','O2Script.customers.fullname','custom_view.users.full_name')
                ->join('custom_view.inbound_log','O2Script.customers.lead_id','custom_view.inbound_log.lead_id')
                ->where('O2Script.customers.DateSent','>=',$start.' 00:00:00')
                ->where('O2Script.customers.DateSent','<=',$end.' 23:59:59')
                ->where('O2Script.customers.bb_switch_call','yes')
                ->whereIn('campaign_id',$InboundCampaignID)
                ->select('O2Script.customers.lead_id', 'O2Script.customers.title', 'O2Script.customers.first_name', 'O2Script.customers.last_name', 'O2Script.customers.phone_number', 'O2Script.customers.email', 'O2Script.customers.address1', 'O2Script.customers.address2', 'O2Script.customers.city', 'O2Script.customers.postal_code','O2Script.customers.teamname','O2Script.customers.current_supplier', 'O2Script.customers.happy_with_supplier', 'O2Script.customers.in_contract','O2Script.customers.bb_switch_call', 'custom_view.inbound_log.call_date','custom_view.inbound_log.campaign_id','custom_view.users.full_name','custom_view.users.user')
                ->groupBy('O2Script.customers.lead_id')
                ->get();
       
        $ExistCampaign = [];
        foreach($InboundData as $value){
            $Count++;
            $CampaignID = $value->campaign_id;
            if(!in_array($value->campaign_id,$ExistCampaign)){
                $ExistCampaign[] = $CampaignID;
                $dataCheck[$CampaignID]['total'] = 0;
                $dataCheck[$CampaignID]['success'] = 0;
                $dataCheck[$CampaignID]['duplicate'] = 0;
            }
            
            $ExistDataInList = DB::connection('OmniDialer')
                                            ->table('list')
                                            ->where('list_id',$listId)
                                            ->where('phone_number',$value->phone_number)
                                            ->count();
            
            if($ExistDataInList > 0){
                $duplicate = 'yes';
            }else{
                $duplicate = 'no';
            }
            
            $dataCheck[$CampaignID]['total']++;
            
            $uniCode = $CampaignID."_OPTIN-" . $value->lead_id;
            $DataSource = $CampaignID.'_OPTIN';
            $SourceCode = $CampaignID.'OPTin-'.date('Ymd').'-'.date('His');
            
            $Optins = new SwitchExpertOPTins();
            $Optins->title = @$value->title;
            $Optins->first_name = @$value->first_name;
            $Optins->last_name = @$value->last_name;
            $Optins->main_phone = @$value->phone_number;
            $Optins->address1 = @$value->address1;
            $Optins->address2 = @$value->address2;
            $Optins->city = @$value->city;
            $Optins->postcode = @$value->postal_code;
            $Optins->email = (!empty(@$value->email)) ? $value->email : 'Test@Test.co.uk';
            $Optins->SSID = $DataSource;
            $Optins->data_list = $listId;
            $Optins->custom_fields_AdentID = $value->user;
            $Optins->campaign_name = $value->campaign_id;
            $Optins->custom_fields_optindate = $value->call_date;
            $Optins->UniqueCode = $uniCode;
            $Optins->duplicate_status = $duplicate;
            $Optins->source = $DataSource;
            $Optins->source_code = $SourceCode;
            if ($Optins->save()) {
                    if($duplicate == 'no'){
                        $postData[$Count]['import_id'] = $Optins->saleid;
                        $postData[$Count]['data_list'] = $listId;
                        $postData[$Count]['main_phone'] = @$value->phone_number;
                        $postData[$Count]['title'] = get_empty(@$value->title,'');
                        $postData[$Count]['first_name'] = get_empty(@$value->first_name,'');
                        $postData[$Count]['last_name'] = get_empty(@$value->last_name,'');
                        $postData[$Count]['postcode'] = get_empty(@$value->postal_code,'');
                        $postData[$Count]['address1'] = get_empty(@$value->address1,'');
                        $postData[$Count]['address2'] = get_empty(@$value->address2,'');
                        $postData[$Count]['address3'] = get_empty(@$value->address1,'');
                        $postData[$Count]['city'] = get_empty(@$value->city,'');
                        $postData[$Count]['email'] = get_empty(@$value->email,'test@gmail.com');
                        $postData[$Count]['source_code'] = $SourceCode;
                        $postData[$Count]['source'] = $DataSource;
                        $postData[$Count]['custom_fields'] = ['optindate'=>$value->call_date,'Agent ID' => $value->user,'Datasource' => $DataSource];
                        $dataCheck[$CampaignID]['success']++;
                    }else{
                        $dataCheck[$CampaignID]['duplicate']++;
                    }
            }
        }
        
       /*END O2 Inbound*/
        
       /*START O2 OUTBOUND*/
         $O2OutboundData = DB::connection('O2Combine')
                            ->table('O2Sales')
                            ->where('createddate', '>=', $start . ' 00:00:00')
                            ->where('createddate', '<=', $end . ' 23:59:59')
                            ->where('opt_in', 'yes')
//                             ->select('saleid','title','first_name','last_name','address1','address2','address3','city','postal_code','campaign_name','email','agentid','createddate')
                            ->get();
        
        $dataCheck['PREM_OPTIN']['total'] = 0;
        $dataCheck['PREM_OPTIN']['success'] = 0;
        $dataCheck['PREM_OPTIN']['duplicate'] = 0;
        
        if(!empty($O2OutboundData) && count($O2OutboundData)){
            foreach ($O2OutboundData as $key => $value) {
                
                $Count++;
                $dataCheck['PREM_OPTIN']['total']++;
                $uniCode = "PREM_OPTIN-" . $value->saleid;
            
                $dataExistDialer = DB::connection('OmniDialer')
                                            ->table('list')
                                            ->where('list_id',$listId)
                                            ->where('phone_number',$value->phone_number)
                                            ->count();
                
                if($dataExistDialer > 0){
                    $duplicate = 'yes';
                }else{
                    $duplicate = 'no';
                }
                
                if($value->campaign_name == 3036){
                    $SourceID = 'OPTIN_FUNNEL8O2';
                }else{
                    $SourceID = 'PREM_OPTIN';
                }
                $Optins = new SwitchExpertOPTins();
                $Optins->title = @$value->title;
                $Optins->first_name = @$value->first_name;
                $Optins->last_name = @$value->last_name;
                $Optins->main_phone = @$value->phone_number;
                $Optins->address1 = @$value->address1;
                $Optins->address2 = @$value->address2;
                $Optins->address3 = @$value->address3;
                $Optins->city = @$value->city;
                $Optins->postcode = @$value->postal_code;
                $Optins->date_of_birth = '';
                $Optins->email = (!empty(@$value->email)) ? $value->email : 'Test@Test.co.uk';
                $Optins->custom_fields_currentprovider = '';
                $Optins->source = '';
                $Optins->SSID = $SourceID;
                $Optins->source_code = '';
                $Optins->data_list = '';
                $Optins->token = '';
                $Optins->custom_fields_AdentID = @$value->agentid;
                $Optins->campaign_name = @$value->campaign_name;
                $Optins->UniqueCode = $uniCode;
                $Optins->custom_fields_optindate = $value->createddate;
                $Optins->duplicate_status = $duplicate;
                if ($Optins->save()) {
                    if($duplicate == 'no'){
                        $postData[$Count]['import_id'] = $Optins->saleid;
                        $postData[$Count]['data_list'] = $listId;
                        $postData[$Count]['main_phone'] = @$value->phone_number;
                        $postData[$Count]['title'] = get_empty(@$value->title,'');
                        $postData[$Count]['first_name'] = get_empty(@$value->first_name,'');
                        $postData[$Count]['last_name'] = get_empty(@$value->last_name,'');
                        $postData[$Count]['postcode'] = get_empty(@$value->postal_code,'');
                        $postData[$Count]['address1'] = get_empty(@$value->address1,'');
                        $postData[$Count]['address2'] = get_empty(@$value->address2,'');
                        $postData[$Count]['address3'] = get_empty(@$value->address3,'');
                        $postData[$Count]['city'] = get_empty(@$value->city,'');
                        $postData[$Count]['email'] = get_empty(@$value->email,'test@gmail.com');
                        $postData[$Count]['source_code'] = 'EZOptin-'.date('Ymd').'-'.date('His');
                        $postData[$Count]['source'] = $SourceID;
                        $postData[$Count]['custom_fields'] = ['optindate'=>$value->createddate,'Agent ID' => $value->agentid,'Datasource' => $SourceID];
                        $dataCheck['PREM_OPTIN']['success']++;
                    }else{
                        $dataCheck['PREM_OPTIN']['duplicate']++;
                    }
                }
            }
        }
        
       /*END O2 OUTBOUND*/
       
        
        /*START Consumer*/
        $dataP2Consumer = DB::connection('NewDialer')->select("Select c.lead_id, c.title, c.first_name, c.last_name, c.phone_number, c.email, c.address1, c.address2, c.city, c.postal_code,c.fullname,c.teamname,c.current_supplier, c.happy_with_supplier, c.in_contract,c.bb_switch_call, l.last_local_call_time, u.user,c.campaign
                      from Intelling.customers c
                      JOIN custom_view.list l
                      on l.lead_id=c.lead_id
                      JOIN custom_view.users u
                      on c.fullname=u.full_name
                      where l.last_local_call_time between '".$start." 00:00:00' and '".$end." 23:59:59'
                      and c.bb_switch_call = 'yes'");
        
        $dataCheck['Consumer']['total'] = 0;
        $dataCheck['Consumer']['success'] = 0;
        $dataCheck['Consumer']['duplicate'] = 0;
        if(!empty($dataP2Consumer) && count($dataP2Consumer)){
            foreach ($dataP2Consumer as $key => $value) {
                switch($value->campaign){
                    case 1307:
                        $SSID = 'CON_OPTIN';
                        $CampaignName = 'O2 Consumer';
                        break;
                    case 1309:
                        $SSID = 'GRAD_SYN_OPTIN';
                        $CampaignName = 'O2 E2E Platinum SYN';
                        break;
                    case 1306:
                        $SSID = 'PLAT_SYN_OPTIN';
                        $CampaignName = 'O2 E2E Gradbay SYN';
                        break;
                    default:
                     $SSID = 'CON_OPTIN';  
                     $CampaignName = 'O2 Consumer';
                       
                }
                
                $Count++;
                $dataExistDialer = DB::connection('OmniDialer')
                                            ->table('list')
                                            ->where('list_id',$listId)
                                            ->where('phone_number',$value->phone_number)
                                            ->count();
            if($dataExistDialer > 0){
                $duplicate = 'yes';
            }else{
                $duplicate = 'no';
            }
            $dataCheck['Consumer']['total']++;
            $uniCode = $SSID."-" . $value->lead_id;
            
            $Optins = new SwitchExpertOPTins();
            $Optins->title = @$value->title;
            $Optins->first_name = @$value->first_name;
            $Optins->last_name = @$value->last_name;
            $Optins->main_phone = @$value->phone_number;
            $Optins->address1 = @$value->address1;
            $Optins->address2 = @$value->address2;
            $Optins->address3 = '';
            $Optins->city = @$value->city;
            $Optins->postcode = @$value->postal_code;
            $Optins->date_of_birth = '';
            $Optins->email = (!empty(@$value->email)) ? $value->email : 'Test@Test.co.uk';
            $Optins->custom_fields_currentprovider = '';
            $Optins->source = '';
            $Optins->SSID = $SSID;
            $Optins->source_code = '';
            $Optins->data_list = '';
            $Optins->token = '';
            $Optins->custom_fields_AdentID = @$value->user;
            $Optins->campaign_name = $CampaignName;
            $Optins->custom_fields_optindate = $value->last_local_call_time;
            $Optins->UniqueCode = $uniCode;
            $Optins->duplicate_status = $duplicate;

            if ($Optins->save()) {
                if($duplicate == 'no'){
                    $postData[$Count]['import_id'] = $Optins->saleid;
                    $postData[$Count]['data_list'] = $listId;
                    $postData[$Count]['main_phone'] = @$value->phone_number;
                    $postData[$Count]['title'] = get_empty(@$value->title,'');
                    $postData[$Count]['first_name'] = get_empty(@$value->first_name,'');
                    $postData[$Count]['last_name'] = get_empty(@$value->last_name,'');
                    $postData[$Count]['postcode'] = get_empty(@$value->postal_code,'');
                    $postData[$Count]['address1'] = get_empty(@$value->address1,'');
                    $postData[$Count]['address2'] = get_empty(@$value->address2,'');
                    $postData[$Count]['address3'] = get_empty(@$value->address3,'');
                    $postData[$Count]['city'] = get_empty(@$value->city,'');
                    $postData[$Count]['email'] = get_empty(@$value->email,'test@gmail.com');
                    $postData[$Count]['source_code'] = $SSID.'-'.date('Ymd').'-'.date('His');
                    $postData[$Count]['source'] = $SSID;
                    $postData[$Count]['custom_fields'] = ['optindate'=>$value->last_local_call_time,'Agent ID' => $value->user,'Datasource' => $SSID];

                    $dataCheck['Consumer']['success']++;
                }else{
                    $dataCheck['Consumer']['duplicate']++;
                 }
            }
        }
        }
        
        /*END Consumer*/
        
        /*END*/
        $postData1 = [];
        $postData1['token'] = $token;
        $postData1['customers'] = $postData;
        $dataResponse = get_omni_api_curl_test($user, $pass, $token, $postData1);
        
        get_omni_response_update($dataResponse);
        
//        $arrayMailTo = ['diallersupport@intelling.co.uk'];
        $arrayMailTo = [env('DIALER_TEAM_EMAIL')];
//        $arrayMailTo = ['apanwar@usethegeeks.co.uk'];
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.logs.optin';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com','Kelly.McNeill@intelling.co.uk','George.Eastham@switchexperts.co.uk','Mike.Oxton@intelling.co.uk','Anthony.Monks@intelling.co.uk','apanwar@usethegeeks.co.uk'];
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'SE Broadband OPTin '.$subjectMesage;
        $mail_data['data'] = @$dataCheck;
        $mail_data['timeFormat'] = array('start'=>$start,'end'=>$end);

        $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                    $m->from($mail_data['from'], 'Intelling');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                });
        /*END*/
    }

}
