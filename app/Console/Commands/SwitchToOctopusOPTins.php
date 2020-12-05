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

class SwitchToOctopusOPTins extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:SwitchToOctopusOPTins';

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

//        $start = $end = date('Y-m-d');
        $query_date = date('Y-m-d');
        $start = date('Y-m-01', strtotime($query_date));
        $end = date('Y-m-t', strtotime($query_date));


        $CampaignID1 = 1004;
        $CampaignID2 = 3009;

        $query = "Select CallDate,campaign_id,Calls, Connects, ROUND(Connects/Calls*100,2) as ConnectRate,DMCs, ROUND(DMCs/Connects*100,2) as DMCRate,Sales,ROUND(Sales/DMCs*100,2) as ConversionRate
from
 (select DATE(call_date) as CallDate, campaign_id,
  sum(case when status is not null and (comments NOT IN ('CHAT','EMAIL') OR comments IS NULL) then 1 else 0 end) as Calls,
  sum(case when status in (select status from status_combined where human_answered = 'Y') then 1 else 0 end) as Connects,
  sum(case when status in (select status from status_combined where customer_contact = 'Y') then 1 else 0 end) as DMCs,
  sum(case when status in (select status from status_combined where Sale = 'Y') then 1 else 0 end) as Sales
  from outbound_log WHERE call_date >= '" . $start . " 00:00:00' AND call_date <= '" . $end . " 23:59:59' AND campaign_id = 3009 AND list_id = 30083 group by DATE(call_date)) a";

        $data1 = DB::connection('OmniDialer')->select($query);

        $data2 = DB::connection('OmniDialer')->table('campaigns')->where('campaign_id', 1004)->first();
        $InboundCampaigns = array_filter(explode(' ', rtrim($data2->closer_campaigns, '-')));
        
        $query = "Select CallDate,Calls, Connects, ROUND(Connects/Calls*100,2) as ConnectRate,DMCs, ROUND(DMCs/Connects*100,2) as DMCRate,Sales,ROUND(Sales/DMCs*100,2) as ConversionRate
