<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use Mail;
use App\Model\Intelling\NeatleyAPIConnex;
use App\Model\Intelling\SEMobileOPTins;
use App\Model\UTGAPI\FileImportLog;
use App\Model\MainDialer\MDList;
use App\Model\UTGAPI\O2ReturnProcessFile;

class HomeController extends Controller {

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
    public function index() {
        
        return view('home');
    }
    
    public function index_old() {
               
        echo 'Hello';
        
//        $query1 = "SELECT c.campaign_id,c.campaign_name AS `CampaignName`,count(*) AS `LeadsLoaded`
//from custom_view.`list` l
//JOIN lists ls ON l.list_id=ls.list_id
//JOIN campaigns c ON c.campaign_id=ls.campaign_id
//WHERE l.entry_date BETWEEN '2020-03-05 00:00:00' and '2020-03-05 23:59:59'
//and l.source_id not in('VDCL') AND c.campaign_id = 3013
//group BY c.campaign_name";
//        
////        $OmniDialer1 = DB::connection('OmniDialer')->select($query1);
//        $OmniDialer1 = DB::connection('OmniDialer')->table('lists')->select('list_id','list_name')->get();
//        pr($OmniDialer1);
//        exit;
//        Artisan::call('command:P2PAddconProcess');
        
//        $data = DB::connection('OmniDialer')->table('campaign_status')->where('campaign_id',3005)->get();
//        pr($data);
        exit;
//        $query = "select
//
//s.sale_id,
//
//s.lead_id,
//
//s.sale_date,
//
//c.phone_number,
//
//s.sold_by,
//
//coalesce(al.user_group, ol.user_group, il.user_group) as user_group,
//
//s.product_type,
//
//s.make,
//
//s.model,
//
//s.order_num,
//
//s.campaign_sold_on,
//
//s.tariff_type,
//
//s.upfront_cost,
//
//c.vendor_id,
//
//c.list_id,
//
//ss.source_id,
//
// 
//
//s.campaign_sold_on,
//
//coalesce(al.campaign_id, ol.campaign_id) as campaign_id,
//
//il.campaign_id as inbound_group,
//c.first_name as FirstName,
//c.last_name as LastName,
//c.title as Title,
//c.postal_code as PostalCode
// 
//
//from O2Script.sales s
//
//join O2Script.customers c on s.lead_id = c.lead_id
//
//JOIN custom_view.sales_by_source_O2script ss ON ss.sale_id = s.sale_id
//
//left join
//
//(
//
//select lead_id , from_unixtime(talk_epoch) as log_start, from_unixtime(dispo_epoch + dispo_sec)  as log_end, u.full_name as agent_name, campaign_id,
//
//ifnull(al.user_group, u.user_group) as user_group
//
//from custom_view.agent_log al
//
//join custom_view.users u on al.user = u.user
//
//) al on s.lead_id = al.lead_id and s.sale_date between al.log_start and al.log_end and s.sold_by = al.agent_name
//
// 
//
//left join (select lead_id, ol.call_date as call_start, from_unixtime(ol.end_epoch) as call_end, u.full_name as agent_name, ifnull(ol.user_group, u.user_group) as user_group,
//
//campaign_id
//
//from custom_view.inbound_log ol
//
//join custom_view.users u on ol.user = u.user
//
//) il on s.lead_id = il.lead_id and s.sale_date between il.call_start and il.call_end and s.sold_by = il.agent_name
//
//left join  (select lead_id, ol.call_date as call_start, from_unixtime(ol.end_epoch) as call_end, u.full_name as agent_name, ifnull(ol.user_group, u.user_group) as user_group,
//
//campaign_id
//
//from custom_view.outbound_log ol
//
//join custom_view.users u on ol.user = u.user
//
//) ol
//
//on s.lead_id = ol.lead_id and s.sale_date between ol.call_start and ol.call_end and s.sold_by = ol.agent_name
//
// 
//
//where s.sale_date between  '2020-02-13 00:00:00' and '2020-02-13 23:59:59';
//
//#WHERE s.campaign_sold_on = 3001";
        
//        $data = DB::connection('OmniDialer')->table('user_groups')->where('allowed_campaigns','LIKE','%3005%')->select('user_group','group_name')->get();
//        $data = DB::connection('MainDialer')->select($query);
        
//        $data = DB::connection('MainDialer')->table('list')->limit(1)->get();
//        pr($data);
//        exit;
//        $query1 = "SELECT c.campaign_id,c.campaign_name AS `CampaignName`,l.source_id,count(*) AS `LeadsLoaded`
//from custom_view.`list` l
//JOIN lists ls ON l.list_id=ls.list_id
//JOIN campaigns c ON c.campaign_id=ls.campaign_id
//WHERE l.entry_date BETWEEN '2020-02-12 00:00:00' and '2020-02-12 23:59:59'
//and l.source_id not in('VDCL') AND c.campaign_id = 3003
//group BY l.source_id";
//        
//        $MainDialer1 = DB::connection('MainDialer')->select($query1);
//        
//        pr($data);
        exit;
//        $data = DB::connection('main__dialer')->select('SHOW TABLES');
//        $data = DB::connection('MainDialer')->select("SELECT c.campaign_name AS `Campaign Name`,count(*) AS `Leads Loaded`
//from custom_view.`list` l
//JOIN lists ls ON l.list_id=ls.list_id
//JOIN campaigns c ON c.campaign_id=ls.campaign_id
//WHERE l.entry_date BETWEEN '2019-10-10 00:00:00' and '2019-10-10 23:59:59'
//and l.source_id not in('VDCL')
//group BY c.campaign_name
//");
//        pr($data);
//        Artisan::call('command:O2ReturnProcessAutomationAddcon');
        
//        echo $start = '2020-01-02';
//        $Month = date('F', strtotime('+1 month', strtotime($start))); 
//        echo $end = date('Y-m-d',strtotime('first thursday of '. strtolower($Month).' 2020'));
//        $start = date('Y-m-d',strtotime('+1 day',strtotime($start)));
//        echo get_count_WorkingDays($start,$end);
//        die('Hello123');
        exit;
//         $query = "SELECT CompanyRegNo,CompanyName,address_line_1,address_line_2,address_line_3,address_line_4,Town,County,Telephone_Number,Employees,Website,Company_Turnover,SIC2_Description,
//Contact_Title,Contact_First_Name,Contact_Last_Name,Contact_DOB,SO_Contact_Job_Title,Contact_Mobile,Contact_Email
//FROM intelling.B2B_intelling limit 0,10";
//        $data = DB::connection('Intelling')->select($query);
//        $query = "SELECT c.campaign_id,c.campaign_name AS `Campaign Name`,count(*) AS `Leads Loaded`
//from custom_view.`list` l
//JOIN lists ls ON l.list_id=ls.list_id
//JOIN campaigns c ON c.campaign_id=ls.campaign_id
//WHERE l.entry_date BETWEEN '2019-10-10 00:00:00' and '2019-10-10 23:59:59'
//and l.source_id not in('VDCL')
//group BY c.campaign_name";
//        $data = DB::connection('MainDialer')->select($query);
//        pr($data);
//        exit;
//        $data = DB::connection('OmniDialer')->table('list')->where('phone_number','07895833031')->select('lead_id','entry_date','modify_date','vendor_lead_code','source_id','phone_number','list_id')->get();
//        pr($data);
//        
//        $data1 = \App\Model\O2UNICA\DataListing::where('phone_number','07895833031')->get()->toArray();
//        pr($data1);
//        
//        exit;
////         $server = '158.230.101.193';
//////        $server = '10.68.120.59';
////        $serverPort = 22;
////        $connection = ssh2_connect($server, $serverPort);
////        $serverUser = 'int2nuc';
//////        $serverUser = 'root';
////        $serverPassword = 'qwerty123';
//////        $serverPassword = '16IndiaGeeksUK';
//        $data = O2ReturnProcessFile::where('bussinessdate','20191006235959')->first();
//        
//        $fileName = $data->File_name;
//        $MSTfileName = $data->mfst_file_name;
////
////        if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
////            if (!empty($fileName)) {
////                ssh2_scp_send($connection, "/var/www/html/cron/storage/Automation/O2ReturnProcess/O2ReturnFileBCK/" . $fileName . '.dat.gz.gpg', "/export/home/int2nuc/export/home/int2nuc/received/" . $fileName . '.dat.gz.gpg');
//////                ssh2_scp_send($connection, "/var/www/html/cron/storage/Automation/O2ReturnProcess/export/1330/" . $fileName . '.dat.gz.gpg', "/home/Ankit/" . $fileName . '.dat.gz.gpg');
////            }
////            if (!empty($MSTfileName)) {
////                ssh2_scp_send($connection, "/var/www/html/cron/storage/Automation/O2ReturnProcess/O2ReturnFileBCK/" . $MSTfileName . '.mfst', "/export/home/int2nuc/export/home/int2nuc/received/" . $MSTfileName . '.mfst');
//////                ssh2_scp_send($connection, "/var/www/html/cron/storage/Automation/O2ReturnProcess/export/1330/" . $MSTfileName . '.mfst', "/home/Ankit/" . $MSTfileName . '.mfst');
////            }
////        }
//        
//        $LocalServer = '3.8.11.11';
//        $LocalUser = 'root';
//        $LocalPass = 'Utgesx0012!!';
//        $LocalPort = 22;
//        $LocalConnection = ssh2_connect($LocalServer, $LocalPort);
//        if (ssh2_auth_password($LocalConnection, $LocalUser, $LocalPass)) {
//            if (!empty($fileName)) {
//                ssh2_scp_send($LocalConnection, "/var/www/html/cron/storage/Automation/O2ReturnProcess/O2ReturnFileBCK/" . $fileName . '.dat.gz.gpg', "/home/O2ReturnProcess/" . $fileName . '.dat.gz.gpg');
////                ssh2_scp_send($connection, "/var/www/html/cron/storage/Automation/O2ReturnProcess/export/1330/" . $fileName . '.dat.gz.gpg', "/home/Ankit/" . $fileName . '.dat.gz.gpg');
//            }
//            if (!empty($MSTfileName)) {
//                ssh2_scp_send($LocalConnection, "/var/www/html/cron/storage/Automation/O2ReturnProcess/O2ReturnFileBCK/" . $MSTfileName . '.mfst', "/home/O2ReturnProcess/" . $MSTfileName . '.mfst');
////                ssh2_scp_send($connection, "/var/www/html/cron/storage/Automation/O2ReturnProcess/export/1330/" . $MSTfileName . '.mfst', "/home/Ankit/" . $MSTfileName . '.mfst');
//            }
//        }
//        
        
//        exit;
        
        
//        $CampaignID = 3005;
//        $dialer = 'OmniDialer';
////        $CampaignListID = DB::connection($dialer)->table('lists')->where('campaign_id', $CampaignID)->pluck('list_id');
////        $CampaignListID = DB::connection($dialer)->table('lists')->where('list_id', 9999999992)->get();
////        pr($CampaignListID);
////        exit;
////        $DataExist = DB::connection($dialer)->table('list')->whereIn('list_id', ['9999999992'])->where('phone_number','07966043281')->get();
////        $DataExist = DB::connection($dialer)->table('list')->whereIn('list_id', ['9999999992'])->count();
////        $DataExist = DB::connection($dialer)->table('list')->whereIn('list_id',$CampaignListID)->where('entry_date','>=','2019-12-13 00:00:00')->count();
////        $DataExist = DB::connection($dialer)->table('list')->whereIn('list_id',$CampaignListID)->where('entry_date','>=','2019-12-13 00:00:00')->count();
//        $DataExist = DB::connection($dialer)->table('list')->where('lead_id',24673254)->get();
//        pr($DataExist);
        
//        Artisan::call('command:O2ReturnProcessAutomationAddcon');
//          $data = DB::connection('OmniDialer')->table('lists')->where('list_id',30083)->get();
//          $data = DB::connection('OmniDialer')
//                                ->table('list')
//                                ->where('entry_date','>=','2020-01-03 11:00:00')
////                                ->where('lead_id',24047167)
//                                ->get();
         
//          pr($data);  
//        Artisan::call('DiallerOperations');
        exit;
//        
//        $data = DB::connection('OmniDialer')->table('campaigns')->where('campaign_id',9999)->get();
//        pr($data);
//        exit;
//        
//        return view('test.p2p_addcon_data');
//         $mail_data = array();
//        $mail_data['to'] = ['apanwar@usethegeeks.co.uk'];
//        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
//        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
//        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'test.test';
////        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
//        $mail_data['subject'] = 'P2P CORE - 3001 - Automation - TESTING';
//        $mail_data['data'] = '222';
//
//        $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
//                    $m->from($mail_data['from'], 'Intelling');
//                    if (!empty($mail_data['cc'])) {
//                        $m->cc($mail_data['cc']);
//                    }
//                    $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
//                    $m->to($mail_data['to'])->subject($mail_data['subject']);
//                });
//                exit;
//                
                
                
                
                
                
                
                
//                die('Hello');
//        $data = MDList::where('entry_date', '>=', '2019-11-01 00:00:00')
//                ->where('entry_date', '<=','2019-11-20 23:59:59')
//                ->whereIn('list_id', [3655, 1218, 1422])
//                ->whereIn('status', ['WAS', 'WASC', 'DECCC', 'DECAD', 'ADINFO', 'LODD', 'NI', 'NBD', 'LU'])
//                ->whereIn('source_id',['SID1', 'SID72', 'SID76','SID46', 'SID84', 'SID464', 'SID460', 'SID34', 'SID74', 'SID130', 'SID414', 'SID312', 'SID384', 'SID98R', 'SID452', 'SID50','SID466','SID100', 'SID450'])
//                ->count();
//        echo $data;
//        exit;
        
//        $DataExist = DB::connection('OmniDialer')->table('lists')->get();
//        $DataExist = DB::connection('OmniDialer')->table('list')->where('entry_date','>=','2019-11-02 00:00:00')->select('source_id',DB::RAW('count(*) as total'))->groupBy('source_id')->get();
//        $DataExist = DB::connection('OmniDialer')->table('list')->where('entry_date','>=','2019-11-02 00:00:00')->where('phone_number','07851646134')->get();
//        $DataExist = DB::connection('OmniDialer')->table('list')->where('entry_date','>=','2019-11-02 00:00:00')->where('source_id','O2_PRETOPOST')->select('phone_number')->get();
//        $DataExist = DB::connection('OmniDialer')->table('list')
////                ->where('list_id',30031)
//                ->where('entry_date','>=','2019-11-29 00:00:00')
//                ->where('entry_date','<=','2019-11-29 23:59:59')
//                ->where('vendor_lead_code','LIKE','O2_FREESIM_RD%')
//                ->count();
//        pr($DataExist);
//        exit;
//        
//        $data = DB::table('file_import_logs')
//                ->where('created_at','>=','2019-11-07 00:00:00')
//                ->where('type','O2UNICA')
//                ->where('total','!=',0)
////                ->sum('total');
//                ->get();
//        
//        
//        
//        pr($data);
//        die();
//        echo get_phone_numbers('078002505',0);
        
         
//        exit;
////        999999999
        $data = DB::connection('OmniDialer')->table('lists')->where('list_id',9999999992)->get();
        $data1 = DB::connection('OmniDialer')->table('custom_fields')->where('list_id',9999999992)->get();
        pr($data);
        pr($data1);
//        
        $data11 = DB::connection('OmniDialer')->table('list')->where('lead_id',24265955)->first();
        $data12 = DB::connection('OmniDialer')->table('custom_fields_data')->where('lead_id',24265955)->first();
//        
        pr($data11);
        pr($data12);
        die('BYE');
//         return view('emails.automation.p2p_core_data');
        $end = date("Y-m-d", strtotime("first thursday of next month"));
        $start = date("Y-m-d", strtotime("first friday of this month"));
        
        $WorkingDays = get_count_WorkingDays($start,$end);
        
        $totalCount = 20003;
        
        $PerDay =  round($totalCount/$WorkingDays);
        
        
       $CurrentDate = '2019-11-04';
       
       $DayDifference = datediff('d',$start, $CurrentDate);
       
       $WeekDayCountLIMIT = ($PerDay*5);
       
       if($DayDifference == 0){
           $Limit = round((($WeekDayCountLIMIT*12)/100));
           
       }elseif(($DayDifference >= 3 && $DayDifference <= 7) || ($DayDifference >= 10 && $DayDifference <= 14) || ($DayDifference >= 17 && $DayDifference <= 21) || ($DayDifference >= 24 && $DayDifference <= 28) || ($DayDifference >= 31 && $DayDifference <= 34)){
           
           $CurrentDay = date('l',strtotime($CurrentDate));
           
           if(in_array($CurrentDay,['Monday','Tuesday','Wednesday','Thursday'])){
               $Limit = round((($WeekDayCountLIMIT*22)/100));
           }else{
               $Limit = round((($WeekDayCountLIMIT*12)/100));
           }
//       }elseif($DayDifference >= 10 && $DayDifference <= 14){
//           $WeekDayCountLIMIT = ($PerDay*5);
//           $CurrentDay = date('l',strtotime($CurrentDate));
//           
//           if(in_array($CurrentDay,['Monday','Tuesday','Wednesday','Thursday'])){
//               $Limit = round((($WeekDayCountLIMIT*22)/100));
//           }else{
//               $Limit = round((($WeekDayCountLIMIT*12)/100));
//           }
//       }elseif($DayDifference >= 17 && $DayDifference <= 21){
//           $WeekDayCountLIMIT = ($PerDay*5);
//           $CurrentDay = date('l',strtotime($CurrentDate));
//           
//           if(in_array($CurrentDay,['Monday','Tuesday','Wednesday','Thursday'])){
//               $Limit = round((($WeekDayCountLIMIT*22)/100));
//           }else{
//               $Limit = round((($WeekDayCountLIMIT*12)/100));
//           }
//       }elseif($DayDifference >= 24 && $DayDifference <= 28){
//           $WeekDayCountLIMIT = ($PerDay*5);
//           $CurrentDay = date('l',strtotime($CurrentDate));
//           
//           if(in_array($CurrentDay,['Monday','Tuesday','Wednesday','Thursday'])){
//               $Limit = round((($WeekDayCountLIMIT*22)/100));
//           }else{
//               $Limit = round((($WeekDayCountLIMIT*12)/100));
//           }
       }else{
           die('BYE');
       }
       
       echo $Limit.'<br/>';
        
        exit;
        $data = DB::connection('OmniDialer')
                ->table('custom_fields')
                ->where('list_id',99999991)
                ->get();
        $data1 = DB::connection('OmniDialer')
                ->table('custom_fields_data')
                ->where('list_id',99999991)
                ->where('lead_id',24144708)
                ->get();
        
        $data2 = DB::connection('OmniDialer')
                ->table('list')
                ->where('lead_id',24144708)
                ->get();
        pr($data);
        pr($data1);
        pr($data2);
        
        $data11 = DB::connection('OmniDialer')
                ->table('custom_fields')
                ->where('list_id',99999991)
                ->get();
        $data12 = DB::connection('OmniDialer')
                ->table('custom_fields_data')
                ->where('list_id',99999991)
                ->where('lead_id',24144738)
                ->get();
        
        $data13 = DB::connection('OmniDialer')
                ->table('list')
                ->where('lead_id',24144738)
                ->get();
        pr($data11);
        pr($data12);
        pr($data13);
        exit;
//        $FileImportLog = new FileImportLog();
//        $FileImportLog->total = 10;
//        $FileImportLog->success = 10;
//        $FileImportLog->failed = 10;
//        $FileImportLog->duplicate = 10;
//        if($FileImportLog->save()){
//            echo $FileImportLog->total;
//        }
//        exit;
//        $data = DB::connection('OmniDialer')->table('list')->where('entry_date','>=','2019-10-24 13:39:00')->count();
//        $data = DB::connection('OmniDialer')->table('list')->where('entry_date','>=','2019-10-22 02:00:00')->where('status','NEW')->count();
//        pr($data);
//        exit;
//        echo get_phone_numbers('449780025056',0);
//        exit;
//        $curl = curl_init();
//
//curl_setopt_array($curl, array(
//  CURLOPT_URL => "https://api3.cnx1.uk/consumer/login",
//  CURLOPT_RETURNTRANSFER => true,
//  CURLOPT_ENCODING => "",
//  CURLOPT_MAXREDIRS => 10,
//  CURLOPT_TIMEOUT => 30,
//  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//  CURLOPT_CUSTOMREQUEST => "POST",
//  CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"username\"\r\n\r\nIntellingTwo\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"password\"\r\n\r\nEg926bD5GfbGEJwG\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
//  CURLOPT_HTTPHEADER => array(
//    "cache-control: no-cache",
//    "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW",
//    "postman-token: 81f11582-800b-5a74-688a-65c5543cd032",
//    "user-agent: Intelling-API"
//  ),
//));
//
//$response = curl_exec($curl);
//$err = curl_error($curl);
//
//curl_close($curl);
//
//if ($err) {
//  echo "cURL Error #:" . $err;
//} else {
//  echo $response;
//}
        
//$data = DB::connection('OmniDialer')->table('custom_fields_data')->where('list_id',9999999)->get()->toArray(); 
//        pr($data);
//exit;
//        $Query = "SELECT c.campaign_name AS `Campaign Name`,count(*) AS `Leads Loaded`
//from custom_view.`list` l
//JOIN lists ls ON l.list_id=ls.list_id
//JOIN campaigns c ON c.campaign_id=ls.campaign_id
//WHERE c.campaign_id IN ('1307','3003','3040','3042','3043','3011','1330','3001','3002','3005','3006')
//and l.entry_date BETWEEN '2019-10-10 00:00:00' and '2019-10-10 23:59:59'
//and l.source_id not in('VDCL')
//group BY c.campaign_name
//";
//        $data = DB::connection('OmniDialer')->select($Query);
//        Artisan::call('command:O2UNICAProcess');
//         $data = DB::connection('OmniDialer')->select('SELECT * FROM list L JOIN custom_fields_data CFD ON L.lead_id=CFD.lead_id where L.list_id = 9999999');
//        $data = DB::connection('OmniDialer')->table('list')->where('list_id',9999999)->whereNull('source_id')->count();
//        pr($data);
//        exit;
        
        
        $testListID = 9999999;
        $user = 'Intelling-OmniChannel';
        $pass = '2j4VHhYYHqkTnBjJ';
        $token = '0HeIOWyak5UIdeXKmD4twR7kty030UWXQ4fiU6e6kr7cuPgTm2';
        
        $postData = [];
        $ImportID = 1;
                        $postData[$ImportID]['import_id'] = $ImportID;
                        $postData[$ImportID]['data_list'] = $testListID;
                        $postData[$ImportID]['main_phone'] = '09780025056';
                        $postData[$ImportID]['title'] = 'Mr';
                        $postData[$ImportID]['first_name'] = 'Test';
                        $postData[$ImportID]['last_name'] = 'Developer';
                        $postData[$ImportID]['source_code'] = 'TD-'.date('Ymd');
                        $postData[$ImportID]['email'] = 'test@gmail.com';
                        $postData[$ImportID]['source'] = 'TEST-DEVELOPER';
//////                        
                        $CustomArray = [];
                        $CustomArray['CustNum'] = 'CUSTNUM';
                        $CustomArray['O2ClientID'] = 'O2CLIENTID';
                        $CustomArray['O2SubscriptionID'] = 'O2SUBSCRIPTIONID';
                        $CustomArray['O2CampaignCode'] = 'O2CAMPAIGNCODE';
                        $CustomArray['O2CellCode'] = 'O2CELLCODE';
                        $CustomArray['O2Channel'] = 'O2CHANNEL';
                        $CustomArray['O2TreatmentCode'] = 'O2TREATMENTCODE';
                        $CustomArray['O2DeploymentDate'] = 'O2DEPLOYMENTDATE';
                        $CustomArray['O2BigBundleFlag'] = 'O2BIGBUNDLEFLAG';
                        $CustomArray['O2P2PScore'] = 'O2P2PSCORE';
                        $CustomArray['O2TransactScore'] = 'O2TRANSACTSCORE';
                        $CustomArray['O2EarlyLifeModel'] = 'O2EARLYLIFEMODEL';
                        $CustomArray['O2CurrentTariff'] = 'O2CURRENTTARIFF';
                        $CustomArray['AboutCust1a'] = 'TENURE';
                        $CustomArray['AboutCust1b'] = 'TENURE1';
                        $CustomArray['AboutCust2a'] = 'AVG SPEND';
                        $CustomArray['AboutCust2b'] = 'AVGSPEND';
                        $CustomArray['AboutCust3a'] = 'LAST MONTH TOP UP';
                        $CustomArray['AboutCust3b'] = 'LASTMONTHTOPUP';
                        $CustomArray['AboutCust4a'] = 'AVG TOP UP 3M';
                        $CustomArray['AboutCust4b'] = 'AVGTOPUP3M';
                        $CustomArray['AboutCust5a'] = 'AVG MIN 3M';
                        $CustomArray['AboutCust5b'] = 'AVGMIN3M';
                        $CustomArray['AboutCust6a'] = 'AVG SMS 3M';
                        $CustomArray['AboutCust6b'] = 'AVGSMS3M';
                        $CustomArray['AboutCust7a'] = 'AVG DATA 3M';
                        $CustomArray['AboutCust7b'] = 'AVGDATA3M';
                        $CustomArray['AboutCust8a'] = 'CURRENT TARRIFF';
                        $CustomArray['AboutCust8b'] = 'CURRENTTARRIFF';
                        $postData[$ImportID]['custom_fields'] = $CustomArray;
       
        $postData1 = [];
        $postData1['token'] = $token;
        $postData1['customers'] = $postData;
        
        $NewData = get_omni_api_curl_test($user, $pass, $token, $postData1);
        pr($NewData);
        die('BYE');
        $user = 'IntellingTwo';
        $pass = 'Eg926bD5GfbGEJwG';
        $token = 'brwh890FraGrLy1VpvwU9KwDhAwT0EdB4dlWc8IqpzBIjh894L';
        
//        get_new_dialer_api_LeadPOST
//        Artisan::call('command:O2UNICAProcess');
//        $data = DB::connection('OmniDialer')
//                ->table('lists')
//                ->where('campaign_id',1330)
//                ->pluck('list_id');
//        $ListArray = [1330,13303,13302,13305];
//        $data = DB::connection('OmniDialer')->table('custom_fields')->where('list_id',1330)->limit(10)->get()->toArray();
//        $data = DB::connection('OmniDialer')->table('list')->where('phone_number','07745293898')->get()->toArray();
        
//        pr($data);
//        exit;
//        $data = DB::connection('MainDialer')->table('list')->where('lead_id','50158853')->get();
//        $data = DB::connection('MainDialer')->table('custom_fields_data')->where('lead_id','50158987')->get();
//        pr($data); 
//        exit;
//         
//        
//        
//        
//        $data = DB::connection('OmniDialer')->table('custom_fields')->get();
        $data1 = DB::table('Octopus_API_O2LG_SE')->where('created_at','>=','2019-08-19 00:00:00')->get();
        pr($data1);
        exit;
        
        foreach($data1 as $data){
            $dataExist = DB::connection('OmniDialer')
                    ->table('list')
                    ->where('phone_number', $data->phone_number)
                    ->where('list_id', 30083)
                    ->count();
            
            if ($dataExist > 0) {
                $Duplicate = 'yes';
//                $OctopusAPIO2LGSEUpdate->duplicate_status = 'yes';
                $response = '';
            } else {
                $Duplicate = 'no';
//                $OctopusAPIO2LGSEUpdate->duplicate_status = 'no';

                $postData = [];
                $postData['token'] = $token;
                $postData['data_list'] = 30083;
                $postData['main_phone'] = @$data->phone_number;
                $postData['first_name'] = @$data->first_name;
                $postData['last_name'] = @$data->last_name;
//                $postData['address1'] = @$data->address1;
//                $postData['address2'] = @$data->address2;
//                $postData['address3'] = @$data->address3;
                $postData['city'] = @$data->city;
                $postData['state'] = @$data->state;
                $postData['postal_code'] = @$data->postal_code;
                $postData['province'] = @$data->province;
                $postData['source_code'] = $data->inbound_group.'-'.$data->id;
                $postData['source'] = 'LIVE_OPTIN';
                $postData['email'] = (!empty($data->email)) ? $data->email : 'test@gmail.com';
                $postData['custom_fields'] = ['optindate' => date('Y-m-d H:i:s'), 'agentid' => @$data->agent_id, 'datasource' => @$data->datasource];
//                pr($postData);
                $dataPostAPI = get_omni_api_curl($user, $pass,$postData);

                $response = serialize($dataPostAPI);
            }
            
            \App\Model\UTGAPI\OctopusAPIO2LGSE::where('id',$data->id)->update(['duplicate_status'=>$Duplicate,'api_response'=>$response]);
//            if ($OctopusAPIO2LGSEUpdate->save()) {
//                
//            }
        }
        
        exit;
        $postData = [];
        $postData[1]['import_id'] = '123123';
        $postData[1]['data_list'] = 9999999;
        $postData[1]['main_phone'] = '01231231231';
        $postData[1]['title'] = '';
        $postData[1]['first_name'] = '';
        $postData[1]['last_name'] = '';
        $postData[1]['postcode'] = '';
        $postData[1]['address1'] = '';
        $postData[1]['address2'] = '';
        $postData[1]['address3'] = '';
        $postData[1]['city'] = '';
        $postData[1]['email'] = '';
        $postData[1]['source_code'] = 'Test123';
        $postData[1]['source'] = 'TEST_OPT';
        $postData[1]['custom_fields'] = ['CustNum' =>'Test'];
       
         
       $data =  get_OMNI_api_LeadPOST($postData);
       pr($data);
//        die('Hello');
       exit;
        
//        $date = '2015-07-28';
//        $PREJULYDate = '2015-07-31';
////        
//        $query = "select c.lead_id,c.Version,c.connect,c.vendor_id,c.list_id,c.phone_number,c.title,c.first_name,c.last_name,c.address1,c.address2,c.address3,c.city,c.postal_code,c.date_of_birth,c.alt_phone,c.email,c.comments,c.campaign,c.fronter,c.fullname,c.vendor_lead_code,c.DateSent,c.last_submitted,c.pencilWorkIn,c.preferableData,c.okPrice,c.responsiblePersonName,c.responsiblePersonPosition,c.responsiblePersonEmail,c.responsiblePersonPhone,c.responsibleAvailable,c.businessRole,c.authoriseCosts,(select s.status from custom_view.status_combined s where s.status = l.status group by status)as LastOutcome,(select custom_2 from custom_view.custom_fields_data cfd where l.lead_id=cfd.lead_id)as PivotalNumber,(select custom_3 from custom_view.custom_fields_data cfd where l.lead_id=cfd.lead_id)as TotalOrderValue,(select custom_6 from custom_view.custom_fields_data cfd where l.lead_id=cfd.lead_id) as PMVVolume,(select custom_7 from custom_view.custom_fields_data cfd where l.lead_id=cfd.lead_id) as PMVUnitValue,(select custom_8 from custom_view.custom_fields_data cfd where l.lead_id=cfd.lead_id) as TotalPMVvalue,(select custom_9 from custom_view.custom_fields_data cfd where l.lead_id=cfd.lead_id) as DuctVolume,(select custom_10 from custom_view.custom_fields_data cfd where l.lead_id=cfd.lead_id)as DuctunitValue,(select custom_11 from custom_view.custom_fields_data cfd where l.lead_id = cfd.lead_id)as TotalDuctValue from  JLA.customers c
// 
//inner join custom_view.list l on c.lead_id=l.lead_id
//
//where c.DateSent between '2019-06-03 00:00:00' and '2019-06-03 23:59:59'";
        
//       $data = DB::connection('OmniDialer')->table('list')->where('list_id',4000)->where('phone_number','07984545214')->get();
//       $data = DB::connection('MainDialer')->table('list')->where('phone_number','07884112769')->get();
//       $data = DB::connection('MainDialer')->table('custom_fields_data')->where('list_id',3011)->where('lead_id','50126251')->limit(1)->get();
//       $data = DB::connection('MainDialer')->table('custom_fields_data')->where('list_id',3011)->whereNotNull('custom_2')->where('custom_2','!=','')->orderBy('lead_id','desc')->limit(1)->get();
//       $data = DB::connection('MainDialer')->table('custom_fields')->where('list_id',3011)->get();
//       $data = DB::connection('MainDialer')->table('list')->where('lead_id',17803009)->get();
//       $data = DB::connection('MainDialer')->table('list')->where('lead_id',17803009)->get();
       $data = DB::connection('OmniDialer')->table('campaigns')->get();
//       $data = DB::connection('Intelling')->table('SE_Mobile_OPTins')->where('created_at','>=','2019-06-05 00:00:00')->get();
        
        pr($data);
        exit;
//        $data = SEMobileOPTins::where('duplicate','no')->orWhere('created_at','like','2019-05-27%')->where('duplicate','no')->orWhere('created_at','like','2019-05-28%')->get();
//        $data = SEMobileOPTins::where('duplicate','no')->whereIn('optins_date',['2019-05-27','2019-05-28'])->get();
//       
//        $Count = 0;
//        foreach ($data as $key => $value) {
//          
//           echo '<br>'.$Count++.' - ',$value->id;
//            $dataArray = [];
//                    $dataArray['list_id'] = 3011;
//                    $dataArray['phone_number'] = @$value->phone;
//                    $dataArray['title'] = @$value->title;
//                    $dataArray['first_name'] = @$value->first_name;
//                    $dataArray['last_name'] = @$value->last_name;
//                    $dataArray['postal_code'] = @$value->postal_code;
//                    $dataArray['email'] = @$value->email;
//                    $dataArray['address1'] = @$value->add1;
//                    $dataArray['address2'] = @$value->add2;
//                    $dataArray['address3'] = @$value->add3;
//                    $dataArray['city'] = @$value->city;
//                    $dataArray['source_id'] = $value->source_id;
//                    $dataArray['Datasource'] = $value->source_id;
//                    $dataArray['vendor_lead_code'] = $value->vendor_lead_code;
//                    $dataArray['optindate'] = $value->optins_date;
//                    $dataArray['DateofSale'] = $value->optins_date;
//                    $dataArray['AgentID'] = $value->agent_id;
//                    $dataArray['custom_fields'] = 'Y';
//                    
//            $queryString = http_build_query($dataArray);
//           
//                $data = @file_get_contents('http://'.env('NewDialerIP').'/Admin/.functions/non_agent_api.php?source=Test&user=APIUSER&pass=APIUSER&function=add_lead&' . $queryString);
//                $NeatleyAPIConnexUpdate = SEMobileOPTins::find($value->id);
//                $NeatleyAPIConnexUpdate->api_response = $data;
////                $NeatleyAPIConnexUpdate->status = 'success';
//                if ($NeatleyAPIConnexUpdate->save()) {
//                    
//                }
//            
//        }
//        exit;
        
//        $data = \App\Model\Intelling\AgentTableCombined::get()->toArray();
//        $data = DB::connection('OmniDialer')
//                ->table('inbound_log')
//                ->where('call_date','>=','2019-05-02 00:00:00')
//                ->select('campaign_id')
//                ->groupBy('campaign_id')
//                ->get()
//                ->toArray();
//                $data = get_group_sales_O2Consumer('2019-05-28 00:00:00','2019-05-28 23:59:59');
        
//        $data = DB::connection('MainDialer')->select("Select c.lead_id, c.title, c.first_name, c.last_name, c.phone_number, c.email, c.address1, c.address2, c.city, c.postal_code,c.fullname,c.teamname,c.current_supplier, c.happy_with_supplier, c.in_contract,c.bb_switch_call, l.last_local_call_time, u.user,c.campaign
//                        from Customers c
//                        JOIN list l
//                        on l.lead_id=c.lead_id
//                        JOIN users u
//                        on c.fullname=u.full_name
//                        where l.last_local_call_time between '2019-05-28 00:00:00' and '2019-05-28 23:59:59'
//                        and c.bb_switch_call = 'yes'
//                        group By c.lead_id");
//        pr($data);
        exit;
//        $campaignId = ['MTA_Leadgen', 'synergy', 'oilgenco', 'MTALGN', 'TOPIC' => 'Topic', 'TouchstonIn', 'OutworxIn', 'CogentHubin', 'ignition'=>'Ignition','Synthesis'];
//        foreach($campaignId as $key=>$val){
//            echo $val.' - '.get_o2inbound_intraday_sale('2019-04-30', $val).'<br/>';
//        }
////       $NewArray = ['MTA_Leadgen', 'Synergy', 'OilGenco', 'MTALGN', 'Topic', 'TouchstonIn', 'OutworxIn', 'CogentHubin','ignition','Synthesis'];
       $saleMain = \App\Model\O2Inbound\InboundSale::where('createddate', '>=', '2019-04-30 00:00:00')
            ->where('createddate', '<','2019-04-30 23:59:59')
            ->where('orderid','LIKE','MS-5%')
            ->whereIn('report_query', ['MTA_Leadgen', 'Synergy', 'OilGenco', 'MTALGN', 'Topic', 'TouchstonIn', 'OutworxIn', 'CogentHubin','ignition','Synthesis'])
//            ->select('report_query',DB::RAW('count(*) as total'))
//            ->groupBy('report_query')    
            ->get();
////               pr($NewArray);
////        echo get_o2inbound_intraday_all_sale('2019-04-30');
               pr($saleMain->toArray());
        exit;
        $CampaignArray = ['CogentHubin', 'OutworxIn', 'TouchstonIn'];
//        $data = DB::connection('MainDialer')
//                ->table('inbound_log')
//                ->join('campaign_status','inbound_log.status','campaign_status.status')
//                ->whereIn('inbound_log.campaign_id', $CampaignArray)
//                ->where('inbound_log.call_date', '>=', $start)
//                ->where('inbound_log.call_date', '<=', $end)
//                ->select('inbound_log.status','campaign_status.status_name',DB::RAW('count(*) as total'))
//                ->groupBy('inbound_log.status')
//                ->orderBy('total','desc')
//                ->get();
//        $data = DB::connection('MainDialer')
//                ->table('campaign_status')->pluck('status_name');
//        pr($data);
//        pr(get_MainDialerAPI_response());
//        $data = \App\Model\Intelling\O2DataFileLogs::find(1)->toArray();
//        pr($data);
        
        
//        $data = \App\Model\UTGAPI\O2FreeSimFileImport::where('created_at','>=','2019-04-07 00:00:00')
//                ->where('created_at','<=','2019-04-13 23:59:59')
//                ->get()
//                ->toArray();
//        
//        $arrayMailTo = ['apanwar@usethegeeks.co.uk','Jamie.Taylor@intelling.co.uk','Nicola.Sharrock@intelling.co.uk','diallersupport@intelling.co.uk'];
//        
//
//        $result = Mail::send('emails.O2FREESIM_WEEKLY', ['data' => $data], function ($m){
//                    $m->from('intellingreports@intelling.co.uk','API Reports');
//                    $m->cc(['akumar@usethegeeks.com']);
//                    $m->replyTo('intellingreports@intelling.co.uk', 'API Reports');
//                    $m->to(['apanwar@usethegeeks.co.uk','Jamie.Taylor@intelling.co.uk','Nicola.Sharrock@intelling.co.uk','diallersupport@intelling.co.uk'])->subject('O2FreeSim Leads (07-04-2019 to 13-04-2019');
//                });
//        pr($data);
        exit;
//        $data = \App\Model\Intelling\O2Data::select('file_log_id')->groupBy('file_log_id')->get()->toArray();
//        foreach($data as $value){
//            $dataExist = \App\Model\Intelling\O2DataFileLogs::find($value['file_log_id']);
//            if(!empty($dataExist->file_name)){
//                
//            }else{
//                echo $value['file_log_id'].'<br/>';
//            }
//            
//            
//        }
//        pr($data);
//        exit;
//         $server = '10.68.120.59';
//        $serverPort = 22;
//        $connection = ssh2_connect($server, $serverPort);
//        $serverUser = 'root';
//        $serverPassword = '16IndiaGeeksUK';
//
//        if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
//            echo "connected\n";
//            
//            ssh2_scp_send($connection, "/var/www/html/cron/storage/Automation/O2ReturnProcess/export/1330/RESPONSE_INTELLING_001_20190118100922_20190117110000_001.dat.gz.gpg", "/home/Ankit/RESPONSE_INTELLING_001_20190118100922_20190117110000_001.dat.gz.gpg");
//            exit;
//           }
//           phpinfo();
//        $data = \App\Model\Intelling\O2DataFileLogs::where('success',0)
//                                    ->where('total','>=',0)
//                                    ->limit(7)
//                                    ->orderBy('id','desc')
//                                    ->get()
//                                    ->toArray();
//        echo get_break_O2FreeSim('123');
//        exit;
//        $data = \App\Model\Intelling\O2DataFileLogs::limit(7)
//                                    ->orderBy('id','desc')
//                                    ->get()
//                                    ->toArray();
//        pr($data);
//        exit;
           
//        echo get_break_O2FreeSim('O2FreeSIM_20150630.csv');
//        exit;
//        $arrayPhone = ['phone', 'mobile', 'phone_number', 'mpn', 'telephone', 'telephone_number'];
//        $headerRow = [0=>'ref',1=>0,2=>'mpn',3=>'field10'];
//        foreach ($arrayPhone as $phoneValue) {
//                if(in_array($phoneValue,$headerRow,true)){
//                    $PhoneColumn = $phoneValue;
//                    $fail = 0;
//                    break;
//                }else{
//                    $fail = 1;
//                }
//            }
//            echo $PhoneColumn;
//        Artisan::call('command:O2FreeSimSFTPTest'); 
        exit;
//        $phone = '449780025056';
//        $replace = 0;
//        echo get_phone_numbers($phone, $replace);
//        exit;
//        calculate_FTE_3005('2019-01-20 00:00:00','2019-01-25 23:59:59') ;
//        echo get_name();
//        Artisan::call('command:SwitchExpertSales');  
//        Artisan::call('command:OptinAll');  
//        Artisan::call('command:CampaignReportMainGraph'); 
//        pr($_SERVER['HTTP_HOST']);
//        
//        echo str_pad(1000, 10, '0', STR_PAD_LEFT); 
//        Artisan::call('command:CampaignReportOmniGraph'); 
//        $data = DB::connection('OmniDialer')
//                                            ->table('outbound_log')
//                                            ->where('campaign_id',1001)
//                                            ->where('call_date','>=','2019-02-13 00:00:00')
//                                            ->limit(1)
//                                            ->get();
//        $data = DB::connection('MainDialer')
//                        ->table('user_groups')
//                        ->where('allowed_campaigns', 'LIKE', '%3003%')
////                        ->where('group_name', 'like', '%' . $group . '%')
//                        ->pluck('user_group','group_name')->toArray();
//        pr($data);
        exit;
//        $data = DB::connection('Intelling')->table('SEMobile_Sales')->where('salemsorder','MS-511500899')->get();
//        pr($data);
//        exit;
//$data = shell_exec('/home/file_encry.sh');
//pr($data);
//exit;
//        $data = \App\Model\UTGAPI\O2ReturnProcessFile::find(1);
//        
//        $content = $data->File_name.'.dat.gz.gpg|'.$data->bussinessdate.'|'.$data->extractdate.'|9892|001|001|'.$data->File_name.'.dat.gz.gpgEOF|1';
//        
//        $fp = fopen("/var/www/html/cron/storage/Automation/O2ReturnProcess/mfst/text.txt", "w");
//        fwrite($fp, $content);
//        fclose($fp);
        return view('dashboard');
       
    }
    
    public function dashboard() {
          
        return view('dashboard');
       
    }

}
