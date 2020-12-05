<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Model\Intelling\SwitchExpertOPTins;
use Mail;

class OptinAllOLD extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:OptinAllOLD';

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
        
//        if (date('D') == 'Mon') {
//            $start = date('Y-m-d', strtotime(' -3 day'));
//            $end = date('Y-m-d', strtotime(' -1 day'));
//        } else {
//            $start = '2019-03-29';
//            $end = '2019-04-02';
            $start = $end = date('Y-m-d');
//        }
        
        $startMessage = date('Y-m-d',strtotime($start));
        $endMessage = date('Y-m-d',strtotime($end));
        if($startMessage == $endMessage){
            $subjectMesage = '('.$startMessage.')';
        }else{
            $subjectMesage = '('.$startMessage.' - '.$endMessage.')';
        }
//        $listId = 1777;
        $listId = 4000;
        $user = 'Intelling-OmniChannel';
        $pass = '2j4VHhYYHqkTnBjJ';
        $token = '0HeIOWyak5UIdeXKmD4twR7kty030UWXQ4fiU6e6kr7cuPgTm2';
        $dataCheck = [];
        /* Start EZ */
        $campaign_id = 'SWITCH-EXPERTS-OPTIN';
        $Count = 0;
        $dataEZ = DB::connection('O2Combine')
                ->table('O2Sales')
                ->where('createddate', '>=', $start . ' 00:00:00')
                ->where('createddate', '<=', $end . ' 23:59:59')
                ->where('opt_in', 'yes')
                ->get();

        $dataCheck['PREM_OPTIN']['total'] = 0;
        $dataCheck['PREM_OPTIN']['success'] = 0;
        $dataCheck['PREM_OPTIN']['duplicate'] = 0;
        if(!empty($dataEZ) && count($dataEZ)){
            $postData = [];
            foreach ($dataEZ as $key => $value) {
                $Count++;
                $dataCheck['PREM_OPTIN']['total']++;
            $uniCode = "PREM_OPTIN-" . $value->saleid;
            $dataExist = SwitchExpertOPTins::where('UniqueCode', $uniCode)->count();
            
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
            if ($dataExist == 0) {
                
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
                if ($Optins->save()) {
                    
                }
                
            }else{
                $dataCheck['PREM_OPTIN']['duplicate']++;
            }
        }
        
     
        }
        
        /* Start SYN */
        $campaign_id = 'SWITCH-EXPERTS-OPTIN';
        $dataSYN = DB::connection('O2Inbound')
                ->table('inboundSales')
                ->where('createddate', '>=', $start . ' 00:00:00')
                ->where('createddate', '<=', $end . ' 23:59:59')
                ->where('opt_in', 'yes')
                ->where('report_query','Synergy')
                ->get();
        $dataCheck['Synergy']['total'] = 0;
        $dataCheck['Synergy']['success'] = 0;
        $dataCheck['Synergy']['duplicate'] = 0;
        if(!empty($dataSYN) && count($dataSYN)){
            foreach ($dataSYN as $key => $value) {
                $Count++;
                $dataExistDialer = DB::connection('OmniDialer')
                                            ->table('list')
                                            ->where('list_id',$listId)
                                            ->where('phone_number',$value->phone)
                                            ->count();
            if($dataExistDialer > 0){
                $duplicate = 'yes';
            }else{
                $duplicate = 'no';
            }
            
                $dataCheck['Synergy']['total']++;
            $uniCode = "SYN_OPTIN-" . $value->saleid;
            $dataExist = SwitchExpertOPTins::where('UniqueCode', $uniCode)->count();

            if ($dataExist == 0) {
                $Optins = new SwitchExpertOPTins();
                $Optins->title = '';
                $Optins->first_name = $value->customername;
                $Optins->last_name = '';
                $Optins->main_phone = $value->phone;
                $Optins->address1 = $value->address;
                $Optins->address2 = '';
                $Optins->address3 = '';
                $Optins->city = '';
                $Optins->postcode = '';
                $Optins->date_of_birth = '';
                $Optins->email = (!empty(@$value->email)) ? $value->email : 'Test@Test.co.uk';
                $Optins->custom_fields_currentprovider = '';
                $Optins->source = '';
                $Optins->SSID = 'SYN_OPTIN';
                $Optins->source_code = '';
                $Optins->data_list = '';
                $Optins->token = '';
                $Optins->custom_fields_AdentID = $value->agentid;
                $Optins->campaign_name = 'Synergy';
                $Optins->custom_fields_optindate = $value->createddate;
                $Optins->UniqueCode = $uniCode;
                $Optins->duplicate_status = $duplicate;

                if ($Optins->save()) {
                    if($duplicate == 'no'){
                        $postData[$Count]['import_id'] = $Optins->saleid;
                        $postData[$Count]['data_list'] = $listId;
                        $postData[$Count]['main_phone'] = @$value->phone;
                        $postData[$Count]['title'] = get_empty(@$value->title,'');
                        $postData[$Count]['first_name'] = get_empty(@$value->customername,'');
                        $postData[$Count]['last_name'] = get_empty(@$value->last_name,'');
                        $postData[$Count]['postcode'] = get_empty(@$value->postal_code,'');
                        $postData[$Count]['address1'] = get_empty(@$value->address,'');
                        $postData[$Count]['address2'] = get_empty(@$value->address,'');
                        $postData[$Count]['address3'] = get_empty(@$value->address,'');
                        $postData[$Count]['city'] = get_empty(@$value->city,'');
                        $postData[$Count]['email'] = get_empty(@$value->email,'test@gmail.com');
                        $postData[$Count]['source_code'] = 'SYNOptin-'.date('Ymd').'-'.date('His');
                        $postData[$Count]['source'] = 'SYN_OPTIN';
                        $postData[$Count]['custom_fields'] = ['optindate'=>$value->createddate,'Agent ID' => $value->agentid,'Datasource' => 'SYN_OPTIN'];
                    
                        $dataCheck['Synergy']['success']++;
                    }else{
                    $dataCheck['Synergy']['duplicate']++;
                }
                }
                
            }else{
                $dataCheck['Synergy']['duplicate']++;
            }
        }
        }
        
        /* Start RD */
        $campaign_id = 'SWITCH-EXPERTS-OPTIN';
        
        $dataRD = DB::connection('O2Inbound')
                ->table('inboundSales')
                ->where('createddate', '>=', $start . ' 00:00:00')
                ->where('createddate', '<=', $end . ' 23:59:59')
                ->where('opt_in', 'yes')
                ->orderBy('saleid', 'asc')
                ->whereNull('report_query')
                ->get();
        $dataCheck['RightDeal']['total'] = 0;
        $dataCheck['RightDeal']['success'] = 0;
        $dataCheck['RightDeal']['duplicate'] = 0;
        if(!empty($dataRD) && count($dataRD)){
            foreach ($dataRD as $key => $value) {
                $Count++;
                $dataExistDialer = DB::connection('OmniDialer')
                                            ->table('list')
                                            ->where('list_id',$listId)
                                            ->where('phone_number',$value->phone)
                                            ->count();
            if($dataExistDialer > 0){
                $duplicate = 'yes';
            }else{
                $duplicate = 'no';
            }
                $dataCheck['RightDeal']['total']++;
            $uniCode = "RD_OPTIN-" . $value->saleid;
            $dataExist = SwitchExpertOPTins::where('UniqueCode', $uniCode)->count();

            if ($dataExist == 0) {
                $Optins = new SwitchExpertOPTins();
                $Optins->title = '';
                $Optins->first_name = $value->customername;
                $Optins->last_name = '';
                $Optins->main_phone = $value->phone;
                $Optins->address1 = $value->address;
                $Optins->address2 = '';
                $Optins->address3 = '';
                $Optins->city = '';
                $Optins->postcode = '';
                $Optins->date_of_birth = '';
                $Optins->email = (!empty(@$value->email)) ? $value->email : 'Test@Test.co.uk';
                $Optins->custom_fields_currentprovider = '';
                $Optins->source = '';
                $Optins->SSID = 'RD_OPTIN';
                $Optins->source_code = '';
                $Optins->data_list = '';
                $Optins->token = '';
                $Optins->custom_fields_AdentID = $value->agentid;
                $Optins->campaign_name = 'RightDeal';
                $Optins->custom_fields_optindate = $value->createddate;
                $Optins->UniqueCode = $uniCode;
                $Optins->duplicate_status = $duplicate;

                if ($Optins->save()) {
                   if($duplicate == 'no'){
                       $postData[$Count]['import_id'] = $Optins->saleid;
                        $postData[$Count]['data_list'] = $listId;
                        $postData[$Count]['main_phone'] = @$value->phone;
                        $postData[$Count]['title'] = '';
                        $postData[$Count]['first_name'] = @$value->customername;
                        $postData[$Count]['last_name'] = '';
                        $postData[$Count]['postcode'] = '';
                        $postData[$Count]['address1'] = @$value->address;
                        $postData[$Count]['address2'] = '';
                        $postData[$Count]['address3'] = '';
                        $postData[$Count]['city'] = @$value->city;
                        $postData[$Count]['email'] = get_empty(@$value->email,'test@gmail.com');
                        $postData[$Count]['source_code'] = 'RDOptin-'.date('Ymd').'-'.date('His');
                        $postData[$Count]['source'] = 'RD_OPTIN';
                        $postData[$Count]['custom_fields'] = ['optindate'=>$value->createddate,'Agent ID' => $value->agentid,'Datasource' => 'RD_OPTIN'];
                    
                        $dataCheck['RightDeal']['success']++;
                   }else{
                        $dataCheck['RightDeal']['duplicate']++;
                    }
                }

               
            }else{
                $dataCheck['RightDeal']['duplicate']++;
            }
        }
        }
        
        /*MTA*/
        $campaign_id = 'SWITCH-EXPERTS-OPTIN';
        
        $dataMTA = DB::connection('O2Inbound')
                ->table('inboundSales')
                ->where('createddate', '>=', $start . ' 00:00:00')
                ->where('createddate', '<=', $end . ' 23:59:59')
                ->where('opt_in', 'yes')
                ->orderBy('saleid', 'asc')
                ->where('report_query','MTASales')
                ->get();
        $dataCheck['MTASales']['total'] = 0;
        $dataCheck['MTASales']['success'] = 0;
        $dataCheck['MTASales']['duplicate'] = 0;
        if(!empty($dataMTA) && count($dataMTA)){
            foreach ($dataMTA as $key => $value) {
                $Count++;
                $dataExistDialer = DB::connection('OmniDialer')
                                            ->table('list')
                                            ->where('list_id',$listId)
                                            ->where('phone_number',$value->phone)
                                            ->count();
            if($dataExistDialer > 0){
                $duplicate = 'yes';
            }else{
                $duplicate = 'no';
            }
                 $dataCheck['MTASales']['total']++;
            $uniCode = "MTA_OPT-" . $value->saleid;
            $dataExist = SwitchExpertOPTins::where('UniqueCode', $uniCode)->count();

            if ($dataExist == 0) {
                $Optins = new SwitchExpertOPTins();
                $Optins->title = '';
                $Optins->first_name = $value->customername;
                $Optins->last_name = '';
                $Optins->main_phone = $value->phone;
                $Optins->address1 = $value->address;
                $Optins->address2 = '';
                $Optins->address3 = '';
                $Optins->city = '';
                $Optins->postcode = '';
                $Optins->date_of_birth = '';
                $Optins->email = (!empty(@$value->email)) ? $value->email : 'Test@Test.co.uk';
                $Optins->custom_fields_currentprovider = '';
                $Optins->source = '';
                $Optins->SSID = 'MTA_OPT';
                $Optins->source_code = '';
                $Optins->data_list = '';
                $Optins->token = '';
                $Optins->custom_fields_AdentID = $value->agentid;
                $Optins->campaign_name = 'MTASales';
                $Optins->custom_fields_optindate = $value->createddate;
                $Optins->UniqueCode = $uniCode;
                $Optins->duplicate_status = $duplicate;

                if ($Optins->save()) {
                    if($duplicate == 'no'){
                        $postData[$Count]['import_id'] = $Optins->saleid;
                        $postData[$Count]['data_list'] = $listId;
                        $postData[$Count]['main_phone'] = @$value->phone;
                        $postData[$Count]['title'] = '';
                        $postData[$Count]['first_name'] = @$value->customername;
                        $postData[$Count]['last_name'] = '';
                        $postData[$Count]['postcode'] = '';
                        $postData[$Count]['address1'] = @$value->address;
                        $postData[$Count]['address2'] = @$value->address2;
                        $postData[$Count]['address3'] = @$value->address3;
                        $postData[$Count]['city'] = @$value->city;
                        $postData[$Count]['email'] = get_empty(@$value->email,'test@gmail.com');
                        $postData[$Count]['source_code'] = 'MTAOptin-'.date('Ymd').'-'.date('His');
                        $postData[$Count]['source'] = 'MTA_OPT';
                        $postData[$Count]['custom_fields'] = ['optindate'=>$value->createddate,'Agent ID' => $value->agentid,'Datasource' => 'MTA_OPT'];
                    
                        $dataCheck['MTASales']['success']++;
                    }else{
                        $dataCheck['MTASales']['duplicate']++;
                     }
                }

            }else{
                $dataCheck['MTASales']['duplicate']++;
            }
        }
        }
        
        /*OILGenco*/
        $campaign_id = 'SWITCH-EXPERTS-OPTIN';
        
        $dataOilGenco = DB::connection('O2Inbound')
                ->table('inboundSales')
                ->where('createddate', '>=', $start . ' 00:00:00')
                ->where('createddate', '<=', $end . ' 23:59:59')
                ->where('opt_in', 'yes')
                ->orderBy('saleid', 'asc')
                ->where('report_query','OilGenco')
                ->get();
        $dataCheck['OilGenco']['total'] = 0;
        $dataCheck['OilGenco']['success'] = 0;
        $dataCheck['OilGenco']['duplicate'] = 0;
        if(!empty($dataOilGenco) && count($dataOilGenco)){
            foreach ($dataOilGenco as $key => $value) {
                $Count++;
                $dataExistDialer = DB::connection('OmniDialer')
                                            ->table('list')
                                            ->where('list_id',$listId)
                                            ->where('phone_number',$value->phone)
                                            ->count();
            if($dataExistDialer > 0){
                $duplicate = 'yes';
            }else{
                $duplicate = 'no';
            }
                $dataCheck['OilGenco']['total']++;
            $uniCode = "OILGEN_OPT-" . $value->saleid;
            $dataExist = SwitchExpertOPTins::where('UniqueCode', $uniCode)->count();

            if ($dataExist == 0) {
                $Optins = new SwitchExpertOPTins();
                $Optins->title = '';
                $Optins->first_name = $value->customername;
                $Optins->last_name = '';
                $Optins->main_phone = $value->phone;
                $Optins->address1 = $value->address;
                $Optins->address2 = '';
                $Optins->address3 = '';
                $Optins->city = '';
                $Optins->postcode = '';
                $Optins->date_of_birth = '';
                $Optins->email = (!empty(@$value->email)) ? $value->email : 'Test@Test.co.uk';
                $Optins->custom_fields_currentprovider = '';
                $Optins->source = '';
                $Optins->SSID = 'OILGEN_OPT';
                $Optins->source_code = '';
                $Optins->data_list = '';
                $Optins->token = '';
                $Optins->custom_fields_AdentID = $value->agentid;
                $Optins->campaign_name = 'OilGenco';
                $Optins->custom_fields_optindate = $value->createddate;
                $Optins->UniqueCode = $uniCode;
                $Optins->duplicate_status = $duplicate;

                if ($Optins->save()) {
                    if($duplicate == 'no'){
                        $postData[$Count]['import_id'] = $Optins->saleid;
                        $postData[$Count]['data_list'] = $listId;
                        $postData[$Count]['main_phone'] = @$value->phone;
                        $postData[$Count]['title'] = '';
                        $postData[$Count]['first_name'] = @$value->customername;
                        $postData[$Count]['last_name'] = '';
                        $postData[$Count]['postcode'] = '';
                        $postData[$Count]['address1'] = @$value->address;
                        $postData[$Count]['address2'] = '';
                        $postData[$Count]['address3'] = '';
                        $postData[$Count]['city'] = '';
                        $postData[$Count]['email'] = get_empty(@$value->email,'test@gmail.com');
                        $postData[$Count]['source_code'] = 'OILGENOptin-'.date('Ymd').'-'.date('His');
                        $postData[$Count]['source'] = 'OILGEN_OPT';
                        $postData[$Count]['custom_fields'] = ['optindate'=>$value->createddate,'Agent ID' => $value->agentid,'Datasource' => 'OILGEN_OPT'];
                    
                         $dataCheck['OilGenco']['success']++;
                    }else{
                        $dataCheck['OilGenco']['duplicate']++;
                     }
                }

                
            }else{
                $dataCheck['OilGenco']['duplicate']++;
            }
        }
        }
        /* Start O2Consumer */
        $campaign_id = 'SWITCH-EXPERTS-OPTIN';

        $dataP2Consumer = DB::connection('NewDialer')->select("Select c.lead_id, c.title, c.first_name, c.last_name, c.phone_number, c.email, c.address1, c.address2, c.city, c.postal_code,c.fullname,c.teamname,c.current_supplier, c.happy_with_supplier, c.in_contract,c.bb_switch_call, l.last_local_call_time, u.user,c.campaign
                      from Intelling.customers c
                      JOIN custom_view.list l
                      on l.lead_id=c.lead_id
                      JOIN custom_view.users u
                      on c.fullname=u.full_name
                      where l.last_local_call_time between '".$start." 00:00:00' and '".$end." 23:59:59'
                      and c.bb_switch_call = 'yes'");
//        $dataP2Consumer = DB::connection('NewDialer')->select("Select c.lead_id, c.title, c.first_name, c.last_name, c.phone_number, c.email, c.address1, c.address2, c.city, c.postal_code,c.fullname,c.teamname,c.current_supplier, c.happy_with_supplier, c.in_contract,c.bb_switch_call, l.last_local_call_time, u.user,c.campaign
//                        from Intelling.Customers c
//                        JOIN custom_view.list l
//                        on l.lead_id=c.lead_id
//                        JOIN custom_view.users u
//                        on c.fullname=u.full_name
//                        where l.last_local_call_time between '" . $start . " 00:00:00' and '" . $end . " 23:59:59'
//                        and c.bb_switch_call = 'yes'
//                        group By c.lead_id");
//        Select c.lead_id, c.title, c.first_name, c.last_name, c.phone_number, c.email, c.address1, c.address2, c.city, c.postal_code,c.fullname,c.teamname,c.current_supplier, c.happy_with_supplier, c.in_contract,c.bb_switch_call, l.last_local_call_time, u.user
//                      from Intelling.customers c
//                      JOIN custom_view.list l
//                      on l.lead_id=c.lead_id
//                      JOIN custom_view.users u
//                      on c.fullname=u.full_name
//                      where l.last_local_call_time between '2019-05-29 00:00:00' and '2019-05-29 23:59:59'
//                      and c.bb_switch_call = 'yes'
//                      group By c.lead_id
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
            $dataExist = SwitchExpertOPTins::where('UniqueCode', $uniCode)->count();

            if ($dataExist == 0) {
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

                
            }else{
                $dataCheck['Consumer']['duplicate']++;
            }
        }
        }
        
        /*START MTALGN*/
        $campaign_id = 'SWITCH-EXPERTS-OPTIN';
        
        $dataMTALGN = DB::connection('O2Inbound')
                ->table('inboundSales')
                ->where('createddate', '>=', $start . ' 00:00:00')
                ->where('createddate', '<=', $end . ' 23:59:59')
                ->where('opt_in', 'yes')
                ->orderBy('saleid', 'asc')
                ->where('report_query','MTALGN')
                ->get();
        
        $dataCheck['MTALGN']['total'] = 0;
        $dataCheck['MTALGN']['success'] = 0;
        $dataCheck['MTALGN']['duplicate'] = 0;
        
        if(!empty($dataMTALGN) && count($dataMTALGN)){
            foreach ($dataMTALGN as $key => $value) {
                $Count++;
                $dataExistDialer = DB::connection('OmniDialer')
                                            ->table('list')
                                            ->where('list_id',$listId)
                                            ->where('phone_number',$value->phone)
                                            ->count();
            if($dataExistDialer > 0){
                $duplicate = 'yes';
            }else{
                $duplicate = 'no';
            }
                $dataCheck['MTALGN']['total']++;
            $uniCode = "MTALGN_OPT-" . $value->saleid;
            $dataExist = SwitchExpertOPTins::where('UniqueCode', $uniCode)->count();

            if ($dataExist == 0) {
                $Optins = new SwitchExpertOPTins();
                $Optins->title = '';
                $Optins->first_name = $value->customername;
                $Optins->last_name = '';
                $Optins->main_phone = $value->phone;
                $Optins->address1 = $value->address;
                $Optins->address2 = '';
                $Optins->address3 = '';
                $Optins->city = '';
                $Optins->postcode = '';
                $Optins->date_of_birth = '';
                $Optins->email = (!empty(@$value->email)) ? $value->email : 'Test@Test.co.uk';
                $Optins->custom_fields_currentprovider = '';
                $Optins->source = '';
                $Optins->SSID = 'MTALGN_OPT';
                $Optins->source_code = '';
                $Optins->data_list = '';
                $Optins->token = '';
                $Optins->custom_fields_AdentID = $value->agentid;
                $Optins->campaign_name = 'MTALGN';
                $Optins->custom_fields_optindate = $value->createddate;
                $Optins->UniqueCode = $uniCode;
                $Optins->duplicate_status = $duplicate;

                if ($Optins->save()) {
                    if($duplicate == 'no'){
                        $postData[$Count]['import_id'] = $Optins->saleid;
                        $postData[$Count]['data_list'] = $listId;
                        $postData[$Count]['main_phone'] = @$value->phone;
                        $postData[$Count]['title'] = '';
                        $postData[$Count]['first_name'] = @$value->customername;
                        $postData[$Count]['last_name'] = '';
                        $postData[$Count]['postcode'] = '';
                        $postData[$Count]['address1'] = @$value->address;
                        $postData[$Count]['address2'] = '';
                        $postData[$Count]['address3'] = '';
                        $postData[$Count]['city'] = '';
                        $postData[$Count]['email'] = get_empty(@$value->email,'test@gmail.com');
                        $postData[$Count]['source_code'] = 'MTALGNOptin-'.date('Ymd').'-'.date('His');
                        $postData[$Count]['source'] = 'MTALGN_OPT';
                        $postData[$Count]['custom_fields'] = ['optindate'=>$value->createddate,'Agent ID' => $value->agentid,'Datasource' => 'MTALGN_OPTIN'];
                    
                        $dataCheck['MTALGN']['success']++;
                    }else{
                        $dataCheck['MTALGN']['duplicate']++;
                     }
                }
            }else{
                 $dataCheck['MTALGN']['duplicate']++;
            }
        }
        }
        /*END*/
        
        /*START TOPIC*/
        $campaign_id = 'SWITCH-EXPERTS-OPTIN';
        
        $dataMTALGN = DB::connection('O2Inbound')
                ->table('inboundSales')
                ->where('createddate', '>=', $start . ' 00:00:00')
                ->where('createddate', '<=', $end . ' 23:59:59')
                ->where('opt_in', 'yes')
                ->orderBy('saleid', 'asc')
                ->where('report_query','TOPIC')
                ->get();
            $dataCheck['TOPIC']['total'] = 0;
            $dataCheck['TOPIC']['success'] = 0;
            $dataCheck['TOPIC']['duplicate'] = 0;
        if(!empty($dataMTALGN) && count($dataMTALGN)){
            foreach ($dataMTALGN as $key => $value) {
                $Count++;
                $dataExistDialer = DB::connection('OmniDialer')
                                            ->table('list')
                                            ->where('list_id',$listId)
                                            ->where('phone_number',$value->phone)
                                            ->count();
            if($dataExistDialer > 0){
                $duplicate = 'yes';
            }else{
                $duplicate = 'no';
            }
                $dataCheck['TOPIC']['total']++;
            $uniCode = "TOPIC_OPT-" . $value->saleid;
            $dataExist = SwitchExpertOPTins::where('UniqueCode', $uniCode)->count();

            if ($dataExist == 0) {
                $Optins = new SwitchExpertOPTins();
                $Optins->title = '';
                $Optins->first_name = $value->customername;
                $Optins->last_name = '';
                $Optins->main_phone = $value->phone;
                $Optins->address1 = $value->address;
                $Optins->address2 = '';
                $Optins->address3 = '';
                $Optins->city = '';
                $Optins->postcode = '';
                $Optins->date_of_birth = '';
                $Optins->email = (!empty(@$value->email)) ? $value->email : 'Test@Test.co.uk';
                $Optins->custom_fields_currentprovider = '';
                $Optins->source = '';
                $Optins->SSID = 'TOPIC_OPT';
                $Optins->source_code = '';
                $Optins->data_list = '';
                $Optins->token = '';
                $Optins->custom_fields_AdentID = $value->agentid;
                $Optins->campaign_name = 'TOPIC';
                $Optins->custom_fields_optindate = $value->createddate;
                $Optins->UniqueCode = $uniCode;
                $Optins->duplicate_status = $duplicate;

                if ($Optins->save()) {
                    if($duplicate == 'no'){
                        $postData[$Count]['import_id'] = $Optins->saleid;
                        $postData[$Count]['data_list'] = $listId;
                        $postData[$Count]['main_phone'] = @$value->phone;
                        $postData[$Count]['title'] = '';
                        $postData[$Count]['first_name'] = get_empty(@$value->customername,'');
                        $postData[$Count]['last_name'] = '';
                        $postData[$Count]['postcode'] = '';
                        $postData[$Count]['address1'] = '';
                        $postData[$Count]['address2'] = '';
                        $postData[$Count]['address3'] = '';
                        $postData[$Count]['city'] = '';
                        $postData[$Count]['email'] = get_empty(@$value->email,'test@gmail.com');
                        $postData[$Count]['source_code'] = 'TOPICOptin-'.date('Ymd').'-'.date('His');
                        $postData[$Count]['source'] = 'TOPIC_OPT';
                        $postData[$Count]['custom_fields'] = ['optindate'=>$value->createddate,'Agent ID' => $value->agentid,'Datasource' => 'TOPIC_OPTIN'];
                    
                        $dataCheck['TOPIC']['success']++;
                    }else{
                        $dataCheck['TOPIC']['duplicate']++;
                     }
                }

            }else{
                $dataCheck['TOPIC']['duplicate']++;
            }
        }
        }
        
        
        /*START Ignition*/
         $campaign_id = 'SWITCH-EXPERTS-OPTIN';
        
        $dataSynthesis = DB::connection('O2Inbound')
                ->table('inboundSales')
                ->where('createddate', '>=', $start . ' 00:00:00')
                ->where('createddate', '<=', $end . ' 23:59:59')
                ->where('opt_in', 'yes')
                ->orderBy('saleid', 'asc')
                ->where('report_query','Synthesis')
                ->get();
            $dataCheck['Synthesis']['total'] = 0;
            $dataCheck['Synthesis']['success'] = 0;
            $dataCheck['Synthesis']['duplicate'] = 0;
        if(!empty($dataSynthesis) && count($dataSynthesis)){
            foreach ($dataSynthesis as $key => $value) {
                $Count++;
                $dataExistDialer = DB::connection('OmniDialer')
                                            ->table('list')
                                            ->where('list_id',$listId)
                                            ->where('phone_number',$value->phone)
                                            ->count();
            if($dataExistDialer > 0){
                $duplicate = 'yes';
            }else{
                $duplicate = 'no';
            }
                $dataCheck['Synthesis']['total']++;
            $uniCode = "Synthesis_OPT-" . $value->saleid;
            $dataExist = SwitchExpertOPTins::where('UniqueCode', $uniCode)->count();

            if ($dataExist == 0) {
                $Optins = new SwitchExpertOPTins();
                $Optins->title = '';
                $Optins->first_name = $value->customername;
                $Optins->last_name = '';
                $Optins->main_phone = $value->phone;
                $Optins->address1 = $value->address;
                $Optins->address2 = '';
                $Optins->address3 = '';
                $Optins->city = '';
                $Optins->postcode = '';
                $Optins->date_of_birth = '';
                $Optins->email = (!empty(@$value->email)) ? $value->email : 'Test@Test.co.uk';
                $Optins->custom_fields_currentprovider = '';
                $Optins->source = '';
                $Optins->SSID = 'Synthesis_OPT';
                $Optins->source_code = '';
                $Optins->data_list = '';
                $Optins->token = '';
                $Optins->custom_fields_AdentID = $value->agentid;
                $Optins->campaign_name = 'Synthesis';
                $Optins->custom_fields_optindate = $value->createddate;
                $Optins->UniqueCode = $uniCode;
                $Optins->duplicate_status = $duplicate;

                if ($Optins->save()) {
                    if($duplicate == 'no'){
                        $postData[$Count]['import_id'] = $Optins->saleid;
                        $postData[$Count]['data_list'] = $listId;
                        $postData[$Count]['main_phone'] = @$value->phone;
                        $postData[$Count]['title'] = '';
                        $postData[$Count]['first_name'] = get_empty(@$value->customername,'');
                        $postData[$Count]['last_name'] = '';
                        $postData[$Count]['postcode'] = '';
                        $postData[$Count]['address1'] = '';
                        $postData[$Count]['address2'] = '';
                        $postData[$Count]['address3'] = '';
                        $postData[$Count]['city'] = '';
                        $postData[$Count]['email'] = get_empty(@$value->email,'test@gmail.com');
                        $postData[$Count]['source_code'] = 'SynthesisOptin-'.date('Ymd').'-'.date('His');
                        $postData[$Count]['source'] = 'Synthesis_OPT';
                        $postData[$Count]['custom_fields'] = ['optindate'=>$value->createddate,'Agent ID' => $value->agentid,'Datasource' => 'Synthesis_OPTIN'];
                    
                        $dataCheck['Synthesis']['success']++;
                    }else{
                        $dataCheck['Synthesis']['duplicate']++;
                     }
                }

            }else{
                $dataCheck['Synthesis']['duplicate']++;
            }
        }
        }
        
        /*END*/
        
        /*START Ignition*/
         $campaign_id = 'SWITCH-EXPERTS-OPTIN';
        
        $dataignition = DB::connection('O2Inbound')
                ->table('inboundSales')
                ->where('createddate', '>=', $start . ' 00:00:00')
                ->where('createddate', '<=', $end . ' 23:59:59')
                ->where('opt_in', 'yes')
                ->orderBy('saleid', 'asc')
                ->where('report_query','ignition')
                ->get();
            $dataCheck['Ignition']['total'] = 0;
            $dataCheck['Ignition']['success'] = 0;
            $dataCheck['Ignition']['duplicate'] = 0;
        if(!empty($dataignition) && count($dataignition)){
            foreach ($dataignition as $key => $value) {
                $Count++;
                $dataExistDialer = DB::connection('OmniDialer')
                                            ->table('list')
                                            ->where('list_id',$listId)
                                            ->where('phone_number',$value->phone)
                                            ->count();
            if($dataExistDialer > 0){
                $duplicate = 'yes';
            }else{
                $duplicate = 'no';
            }
                $dataCheck['Ignition']['total']++;
            $uniCode = "IGNITION_OPT-" . $value->saleid;
            $dataExist = SwitchExpertOPTins::where('UniqueCode', $uniCode)->count();

            if ($dataExist == 0) {
                $Optins = new SwitchExpertOPTins();
                $Optins->title = '';
                $Optins->first_name = $value->customername;
                $Optins->last_name = '';
                $Optins->main_phone = $value->phone;
                $Optins->address1 = $value->address;
                $Optins->address2 = '';
                $Optins->address3 = '';
                $Optins->city = '';
                $Optins->postcode = '';
                $Optins->date_of_birth = '';
                $Optins->email = (!empty(@$value->email)) ? $value->email : 'Test@Test.co.uk';
                $Optins->custom_fields_currentprovider = '';
                $Optins->source = '';
                $Optins->SSID = 'IGNITION_OPT';
                $Optins->source_code = '';
                $Optins->data_list = '';
                $Optins->token = '';
                $Optins->custom_fields_AdentID = $value->agentid;
                $Optins->campaign_name = 'IGNITION';
                $Optins->custom_fields_optindate = $value->createddate;
                $Optins->UniqueCode = $uniCode;
                $Optins->duplicate_status = $duplicate;

                if ($Optins->save()) {
                    if($duplicate == 'no'){
                        $postData[$Count]['import_id'] = $Optins->saleid;
                        $postData[$Count]['data_list'] = $listId;
                        $postData[$Count]['main_phone'] = @$value->phone;
                        $postData[$Count]['title'] = '';
                        $postData[$Count]['first_name'] = get_empty(@$value->customername,'');
                        $postData[$Count]['last_name'] = '';
                        $postData[$Count]['postcode'] = '';
                        $postData[$Count]['address1'] = '';
                        $postData[$Count]['address2'] = '';
                        $postData[$Count]['address3'] = '';
                        $postData[$Count]['city'] = '';
                        $postData[$Count]['email'] = get_empty(@$value->email,'test@gmail.com');
                        $postData[$Count]['source_code'] = 'IGNITIONOptin-'.date('Ymd').'-'.date('His');
                        $postData[$Count]['source'] = 'IGNITION_OPT';
                        $postData[$Count]['custom_fields'] = ['optindate'=>$value->createddate,'Agent ID' => $value->agentid,'Datasource' => 'IGNITION_OPTIN'];
                    
                        $dataCheck['Ignition']['success']++;
                    }else{
                        $dataCheck['Ignition']['duplicate']++;
                     }
                }

            }else{
                $dataCheck['Ignition']['duplicate']++;
            }
        }
        }
        
        /*END*/
        
        /*START CogentHubin*/
         $campaign_id = 'SWITCH-EXPERTS-OPTIN';
        
        $dataCogentHubin = DB::connection('O2Inbound')
                ->table('inboundSales')
                ->where('createddate', '>=', $start . ' 00:00:00')
                ->where('createddate', '<=', $end . ' 23:59:59')
                ->where('opt_in', 'yes')
                ->orderBy('saleid', 'asc')
                ->where('report_query','CogentHubin')
                ->get();
            $dataCheck['CogentHubin']['total'] = 0;
            $dataCheck['CogentHubin']['success'] = 0;
            $dataCheck['CogentHubin']['duplicate'] = 0;
        if(!empty($dataCogentHubin) && count($dataCogentHubin)){
            foreach ($dataCogentHubin as $key => $value) {
                $Count++;
                $dataExistDialer = DB::connection('OmniDialer')
                                            ->table('list')
                                            ->where('list_id',$listId)
                                            ->where('phone_number',$value->phone)
                                            ->count();
            if($dataExistDialer > 0){
                $duplicate = 'yes';
            }else{
                $duplicate = 'no';
            }
                $dataCheck['CogentHubin']['total']++;
            $uniCode = "CogentHubin_OPT-" . $value->saleid;
            $dataExist = SwitchExpertOPTins::where('UniqueCode', $uniCode)->count();

            if ($dataExist == 0) {
                $Optins = new SwitchExpertOPTins();
                $Optins->title = '';
                $Optins->first_name = $value->customername;
                $Optins->last_name = '';
                $Optins->main_phone = $value->phone;
                $Optins->address1 = $value->address;
                $Optins->address2 = '';
                $Optins->address3 = '';
                $Optins->city = '';
                $Optins->postcode = '';
                $Optins->date_of_birth = '';
                $Optins->email = (!empty(@$value->email)) ? $value->email : 'Test@Test.co.uk';
                $Optins->custom_fields_currentprovider = '';
                $Optins->source = '';
                $Optins->SSID = 'CogentHubin_OPT';
                $Optins->source_code = '';
                $Optins->data_list = '';
                $Optins->token = '';
                $Optins->custom_fields_AdentID = $value->agentid;
                $Optins->campaign_name = 'CogentHubin';
                $Optins->custom_fields_optindate = $value->createddate;
                $Optins->UniqueCode = $uniCode;
                $Optins->duplicate_status = $duplicate;

                if ($Optins->save()) {
                    if($duplicate == 'no'){
                        $postData[$Count]['import_id'] = $Optins->saleid;
                        $postData[$Count]['data_list'] = $listId;
                        $postData[$Count]['main_phone'] = @$value->phone;
                        $postData[$Count]['title'] = '';
                        $postData[$Count]['first_name'] = get_empty(@$value->customername,'');
                        $postData[$Count]['last_name'] = '';
                        $postData[$Count]['postcode'] = '';
                        $postData[$Count]['address1'] = '';
                        $postData[$Count]['address2'] = '';
                        $postData[$Count]['address3'] = '';
                        $postData[$Count]['city'] = '';
                        $postData[$Count]['email'] = get_empty(@$value->email,'test@gmail.com');
                        $postData[$Count]['source_code'] = 'CogentHubinOptin-'.date('Ymd').'-'.date('His');
                        $postData[$Count]['source'] = 'CogentHubin_OPT';
                        $postData[$Count]['custom_fields'] = ['optindate'=>$value->createddate,'Agent ID' => $value->agentid,'Datasource' => 'CogentHubin_OPTIN'];
                    
                        $dataCheck['CogentHubin']['success']++;
                    }else{
                        $dataCheck['CogentHubin']['duplicate']++;
                     }
                }

            }else{
                $dataCheck['CogentHubin']['duplicate']++;
            }
        }
        }
        
        /*END*/
        
        /*START OutworxIn*/
         $campaign_id = 'SWITCH-EXPERTS-OPTIN';
        
        $dataOutworxIn = DB::connection('O2Inbound')
                ->table('inboundSales')
                ->where('createddate', '>=', $start . ' 00:00:00')
                ->where('createddate', '<=', $end . ' 23:59:59')
                ->where('opt_in', 'yes')
                ->orderBy('saleid', 'asc')
                ->where('report_query','OutworxIn')
                ->get();
            $dataCheck['OutworxIn']['total'] = 0;
            $dataCheck['OutworxIn']['success'] = 0;
            $dataCheck['OutworxIn']['duplicate'] = 0;
        if(!empty($dataOutworxIn) && count($dataOutworxIn)){
            foreach ($dataOutworxIn as $key => $value) {
                $Count++;
                $dataExistDialer = DB::connection('OmniDialer')
                                            ->table('list')
                                            ->where('list_id',$listId)
                                            ->where('phone_number',$value->phone)
                                            ->count();
            if($dataExistDialer > 0){
                $duplicate = 'yes';
            }else{
                $duplicate = 'no';
            }
                $dataCheck['OutworxIn']['total']++;
            $uniCode = "OutworxIn_OPT-" . $value->saleid;
            $dataExist = SwitchExpertOPTins::where('UniqueCode', $uniCode)->count();

            if ($dataExist == 0) {
                $Optins = new SwitchExpertOPTins();
                $Optins->title = '';
                $Optins->first_name = $value->customername;
                $Optins->last_name = '';
                $Optins->main_phone = $value->phone;
                $Optins->address1 = $value->address;
                $Optins->address2 = '';
                $Optins->address3 = '';
                $Optins->city = '';
                $Optins->postcode = '';
                $Optins->date_of_birth = '';
                $Optins->email = (!empty(@$value->email)) ? $value->email : 'Test@Test.co.uk';
                $Optins->custom_fields_currentprovider = '';
                $Optins->source = '';
                $Optins->SSID = 'OutworxIn_OPT';
                $Optins->source_code = '';
                $Optins->data_list = '';
                $Optins->token = '';
                $Optins->custom_fields_AdentID = $value->agentid;
                $Optins->campaign_name = 'OutworxIn';
                $Optins->custom_fields_optindate = $value->createddate;
                $Optins->UniqueCode = $uniCode;
                $Optins->duplicate_status = $duplicate;

                if ($Optins->save()) {
                    if($duplicate == 'no'){
                        $postData[$Count]['import_id'] = $Optins->saleid;
                        $postData[$Count]['data_list'] = $listId;
                        $postData[$Count]['main_phone'] = @$value->phone;
                        $postData[$Count]['title'] = '';
                        $postData[$Count]['first_name'] = get_empty(@$value->customername,'');
                        $postData[$Count]['last_name'] = '';
                        $postData[$Count]['postcode'] = '';
                        $postData[$Count]['address1'] = '';
                        $postData[$Count]['address2'] = '';
                        $postData[$Count]['address3'] = '';
                        $postData[$Count]['city'] = '';
                        $postData[$Count]['email'] = get_empty(@$value->email,'test@gmail.com');
                        $postData[$Count]['source_code'] = 'OutworxIn-'.date('Ymd').'-'.date('His');
                        $postData[$Count]['source'] = 'OutworxIn_OPT';
                        $postData[$Count]['custom_fields'] = ['optindate'=>$value->createddate,'Agent ID' => $value->agentid,'Datasource' => 'OutworxIn_OPTIN'];
                    
                        $dataCheck['OutworxIn']['success']++;
                    }else{
                        $dataCheck['OutworxIn']['duplicate']++;
                     }
                }

            }else{
                $dataCheck['OutworxIn']['duplicate']++;
            }
        }
        }
        
        /*END*/
        
        /*START TouchstonIn*/
         $campaign_id = 'SWITCH-EXPERTS-OPTIN';
        
        $dataTouchstonIn = DB::connection('O2Inbound')
                ->table('inboundSales')
                ->where('createddate', '>=', $start . ' 00:00:00')
                ->where('createddate', '<=', $end . ' 23:59:59')
                ->where('opt_in', 'yes')
                ->orderBy('saleid', 'asc')
                ->where('report_query','TouchstonIn')
                ->get();
            $dataCheck['TouchstonIn']['total'] = 0;
            $dataCheck['TouchstonIn']['success'] = 0;
            $dataCheck['TouchstonIn']['duplicate'] = 0;
        if(!empty($dataTouchstonIn) && count($dataTouchstonIn)){
            foreach ($dataTouchstonIn as $key => $value) {
                $Count++;
                $dataExistDialer = DB::connection('OmniDialer')
                                            ->table('list')
                                            ->where('list_id',$listId)
                                            ->where('phone_number',$value->phone)
                                            ->count();
            if($dataExistDialer > 0){
                $duplicate = 'yes';
            }else{
                $duplicate = 'no';
            }
                $dataCheck['TouchstonIn']['total']++;
            $uniCode = "TouchstonIn_OPT-" . $value->saleid;
            $dataExist = SwitchExpertOPTins::where('UniqueCode', $uniCode)->count();

            if ($dataExist == 0) {
                $Optins = new SwitchExpertOPTins();
                $Optins->title = '';
                $Optins->first_name = $value->customername;
                $Optins->last_name = '';
                $Optins->main_phone = $value->phone;
                $Optins->address1 = $value->address;
                $Optins->address2 = '';
                $Optins->address3 = '';
                $Optins->city = '';
                $Optins->postcode = '';
                $Optins->date_of_birth = '';
                $Optins->email = (!empty(@$value->email)) ? $value->email : 'Test@Test.co.uk';
                $Optins->custom_fields_currentprovider = '';
                $Optins->source = '';
                $Optins->SSID = 'TouchstonIn_OPT';
                $Optins->source_code = '';
                $Optins->data_list = '';
                $Optins->token = '';
                $Optins->custom_fields_AdentID = $value->agentid;
                $Optins->campaign_name = 'TouchstonIn';
                $Optins->custom_fields_optindate = $value->createddate;
                $Optins->UniqueCode = $uniCode;
                $Optins->duplicate_status = $duplicate;

                if ($Optins->save()) {
                    if($duplicate == 'no'){
                        $postData[$Count]['import_id'] = $Optins->saleid;
                        $postData[$Count]['data_list'] = $listId;
                        $postData[$Count]['main_phone'] = @$value->phone;
                        $postData[$Count]['title'] = '';
                        $postData[$Count]['first_name'] = get_empty(@$value->customername,'');
                        $postData[$Count]['last_name'] = '';
                        $postData[$Count]['postcode'] = '';
                        $postData[$Count]['address1'] = '';
                        $postData[$Count]['address2'] = '';
                        $postData[$Count]['address3'] = '';
                        $postData[$Count]['city'] = '';
                        $postData[$Count]['email'] = get_empty(@$value->email,'test@gmail.com');
                        $postData[$Count]['source_code'] = 'TouchstonIn-'.date('Ymd').'-'.date('His');
                        $postData[$Count]['source'] = 'TouchstonIn_OPT';
                        $postData[$Count]['custom_fields'] = ['optindate'=>$value->createddate,'Agent ID' => $value->agentid,'Datasource' => 'TouchstonIn_OPTIN'];
                    
                        $dataCheck['TouchstonIn']['success']++;
                    }else{
                        $dataCheck['TouchstonIn']['duplicate']++;
                     }
                }

            }else{
                $dataCheck['TouchstonIn']['duplicate']++;
            }
        }
        }
        
        /*END*/
        
        /*Talking CUST*/
        $dataTalkingCust = DB::connection('O2Inbound')
                ->table('inboundSales')
                ->where('createddate', '>=', $start . ' 00:00:00')
                ->where('createddate', '<=', $end . ' 23:59:59')
                ->where('opt_in', 'yes')
