<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;
use Log;
use App\Model\Intelling\O2DataFileLogs;
use App\Model\Intelling\O2Data;
use App\Model\Intelling\SEMobileOPTins;
use App\Model\Intelling\SEMobileSales;
use App\Model\Intelling\SwitchExpertOPTins;

class O2ToSwitchBroadbandOPTins extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:O2ToSwitchBroadbandOPTins';

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
        ini_set('memory_limit', '2048M');
        $InboundCampaignID = ['EurotradeM','EnitreMedia', 'Grosvenor', 'Ignition', 'IPTel', 'MTA_Leadgen', 'Neatley', 'Oil_Genco', 'OilGenco', 'OutworxIn', 'RightDealIN', 'Sandra', 'SEMobSwitch', 'Switch_Expe', 'Synergy', 'Synthesis', 'Topic','ADC'];
        
        $query_date = date('Y-m-d');
        $start = date('Y-m-01', strtotime($query_date));
        $end = date('Y-m-t', strtotime($query_date));
        
        $dateRange = getDatesFromRange($start, $end);
        $RangeCount = count($dateRange);
        
        /*OPTIN START*/
        $OPTins = SwitchExpertOPTins::where('created_at','>=',$start.' 00:00:00')
                                    ->where('created_at','<=',$end.' 23:59:59')
                                    ->where('duplicate_status','no')
                                    ->select(DB::RAW('DATE(created_at) as CallDate,count(*) as total'),'campaign_name')
                                    ->groupBy('campaign_name',DB::RAW('DATE(created_at)'))
                                    ->get()
                                    ->toArray();
//        pr($OPTins);
//        exit;
        $arrayCount = [];
        foreach($OPTins as $value){
            if(in_array($value['campaign_name'],$InboundCampaignID)){
                 if(!empty($arrayCount['1405'][$value['CallDate']])){
                     $arrayCount['1405'][$value['CallDate']] = $arrayCount['1405'][$value['CallDate']] + $value['total'];
                 }else{
                     $arrayCount['1405'][$value['CallDate']] = $value['total'];
                 }   
            }elseif($value['campaign_name'] == 3003){
                $arrayCount['3003'][$value['CallDate']] = $value['total'];
            }elseif($value['campaign_name'] == 'O2 E2E Gradbay SYN'){
                $arrayCount['1306'][$value['CallDate']] = $value['total'];
            }elseif($value['campaign_name'] == 'O2 Consumer'){
                $arrayCount['1307'][$value['CallDate']] = $value['total'];
            }
        }
       
        /*OPTIN END*/
        
        $UserListings = DB::connection('OmniDialer')->table('users')->get()->toArray();


        $CampaignID1 = 1004;
        $CampaignID2 = 1002;

        $query = "Select CallDate,campaign_id,Calls, Connects, ROUND(Connects/Calls*100,2) as ConnectRate,DMCs, ROUND(DMCs/Connects*100,2) as DMCRate,Sales,ROUND(Sales/DMCs*100,2) as ConversionRate
