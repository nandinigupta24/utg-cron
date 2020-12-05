<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Excel;
use Storage;
use App\Model\UTGAPI\O2ReturnProcessData;
use App\Model\UTGAPI\O2ReturnProcessFile;
use Mail;
class O2ReturnProcessAutomation extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:O2ReturnProcessAutomation';

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
        ini_set('max_execution_time', 7200);
//        $start = Carbon::today()->startOfDay()->toDateTimeString();
//        $end = Carbon::today()->endOfDay()->toDateTimeString();
        $date = Carbon::today()->toDateString();
        $start = $date.' 08:00:00';
        $end = $date.' 20:00:00'; 
        
        $query = "select

IF(entry_date IS NULL, '', '') AS Customer_ID,
IF(entry_date IS NULL, '', '') AS Account_Id,
L.lead_id,
L.vendor_lead_code,
L.status,
L.entry_date,
L.phone_number,
P.custom_3 as Subscriber_ID,
P.custom_4 as Campaign_Code,
P.custom_5 as Cell_Code,
P.custom_7 as Treatment_Code,
date_format(L.last_local_call_time,'%Y%m%d%H%i%s') as Response_Date_Time,
(CASE
when L.status in ('A','AA','AB','ADC','ADCT','AFAX','AFTHRS','AL','AM','B','CHU','CPDATB','CPDB','CPDERR','CPDINV','CPDNA','CPDREJ','CPDSI',
'CPDSNC','CPDSR','CPDSUA','CPDSUK','CPDSV','CPDUK','DC','DECAD','DECB','DECCC','DL','DROP','ERI','FAX','INCALL','IVRXFR','LRERR',
'LSMERG','MAXCAL','MLINAT','N','NA','NANQUE','NEW','PDROP','PM','QCFAIL','QUEUE','QVMAIL','RLEDUP','TIMEOT',
'XDROP','DTEST','FAILT') then 25
when L.status in (' Busin','CALLBK','CBHOLD','DNC','DNCC','DNCL','GCALLB','LB','NOP','OPTOUT','PU','TPS','WD','WEBDOW',
'WRNNUM','DNOI','INCSC','Busin') then 24
when L.status in ('ACOnly','HS','SALE','TCAC','TCOnly','TSALE') then 28
when L.status in ('LODD','LU','MAE','NBD','NI','CBDNI','IC','InCon','NONLLU','NOUK','PPNG','WHS','REDPA') then 29
when L.status in ('REFER','UA','UTMUR') then 27
when L.status in ('O2C') then 26
ELSE ''
    END) AS ResponseStatus_Code,   
