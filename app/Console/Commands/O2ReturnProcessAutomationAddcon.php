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
class O2ReturnProcessAutomationAddcon extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:O2ReturnProcessAutomationAddcon {date?}';

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
        if($date = $this->argument('date')) {
          $start = date('Y-m-d 00:00:00', strtotime($date.' -30 days'));
          $end = date('Y-m-d 23:59:59', strtotime($date.' -30 days'));
          $CurrentStart = date('Y-m-d 00:00:00', strtotime($date));
          $CurrentEnd = date('Y-m-d 23:59:59', strtotime($date));
        }else {
          $start = Carbon::now()->subDays(30)->startOfDay();
          $end = Carbon::now()->subDays(30)->endOfDay();
          $CurrentStart = Carbon::now()->startOfDay();
          $CurrentEnd = Carbon::now()->endOfDay();
        }


        $hideStatusArray = ['B','DTEST','FAILT','INCALL','INCSC','N','PDROP','QUEUE','PU','DNCL','DNCC','MAXCAL','NANQUE','PM','QCFAIL','TIMEOT','AL','AM','NEW','QVMAIL','CPDATB','CPDB',
            'CPDERR','CPDINV','CPDNA','CPDREJ','CPDSI','CPDSNC','CPDSR','CPDSUA','CPDSUK','CPDSV','CPDUK','IVRXFR','LRERR','LSMERG','MLINAT','RLEDUP'];
         $HideArrayStatus = implode("','",$hideStatusArray);

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
when L.status in ('CBHOLD','CALLBK','GCALLB','GCALLBK','LB','ALTNUM','AltNo','WD','WRRNU','WRNNUM','CHU','DROP','ERI') then 24

when L.status in ('ADC','ADCT','AB','B','AFAX','A','AA','N','NA','DL','DC','BLOCK') then 25

when L.status in ('TPS','DNC','OPTOUT','OPTOU') then 26

when L.status in ('ACOnly','SALE','HS','REFSAL','SALEDE','SALEDEP','SIMODE','TCAC','TCOnly','TSALE','REFER') then 28

when L.status in ('WEBDC','NI','DNOFS','CBDNI','CBDNI','CNAF','WHS','IC','HUC','DECAD','DECB','DECB','LODD','LU','MAE','NBD','NOP','ALREAD','O2C','PPNG','UTMUR',

'NoneUK','HWCP','NNC','REFDEC','DECCC','REDPA','ADINFO') then 29

when L.status in ('Busin','UA') then 27

ELSE 'NULL'

   END) AS ResponseStatus_Code,

IF(entry_date IS NULL, 'T', 'T') Response_Channel,

IF(entry_date IS NULL, '', '') AS Link_ID,

IF(entry_date IS NULL, '', '') AS Link_Name,

IF(entry_date IS NULL, '', '') AS Sub_Id,

IF(entry_date IS NULL, '', '') AS Sub_Id_Description,

IF(entry_date IS NULL, '', '') AS Response_Text,

(case

when status in ('CBHOLD','GCALLB','GCALLBK','CALLBK') then 'A02'

when status in ('LB','ALTNUM','AltNo') then 'A03'

#when status in ('ADC','ADCT') then 'A05'

when status in ('WD','WRNNU','WRNNUM') then 'A06'

when status in ('CHU') then 'A09'

when status in ('DROP','ERI') then 'A10'

when status in ('AB','B') then 'B01'

when status in ('AFAX') then 'B02'

when status in ('A','AA') then 'B03'

when status in ('NA','N') then 'B04'

when status in ('DL') then 'B05'

when status in ('DC','BLOCK') then 'B06'

when status in ('TPS','DNC','OPTOUT','OPTOU') then 'C03'

when status in ('UA') then 'D06'

when status in ('Busin') then 'D02'

when status in ('REFER') then 'E06'

when status in ('ACOnly','SALE','HS','REFSAL','SALEDE','SALEDEP','SIMODE','TCAC','TCOnly','TSALE') then 'E01'

when status in ('NI','DNOFS','CBDNI','CBDNI','CNAF','WHS','HUC') then 'F03'

when status in ('IC','ALREAD','O2C') then 'F05'

when status in ('WEBDC','LODD','LU','MAE','NBD','NOP','PPNG','UTMUR','NoneUK','HWCP','NNC') then 'F07'

when status in ('REFDEC') then 'F08'

when status in ('DECAD','DECB','DECB','DECCC','ADINFO') then 'F10'

when status in ('REDPA') then 'F11'

Else 'NULL'

END) as ResponseReason_Code,