from
 (select DATE(call_date) as CallDate,campaign_id,
  sum(case when status is not null and (comments NOT IN ('CHAT','EMAIL') OR comments IS NULL) then 1 else 0 end) as Calls,
  sum(case when status in (select status from status_combined where human_answered = 'Y') then 1 else 0 end) as Connects,
  sum(case when status in (select status from status_combined where customer_contact = 'Y') then 1 else 0 end) as DMCs,
  sum(case when status in (select status from status_combined where Sale = 'Y') then 1 else 0 end) as Sales
  from outbound_log WHERE call_date >= '" . $start . " 00:00:00' AND call_date <= '" . $end . " 23:59:59' AND campaign_id = " . $CampaignID2 . " group by DATE(call_date)) a";
        $data1 = DB::connection('OmniDialer')->select($query);
        
        
        
        /**/
        
        $query = "select DATE(call_date) as CallDate,campaign_id,
  sum(case when status is not null and (comments NOT IN ('CHAT','EMAIL') OR comments IS NULL) then 1 else 0 end) as Calls,
  sum(case when status in (select status from status_combined where human_answered = 'Y') then 1 else 0 end) as Connects,
  sum(case when status in (select status from status_combined where customer_contact = 'Y') then 1 else 0 end) as DMCs,
  sum(case when status in (select status from status_combined where Sale = 'Y') then 1 else 0 end) as Sales
  from outbound_log WHERE call_date >= '" . $start . " 00:00:00' AND call_date <= '" . $end . " 23:59:59' AND campaign_id = 3003 group by DATE(call_date)";
        $data2 = DB::connection('MainDialer')->select($query);
        
        $query = "select DATE(call_date) as CallDate,campaign_id,
  sum(case when status is not null and (comments NOT IN ('CHAT','EMAIL') OR comments IS NULL) then 1 else 0 end) as Calls,
  sum(case when status in (select status from status_combined where human_answered = 'Y') then 1 else 0 end) as Connects,
  sum(case when status in (select status from status_combined where customer_contact = 'Y') then 1 else 0 end) as DMCs,
  sum(case when status in (select status from status_combined where Sale = 'Y') then 1 else 0 end) as Sales
  from outbound_log WHERE call_date >= '" . $start . " 00:00:00' AND call_date <= '" . $end . " 23:59:59' AND campaign_id = 1307 group by DATE(call_date)";
        $data3 = DB::connection('MainDialer')->select($query);
        
        $query = "select DATE(call_date) as CallDate,campaign_id,
  sum(case when status is not null and (comments NOT IN ('CHAT','EMAIL') OR comments IS NULL) then 1 else 0 end) as Calls,
  sum(case when status in (select status from status_combined where human_answered = 'Y') then 1 else 0 end) as Connects,
  sum(case when status in (select status from status_combined where customer_contact = 'Y') then 1 else 0 end) as DMCs,
  sum(case when status in (select status from status_combined where Sale = 'Y') then 1 else 0 end) as Sales
  from outbound_log WHERE call_date >= '" . $start . " 00:00:00' AND call_date <= '" . $end . " 23:59:59' AND campaign_id = 1306 group by DATE(call_date)";
        $data4 = DB::connection('MainDialer')->select($query);
       
        
        
        
        $data6 = DB::connection('MainDialer')->table('campaigns')->where('campaign_id', 1405)->first();
        $InboundCampaigns = array_filter(explode(' ', rtrim($data6->closer_campaigns, '-')));
        
        $query = "Select CallDate,Calls, Connects, ROUND(Connects/Calls*100,2) as ConnectRate,DMCs, ROUND(DMCs/Connects*100,2) as DMCRate,Sales,ROUND(Sales/DMCs*100,2) as ConversionRate