IF(entry_date IS NULL, 'T', 'T') Response_Channel,
IF(entry_date IS NULL, '', '') AS Link_ID,
IF(entry_date IS NULL, '', '') AS Link_Name,
IF(entry_date IS NULL, '', '') AS Sub_Id,
IF(entry_date IS NULL, '', '') AS Sub_Id_Description,
IF(entry_date IS NULL, '', '') AS Response_Text,
(case
when status in ('CALLBK','CBHOLD','GCALLB','UA','INCSC') then 'A02'
when status in ('LB') then 'A03'
when status in ('WEBDOW','WD','WRNNUM') then 'A06'
when status in ('PU','CHU','AFTHRS') then 'A09'
when status in ('CPDB') then 'C03'
when status in (' Busin','DECAD','DL','LODD','LU','MAE','NBD','NOP','QUEUE','IC','InCon','NONLLU','NOUK','O2C','PPNG','UTMUR','WHS','Busin') then 'F07'
when status in ('DROP','ERI','MAXCAL','NANQUE','PM','QCFAIL','TIMEOT','XDROP','FAILT') then 'A10'
when status in ('AB','B') then 'B01'
when status in ('FAX','AFAX') then 'B02'
when status in ('A','AA','AL','AM') then 'B03'
when status in ('NEW','NA','N','QVMAIL','REFER') then 'B04'
when status in ('CPDATB','CPDB','CPDERR','CPDINV','CPDNA','CPDREJ','CPDSUA','CPDSI','CPDSNC','CPDSR','CPDSUK','CPDSV','CPDUK','RLEDUP','DECCC','DC','ADC','ADCT','IVRXFR','LRERR','PDROP','INCALL',
'LSMERG','MLINAT','DTEST') then 'B05'
when status in ('DECB') then 'F10'
when status in ('HS','SALE','ACOnly','TCAC','TCOnly','TSALE') then 'E01'
when status in ('NI','CBDNI') then 'F03'
when status in ('DNC','OPTOUT','DNCC','DNCL','DNOI') then 'C03'
when status in ('TPS') then 'D06'
when status in ('REDPA') then 'F11'
Else ''
END) as ResponseReason_Code,
(case
when L.status in ('ACOnly') then 'SALE_ACOnly'
when L.status in ('HS') then 'Handset_Sale'
when L.status in ('SALE') then 'sale'
when L.status in ('TCAC') then 'SALE-TC&AC'
when L.status in ('TCOnly') then 'SALE-TCOnly'
when L.status in ('TSALE') then 'Tablet_Sale'
Else ''
END) AS Product_Offer_Code,
IF(entry_date IS NULL, '', '') AS Forward_Count,
IF(entry_date IS NULL, '', '') AS Product_Offer_Desc,
IF(entry_date IS NULL, '', '') AS Responding_MPN,
IF(entry_date IS NULL, '', '') AS Product_Source_System,
P.custom_2 as custom_1,
P.custom_6 as custom_2,
P.custom_9 as custom_3,
P.custom_11 as custom_4,
P.custom_13 as custom_5,
P.custom_2 as CustId
from list L
JOIN custom_fields_data P
on L.lead_id=P.lead_id
where L.list_id = '1330'  
and L.last_local_call_time !='NULL'
#and P.custom_4 = 5636
and P.custom_7 != ''
and L.last_local_call_time between '" . $start . "' and '" . $end . "'";

        $ExtractDateTime = date('YmdHis');
        $BusinessDateTime = date('YmdHis', strtotime($end));
        $data = DB::connection('OmniDialer')->select($query);
        
        $queryList3001 = get_query_O2ReturnProcess(3001, $start, $end);
        $data3001 = DB::connection('OmniDialer')->select($queryList3001);
        
        $queryList3005 = get_query_O2ReturnProcess(3005, $start, $end);
        $data3005 = DB::connection('OmniDialer')->select($queryList3005);
        
        $totalArray = [];
        $totalArray[1330] = $data;
        $totalArray[3001] = $data3001;
        $totalArray[3005] = $data3005;
        
