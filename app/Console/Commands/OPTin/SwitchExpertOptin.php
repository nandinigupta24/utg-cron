<?php

namespace App\Console\Commands\OPTin;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Model\Intelling\SEMobileOPTins;
use App\Model\Intelling\AgentTableCombined;
use Mail;
use App\Model\IntellingScriptDB\SDSales;

class SwitchExpertOPTin extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:SwitchExpertOPTin';

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
        
            $start = $end = date('Y-m-d');
        
            
        $Agents = AgentTableCombined::where('dialer_name', 'omni')->pluck('full_name', 'user');
        /* Start EZ */
        $CountTC2 = SEMobileOPTins::where('source_id', 'SEMobile_TC2_OPT')->where('duplicate', 'no')->count();
        $CountTC1 = SEMobileOPTins::where('source_id', 'SEMobile_TC1_OPT')->where('duplicate', 'no')->count();
        $CountRightDeal = SEMobileOPTins::where('source_id', 'SEMobile_RD_OPT')->where('duplicate', 'no')->count();
        $CountOilGen = SEMobileOPTins::where('source_id', 'SEMobile_OILG_OPT')->where('duplicate', 'no')->count();
        $CountSE = SEMobileOPTins::where('source_id', 'SEMobile_SE_OPT')->where('duplicate', 'no')->count();
        $dataCheck = [];

        $list_id = 3011;
        $postData = [];
        $talkingPeople = SDSales::where('createddate', '>=', $start . ' 00:00:00')
                ->where('createddate', '<=', $end . ' 23:59:59')
                ->whereIn('script_query', ['TalkingPeople', 'RightDeal'])
                ->where('neatley_opt', 'yes')
                ->select('saleid', 'lead_id', 'security_phrase', 'title', 'first_name', 'last_name', 'phone_number', 'address1', 'address2', 'address3', 'city', 'postal_code', 'date_of_birth', 'email', 'agentid', 'createddate', 'lastupdated')
                ->get();
        
        $dataCheck['TC_1']['total'] = 0;
        $dataCheck['TC_1']['success'] = 0;
        $dataCheck['TC_1']['duplicate'] = 0;
        
        $dataCheck['TC_2']['total'] = 0;
        $dataCheck['TC_2']['success'] = 0;
        $dataCheck['TC_2']['duplicate'] = 0;
        
        $dataCheck['RightDeal']['total'] = 0;
        $dataCheck['RightDeal']['success'] = 0;
        $dataCheck['RightDeal']['duplicate'] = 0;
        
        if (!empty($talkingPeople) && count($talkingPeople)) {
            foreach ($talkingPeople as $key => $value) {
                
                if($value->security_phrase == 'TC_1') {
                     $dataCheck['TC_1']['total']++;
                     $dataExist = SEMobileOPTins::where('phone', @$value->phone_number)->where('datasource','SEMobile_TC1_OPT')->count();
                }elseif($value->security_phrase == 'TC_2'){
                     $dataCheck['TC_2']['total']++;
                     $dataExist = SEMobileOPTins::where('phone', @$value->phone_number)->where('datasource','SEMobile_TC2_OPT')->count();
                }else{
                     $dataCheck['RightDeal']['total']++;
                     $dataExist = SEMobileOPTins::where('phone', @$value->phone_number)->where('datasource','SEMobile_RD_OPT')->count();
                }
                
                
                if ($dataExist == 0) {
                    $Optins = new SEMobileOPTins();
                    $Optins->title = @$value->title;
                    $Optins->first_name = @$value->first_name;
                    $Optins->last_name = @$value->last_name;
                    $Optins->phone = @$value->phone_number;
                    $Optins->add1 = @$value->address1;
                    $Optins->add2 = @$value->address2;
                    $Optins->add3 = @$value->address3;
                    $Optins->city = @$value->city;
                    $Optins->postal_code = @$value->postal_code;
                    $Optins->date_of_birth = @$value->date_of_birth;
                    $Optins->email = (!empty(@$value->email)) ? $value->email : '';
                    $Optins->campaign_name = '';
                    $Optins->lead_id = $value->lead_id;
                    $Optins->optins_date = date('Y-m-d', strtotime($value->createddate));
                    $Optins->agent_id = @$value->agentid;
                    $Optins->duplicate = 'no';
                    if ($value->security_phrase == 'TC_1') {
                        $CountTC1++;
                        $Optins->source_id = 'SEMobile_TC1_OPT';
                        $Optins->datasource = 'SEMobile_TC1_OPT';
                        $Optins->vendor_lead_code = 'SEMobile_TC1_OPT_' . str_pad($CountTC1, 8, '0', STR_PAD_LEFT);
                         $dataCheck['TC_1']['success']++;
                    } elseif ($value->security_phrase == 'TC_2') {
                        $CountTC2++;
                        $Optins->source_id = 'SEMobile_TC2_OPT';
                        $Optins->datasource = 'SEMobile_TC2_OPT';
                        $Optins->vendor_lead_code = 'SEMobile_TC2_OPT_' . str_pad($CountTC2, 8, '0', STR_PAD_LEFT);
                         $dataCheck['TC_2']['success']++;
                    } else {
                        $CountRightDeal++;
                        $Optins->source_id = 'SEMobile_RD_OPT';
                        $Optins->datasource = 'SEMobile_RD_OPT';
                        $Optins->vendor_lead_code = 'SEMobile_RD_OPT_' . str_pad($CountTC2, 8, '0', STR_PAD_LEFT);
                         $dataCheck['RightDeal']['success']++;
                    }
                    if ($Optins->save()) {
                        $updatedId = $Optins->id;
                    }
                    $postData[$updatedId]['import_id'] = $updatedId;
                    $postData[$updatedId]['data_list'] = $list_id;
                    $postData[$updatedId]['main_phone'] = @$value->phone_number;
                    $postData[$updatedId]['title'] = get_empty(@$value->title, '');
                    $postData[$updatedId]['first_name'] = get_empty(@$value->first_name, '');
                    $postData[$updatedId]['last_name'] = get_empty(@$value->last_name, '');
                    $postData[$updatedId]['postcode'] = get_empty(@$value->postal_code, '');
                    $postData[$updatedId]['address1'] = get_empty(@$value->add1, '');
                    $postData[$updatedId]['address2'] = get_empty(@$value->add2, '');
                    $postData[$updatedId]['address3'] = get_empty(@$value->add2, '');
                    $postData[$updatedId]['city'] = get_empty(@$value->city, '');
                    $postData[$updatedId]['email'] = get_empty(@$value->email, 'test@gmail.com');
                    
                    
//                    $dataCheck[$CampaignID]['success'] ++;
//                    $dataArray = [];
//                    $dataArray['list_id'] = $list_id;
//                    $dataArray['phone_number'] = @$value->phone_number;
//                    $dataArray['title'] = @$value->title;
//                    $dataArray['first_name'] = @$value->first_name;
//                    $dataArray['last_name'] = @$value->last_name;
//                    $dataArray['postal_code'] = @$value->postal_code;
//                    $dataArray['email'] = @$value->email;
//                    $dataArray['address1'] = @$value->add1;
//                    $dataArray['address2'] = @$value->add2;
//                    $dataArray['address3'] = @$value->add3;
//                    $dataArray['city'] = @$value->city;
                    if ($value->security_phrase == 'TC_1') {
//                        $dataArray['source_id'] = 'SEMobile_TC1_OPT';
//                        $dataArray['Datasource'] = 'SEMobile_TC1_OPT';
                        $OPTINDate = date('Y-m-d', strtotime($value->createddate));
                        $vendor_lead_code = 'SEMobile_TC1_OPT_' . str_pad($CountTC1, 8, '0', STR_PAD_LEFT);
                        $AgentID =  (!empty($value->agentid)) ? @$value->agentid : '';
                        $postData[$updatedId]['source_code'] = $vendor_lead_code;
                        $postData[$updatedId]['source'] = 'SEMobile_TC1_OPT';
                        $postData[$updatedId]['custom_fields'] = ['optindate' =>$OPTINDate, 'AgentID' =>$AgentID, 'Datasource' => 'SEMobile_TC1_OPT'];
                    } elseif ($value->security_phrase == 'TC_2') {
                        $OPTINDate = date('Y-m-d', strtotime($value->createddate));
                        $vendor_lead_code = 'SEMobile_TC2_OPT_' . str_pad($CountTC2, 8, '0', STR_PAD_LEFT);
                        $AgentID =  (!empty($value->agentid)) ? @$value->agentid : '';
                        $postData[$updatedId]['source_code'] = $vendor_lead_code;
                        $postData[$updatedId]['source'] = 'SEMobile_TC2_OPT';
                        $postData[$updatedId]['custom_fields'] = ['optindate' =>$OPTINDate, 'AgentID' =>$AgentID, 'Datasource' => 'SEMobile_TC2_OPT'];
                        
                    } else {
                        $OPTINDate = date('Y-m-d', strtotime($value->createddate));
                        $vendor_lead_code = 'SEMobile_RD_OPT_' . str_pad($CountTC2, 8, '0', STR_PAD_LEFT);
                        $AgentID =  (!empty($value->agentid)) ? @$value->agentid : '';
                        $postData[$updatedId]['source_code'] = $vendor_lead_code;
                        $postData[$updatedId]['source'] = 'SEMobile_RD_OPT';
                        $postData[$updatedId]['custom_fields'] = ['optindate' =>$OPTINDate, 'Agent ID' =>$AgentID, 'Datasource' => 'SEMobile_RD_OPT'];
                    }
                    
                    
                } else {
                    $Optins = new SEMobileOPTins();
                    $Optins->title = @$value->title;
                    $Optins->first_name = @$value->first_name;
                    $Optins->last_name = @$value->last_name;
                    $Optins->phone = @$value->phone_number;
                    $Optins->add1 = @$value->address1;
                    $Optins->add2 = @$value->address2;
                    $Optins->add3 = @$value->address3;
                    $Optins->city = @$value->city;
                    $Optins->postal_code = @$value->postal_code;
                    $Optins->date_of_birth = @$value->date_of_birth;
                    $Optins->email = (!empty(@$value->email)) ? $value->email : '';
                    $Optins->campaign_name = '';
                    $Optins->lead_id = $value->lead_id;
                    $Optins->optins_date = date('Y-m-d', strtotime($value->createddate));
                    $Optins->agent_id = @$value->agentid;
                    $Optins->duplicate = 'yes';
                    if ($value->security_phrase == 'TC_1') {
                        $Optins->source_id = 'SEMobile_TC1_OPT';
                        $Optins->datasource = 'SEMobile_TC1_OPT';
                    } elseif ($value->security_phrase == 'TC_2') {
                        $Optins->source_id = 'SEMobile_TC2_OPT';
                        $Optins->datasource = 'SEMobile_TC2_OPT';
                    } else {
                        $Optins->source_id = 'SEMobile_RD_OPT';
                        $Optins->datasource = 'SEMobile_RD_OPT';
                    }
                    if ($Optins->save()) {
                        if($value->security_phrase == 'TC_1') {
                            $dataCheck['TC_1']['duplicate']++;
                        }elseif($value->security_phrase == 'TC_2'){
                             $dataCheck['TC_2']['duplicate']++;
                        }else{
                             $dataCheck['RightDeal']['duplicate']++;
                        }
                    }
                }
            }
        }