//                ->orderBy('saleid', 'asc')
                ->where('report_query','TalkingCust')
                ->get();
            $dataCheck['TalkingCust']['total'] = 0;
            $dataCheck['TalkingCust']['success'] = 0;
            $dataCheck['TalkingCust']['duplicate'] = 0;
        if(!empty($dataTalkingCust) && count($dataTalkingCust)){
            foreach ($dataTalkingCust as $key => $value) {
                $Count++;
                $dataExistDialer = DB::connection('OmniDialer')
                                            ->table('list')
                                            ->where('list_id',$listId)
                                            ->where('phone_number',$value->phone)
                                            ->count();
            if($dataExistDialer > 0){
                $duplicate = 'yes';
            }else{
                $duplicate = 'no';
            }
                $dataCheck['TalkingCust']['total']++;
            $uniCode = "TalkingCust_OPT-" . $value->saleid;
            $dataExist = SwitchExpertOPTins::where('UniqueCode', $uniCode)->count();

            if ($dataExist == 0) {
                $Optins = new SwitchExpertOPTins();
                $Optins->title = '';
                $Optins->first_name = $value->customername;
                $Optins->last_name = '';
                $Optins->main_phone = $value->phone;
                $Optins->address1 = $value->address;
                $Optins->address2 = '';
                $Optins->address3 = '';
                $Optins->city = '';
                $Optins->postcode = '';
                $Optins->date_of_birth = '';
                $Optins->email = (!empty(@$value->email)) ? $value->email : 'Test@Test.co.uk';
                $Optins->custom_fields_currentprovider = '';
                $Optins->source = '';
                $Optins->SSID = 'TalkingCust_OPT';
                $Optins->source_code = '';
                $Optins->data_list = '';
                $Optins->token = '';
                $Optins->custom_fields_AdentID = $value->agentid;
                $Optins->campaign_name = 'TalkingCust';
                $Optins->custom_fields_optindate = $value->createddate;
                $Optins->UniqueCode = $uniCode;
                $Optins->duplicate_status = $duplicate;

                if ($Optins->save()) {
                    if($duplicate == 'no'){
                        $postData[$Count]['import_id'] = $Optins->saleid;
                        $postData[$Count]['data_list'] = $listId;
                        $postData[$Count]['main_phone'] = @$value->phone;
                        $postData[$Count]['title'] = '';
                        $postData[$Count]['first_name'] = get_empty(@$value->customername,'');
                        $postData[$Count]['last_name'] = '';
                        $postData[$Count]['postcode'] = '';
                        $postData[$Count]['address1'] = '';
                        $postData[$Count]['address2'] = '';
                        $postData[$Count]['address3'] = '';
                        $postData[$Count]['city'] = '';
                        $postData[$Count]['email'] = get_empty(@$value->email,'test@gmail.com');
                        $postData[$Count]['source_code'] = 'TalkingCust-'.date('Ymd').'-'.date('His');
                        $postData[$Count]['source'] = 'TalkingCust_OPT';
                        $postData[$Count]['custom_fields'] = ['optindate'=>$value->createddate,'Agent ID' => $value->agentid,'Datasource' => 'TalkingCust_OPTIN'];
                    
                        $dataCheck['TalkingCust']['success']++;
                    }else{
                        $dataCheck['TalkingCust']['duplicate']++;
                     }
                }

            }else{
                $dataCheck['TalkingCust']['duplicate']++;
            }
        }
        }
        
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