//        $totalCountArray = [];
        
        $PostArray = [];
        
        $Count = 0;
        $Failed = 0;
        foreach($totalArray as $key=>$val){
            $SuccessResponse = 0;
        foreach($val as $value){
            $O2ReturnProcessData = new O2ReturnProcessData();
            if($key == 1330){
                $response = O2ReturnProcessValidation1330($value);
                $O2ReturnProcessData->Customer_Id = @$value->CustId;
                $O2ReturnProcessData->Account_Id = $value->vendor_lead_code;
                $O2ReturnProcessData->Response_Channel = 'T';
                $O2ReturnProcessData->Link_ID = '';
                $O2ReturnProcessData->Link_Name = '';
                $O2ReturnProcessData->Sub_Id = '';
                $O2ReturnProcessData->Sub_Id_Description = '';
                $O2ReturnProcessData->Response_Text = '';
                $O2ReturnProcessData->Forward_Count = '';
                $O2ReturnProcessData->Product_Offer_Description = '';
                $O2ReturnProcessData->Responding_MPN = @$value->phone_number;
                $O2ReturnProcessData->Product_Source_System = '';
            }elseif($key == 3001){
                $response = O2ReturnProcessValidation3001($value);
                $O2ReturnProcessData->Customer_Id = $value->Customer_ID;
                $O2ReturnProcessData->Account_Id = $value->vendor_lead_code;
                $O2ReturnProcessData->Response_Channel = 'T';
                $O2ReturnProcessData->Link_ID = '';
                $O2ReturnProcessData->Link_Name = '';
                $O2ReturnProcessData->Sub_Id = '';
                $O2ReturnProcessData->Sub_Id_Description = '';
                $O2ReturnProcessData->Response_Text = '';
                $O2ReturnProcessData->Forward_Count = '';
                $O2ReturnProcessData->Product_Offer_Description = '';
                $O2ReturnProcessData->Responding_MPN = @$value->phone_number;
                $O2ReturnProcessData->Product_Source_System = '';
                
            }else{
                $response = O2ReturnProcessValidation($value);
                $O2ReturnProcessData->Customer_Id = @$value->Customer_ID;
                $O2ReturnProcessData->Account_Id = $value->vendor_lead_code;
                $O2ReturnProcessData->Response_Channel = @$value->Response_Channel;
                $O2ReturnProcessData->Link_ID = @$value->Link_ID;
                $O2ReturnProcessData->Link_Name = @$value->Link_Name;
                $O2ReturnProcessData->Sub_Id = @$value->Sub_Id;
                $O2ReturnProcessData->Sub_Id_Description = @$value->Sub_Id_Description;
                $O2ReturnProcessData->Response_Text = @$value->Response_Text;
                $O2ReturnProcessData->Forward_Count = @$value->Forward_Count;
                $O2ReturnProcessData->Product_Offer_Description = @$value->Product_Offer_Desc;
                $O2ReturnProcessData->Responding_MPN = @$value->phone_number;
                $O2ReturnProcessData->Product_Source_System = @$value->Product_Source_System;
            }
            
            $O2ReturnProcessData->Campaign_Code = @$value->Campaign_Code;
            $O2ReturnProcessData->Cell_Code = @$value->Cell_Code;
            $O2ReturnProcessData->Treatment_Code = @$value->Treatment_Code;
            $O2ReturnProcessData->Response_Date_Time = @$value->Response_Date_Time;
            $O2ReturnProcessData->Response_Status_Code = @$value->ResponseStatus_Code;
            
            $O2ReturnProcessData->Response_Reason_Code = @$value->ResponseReason_Code;
            $O2ReturnProcessData->Product_Offering = @$value->Product_Offer_Code;
            $O2ReturnProcessData->lead_id = @$value->lead_id;
            $O2ReturnProcessData->data_loaded = @$value->entry_date;
            $O2ReturnProcessData->call_Outcome = @$value->status;
            
            $O2ReturnProcessData->Custom_Field_1 = '';
            $O2ReturnProcessData->Custom_Field_2 = '';
            $O2ReturnProcessData->Custom_Field_3 = '';
            $O2ReturnProcessData->Custom_Field_4 = '';
            $O2ReturnProcessData->Custom_Field_5 = '';
            $O2ReturnProcessData->list_id = $key;
            
            if(!empty($response) && count($response) > 0){
                $Failed++;
                $O2ReturnProcessData->status = 'cancel';
                $O2ReturnProcessData->response = serialize($response);
            }else{
                $Count++;
                if($key == 1330){
                $PostArray[$Count]['Customer_ID'] = @$value->CustId;
                $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                $PostArray[$Count]['Treatment_Code'] = $value->Treatment_Code;
                $PostArray[$Count]['Response_Date_Time'] = $value->Response_Date_Time;
                $PostArray[$Count]['ResponseStatus_Code'] = $value->ResponseStatus_Code;
                $PostArray[$Count]['Response_Channel'] = 'T';
                $PostArray[$Count]['Link_ID'] = '';
                $PostArray[$Count]['Link_Name'] = '';
                $PostArray[$Count]['Sub_Id'] = '';
                $PostArray[$Count]['Sub_Id_Description'] = '';
                $PostArray[$Count]['Response_Text'] = '';
                $PostArray[$Count]['ResponseReason_Code'] = @$value->ResponseReason_Code;
                $PostArray[$Count]['Product_Offer_Code'] = '';
                $PostArray[$Count]['Forward_Count'] = '';
                $PostArray[$Count]['Product_Offer_Desc'] = '';
                $PostArray[$Count]['Responding_MPN'] = $value->phone_number;
                $PostArray[$Count]['Product_Source_System'] = '';
            }elseif($key == 3001){
                $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                $PostArray[$Count]['Treatment_Code'] = $value->Treatment_Code;
                $PostArray[$Count]['Response_Date_Time'] = $value->Response_Date_Time;
                $PostArray[$Count]['ResponseStatus_Code'] = $value->ResponseStatus_Code;
                $PostArray[$Count]['Response_Channel'] = 'T';
                $PostArray[$Count]['Link_ID'] = '';
                $PostArray[$Count]['Link_Name'] = '';
                $PostArray[$Count]['Sub_Id'] = '';
                $PostArray[$Count]['Sub_Id_Description'] = '';
                $PostArray[$Count]['Response_Text'] = '';
                $PostArray[$Count]['ResponseReason_Code'] = @$value->ResponseReason_Code;
                $PostArray[$Count]['Product_Offer_Code'] = '';
                $PostArray[$Count]['Forward_Count'] = '';
                $PostArray[$Count]['Product_Offer_Desc'] = '';
                $PostArray[$Count]['Responding_MPN'] = $value->phone_number;
                $PostArray[$Count]['Product_Source_System'] = '';
            }else{
                $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                $PostArray[$Count]['Treatment_Code'] = $value->Treatment_Code;
                $PostArray[$Count]['Response_Date_Time'] = $value->Response_Date_Time;
                $PostArray[$Count]['ResponseStatus_Code'] = $value->ResponseStatus_Code;
                $PostArray[$Count]['Response_Channel'] = $value->Response_Channel;
                $PostArray[$Count]['Link_ID'] = $value->Link_ID;
                $PostArray[$Count]['Link_Name'] = $value->Link_Name;
                $PostArray[$Count]['Sub_Id'] = $value->Sub_Id;
                $PostArray[$Count]['Sub_Id_Description'] = $value->Sub_Id_Description;
                $PostArray[$Count]['Response_Text'] = $value->Response_Text;
                $PostArray[$Count]['ResponseReason_Code'] = $value->ResponseReason_Code;
                $PostArray[$Count]['Product_Offer_Code'] = $value->Product_Offer_Code;
                $PostArray[$Count]['Forward_Count'] = $value->Forward_Count;
                $PostArray[$Count]['Product_Offer_Desc'] = $value->Product_Offer_Desc;
                $PostArray[$Count]['Responding_MPN'] = $value->phone_number;
                $PostArray[$Count]['Product_Source_System'] = $value->Product_Source_System;
            }
                $PostArray[$Count]['custom_1'] = '';
                $PostArray[$Count]['custom_2'] = '';
                $PostArray[$Count]['custom_3'] = '';
                $PostArray[$Count]['custom_4'] = '';
                $PostArray[$Count]['custom_5'] = '';
                $O2ReturnProcessData->status = 'success';
            }
            if ($O2ReturnProcessData->save()) {

            }
        }
        }
        
        
        $fileName = 'RESPONSE_INTELLING_001_' . $ExtractDateTime . '_' . $BusinessDateTime . '_001';
        Excel::create($fileName, function($excel) use($PostArray) {
            $excel->setTitle('O2 Return Process');
            $excel->sheet('O2 Return Process', function($sheet) use($PostArray) {
                $sheet->setOrientation('landscape');
                $sheet->fromArray($PostArray);
            });
        })->store('csv', storage_path('Automation/O2ReturnProcess/import/1330'), true);
        
        
        /*Process for Save TEXT file MFST*/