(case

when L.status in ('ACOnly') then 'SALE_ACOnly'

when L.status in ('HS') then 'Handset_Sale'

when L.status in ('SALE') then 'sale'

when L.status in ('TCAC') then 'SALE-TC&AC'

when L.status in ('TCOnly') then 'SALE-TCOnly'

when L.status in ('TSALE') then 'Tablet_Sale'

when L.status in ('REFSAL') then 'Referral_Sale'

when L.status in ('SALEDE') then 'Sale_1st_Deposit'

when L.status in ('SALEDEP') then 'Sale_1st_Deposit'

when L.status in ('SIMODE') then 'SIM_Only_Deposit'

when L.status in ('REFER') then 'Referral_Callback '

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

P.custom_2 as CustId,

L.list_id

from list L

#JOIN custom_fields_data P

#on L.lead_id=P.lead_id

#where L.list_id IN (1330,3001,3005)

#and L.last_local_call_time !='NULL'

#and P.custom_4 = 5636
join lists LS
on L.list_id = LS.list_id
JOIN custom_fields_data P
on L.lead_id=P.lead_id
where LS.campaign_id in (1330,3001,3002,3004,3005)

and P.custom_7 != ''

and L.entry_date between '".$start."' and '".$end."' AND L.vendor_lead_code != '' AND L.status NOT IN ('".$HideArrayStatus."')";

        $ExtractDateTime = date('YmdHis');
        $BusinessDateTime = date('YmdHis', strtotime($CurrentEnd));

        $data = DB::connection('OmniDialer')->select($query);

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
when L.status in ('CBHOLD','CALLBK','GCALLB','GCALLBK','LB','ALTNUM','AltNo','WD','WRRNU','WRNNUM','CHU','DROP','ERI') then 24

when L.status in ('ADC','ADCT','AB','B','AFAX','A','AA','N','NA','DL','DC','BLOCK') then 25

when L.status in ('TPS','DNC','OPTOUT','OPTOU') then 26

when L.status in ('ACOnly','SALE','HS','REFSAL','SALEDE','SALEDEP','SIMODE','TCAC','TCOnly','TSALE','REFER') then 28

when L.status in ('WEBDC','NI','DNOFS','CBDNI','CBDNI','CNAF','WHS','IC','HUC','DECAD','DECB','DECB','LODD','LU','MAE','NBD','NOP','ALREAD','O2C','PPNG','UTMUR',

'NoneUK','HWCP','NNC','REFDEC','DECCC','REDPA','ADINFO') then 29

when L.status in ('Busin','UA') then 27

ELSE 'NULL'

   END) AS ResponseStatus_Code,

IF(entry_date IS NULL, 'T', 'T') Response_Channel,

IF(entry_date IS NULL, '', '') AS Link_ID,

IF(entry_date IS NULL, '', '') AS Link_Name,

IF(entry_date IS NULL, '', '') AS Sub_Id,

IF(entry_date IS NULL, '', '') AS Sub_Id_Description,

IF(entry_date IS NULL, '', '') AS Response_Text,

(case

when status in ('CBHOLD','GCALLB','GCALLBK','CALLBK') then 'A02'

when status in ('LB','ALTNUM','AltNo') then 'A03'

#when status in ('ADC','ADCT') then 'A05'

when status in ('WD','WRNNU','WRNNUM') then 'A06'

when status in ('CHU') then 'A09'

when status in ('DROP','ERI') then 'A10'

when status in ('AB','B') then 'B01'

when status in ('AFAX') then 'B02'

when status in ('A','AA') then 'B03'

when status in ('NA','N') then 'B04'

when status in ('DL') then 'B05'

when status in ('DC','BLOCK') then 'B06'

when status in ('TPS','DNC','OPTOUT','OPTOU') then 'C03'

when status in ('UA') then 'D06'

when status in ('Busin') then 'D02'

when status in ('REFER') then 'E06'

when status in ('ACOnly','SALE','HS','REFSAL','SALEDE','SALEDEP','SIMODE','TCAC','TCOnly','TSALE') then 'E01'

when status in ('NI','DNOFS','CBDNI','CBDNI','CNAF','WHS','HUC') then 'F03'

when status in ('IC','ALREAD','O2C') then 'F05'

when status in ('WEBDC','LODD','LU','MAE','NBD','NOP','PPNG','UTMUR','NoneUK','HWCP','NNC') then 'F07'

when status in ('REFDEC') then 'F08'

when status in ('DECAD','DECB','DECB','DECCC','ADINFO') then 'F10'

when status in ('REDPA') then 'F11'

Else 'NULL'

END) as ResponseReason_Code,