//        /* OIL Genco */
//        $OilGenco = SDSales::where('createddate', '>=', $start . ' 00:00:00')
//                ->where('createddate', '<=', $end . ' 23:59:59')
//                ->where('neatley_opt', 'yes')
//                ->where('security_phrase', 'OilGenco')
//                ->select('saleid', 'lead_id', 'title', 'first_name', 'last_name', 'phone_number', 'address1', 'address2', 'address3', 'city', 'postal_code', 'date_of_birth', 'email', 'agentid', 'createddate', 'lastupdated')
//                ->get();
//        
//        $dataCheck['OilGenco']['total'] = 0;
//        $dataCheck['OilGenco']['success'] = 0;
//        $dataCheck['OilGenco']['duplicate'] = 0;
//        
//        if (!empty($OilGenco) && count($OilGenco)) {
//            foreach ($OilGenco as $key => $value) {
//                $dataCheck['OilGenco']['total']++;
//                $dataExist = SEMobileOPTins::where('phone', @$value->phone_number)->where('datasource','SEMobile_OILG_OPT')->count();
//                if ($dataExist == 0) {
//                    $CountOilGen++;
//                    $Optins = new SEMobileOPTins();
//                    $Optins->title = @$value->title;
//                    $Optins->first_name = @$value->first_name;
//                    $Optins->last_name = @$value->last_name;
//                    $Optins->phone = @$value->phone_number;
//                    $Optins->add1 = @$value->address1;
//                    $Optins->add2 = @$value->address2;
//                    $Optins->add3 = @$value->address3;
//                    $Optins->city = @$value->city;
//                    $Optins->postal_code = @$value->postal_code;
//                    $Optins->date_of_birth = @$value->date_of_birth;
//                    $Optins->email = (!empty(@$value->email)) ? $value->email : '';
//                    $Optins->campaign_name = '';
//                    $Optins->lead_id = $value->lead_id;
//                    $Optins->optins_date = date('Y-m-d', strtotime($value->createddate));
//                    $Optins->agent_id = @$value->agentid;
//                    $Optins->duplicate = 'no';
//                    $Optins->source_id = 'SEMobile_OILG_OPT';
//                    $Optins->datasource = 'SEMobile_OILG_OPT';
//                    $Optins->vendor_lead_code = 'SEMobile_OILG_OPT_' . str_pad($CountOilGen, 8, '0', STR_PAD_LEFT);
//
//                    if ($Optins->save()) {
//                        $updatedId = $Optins->id;
//                    }
//
//                    $dataArray = [];
//                    $dataArray['list_id'] = $list_id;
//                    $dataArray['phone_number'] = @$value->phone_number;
//                    $dataArray['title'] = @$value->title;
//                    $dataArray['first_name'] = @$value->first_name;
//                    $dataArray['last_name'] = @$value->last_name;
//                    $dataArray['postal_code'] = @$value->postal_code;
//                    $dataArray['email'] = @$value->email;
//                    $dataArray['address1'] = @$value->add1;
//                    $dataArray['address2'] = @$value->add2;
//                    $dataArray['address3'] = @$value->add3;
//                    $dataArray['city'] = @$value->city;
//                    $dataArray['source_id'] = 'SEMobile_OILG_OPT';
//                    $dataArray['Datasource'] = 'SEMobile_OILG_OPT';
//                    $dataArray['vendor_lead_code'] = 'SEMobile_OILG_OPT_' . str_pad($CountOilGen, 8, '0', STR_PAD_LEFT);
//                    $dataArray['optindate'] = date('Y-m-d', strtotime($value->createddate));
//                    $dataArray['DateofSale'] = $value->createddate;
//                    $dataArray['AgentID'] = (!empty($value->agentid)) ? @$Agents[strtoupper(@$value->agentid)] : '';
//                    $dataArray['custom_fields'] = 'Y';
//                    $queryString = http_build_query($dataArray);
//                    $data = @file_get_contents('http://10.29.104.7/Admin/.functions/non_agent_api.php?source=Test&user=APIUSER&pass=APIUSER&function=add_lead&' . $queryString);
//                    $NeatleyAPIConnexUpdate = SEMobileOPTins::find($updatedId);
//                    $NeatleyAPIConnexUpdate->api_response = $data;
//                    if ($NeatleyAPIConnexUpdate->save()) {
//                        $dataCheck['OilGenco']['success']++;
//                    }
//                } else {
//                    $Optins = new SEMobileOPTins();
//                    $Optins->title = @$value->title;
//                    $Optins->first_name = @$value->first_name;
//                    $Optins->last_name = @$value->last_name;
//                    $Optins->phone = @$value->phone_number;
//                    $Optins->add1 = @$value->address1;
//                    $Optins->add2 = @$value->address2;
//                    $Optins->add3 = @$value->address3;
//                    $Optins->city = @$value->city;
//                    $Optins->postal_code = @$value->postal_code;
//                    $Optins->date_of_birth = @$value->date_of_birth;
//                    $Optins->email = (!empty(@$value->email)) ? $value->email : '';
//                    $Optins->campaign_name = '';
//                    $Optins->lead_id = $value->lead_id;
//                    $Optins->optins_date = date('Y-m-d', strtotime($value->createddate));
//                    $Optins->agent_id = @$value->agentid;
//                    $Optins->duplicate = 'yes';
//                    $Optins->source_id = 'SEMobile_OILG_OPT';
//                    $Optins->datasource = 'SEMobile_OILG_OPT';
//                    if ($Optins->save()) {
//                        $dataCheck['OilGenco']['duplicate']++;
//                    }
//                }
//            }
//        }

        /* Switch Expert */
        $SwitchExperts = SDSales::where('createddate', '>=', $start . ' 00:00:00')
                ->where('createddate', '<=', $end . ' 23:59:59')
                ->where('neatley_opt', 'yes')
                ->where('security_phrase', 'SwitchExpert')
                ->select('lead_id', 'saleid', 'createddate', 'lastupdated', 'agentid', 'title', 'first_name', 'last_name', 'address1', 'address2', 'address3', 'city', 'postal_code', 'date_of_birth', 'email', 'phone_number')
                ->get();
        $dataCheck['SwitchExpert']['total'] = 0;
        $dataCheck['SwitchExpert']['success'] = 0;
        $dataCheck['SwitchExpert']['duplicate'] = 0;
        if (!empty($SwitchExperts) && count($SwitchExperts)) {
            foreach ($SwitchExperts as $key => $value) {
                $dataCheck['SwitchExpert']['total']++;
                $dataExist = SEMobileOPTins::where('phone', @$value->phone_number)->where('datasource','SEMobile_SE_OPT')->count();
                if ($dataExist == 0) {
                    $CountSE++;
                    $Optins = new SEMobileOPTins();
                    $Optins->title = @$value->title;
                    $Optins->first_name = @$value->first_name;
                    $Optins->last_name = @$value->last_name;
                    $Optins->phone = @$value->phone_number;
                    $Optins->add1 = @$value->address1;
                    $Optins->add2 = @$value->address2;
                    $Optins->add3 = @$value->address3;
                    $Optins->city = @$value->city;
                    $Optins->postal_code = @$value->postal_code;
                    $Optins->date_of_birth = @$value->date_of_birth;
                    $Optins->email = (!empty(@$value->email)) ? $value->email : '';
                    $Optins->campaign_name = '';
                    $Optins->lead_id = $value->lead_id;
                    $Optins->optins_date = date('Y-m-d', strtotime($value->createddate));
                    $Optins->agent_id = @$value->agentid;
                    $Optins->duplicate = 'no';
                    $Optins->source_id = 'SEMobile_SE_OPT';
                    $Optins->datasource = 'SEMobile_SE_OPT';
                    $Optins->vendor_lead_code = 'SEMobile_SE_OPT_' . str_pad($CountOilGen, 8, '0', STR_PAD_LEFT);

                    if ($Optins->save()) {
                        $updatedId = $Optins->id;
                    }
                    
                    $postData[$updatedId]['import_id'] = $updatedId;
                    $postData[$updatedId]['data_list'] = $list_id;
                    $postData[$updatedId]['main_phone'] = @$value->phone_number;
                    $postData[$updatedId]['title'] = get_empty(@$value->title, '');
                    $postData[$updatedId]['first_name'] = get_empty(@$value->first_name, '');
                    $postData[$updatedId]['last_name'] = get_empty(@$value->last_name, '');
                    $postData[$updatedId]['postcode'] = get_empty(@$value->postal_code, '');
                    $postData[$updatedId]['address1'] = get_empty(@$value->add1, '');
                    $postData[$updatedId]['address2'] = get_empty(@$value->add2, '');
                    $postData[$updatedId]['address3'] = get_empty(@$value->add2, '');
                    $postData[$updatedId]['city'] = get_empty(@$value->city, '');
                    $postData[$updatedId]['email'] = get_empty(@$value->email, 'test@gmail.com');
                    
                    $OPTINDate = date('Y-m-d', strtotime($value->createddate));
                    $vendor_lead_code = 'SEMobile_SE_OPT_' . str_pad($CountSE, 8, '0', STR_PAD_LEFT);
                    $AgentID =  (!empty($value->agentid)) ? @$value->agentid : '';
                    $postData[$updatedId]['source_code'] = $vendor_lead_code;
                    $postData[$updatedId]['source'] = 'SEMobile_SE_OPT';
                    $postData[$updatedId]['custom_fields'] = ['optindate' =>$OPTINDate, 'AgentID' =>$AgentID, 'Datasource' => 'SEMobile_SE_OPT'];
                    
//                    $dataArray = [];
//                    $dataArray['list_id'] = $list_id;
//                    $dataArray['phone_number'] = @$value->phone_number;
//                    $dataArray['title'] = @$value->title;
//                    $dataArray['first_name'] = @$value->first_name;
//                    $dataArray['last_name'] = @$value->last_name;
//                    $dataArray['postal_code'] = @$value->postal_code;
//                    $dataArray['email'] = @$value->email;
//                    $dataArray['address1'] = @$value->add1;
//                    $dataArray['address2'] = @$value->add2;
//                    $dataArray['address3'] = @$value->add3;
//                    $dataArray['city'] = @$value->city;
//                    $dataArray['source_id'] = 'SEMobile_SE_OPT';
//                    $dataArray['Datasource'] = 'SEMobile_SE_OPT';
//                    $dataArray['vendor_lead_code'] = 'SEMobile_SE_OPT_' . str_pad($CountOilGen, 8, '0', STR_PAD_LEFT);
//                    $dataArray['optindate'] = date('Y-m-d', strtotime($value->createddate));
//                    $dataArray['DateofSale'] = $value->createddate;
//                    $dataArray['AgentID'] = (!empty($value->agentid)) ? @$Agents[strtoupper(@$value->agentid)] : '';
//                    $dataArray['custom_fields'] = 'Y';
//                    $queryString = http_build_query($dataArray);
//                    $data = @file_get_contents('http://10.29.104.7/Admin/.functions/non_agent_api.php?source=Test&user=APIUSER&pass=APIUSER&function=add_lead&' . $queryString);
//                    $NeatleyAPIConnexUpdate = SEMobileOPTins::find($updatedId);
//                    $NeatleyAPIConnexUpdate->api_response = $data;
//                    if ($NeatleyAPIConnexUpdate->save()) {
                        $dataCheck['SwitchExpert']['success']++;
//                    }
                } else {
                    $Optins = new SEMobileOPTins();
                    $Optins->title = @$value->title;
                    $Optins->first_name = @$value->first_name;
                    $Optins->last_name = @$value->last_name;
                    $Optins->phone = @$value->phone_number;
                    $Optins->add1 = @$value->address1;
                    $Optins->add2 = @$value->address2;
                    $Optins->add3 = @$value->address3;
                    $Optins->city = @$value->city;
                    $Optins->postal_code = @$value->postal_code;
                    $Optins->date_of_birth = @$value->date_of_birth;
                    $Optins->email = (!empty(@$value->email)) ? $value->email : '';
                    $Optins->campaign_name = '';
                    $Optins->lead_id = $value->lead_id;
                    $Optins->optins_date = date('Y-m-d', strtotime($value->createddate));
                    $Optins->agent_id = @$value->agentid;
                    $Optins->duplicate = 'yes';
                    $Optins->source_id = 'SEMobile_SE_OPT';
                    $Optins->datasource = 'SEMobile_SE_OPT';
                    if ($Optins->save()) {
                         $dataCheck['SwitchExpert']['duplicate']++;
                    }
                }
            }
        }
        
        
        $dataResponse = get_new_dialer_api_LeadPOST($postData);
        get_main_response_update($dataResponse);
       
        $arrayMailTo = [env('DIALER_TEAM_EMAIL')];
//        $arrayMailTo = ['apanwar@usethegeeks.co.uk'];
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.logs.optin';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com','Kelly.McNeill@intelling.co.uk','George.Eastham@switchexperts.co.uk','mike.hoye@intelling.co.uk'];
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'Switch Expert Mobile OPTin - '.$start;
        $mail_data['data'] = @$dataCheck;
        $mail_data['timeFormat'] = array('start' => $start, 'end' => $end);

        $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                    $m->from($mail_data['from'], 'Intelling');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                });
    }

}