//        $content = $fileName.'.dat.gz.gpg|'.$BusinessDateTime.'|'.$ExtractDateTime.'|'.$Count.'|001|001|'.$fileName.'.dat.gz.gpg
//EOF|1';
        $content = $fileName.'.dat.gz.gpg|'.$BusinessDateTime.'|'.$ExtractDateTime.'|'.$Count.'|001|001|'.$fileName.'.dat.gz.gpg
EOF|1
';
        $mstfileName = 'RESPONSE_INTELLING_002_'.$ExtractDateTime.'_'.$BusinessDateTime.'_001';
        $fp = fopen("/var/www/html/cron/storage/Automation/O2ReturnProcess/mfst/".$mstfileName.".txt", "w");
        fwrite($fp, $content);
        fclose($fp);
        
        /*Process for SAVE data in TABLE*/
        $O2ReturnProcessFile = new O2ReturnProcessFile();
        $O2ReturnProcessFile->File_name = $fileName;
        $O2ReturnProcessFile->mfst_file_name = $mstfileName;
        $O2ReturnProcessFile->records = $Count;
        $O2ReturnProcessFile->failed = $Failed;
        $O2ReturnProcessFile->list_id = '1330,3001,3005';
        $O2ReturnProcessFile->bussinessdate = $BusinessDateTime;
        $O2ReturnProcessFile->extractdate = $ExtractDateTime;
        if ($O2ReturnProcessFile->save()) {
            
        }
        
        
        shell_exec('/home/file_conversion.sh');
        shell_exec('/home/file_conversion_mfst.sh');
        
        
        /*SEND to SFTP*/
        $server = '158.230.101.193';