from
 (select DATE(call_date) as CallDate,sum(case when status is not null and (comments NOT IN ('CHAT','EMAIL') OR comments IS NULL) then 1 else 0 end) as Calls,
  sum(case when status in (select status from status_combined where human_answered = 'Y') then 1 else 0 end) as Connects,
  sum(case when status in (select status from status_combined where customer_contact = 'Y') then 1 else 0 end) as DMCs,
  sum(case when status in (select status from status_combined where Sale = 'Y') then 1 else 0 end) as Sales
  from inbound_log WHERE call_date >= '" . $start . " 00:00:00' AND call_date <= '" . $end . " 23:59:59' AND campaign_id IN ('" . implode("','", $InboundCampaigns) . "') group by DATE(call_date)) a";
        $data3 = DB::connection('OmniDialer')->select($query);
       
        $data44 = \App\Model\UTGAPI\OctopusAPIO2LGSE::where('created_at', '>=', $start . ' 00:00:00')
                                                    ->where('updated_at', '<=', $end . ' 23:59:59')
                                                    ->select(DB::RAW('DATE(created_at) as CallDate,count(*) as total'))
                                                    ->groupBy(DB::RAW('DATE(created_at)'))
                                                    ->get()->toArray();

        
        /*Sale Detail Sheet*/
        $SaleDetailArray = DB::connection('OmniDialer')
                                    ->table('outbound_log')
                                    ->where('call_date','>=',$start.' 00:00:00')
                                    ->where('call_date','<=',$end.' 23:59:59')
                                    ->where('campaign_id',$CampaignID2)
                                    ->where('list_id',30083)
                                    ->whereIn('status',['SALE','SaleDD','SaleDS'])
                                    ->select(DB::RAW('DATE(call_date) as CallDate,count(*) as total'))
                                    ->groupBy(DB::RAW('DATE(call_date)'))
                                    ->get()->toArray();
        
        
        $arrayInsert = [];
        foreach ($data1 as $k => $v) {
            $key = array_search($v->CallDate, array_column($data3, 'CallDate'));
            if (!empty($data3[$key]) && $data3[$key]) {
                $Vinput = $data3[$key];
                if ($data3[$key]->CallDate != $v->CallDate) {
                    continue;
                }
            }

            $key1 = array_search($v->CallDate, array_column($data44, 'CallDate'));
            if (!empty($data44[$key1]) && $data44[$key1]) {
                $Vresult = $data44[$key1]['total'];
                if ($data44[$key1]['CallDate'] != $v->CallDate) {
                    continue;
                }
            }


            $key2 = array_search($v->CallDate, array_column($SaleDetailArray, 'CallDate'));

            if (strlen($key2) > 0) {
                $SaleResult = @$SaleDetailArray[$key2]->total;
                if ($SaleDetailArray[$key2]->CallDate != $v->CallDate) {
                    continue;
                }
            }


            $arrayInsert[$v->CallDate]['OptinVolum'] = $Vresult;
            $arrayInsert[$v->CallDate]['SaleDetail'] = @$SaleResult;

            $arrayInsert[$v->CallDate][3009]['Calls'] = $v->Calls;
            $arrayInsert[$v->CallDate][3009]['Connects'] = $v->Connects;
            $arrayInsert[$v->CallDate][3009]['ConnectRate'] = $v->ConnectRate;
            $arrayInsert[$v->CallDate][3009]['DMCs'] = $v->DMCs;
            $arrayInsert[$v->CallDate][3009]['DMCRate'] = $v->DMCRate;
            $arrayInsert[$v->CallDate][3009]['Sales'] = $v->Sales;
            $arrayInsert[$v->CallDate][3009]['ConversionRate'] = $v->ConversionRate;

            $arrayInsert[$v->CallDate][3009]['Conversion'] = (!empty($v->DMCs) && $v->DMCs) ? round(((@$SaleResult * 100) / $v->DMCs), 2) : 0;

            $arrayInsert[$v->CallDate][1004]['Calls'] = $Vinput->Calls;
            $arrayInsert[$v->CallDate][1004]['Connects'] = $Vinput->Connects;
            $arrayInsert[$v->CallDate][1004]['ConnectRate'] = $Vinput->ConnectRate;
            $arrayInsert[$v->CallDate][1004]['DMCs'] = $Vinput->DMCs;
            $arrayInsert[$v->CallDate][1004]['DMCRate'] = $Vinput->DMCRate;
            $arrayInsert[$v->CallDate][1004]['Sales'] = $Vinput->Sales;
            $arrayInsert[$v->CallDate][1004]['ConversionRate'] = $Vinput->ConversionRate;


            $arrayInsert[$v->CallDate][1004]['Call-OptIN'] = (!empty($Vinput->Calls) && $Vinput->Calls) ? round((($Vresult * 100) / $Vinput->Calls), 2) : 0;
            $arrayInsert[$v->CallDate][1004]['DMC-OptIN'] = (!empty($Vinput->DMCs) && $Vinput->DMCs) ? round((($Vresult * 100) / $Vinput->DMCs), 2) : 0;
        }
        
        
        $SaleDetail = DB::connection('OmniDialer')
                                    ->table('outbound_log')
                                    ->join('custom_fields_data', 'outbound_log.lead_id', 'custom_fields_data.lead_id')
                                    ->join('users','users.user','custom_fields_data.custom_2')
                                    ->where('outbound_log.call_date','>=',$start.' 00:00:00')
                                    ->where('outbound_log.call_date','<=',$end.' 23:59:59')
                                    ->where('outbound_log.campaign_id',$CampaignID2)
                                    ->where('outbound_log.list_id',30083)
                                    ->where('custom_fields_data.list_id',30083)
                                    ->whereIn('outbound_log.status',['SALE','SaleDD','SaleDS'])
                                    ->orderBy('outbound_log.call_date','ASC')
                                    ->get();
       
        $fileName = $start;
        $file = Excel::create($fileName, function($excel) use($data1, $data3, $data44,$SaleDetail,$start,$end,$InboundCampaigns,$arrayInsert) {
                    $excel->sheet('Opt-In Volume', function($sheet) use($data1, $data3, $data44,$start,$arrayInsert) {
                        $sheet->setOrientation('landscape');
                        $sheet->appendRow(['', 'Switch Experts Inbound', '', '', '', '', 'Octopus Energy Opt-In Performance', '', '', '', '', '', '']);
                        $sheet->appendRow(['Date', 'Opt In Vol', 'Inbound Calls', 'Call / Opt-In', 'Inbound DMCs', 'Call / Opt-In', 'Calls', 'Connects', 'Connect Rate', 'DMC', 'DMC Rate', 'Conversion', 'Sales']);
                            foreach ($arrayInsert as $k => $insertArray) {
                            $sheet->appendRow([
                                'id1' => $k,
                                'id2' => $insertArray['OptinVolum'],
                                'id3' => $insertArray[1004]['Calls'],
                                'id4' => $insertArray[1004]['Call-OptIN'] . '%',
                                'id5' => $insertArray[1004]['DMCs'],
                                'id6' => $insertArray[1004]['DMC-OptIN'] . '%',
                                'id7' => $insertArray[3009]['Calls'],
                                'id8' => $insertArray[3009]['Connects'],
                                'id9' => $insertArray[3009]['ConnectRate'] . '%',
                                'id10' => $insertArray[3009]['DMCs'],
                                'id11' => $insertArray[3009]['DMCRate'] . '%',
                                'id12' => $insertArray[3009]['Conversion'] . '%',
                                'id13' => $insertArray['SaleDetail'],
                            ]);
                        }
                        $sheet->cells('B1:F1', function ($cells) {
                            $cells->setBackground('#99FFCC');
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('G1:M1', function ($cells) {
                            $cells->setBackground('#FF66CC');
                            $cells->setAlignment('center');
                        });
                        $sheet->mergeCells('B1:F1');
                        $sheet->mergeCells('G1:M1');
                    });
                    
                    $excel->sheet('Sale Details', function($sheet) use($SaleDetail,$end,$InboundCampaigns) {
                        $sheet->setOrientation('landscape');
                        $sheet->appendRow(['Agent','Call Date','Campaign','List','Lead ID', 'Status', 'Phone Number','Call Length','Agent ID','Agent Name','Commission','Site']);
                        foreach($SaleDetail as $detail){
                            $sheet->appendRow([
                                'id1' => $detail->user,
                                'id2' => date('Y-m-d',strtotime($detail->call_date)),
                                'id3' => $detail->campaign_id,
                                'id4' => $detail->list_id,
                                'id5' => $detail->lead_id,
                                'id6' => $detail->status,
                                'id7' => $detail->phone_number,
                                'id8' => gmdate("H:i:s",$detail->length_in_sec),
                                'id9' => @$detail->custom_2,
                                'id10' => @$detail->full_name,
                                'id11' => '€10.00',
                                'id12' => check_SITE(@$detail->user_group),
                            ]);
                        }
                    });
                    
                    $excel->sheet('YTD', function($sheet) use($SaleDetail,$end,$InboundCampaigns) {
                        $sheet->setOrientation('landscape');
                        $sheet->appendRow(['Row Labels','Commission']);
                        $array = [];
                        
                        foreach($SaleDetail as $Sale){
                            if(!empty($array[@$Sale->custom_2.' - '. @$Sale->full_name])){
                                $array[@$Sale->custom_2.' - '. @$Sale->full_name] = ($array[@$Sale->custom_2.' - '. @$Sale->full_name] + 10);
                            }else{ 
                                $array[@$Sale->custom_2.' - '. @$Sale->full_name] = 10;
                            }
                        }
                        
                        
                        foreach($array as $k=>$v){
                            $sheet->appendRow([
                                'id1' => $k,
                                'id2' => $v,
                            ]);
                        }
                        
                        $sheet->appendRow([
                                'id1' => 'Grand Total',
                                'id2' => array_sum($array),
                            ]);
                        
                    });
                    
                })->store("xls", storage_path('Daily/SwitchToOctopusOPTins/'), true);


//        $arrayMailTo = ['apanwar@usethegeeks.co.uk'];
        $arrayMailTo = ['apanwar@usethegeeks.co.uk','sarah.berry@intelling.co.uk'];
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.email';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'Daily Switch To Octopus OPTins - '.$start;

        $result = Mail::send($mail_data['view'], $mail_data, function ($m) use ($mail_data, $fileName) {
                    $m->from($mail_data['from'], 'Intelling');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                    $m->attach(storage_path('Daily/SwitchToOctopusOPTins/') . $fileName . '.xls');
                });
    }

}