from
 (select DATE(call_date) as CallDate,sum(case when status is not null and (comments NOT IN ('CHAT','EMAIL') OR comments IS NULL) then 1 else 0 end) as Calls,
  sum(case when status in (select status from status_combined where human_answered = 'Y') then 1 else 0 end) as Connects,
  sum(case when status in (select status from status_combined where customer_contact = 'Y') then 1 else 0 end) as DMCs,
  sum(case when status in (select status from status_combined where Sale = 'Y') then 1 else 0 end) as Sales
  from inbound_log WHERE call_date >= '" . $start . " 00:00:00' AND call_date <= '" . $end . " 23:59:59' AND campaign_id IN ('" . implode("','", $InboundCampaigns) . "') AND user != 'VDCL' group by DATE(call_date)) a";
        $data5 = DB::connection('MainDialer')->select($query);
        
        
        
        $arrayInsert = [];
        foreach($dateRange as $date){
          $key = array_search($date, array_column($data1, 'CallDate'));
            if (!empty($data1[$key]) && $data1[$key]) {
                $Result1 = $data1[$key];
                if ($data1[$key]->CallDate != $date){
                    
                }else{
                $arrayInsert[$date][1002]['Calls'] = $Result1->Calls; 
                $arrayInsert[$date][1002]['Connects'] = $Result1->Connects; 
                $arrayInsert[$date][1002]['ConnectRate'] = $Result1->ConnectRate;
                $arrayInsert[$date][1002]['DMCs'] = $Result1->DMCs;
                $arrayInsert[$date][1002]['DMCRate'] = $Result1->DMCRate;
                $arrayInsert[$date][1002]['Conversion'] = $Result1->ConversionRate;
                $arrayInsert[$date][1002]['Sales'] = $Result1->Sales;
                }
            }
            
            /*Campaign - 3003*/
           $key2 = array_search($date, array_column($data2, 'CallDate'));
            if (!empty($data2[$key2]) && $data2[$key2]) {
                $Result2 = $data2[$key2];
                if ($data2[$key2]->CallDate != $date){
                    
                }else{
                    $OPTinkey2 = (!empty($arrayCount[3003][$date]) && $arrayCount[3003][$date]) ? $arrayCount[3003][$date] : 0;
                    
                    $arrayInsert[$date][3003]['DMC'] = $Result2->DMCs; 
                    $arrayInsert[$date][3003]['OPTIN'] = $OPTinkey2; 
                    $arrayInsert[$date][3003]['PERCENTAGE'] = (!empty($Result2->DMCs) && $Result2->DMCs) ? round((($OPTinkey2/$Result2->DMCs)*100),2) : 0;
                }
            }
            
            /*Campaign - 1307*/
           $key3 = array_search($date, array_column($data3, 'CallDate'));
            if (!empty($data3[$key3]) && $data3[$key3]) {
                $Result3 = $data3[$key3];
                if ($data3[$key3]->CallDate != $date){
                    
                }else{
                    $OPTinkey3 = (!empty($arrayCount[1307][$date]) && $arrayCount[1307][$date]) ? $arrayCount[1307][$date] : 0;
                    
                    $arrayInsert[$date][1307]['DMC'] = $Result3->DMCs; 
                    $arrayInsert[$date][1307]['OPTIN'] = $OPTinkey3; 
                    $arrayInsert[$date][1307]['PERCENTAGE'] = (!empty($Result3->DMCs) && $Result3->DMCs) ? round((($OPTinkey3/$Result3->DMCs)*100),2) : 0;
                }
            }
            
            /*Campaign - 1306*/
           $key4 = array_search($date, array_column($data4, 'CallDate'));
            if (!empty($data4[$key4]) && $data4[$key4]) {
                $Result4 = $data4[$key4];
                if ($data4[$key4]->CallDate != $date){
                    
                }else{
                    $OPTinkey4 = (!empty($arrayCount[1306][$date]) && $arrayCount[1306][$date]) ? $arrayCount[1306][$date] : 0;
                    
                    $arrayInsert[$date][1306]['DMC'] = $Result4->DMCs; 
                    $arrayInsert[$date][1306]['OPTIN'] = $OPTinkey4; 
                    $arrayInsert[$date][1306]['PERCENTAGE'] = (!empty($Result4->DMCs) && $Result4->DMCs) ? round((($OPTinkey4/$Result4->DMCs)*100),2) : 0;
                }
            }
            
            /*Campaign - 1405*/
           $key5 = array_search($date, array_column($data5, 'CallDate'));
            if (!empty($data5[$key5]) && $data5[$key5]) {
                $Result5 = $data5[$key5];
                if ($data5[$key5]->CallDate != $date){
                    
                }else{
                    $OPTinkey5 = (!empty($arrayCount[1405][$date]) && $arrayCount[1405][$date]) ? $arrayCount[1405][$date] : 0;
                    
                    $arrayInsert[$date][1405]['DMC'] = $Result5->DMCs; 
                    $arrayInsert[$date][1405]['OPTIN'] = $OPTinkey5; 
                    $arrayInsert[$date][1405]['PERCENTAGE'] = (!empty($Result5->DMCs) && $Result5->DMCs) ? round((($OPTinkey5/$Result5->DMCs)*100),2) : 0;
                }
            }
            
        }
       
       
        
        
        /* Sale Detail Sheet */
        $SaleDetail = DB::connection('OmniDialer')
                ->table('outbound_log')
                ->where('call_date', '>=', $start . ' 00:00:00')
                ->where('call_date', '<=', $end . ' 23:59:59')
                ->where('campaign_id', 1002)
                ->where('list_id', 4000)
                ->whereIn('status', ['SALE'])
                ->get();