//        $server = '10.68.120.59';
        $serverPort = 22;
        $connection = ssh2_connect($server, $serverPort);
        $serverUser = 'int2nuc';
//        $serverUser = 'root';
        $serverPassword = 'qwerty123';
//        $serverPassword = '16IndiaGeeksUK';
        $data = O2ReturnProcessFile::orderBy('id', 'desc')->first();
        $fileName = $data->File_name;
        $MSTfileName = $data->mfst_file_name;

        if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
            if (!empty($fileName)) {
                ssh2_scp_send($connection, "/var/www/html/cron/storage/Automation/O2ReturnProcess/export/1330/" . $fileName . '.dat.gz.gpg', "/export/home/int2nuc/export/home/int2nuc/received/" . $fileName . '.dat.gz.gpg');
//                ssh2_scp_send($connection, "/var/www/html/cron/storage/Automation/O2ReturnProcess/export/1330/" . $fileName . '.dat.gz.gpg', "/home/Ankit/" . $fileName . '.dat.gz.gpg');
            }
            if (!empty($MSTfileName)) {
                ssh2_scp_send($connection, "/var/www/html/cron/storage/Automation/O2ReturnProcess/export/1330/" . $MSTfileName . '.mfst', "/export/home/int2nuc/export/home/int2nuc/received/" . $MSTfileName . '.mfst');
//                ssh2_scp_send($connection, "/var/www/html/cron/storage/Automation/O2ReturnProcess/export/1330/" . $MSTfileName . '.mfst', "/home/Ankit/" . $MSTfileName . '.mfst');
            }
        }


        $arrayMailTo = ['apanwar@usethegeeks.co.uk','Andy.Hughes@intelling.co.uk','Nicola.Sharrock@intelling.co.uk',env('DIALER_TEAM_EMAIL'),'Sarah.Berry@intelling.co.uk'];
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.o2_return_process';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'O2 Return Process (' . date('Y-m-d') . ')';
        $mail_data['data'] = @$data;

        Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                    $m->from($mail_data['from'], 'API Reports');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'API Reports');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                });
        if (!empty($fileName)) {
            unlink("/var/www/html/cron/storage/Automation/O2ReturnProcess/export/1330/" . $fileName . '.dat.gz.gpg');
        }
        if (!empty($MSTfileName)) {
            unlink("/var/www/html/cron/storage/Automation/O2ReturnProcess/export/1330/" . $MSTfileName . '.mfst');
        }
    }

}