(case

when L.status in ('ACOnly') then 'SALE_ACOnly'

when L.status in ('HS') then 'Handset_Sale'

when L.status in ('SALE') then 'sale'

when L.status in ('TCAC') then 'SALE-TC&AC'

when L.status in ('TCOnly') then 'SALE-TCOnly'

when L.status in ('TSALE') then 'Tablet_Sale'

when L.status in ('REFSAL') then 'Referral_Sale'

when L.status in ('SALEDE') then 'Sale_1st_Deposit'

when L.status in ('SALEDEP') then 'Sale_1st_Deposit'

when L.status in ('SIMODE') then 'SIM_Only_Deposit'

when L.status in ('REFER') then 'Referral_Callback '

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

P.custom_2 as CustId,

L.list_id,

L.called_count,

L.last_local_call_time

from list L
join lists LS
on L.list_id = LS.list_id
JOIN custom_fields_data P
on L.lead_id=P.lead_id
where LS.campaign_id in (1330,3001,3002,3004,3005)

and L.last_local_call_time !='NULL'

#and P.custom_4 = 5636

and P.custom_7 != ''

and (L.status in ('TPS','DNC','OPTOUT','OPTOU') OR L.called_count = 8)

and L.last_local_call_time between '".$CurrentStart."' and '".$CurrentEnd."' AND L.vendor_lead_code != '' AND L.status NOT IN ('".$HideArrayStatus."')";

        $CurrentDateData = DB::connection('OmniDialer')->select($query);

        $totalArray = [];
        $totalArray[3005] = $data;

        $PostArray = [];

        $Count = 0;
        $Failed = 0;
        $SuccessResponse = 0;
        $PhoneNumberArray = [];
        foreach($CurrentDateData as $value){
            $O2ReturnProcessData = new O2ReturnProcessData();
            $response = O2ReturnProcessValidationNew($value);

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
            $O2ReturnProcessData->list_id = @$value->list_id;
            if(!empty($response) && count($response) > 0){
                $Failed++;
                $O2ReturnProcessData->status = 'cancel';
                $O2ReturnProcessData->response = serialize($response);
            }else{
                $PhoneNumberArray[] = $value->phone_number;
                $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = str_pad($value->Treatment_Code, 9, '0', STR_PAD_LEFT);
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
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';
            }

            if ($O2ReturnProcessData->save()) {

            }

        }


        foreach($totalArray as $key=>$val){

        foreach($val as $value){
            if(!empty($value->ResponseReason_Code) && $value->ResponseReason_Code == 'NULL'){
                continue;
            }
            if(!empty($value->ResponseStatus_Code) && $value->ResponseStatus_Code == 'NULL'){
                continue;
            }

            $O2ReturnProcessData = new O2ReturnProcessData();
            $response = O2ReturnProcessValidationNew($value);

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
            $O2ReturnProcessData->list_id = @$value->list_id;

            if(!empty($response) && count($response) > 0){
                $Failed++;
                $O2ReturnProcessData->status = 'cancel';
                $O2ReturnProcessData->response = serialize($response);
            }else{
                if(in_array($value->phone_number,$PhoneNumberArray)){
                    continue;
                }
                if($value->ResponseStatus_Code == 24){
                    $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = str_pad($value->Treatment_Code, 9, '0', STR_PAD_LEFT);
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
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';

                }

                if($value->ResponseStatus_Code == 25){
                    $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = str_pad($value->Treatment_Code, 9, '0', STR_PAD_LEFT);
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
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';

                }

                if($value->ResponseStatus_Code == 26){
                    $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = str_pad($value->Treatment_Code, 9, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Response_Date_Time'] = $value->Response_Date_Time;
                    $PostArray[$Count]['ResponseStatus_Code'] = 24;
                    $PostArray[$Count]['Response_Channel'] = $value->Response_Channel;
                    $PostArray[$Count]['Link_ID'] = $value->Link_ID;
                    $PostArray[$Count]['Link_Name'] = $value->Link_Name;
                    $PostArray[$Count]['Sub_Id'] = $value->Sub_Id;
                    $PostArray[$Count]['Sub_Id_Description'] = $value->Sub_Id_Description;
                    $PostArray[$Count]['Response_Text'] = $value->Response_Text;
                    $PostArray[$Count]['ResponseReason_Code'] = 'A08';
                    $PostArray[$Count]['Product_Offer_Code'] = '';
                    $PostArray[$Count]['Forward_Count'] = $value->Forward_Count;
                    $PostArray[$Count]['Product_Offer_Desc'] = $value->Product_Offer_Desc;
                    $PostArray[$Count]['Responding_MPN'] = $value->phone_number;
                    $PostArray[$Count]['Product_Source_System'] = $value->Product_Source_System;
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';



                    $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = str_pad($value->Treatment_Code, 9, '0', STR_PAD_LEFT);
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
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';


                }

                if($value->ResponseStatus_Code == 27){
                    $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = str_pad($value->Treatment_Code, 9, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Response_Date_Time'] = $value->Response_Date_Time;
                    $PostArray[$Count]['ResponseStatus_Code'] = 24;
                    $PostArray[$Count]['Response_Channel'] = $value->Response_Channel;
                    $PostArray[$Count]['Link_ID'] = $value->Link_ID;
                    $PostArray[$Count]['Link_Name'] = $value->Link_Name;
                    $PostArray[$Count]['Sub_Id'] = $value->Sub_Id;
                    $PostArray[$Count]['Sub_Id_Description'] = $value->Sub_Id_Description;
                    $PostArray[$Count]['Response_Text'] = $value->Response_Text;
                    $PostArray[$Count]['ResponseReason_Code'] = 'A08';
                    $PostArray[$Count]['Product_Offer_Code'] = '';
                    $PostArray[$Count]['Forward_Count'] = $value->Forward_Count;
                    $PostArray[$Count]['Product_Offer_Desc'] = $value->Product_Offer_Desc;
                    $PostArray[$Count]['Responding_MPN'] = $value->phone_number;
                    $PostArray[$Count]['Product_Source_System'] = $value->Product_Source_System;
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';



                    $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = str_pad($value->Treatment_Code, 9, '0', STR_PAD_LEFT);
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
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';


                }

                if($value->ResponseStatus_Code == 29){
                   $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = str_pad($value->Treatment_Code, 9, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Response_Date_Time'] = $value->Response_Date_Time;
                    $PostArray[$Count]['ResponseStatus_Code'] = 24;
                    $PostArray[$Count]['Response_Channel'] = $value->Response_Channel;
                    $PostArray[$Count]['Link_ID'] = $value->Link_ID;
                    $PostArray[$Count]['Link_Name'] = $value->Link_Name;
                    $PostArray[$Count]['Sub_Id'] = $value->Sub_Id;
                    $PostArray[$Count]['Sub_Id_Description'] = $value->Sub_Id_Description;
                    $PostArray[$Count]['Response_Text'] = $value->Response_Text;
                    $PostArray[$Count]['ResponseReason_Code'] = 'A08';
                    $PostArray[$Count]['Product_Offer_Code'] = '';
                    $PostArray[$Count]['Forward_Count'] = $value->Forward_Count;
                    $PostArray[$Count]['Product_Offer_Desc'] = $value->Product_Offer_Desc;
                    $PostArray[$Count]['Responding_MPN'] = $value->phone_number;
                    $PostArray[$Count]['Product_Source_System'] = $value->Product_Source_System;
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';


                    $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = str_pad($value->Treatment_Code, 9, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Response_Date_Time'] = $value->Response_Date_Time;
                    $PostArray[$Count]['ResponseStatus_Code'] = 26;
                    $PostArray[$Count]['Response_Channel'] = $value->Response_Channel;
                    $PostArray[$Count]['Link_ID'] = $value->Link_ID;
                    $PostArray[$Count]['Link_Name'] = $value->Link_Name;
                    $PostArray[$Count]['Sub_Id'] = $value->Sub_Id;
                    $PostArray[$Count]['Sub_Id_Description'] = $value->Sub_Id_Description;
                    $PostArray[$Count]['Response_Text'] = $value->Response_Text;
                    $PostArray[$Count]['ResponseReason_Code'] = 'C02';
                    $PostArray[$Count]['Product_Offer_Code'] = '';
                    $PostArray[$Count]['Forward_Count'] = $value->Forward_Count;
                    $PostArray[$Count]['Product_Offer_Desc'] = $value->Product_Offer_Desc;
                    $PostArray[$Count]['Responding_MPN'] = $value->phone_number;
                    $PostArray[$Count]['Product_Source_System'] = $value->Product_Source_System;
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';

                    $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = str_pad($value->Treatment_Code, 9, '0', STR_PAD_LEFT);
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
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';

                }

                if($value->ResponseStatus_Code == 28){
                    $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = str_pad($value->Treatment_Code, 9, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Response_Date_Time'] = $value->Response_Date_Time;
                    $PostArray[$Count]['ResponseStatus_Code'] = 24;
                    $PostArray[$Count]['Response_Channel'] = $value->Response_Channel;
                    $PostArray[$Count]['Link_ID'] = $value->Link_ID;
                    $PostArray[$Count]['Link_Name'] = $value->Link_Name;
                    $PostArray[$Count]['Sub_Id'] = $value->Sub_Id;
                    $PostArray[$Count]['Sub_Id_Description'] = $value->Sub_Id_Description;
                    $PostArray[$Count]['Response_Text'] = $value->Response_Text;
                    $PostArray[$Count]['ResponseReason_Code'] = 'A08';
                    $PostArray[$Count]['Product_Offer_Code'] = '';
                    $PostArray[$Count]['Forward_Count'] = $value->Forward_Count;
                    $PostArray[$Count]['Product_Offer_Desc'] = $value->Product_Offer_Desc;
                    $PostArray[$Count]['Responding_MPN'] = $value->phone_number;
                    $PostArray[$Count]['Product_Source_System'] = $value->Product_Source_System;
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';

                    $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = str_pad($value->Treatment_Code, 9, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Response_Date_Time'] = $value->Response_Date_Time;
                    $PostArray[$Count]['ResponseStatus_Code'] = 26;
                    $PostArray[$Count]['Response_Channel'] = $value->Response_Channel;
                    $PostArray[$Count]['Link_ID'] = $value->Link_ID;
                    $PostArray[$Count]['Link_Name'] = $value->Link_Name;
                    $PostArray[$Count]['Sub_Id'] = $value->Sub_Id;
                    $PostArray[$Count]['Sub_Id_Description'] = $value->Sub_Id_Description;
                    $PostArray[$Count]['Response_Text'] = $value->Response_Text;
                    $PostArray[$Count]['ResponseReason_Code'] = 'C01';
                    $PostArray[$Count]['Product_Offer_Code'] = '';
                    $PostArray[$Count]['Forward_Count'] = $value->Forward_Count;
                    $PostArray[$Count]['Product_Offer_Desc'] = $value->Product_Offer_Desc;
                    $PostArray[$Count]['Responding_MPN'] = $value->phone_number;
                    $PostArray[$Count]['Product_Source_System'] = $value->Product_Source_System;
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';



                    $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = str_pad($value->Treatment_Code, 9, '0', STR_PAD_LEFT);
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
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';
                }


            }
            if ($O2ReturnProcessData->save()) {

            }

        }
        }

        /*Dialer 1 Start HERE*/

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
when L.status in ('CBHOLD','CALLBK','GCALLB','GCALLBK','LB','ALTNUM','AltNo','WD','WRRNU','WRNNUM','CHU','DROP','ERI') then 24

when L.status in ('ADC','ADCT','AB','B','AFAX','A','AA','N','NA','DL','DC','BLOCK') then 25

when L.status in ('TPS','DNC','OPTOUT','OPTOU') then 26

when L.status in ('ACOnly','SALE','HS','REFSAL','SALEDE','SALEDEP','SIMODE','TCAC','TCOnly','TSALE','REFER') then 28

when L.status in ('WEBDC','NI','DNOFS','CBDNI','CBDNI','CNAF','WHS','IC','HUC','DECAD','DECB','DECB','LODD','LU','MAE','NBD','NOP','ALREAD','O2C','PPNG','UTMUR',

'NoneUK','HWCP','NNC','REFDEC','DECCC','REDPA','ADINFO') then 29

when L.status in ('Busin','UA') then 27

ELSE 'NULL'

   END) AS ResponseStatus_Code,

IF(entry_date IS NULL, 'T', 'T') Response_Channel,

IF(entry_date IS NULL, '', '') AS Link_ID,

IF(entry_date IS NULL, '', '') AS Link_Name,

IF(entry_date IS NULL, '', '') AS Sub_Id,

IF(entry_date IS NULL, '', '') AS Sub_Id_Description,

IF(entry_date IS NULL, '', '') AS Response_Text,

(case

when status in ('CBHOLD','GCALLB','GCALLBK','CALLBK') then 'A02'

when status in ('LB','ALTNUM','AltNo') then 'A03'

#when status in ('ADC','ADCT') then 'A05'

when status in ('WD','WRNNU','WRNNUM') then 'A06'

when status in ('CHU') then 'A09'

when status in ('DROP','ERI') then 'A10'

when status in ('AB','B') then 'B01'

when status in ('AFAX') then 'B02'

when status in ('A','AA') then 'B03'

when status in ('NA','N') then 'B04'

when status in ('DL') then 'B05'

when status in ('DC','BLOCK') then 'B06'

when status in ('TPS','DNC','OPTOUT','OPTOU') then 'C03'

when status in ('UA') then 'D06'

when status in ('Busin') then 'D02'

when status in ('REFER') then 'E06'

when status in ('ACOnly','SALE','HS','REFSAL','SALEDE','SALEDEP','SIMODE','TCAC','TCOnly','TSALE') then 'E01'

when status in ('NI','DNOFS','CBDNI','CBDNI','CNAF','WHS','HUC') then 'F03'

when status in ('IC','ALREAD','O2C') then 'F05'

when status in ('WEBDC','LODD','LU','MAE','NBD','NOP','PPNG','UTMUR','NoneUK','HWCP','NNC') then 'F07'

when status in ('REFDEC') then 'F08'

when status in ('DECAD','DECB','DECB','DECCC','ADINFO') then 'F10'

when status in ('REDPA') then 'F11'

Else 'NULL'

END) as ResponseReason_Code,

(case

when L.status in ('ACOnly') then 'SALE_ACOnly'

when L.status in ('HS') then 'Handset_Sale'

when L.status in ('SALE') then 'sale'

when L.status in ('TCAC') then 'SALE-TC&AC'

when L.status in ('TCOnly') then 'SALE-TCOnly'

when L.status in ('TSALE') then 'Tablet_Sale'

when L.status in ('REFSAL') then 'Referral_Sale'

when L.status in ('SALEDE') then 'Sale_1st_Deposit'

when L.status in ('SALEDEP') then 'Sale_1st_Deposit'

when L.status in ('SIMODE') then 'SIM_Only_Deposit'

when L.status in ('REFER') then 'Referral_Callback '

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

P.custom_2 as CustId,

L.list_id

from list L

#JOIN custom_fields_data P

#on L.lead_id=P.lead_id

#where L.list_id IN (1330,3001,3005)

#and L.last_local_call_time !='NULL'

#and P.custom_4 = 5636
join lists LS
on L.list_id = LS.list_id
JOIN custom_fields_data P
on L.lead_id=P.lead_id
where LS.campaign_id in (3045)

and P.custom_7 != ''

and L.entry_date between '".$start."' and '".$end."' AND L.vendor_lead_code != '' AND L.status NOT IN ('".$HideArrayStatus."')";

        $data1 = DB::connection('MainDialer')->select($query);

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
when L.status in ('CBHOLD','CALLBK','GCALLB','GCALLBK','LB','ALTNUM','AltNo','WD','WRRNU','WRNNUM','CHU','DROP','ERI') then 24

when L.status in ('ADC','ADCT','AB','B','AFAX','A','AA','N','NA','DL','DC','BLOCK') then 25

when L.status in ('TPS','DNC','OPTOUT','OPTOU') then 26

when L.status in ('ACOnly','SALE','HS','REFSAL','SALEDE','SALEDEP','SIMODE','TCAC','TCOnly','TSALE','REFER') then 28

when L.status in ('WEBDC','NI','DNOFS','CBDNI','CBDNI','CNAF','WHS','IC','HUC','DECAD','DECB','DECB','LODD','LU','MAE','NBD','NOP','ALREAD','O2C','PPNG','UTMUR',

'NoneUK','HWCP','NNC','REFDEC','DECCC','REDPA','ADINFO') then 29

when L.status in ('Busin','UA') then 27

ELSE 'NULL'

   END) AS ResponseStatus_Code,

IF(entry_date IS NULL, 'T', 'T') Response_Channel,

IF(entry_date IS NULL, '', '') AS Link_ID,

IF(entry_date IS NULL, '', '') AS Link_Name,

IF(entry_date IS NULL, '', '') AS Sub_Id,

IF(entry_date IS NULL, '', '') AS Sub_Id_Description,

IF(entry_date IS NULL, '', '') AS Response_Text,

(case

when status in ('CBHOLD','GCALLB','GCALLBK','CALLBK') then 'A02'

when status in ('LB','ALTNUM','AltNo') then 'A03'

#when status in ('ADC','ADCT') then 'A05'

when status in ('WD','WRNNU','WRNNUM') then 'A06'

when status in ('CHU') then 'A09'

when status in ('DROP','ERI') then 'A10'

when status in ('AB','B') then 'B01'

when status in ('AFAX') then 'B02'

when status in ('A','AA') then 'B03'

when status in ('NA','N') then 'B04'

when status in ('DL') then 'B05'

when status in ('DC','BLOCK') then 'B06'

when status in ('TPS','DNC','OPTOUT','OPTOU') then 'C03'

when status in ('UA') then 'D06'

when status in ('Busin') then 'D02'

when status in ('REFER') then 'E06'

when status in ('ACOnly','SALE','HS','REFSAL','SALEDE','SALEDEP','SIMODE','TCAC','TCOnly','TSALE') then 'E01'

when status in ('NI','DNOFS','CBDNI','CBDNI','CNAF','WHS','HUC') then 'F03'

when status in ('IC','ALREAD','O2C') then 'F05'

when status in ('WEBDC','LODD','LU','MAE','NBD','NOP','PPNG','UTMUR','NoneUK','HWCP','NNC') then 'F07'

when status in ('REFDEC') then 'F08'

when status in ('DECAD','DECB','DECB','DECCC','ADINFO') then 'F10'

when status in ('REDPA') then 'F11'

Else 'NULL'

END) as ResponseReason_Code,

(case

when L.status in ('ACOnly') then 'SALE_ACOnly'

when L.status in ('HS') then 'Handset_Sale'

when L.status in ('SALE') then 'sale'

when L.status in ('TCAC') then 'SALE-TC&AC'

when L.status in ('TCOnly') then 'SALE-TCOnly'

when L.status in ('TSALE') then 'Tablet_Sale'

when L.status in ('REFSAL') then 'Referral_Sale'

when L.status in ('SALEDE') then 'Sale_1st_Deposit'

when L.status in ('SALEDEP') then 'Sale_1st_Deposit'

when L.status in ('SIMODE') then 'SIM_Only_Deposit'

when L.status in ('REFER') then 'Referral_Callback '

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

P.custom_2 as CustId,

L.list_id,

L.called_count,

L.last_local_call_time

from list L
join lists LS
on L.list_id = LS.list_id
JOIN custom_fields_data P
on L.lead_id=P.lead_id
where LS.campaign_id in (3045)

and L.last_local_call_time !='NULL'

#and P.custom_4 = 5636

and P.custom_7 != ''

and (L.status in ('TPS','DNC','OPTOUT','OPTOU') OR L.called_count = 8)

and L.last_local_call_time between '".$CurrentStart."' and '".$CurrentEnd."' AND L.vendor_lead_code != '' AND L.status NOT IN ('".$HideArrayStatus."')";

        $CurrentDateData1 = DB::connection('MainDialer')->select($query);

        $totalArray1 = [];
        $totalArray1[3045] = $data1;


        foreach($CurrentDateData1 as $value){
            $O2ReturnProcessData = new O2ReturnProcessData();
            $response = O2ReturnProcessValidationNew($value);

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
            $O2ReturnProcessData->list_id = @$value->list_id;
            if(!empty($response) && count($response) > 0){
                $Failed++;
                $O2ReturnProcessData->status = 'cancel';
                $O2ReturnProcessData->response = serialize($response);
            }else{
                $PhoneNumberArray[] = $value->phone_number;
                $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = str_pad($value->Treatment_Code, 9, '0', STR_PAD_LEFT);
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
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';
            }

            if ($O2ReturnProcessData->save()) {

            }

        }


        foreach($totalArray1 as $key=>$val){

        foreach($val as $value){
            if(!empty($value->ResponseReason_Code) && $value->ResponseReason_Code == 'NULL'){
                continue;
            }
            if(!empty($value->ResponseStatus_Code) && $value->ResponseStatus_Code == 'NULL'){
                continue;
            }

            $O2ReturnProcessData = new O2ReturnProcessData();
            $response = O2ReturnProcessValidationNew($value);

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
            $O2ReturnProcessData->list_id = @$value->list_id;

            if(!empty($response) && count($response) > 0){
                $Failed++;
                $O2ReturnProcessData->status = 'cancel';
                $O2ReturnProcessData->response = serialize($response);
            }else{
                if(in_array($value->phone_number,$PhoneNumberArray)){
                    continue;
                }
                if($value->ResponseStatus_Code == 24){
                    $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = str_pad($value->Treatment_Code, 9, '0', STR_PAD_LEFT);
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
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';

                }

                if($value->ResponseStatus_Code == 25){
                    $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = str_pad($value->Treatment_Code, 9, '0', STR_PAD_LEFT);
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
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';

                }

                if($value->ResponseStatus_Code == 26){
                    $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = str_pad($value->Treatment_Code, 9, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Response_Date_Time'] = $value->Response_Date_Time;
                    $PostArray[$Count]['ResponseStatus_Code'] = 24;
                    $PostArray[$Count]['Response_Channel'] = $value->Response_Channel;
                    $PostArray[$Count]['Link_ID'] = $value->Link_ID;
                    $PostArray[$Count]['Link_Name'] = $value->Link_Name;
                    $PostArray[$Count]['Sub_Id'] = $value->Sub_Id;
                    $PostArray[$Count]['Sub_Id_Description'] = $value->Sub_Id_Description;
                    $PostArray[$Count]['Response_Text'] = $value->Response_Text;
                    $PostArray[$Count]['ResponseReason_Code'] = 'A08';
                    $PostArray[$Count]['Product_Offer_Code'] = '';
                    $PostArray[$Count]['Forward_Count'] = $value->Forward_Count;
                    $PostArray[$Count]['Product_Offer_Desc'] = $value->Product_Offer_Desc;
                    $PostArray[$Count]['Responding_MPN'] = $value->phone_number;
                    $PostArray[$Count]['Product_Source_System'] = $value->Product_Source_System;
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';



                    $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = str_pad($value->Treatment_Code, 9, '0', STR_PAD_LEFT);
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
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';


                }

                if($value->ResponseStatus_Code == 27){
                    $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = str_pad($value->Treatment_Code, 9, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Response_Date_Time'] = $value->Response_Date_Time;
                    $PostArray[$Count]['ResponseStatus_Code'] = 24;
                    $PostArray[$Count]['Response_Channel'] = $value->Response_Channel;
                    $PostArray[$Count]['Link_ID'] = $value->Link_ID;
                    $PostArray[$Count]['Link_Name'] = $value->Link_Name;
                    $PostArray[$Count]['Sub_Id'] = $value->Sub_Id;
                    $PostArray[$Count]['Sub_Id_Description'] = $value->Sub_Id_Description;
                    $PostArray[$Count]['Response_Text'] = $value->Response_Text;
                    $PostArray[$Count]['ResponseReason_Code'] = 'A08';
                    $PostArray[$Count]['Product_Offer_Code'] = '';
                    $PostArray[$Count]['Forward_Count'] = $value->Forward_Count;
                    $PostArray[$Count]['Product_Offer_Desc'] = $value->Product_Offer_Desc;
                    $PostArray[$Count]['Responding_MPN'] = $value->phone_number;
                    $PostArray[$Count]['Product_Source_System'] = $value->Product_Source_System;
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';



                    $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = str_pad($value->Treatment_Code, 9, '0', STR_PAD_LEFT);
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
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';


                }

                if($value->ResponseStatus_Code == 29){
                   $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = str_pad($value->Treatment_Code, 9, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Response_Date_Time'] = $value->Response_Date_Time;
                    $PostArray[$Count]['ResponseStatus_Code'] = 24;
                    $PostArray[$Count]['Response_Channel'] = $value->Response_Channel;
                    $PostArray[$Count]['Link_ID'] = $value->Link_ID;
                    $PostArray[$Count]['Link_Name'] = $value->Link_Name;
                    $PostArray[$Count]['Sub_Id'] = $value->Sub_Id;
                    $PostArray[$Count]['Sub_Id_Description'] = $value->Sub_Id_Description;
                    $PostArray[$Count]['Response_Text'] = $value->Response_Text;
                    $PostArray[$Count]['ResponseReason_Code'] = 'A08';
                    $PostArray[$Count]['Product_Offer_Code'] = '';
                    $PostArray[$Count]['Forward_Count'] = $value->Forward_Count;
                    $PostArray[$Count]['Product_Offer_Desc'] = $value->Product_Offer_Desc;
                    $PostArray[$Count]['Responding_MPN'] = $value->phone_number;
                    $PostArray[$Count]['Product_Source_System'] = $value->Product_Source_System;
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';


                    $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = str_pad($value->Treatment_Code, 9, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Response_Date_Time'] = $value->Response_Date_Time;
                    $PostArray[$Count]['ResponseStatus_Code'] = 26;
                    $PostArray[$Count]['Response_Channel'] = $value->Response_Channel;
                    $PostArray[$Count]['Link_ID'] = $value->Link_ID;
                    $PostArray[$Count]['Link_Name'] = $value->Link_Name;
                    $PostArray[$Count]['Sub_Id'] = $value->Sub_Id;
                    $PostArray[$Count]['Sub_Id_Description'] = $value->Sub_Id_Description;
                    $PostArray[$Count]['Response_Text'] = $value->Response_Text;
                    $PostArray[$Count]['ResponseReason_Code'] = 'C02';
                    $PostArray[$Count]['Product_Offer_Code'] = '';
                    $PostArray[$Count]['Forward_Count'] = $value->Forward_Count;
                    $PostArray[$Count]['Product_Offer_Desc'] = $value->Product_Offer_Desc;
                    $PostArray[$Count]['Responding_MPN'] = $value->phone_number;
                    $PostArray[$Count]['Product_Source_System'] = $value->Product_Source_System;
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';

                    $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = str_pad($value->Treatment_Code, 9, '0', STR_PAD_LEFT);
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
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';

                }

                if($value->ResponseStatus_Code == 28){
                    $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = str_pad($value->Treatment_Code, 9, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Response_Date_Time'] = $value->Response_Date_Time;
                    $PostArray[$Count]['ResponseStatus_Code'] = 24;
                    $PostArray[$Count]['Response_Channel'] = $value->Response_Channel;
                    $PostArray[$Count]['Link_ID'] = $value->Link_ID;
                    $PostArray[$Count]['Link_Name'] = $value->Link_Name;
                    $PostArray[$Count]['Sub_Id'] = $value->Sub_Id;
                    $PostArray[$Count]['Sub_Id_Description'] = $value->Sub_Id_Description;
                    $PostArray[$Count]['Response_Text'] = $value->Response_Text;
                    $PostArray[$Count]['ResponseReason_Code'] = 'A08';
                    $PostArray[$Count]['Product_Offer_Code'] = '';
                    $PostArray[$Count]['Forward_Count'] = $value->Forward_Count;
                    $PostArray[$Count]['Product_Offer_Desc'] = $value->Product_Offer_Desc;
                    $PostArray[$Count]['Responding_MPN'] = $value->phone_number;
                    $PostArray[$Count]['Product_Source_System'] = $value->Product_Source_System;
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';

                    $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = str_pad($value->Treatment_Code, 9, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Response_Date_Time'] = $value->Response_Date_Time;
                    $PostArray[$Count]['ResponseStatus_Code'] = 26;
                    $PostArray[$Count]['Response_Channel'] = $value->Response_Channel;
                    $PostArray[$Count]['Link_ID'] = $value->Link_ID;
                    $PostArray[$Count]['Link_Name'] = $value->Link_Name;
                    $PostArray[$Count]['Sub_Id'] = $value->Sub_Id;
                    $PostArray[$Count]['Sub_Id_Description'] = $value->Sub_Id_Description;
                    $PostArray[$Count]['Response_Text'] = $value->Response_Text;
                    $PostArray[$Count]['ResponseReason_Code'] = 'C01';
                    $PostArray[$Count]['Product_Offer_Code'] = '';
                    $PostArray[$Count]['Forward_Count'] = $value->Forward_Count;
                    $PostArray[$Count]['Product_Offer_Desc'] = $value->Product_Offer_Desc;
                    $PostArray[$Count]['Responding_MPN'] = $value->phone_number;
                    $PostArray[$Count]['Product_Source_System'] = $value->Product_Source_System;
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';



                    $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = str_pad($value->Treatment_Code, 9, '0', STR_PAD_LEFT);
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
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';
                }


            }
            if ($O2ReturnProcessData->save()) {

            }

        }
        }

        /*Dialer 1 END HERE*/

        $fileName = 'RESPONSE_INTELLING_001_' . $ExtractDateTime . '_' . $BusinessDateTime . '_001';
        Excel::create($fileName, function($excel) use($PostArray) {
            $excel->setTitle('O2 Return Process');
            $excel->sheet('O2 Return Process', function($sheet) use($PostArray) {
                $sheet->setOrientation('landscape');
                $sheet->fromArray($PostArray,null, 'A1', true);
            });
        })->store('csv', storage_path('Automation/O2ReturnProcess/import/1330'), true);

//             die('BYE');
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

        $LocalServer = '3.8.11.11';
        $LocalUser = 'root';
        $LocalPass = 'Utgesx0012!!';
        $LocalPort = 22;
        $LocalConnection = ssh2_connect($LocalServer, $LocalPort);
        if (ssh2_auth_password($LocalConnection, $LocalUser, $LocalPass)) {
            if (!empty($fileName)) {
                ssh2_scp_send($LocalConnection, "/var/www/html/cron/storage/Automation/O2ReturnProcess/export/1330/" . $fileName . '.dat.gz.gpg', "/home/O2ReturnProcess/" . $fileName . '.dat.gz.gpg');
//                ssh2_scp_send($connection, "/var/www/html/cron/storage/Automation/O2ReturnProcess/export/1330/" . $fileName . '.dat.gz.gpg', "/home/Ankit/" . $fileName . '.dat.gz.gpg');
            }
            if (!empty($MSTfileName)) {
                ssh2_scp_send($LocalConnection, "/var/www/html/cron/storage/Automation/O2ReturnProcess/export/1330/" . $MSTfileName . '.mfst', "/home/O2ReturnProcess/" . $MSTfileName . '.mfst');
//                ssh2_scp_send($connection, "/var/www/html/cron/storage/Automation/O2ReturnProcess/export/1330/" . $MSTfileName . '.mfst', "/home/Ankit/" . $MSTfileName . '.mfst');
            }
        }

        $arrayMailTo = ['ngupta@usethegeeks.co.uk','apanwar@usethegeeks.co.uk','Andy.Hughes@intelling.co.uk','Nicola.Sharrock@intelling.co.uk',config('app.dialer_team_email'),'Sarah.Berry@intelling.co.uk'];
//        $arrayMailTo = ['apanwar@usethegeeks.co.uk'];
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