//        pr($SaleDetail);
//        exit;
        $fileName = $start;
        $file = Excel::create($fileName, function($excel) use($data1,$start,$end,$SaleDetail,$arrayInsert,$dateRange,$RangeCount) {
                    $excel->sheet('Overview', function($sheet) use($data1,$start,$arrayInsert,$dateRange,$RangeCount) {
                        $sheet->setOrientation('landscape');
                        $sheet->appendRow(['', 'Premium', '', '','Consumer', '', '', 'Outbound Synergy (E2E)', '', '', 'Inbound','','','Total','','','Switch Experts O2 Opt-Ins - Omni 1002','','','','','','']);
                        $sheet->appendRow(['Date', 'DMC', 'Opt-Ins', 'Con%','DMC', 'Opt-Ins', 'Con%','DMC', 'Opt-Ins', 'Con%','DMC', 'Opt-Ins', 'Con%','DMC', 'Opt-Ins', 'Con%','Calls','Connects','Connect%','DMC','DMC%','Conversion','Sales']);
                        
                        foreach($dateRange as $date){
                             $sheet->appendRow([
                                'id1' => $date,
                                'id2' => (!empty($arrayInsert[$date][3003]['DMC'])) ? $arrayInsert[$date][3003]['DMC'] : 0,
                                'id3' => (!empty($arrayInsert[$date][3003]['OPTIN'])) ? $arrayInsert[$date][3003]['OPTIN'] : 0,
                                'id4' => (!empty($arrayInsert[$date][3003]['PERCENTAGE'])) ? $arrayInsert[$date][3003]['PERCENTAGE'] : 0,
                                'id5' => (!empty($arrayInsert[$date][1307]['DMC'])) ? $arrayInsert[$date][1307]['DMC'] : 0,
                                'id6' => (!empty($arrayInsert[$date][1307]['OPTIN'])) ? $arrayInsert[$date][1307]['OPTIN'] : 0,
                                'id7' => (!empty($arrayInsert[$date][1307]['PERCENTAGE'])) ? $arrayInsert[$date][1307]['PERCENTAGE'] : 0,
                                'id8' => (!empty($arrayInsert[$date][1306]['DMC'])) ? $arrayInsert[$date][1306]['DMC'] : 0,
                                'id9' => (!empty($arrayInsert[$date][1306]['OPTIN'])) ? $arrayInsert[$date][1306]['OPTIN'] : 0,
                                'id10' => (!empty($arrayInsert[$date][1306]['PERCENTAGE'])) ? $arrayInsert[$date][1306]['PERCENTAGE'] : 0,
                                'id11' => (!empty($arrayInsert[$date][1405]['DMC'])) ? $arrayInsert[$date][1405]['DMC'] : 0,
                                'id12' => (!empty($arrayInsert[$date][1405]['OPTIN'])) ? $arrayInsert[$date][1405]['OPTIN'] : 0,
                                'id13' => (!empty($arrayInsert[$date][1405]['PERCENTAGE'])) ? $arrayInsert[$date][1405]['PERCENTAGE'] : 0,
                                'id14' => 0,
                                'id15' => 0,
                                'id16' => 0,
                                'id17' => (!empty($arrayInsert[$date][1002]['Calls'])) ? $arrayInsert[$date][1002]['Calls'] : 0,
                                'id18' => (!empty($arrayInsert[$date][1002]['Connects'])) ? $arrayInsert[$date][1002]['Connects'] : 0,
                                'id19' => (!empty($arrayInsert[$date][1002]['ConnectRate'])) ? $arrayInsert[$date][1002]['ConnectRate'] : 0,
                                'id20' => (!empty($arrayInsert[$date][1002]['DMCs'])) ? $arrayInsert[$date][1002]['DMCs'] : 0,
                                'id21' => (!empty($arrayInsert[$date][1002]['DMCRate'])) ? $arrayInsert[$date][1002]['DMCRate'] : 0,
                                'id22' => (!empty($arrayInsert[$date][1002]['Conversion'])) ? $arrayInsert[$date][1002]['Conversion'] : 0,
                                'id23' => (!empty($arrayInsert[$date][1002]['Sales'])) ? $arrayInsert[$date][1002]['Sales'] : 0,
                            ]);
                        }
//                        $sheet->appendRow([
//                            'id1' => $start,
//                            'id2' => (!empty($Performance[3003]['DMC'])) ? $Performance[3003]['DMC'] : 0,
//                            'id3' => (!empty($Performance[3003]['OPTIN'])) ? $Performance[3003]['OPTIN'] : 0,
//                            'id4' => (!empty($Performance[3003]['PERCENTAGE'])) ? $Performance[3003]['PERCENTAGE'] : 0,
//                            'id5' => (!empty($Performance[1307]['DMC'])) ? $Performance[1307]['DMC'] : 0,
//                            'id6' => (!empty($Performance[1307]['OPTIN'])) ? $Performance[1307]['OPTIN'] : 0,
//                            'id7' => (!empty($Performance[1307]['PERCENTAGE'])) ? $Performance[1307]['PERCENTAGE'] : 0,
//                            'id8' => (!empty($Performance[1306]['DMC'])) ? $Performance[1306]['DMC'] : 0,
//                            'id9' => (!empty($Performance[1306]['OPTIN'])) ? $Performance[1306]['OPTIN'] : 0,
//                            'id10' => (!empty($Performance[1306]['PERCENTAGE'])) ? $Performance[1306]['PERCENTAGE'] : 0,
//                            'id11' => (!empty($Performance[1405]['DMC'])) ? $Performance[1405]['DMC'] : 0,
//                            'id12' => (!empty($Performance[1405]['OPTIN'])) ? $Performance[1405]['OPTIN'] : 0,
//                            'id13' => (!empty($Performance[1405]['PERCENTAGE'])) ? $Performance[1405]['PERCENTAGE'] : 0,
//                            'id14' => (!empty($Performance['Total']['DMC'])) ? $Performance['Total']['DMC'] : 0,
//                            'id15' => (!empty($Performance['Total']['OPTIN'])) ? $Performance['Total']['OPTIN'] : 0,
//                            'id16' => (!empty($Performance['Total']['PERCENTAGE'])) ? $Performance['Total']['PERCENTAGE'] : 0,
//                            'id17' => @$data1[0]->Calls,
//                            'id18' => @$data1[0]->Connects,
//                            'id19' => @$data1[0]->ConnectRate,
//                            'id20' => @$data1[0]->DMCs,
//                            'id21' => @$data1[0]->DMCRate,
//                            'id22' => (!empty($data1[0]->DMCs) && $data1[0]->DMCs) ? round((($data1[0]->Sales * 100) / $data1[0]->DMCs), 2) : 0,
//                            'id23' => @$data1[0]->Sales,
//                        ]);
                        $sheet->cells('A1', function ($cells) {
                            $cells->setBackground('#D9D9D9');
                        });
                        $sheet->cells('B1:D1', function ($cells) {
                            $cells->setBackground('#FF7C80');
                            $cells->setAlignment('center');
                            $cells->setFontWeight('bold');
                        });
                        $sheet->cells('E1:G1', function ($cells) {
                            $cells->setBackground('#8EA9DB');
                            $cells->setAlignment('center');
                            $cells->setFontWeight('bold');
                        });
                        $sheet->cells('H1:J1', function ($cells) {
                            $cells->setBackground('#F4B084');
                            $cells->setAlignment('center');
                            $cells->setFontWeight('bold');
                            $cells->setFontWeight('bold');
                        });
                        $sheet->cells('K1:M1', function ($cells) {
                            $cells->setBackground('#A9D08E');
                            $cells->setAlignment('center');
                            $cells->setFontWeight('bold');
                        });
                        $sheet->cells('N1:P1', function ($cells) {
                            $cells->setBackground('#D9D9D9');
                            $cells->setAlignment('center');
                            $cells->setFontWeight('bold');
                        });
                        $sheet->cells('Q1:W1', function ($cells) {
                            $cells->setBackground('#FFD966');
                            $cells->setAlignment('center');
                            $cells->setFontWeight('bold');
                        });
                        $sheet->mergeCells('B1:D1');
                        $sheet->mergeCells('E1:G1');
                        $sheet->mergeCells('H1:J1');
                        $sheet->mergeCells('K1:M1');
                        $sheet->mergeCells('N1:P1');
                        $sheet->mergeCells('Q1:W1');
                        
                        $sheet->cells('A3', function ($cells) {
                            $cells->setBackground('#D9D9D9');
                        });
                        $sheet->cells('B3:D3', function ($cells) {
                            $cells->setBackground('#FF9999');
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('E3:G3', function ($cells) {
                            $cells->setBackground('#BDD7EE');
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('H3:J3', function ($cells) {
                            $cells->setBackground('#F8CBAD');
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('K3:M3', function ($cells) {
                            $cells->setBackground('#C6E0B4  ');
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('N3:P3', function ($cells) {
                            $cells->setBackground('#D9D9D9 ');
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('Q3:W3', function ($cells) {
                            $cells->setBackground('#FFE699');
                            $cells->setAlignment('center');
                        });
                    });
                    
                    
//                    $excel->sheet('Sale Details', function($sheet) use($SaleDetail) {
//                        $sheet->setOrientation('landscape');
//                        $sheet->appendRow(['Campaign','Agent', 'Call Date', 'List', 'Lead ID', 'Status', 'CLI', 'Call Length', 'Agent ID', 'Agent Name', 'Commission', 'Team']);
//                        foreach ($SaleDetail as $detail) {
//                             $Sale = DB::connection('OmniDialer')
//                                                ->table('custom_fields_data')
//                                                ->join('users','users.user','custom_fields_data.custom_2')
//                                                ->where('custom_fields_data.lead_id',$detail->lead_id)
//                                                ->where('custom_fields_data.list_id',4000)
//                                                ->select(['custom_fields_data.custom_2','custom_fields_data.custom_4','users.user_group','users.full_name'])
//                                                ->first();
//                             if(!empty(@$Sale->custom_2)){
//                            $sheet->appendRow([
//                                'id1' => $detail->campaign_id,
//                                'id2' => $detail->user,
//                                'id3' => $detail->call_date,
//                                'id4' => $detail->list_id,
//                                'id5' => $detail->lead_id,
//                                'id6' => $detail->status,
//                                'id7' => $detail->phone_number,
//                                'id8' => gmdate("H:i:s", $detail->length_in_sec),
//                                'id9' => @$Sale->custom_2,
//                                'id10' => @$Sale->full_name,
//                                'id11' => '€10.00',
//                                'id12' => str_replace('_OPTIN','',@$Sale->custom_4),
//                            ]); 
//                             }
//                        }
//                    });
                    
//                    $excel->sheet('Agent Coms', function($sheet) use($SaleDetail,$end) {
//                        $sheet->setOrientation('landscape');
//                        $sheet->appendRow(['Row Labels','Commission']);
//                        $array = [];
//                        
//                        foreach($SaleDetail as $detail){
//                             $Sale = DB::connection('OmniDialer')
//                                                ->table('custom_fields_data')
//                                                ->join('users','users.user','custom_fields_data.custom_2')
//                                                ->where('custom_fields_data.lead_id',$detail->lead_id)
//                                                ->where('custom_fields_data.list_id',4000)
//                                                ->select(['custom_fields_data.custom_2','users.user_group','users.full_name'])
//                                                ->first();
//                             if(!empty(@$Sale->custom_2)){
//                            if(!empty($array[@$Sale->custom_2.' - '. @$Sale->full_name])){
//                                $array[@$Sale->custom_2.' - '. @$Sale->full_name] = ($array[@$Sale->custom_2.' - '. @$Sale->full_name] + 10);
//                            }else{ 
//                                $array[@$Sale->custom_2.' - '. @$Sale->full_name] = 10;
//                            }
//                             }
//                        }
//                        
//                        
//                        foreach($array as $k=>$v){
//                            $sheet->appendRow([
//                                'id1' => $k,
//                                'id2' => $v,
//                            ]);
//                        }
//                        
//                        $sheet->appendRow([
//                                'id1' => 'Grand Total',
//                                'id2' => array_sum($array),
//                            ]);
//                         $sheet->cells('A1:C1', function ($cells) {
//                                $cells->setFontWeight('bold');
//                                $cells->setBackground('#D9D9D9');
//                            });
//                         $sheet->cells('A'.(count($array)+2).':C'.(count($array)+2), function ($cells) {
//                                $cells->setFontWeight('bold');
//                                $cells->setBackground('#D9D9D9');
//                            });
//                        
//                    });
                    
//                    $excel->sheet('Campaign Coms', function($sheet) use($SaleDetail,$end) {
//                        $sheet->setOrientation('landscape');
//                        $sheet->appendRow(['Row Labels','Commission']);
//                        $array = [];
//                        
//                        foreach($SaleDetail as $detail){
//                             $Sale = DB::connection('OmniDialer')
//                                                ->table('custom_fields_data')
//                                                ->join('users','users.user','custom_fields_data.custom_2')
//                                                ->where('custom_fields_data.lead_id',$detail->lead_id)
//                                                ->where('custom_fields_data.list_id',4000)
//                                                ->select(['custom_fields_data.custom_2','custom_fields_data.custom_4','users.user_group','users.full_name'])
//                                                ->first();
//                             if(!empty(@$Sale->custom_2)){
//                                if(!empty($array[@$Sale->custom_4])){
//                                    $array[@$Sale->custom_4] = ($array[@$Sale->custom_4] + 10);
//                                }else{ 
//                                    $array[@$Sale->custom_4] = 10;
//                                }
//                             }
//                        }
//                        
//                        
//                        foreach($array as $k=>$v){
//                            $sheet->appendRow([
//                                'id1' => str_replace('_OPTIN','',$k),
//                                'id2' => $v,
//                            ]);
//                        }
//                        
//                        $sheet->appendRow([
//                                'id1' => 'Grand Total',
//                                'id2' => array_sum($array),
//                            ]);
//                         $sheet->cells('A1:C1', function ($cells) {
//                                $cells->setFontWeight('bold');
//                                $cells->setBackground('#D9D9D9');
//                            });
//                         $sheet->cells('A'.(count($array)+2).':C'.(count($array)+2), function ($cells) {
//                                $cells->setFontWeight('bold');
//                                $cells->setBackground('#D9D9D9');
//                            });
//                        
//                    });
                    
                })->store("xls", storage_path('Daily/O2ToSwitchBroadbandOPTins/'), true);

//die('BYE');
//        $arrayMailTo = ['apanwar@usethegeeks.co.uk'];
                $arrayMailTo = ['apanwar@usethegeeks.co.uk','sarah.berry@intelling.co.uk'];
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.email';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'Opt in – O2 to Switch Broadband - ' . $start;

        $result = Mail::send($mail_data['view'], $mail_data, function ($m) use ($mail_data, $fileName) {
                    $m->from($mail_data['from'], 'Intelling');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                    $m->attach(storage_path('Daily/O2ToSwitchBroadbandOPTins/') . $fileName . '.xls');
                });
    }

}
