<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use App\User;
use App\Model\Intelling\LeadSupplier;
use Mail;
use App\Model\UTGAPI\O2ReturnProcessData;

class TestController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request) {

//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        insert_command_log('P2P-CHURN-AUTOMATION','This is TEST DESCRIPTION', 'TESTCALL');
//        Artisan::call('command:O2ReturnProcessAutomationAddcon');
        exit;
//        die('Hello');
        $start = Carbon::now()->subDays(90)->startOfDay();
        $end = Carbon::now()->subDays(90)->endOfDay();
        
//        $start = $date.' 08:00:00';
//        $end = $date.' 20:00:00'; 
        
        
        
        
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
when L.status in ('CBHOLD','ADC','ADCT','DROP','ERI','AB','B','B','AFAX','A','AA','N','NA','DL','DC','BLOCK') then 25
when L.status in ('SALE','ACOnly','HS','REFSAL','SALEDE','SALEDEP','SIMODE','TCAC','TCOnly','TSALE') then 28
when L.status in ('CALLBK','WEBDC','TPS','DNC','OPTOUT','ADINFO','OPTOU','REFER','NI','DNOFS','CBDNI','CNAF','WHS','IC','HUC','DECAD','DECB','LODD','LU',
'MAE','NBD','NOP','ALREAD','O2C','PPNG','UTMUR','NoneUK','HWCP','NNC','REFDEC','DECCC','REDPA') then 29
when L.status in ('GCALLB','GCALLBK','LB','ALTNUM','AltNo','WD','WRNNU','WRNNUM','CHU','Busin','UA') then 27
ELSE 'NULL'
   END) AS ResponseStatus_Code,    
IF(entry_date IS NULL, 'T', 'T') Response_Channel,
IF(entry_date IS NULL, '', '') AS Link_ID,
IF(entry_date IS NULL, '', '') AS Link_Name,
IF(entry_date IS NULL, '', '') AS Sub_Id,
IF(entry_date IS NULL, '', '') AS Sub_Id_Description,
IF(entry_date IS NULL, '', '') AS Response_Text,
(case
when status in ('CBHOLD','GCALLB','GCALLBK','CALLBK','WEBDC') then 'A02'
when status in ('LB','ALTNUM','AltNo') then 'A03'
when status in ('ADC','ADCT') then 'A05'
when status in ('WEBDOW','WD','WRNNUM') then 'A06'
when status in ('CHU') then 'A09'
when status in ('SALE') then 'C01'
when status in ('DROP','ERI') then 'A10'
when status in ('DNC','OPTOUT','ADINFO','OPTOU','TPS') then 'F03'
when status in ('LODD','LU','MAE','NBD','NOP','PPNG','UTMUR','NoneUK','HWCP','NNC') then 'F07'
when status in ('AB','B') then 'B01'
when status in ('AFAX') then 'B02'
when status in ('A','AA') then 'B03'
when status in ('NA','N') then 'B04'
when status in ('DL') then 'B05'
when status in ('DC','BLOCK') then 'B06'
when status in ('DECB') then 'F10'
when status in ('SALE','ACOnly','HS','REFSAL','SALEDE','SALEDEP','SIMODE','TCAC','TCOnly','TSALE') then 'E01'
when status in ('NI','DNOFS','CBDNI','CNAF','WHS','HUC') then 'F03'
when status in ('UA') then 'D06'
when status in ('REDPA') then 'F11'
when status in ('Busin') then 'D02'
when status in ('REFER') then 'E06'
when status in ('IC','ALREAD','O2C') then 'F05'
when status in ('REFDEC') then 'F08'
Else 'NULL'
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
where L.list_id = '3005'  
and L.last_local_call_time !='NULL'
#and P.custom_4 = 5636
and P.custom_7 != ''
and L.entry_date between '" . $start . "' and '" . $end . "' AND L.vendor_lead_code is NOT NULL";

        $ExtractDateTime = date('YmdHis');
        $BusinessDateTime = date('YmdHis', strtotime($end));
        $data = DB::connection('OmniDialer')->select($query);
//        pr($data);
//        exit;
        $totalArray = [];
        $totalArray[3005] = $data;
        
//        $totalCountArray = [];
        
        $PostArray = [];
        
        $Count = 0;
        $Failed = 0;
        foreach($totalArray as $key=>$val){
            $SuccessResponse = 0;
        foreach($val as $value){
            if(!empty($value->ResponseReason_Code) && $value->ResponseReason_Code == 'NULL'){
                continue;
            }
            if(!empty($value->ResponseStatus_Code) && $value->ResponseStatus_Code == 'NULL'){
                continue;
            }
            $O2ReturnProcessData = new O2ReturnProcessData();
//            $response = O2ReturnProcessValidation($value);
            
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
            $O2ReturnProcessData->list_id = $key;
            
            if(!empty($response) && count($response) > 0){
                $Failed++;
                $O2ReturnProcessData->status = 'cancel';
                $O2ReturnProcessData->response = serialize($response);
            }else{
                
                if(in_array($value->status,['CBHOLD','ADC','ADCT','DROP','ERI','AB','B','AFAX','A','AA','N','NA','DL','DC','BLOCK']) && $value->ResponseStatus_Code == 25){
                    $Count++;
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
//                    $PostArray[$Count]['Product_Offer_Code'] = $value->;
                    $PostArray[$Count]['Forward_Count'] = $value->Forward_Count;
                    $PostArray[$Count]['Product_Offer_Desc'] = $value->Product_Offer_Desc;
                    $PostArray[$Count]['Responding_MPN'] = $value->phone_number;
                    $PostArray[$Count]['Product_Source_System'] = $value->Product_Source_System;
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';
                    $PostArray[$Count]['Status'] = $value->status;
                }
                
                if(in_array($value->status,['GCALLB','GCALLBK','LB','WD','WRNNU','WRNNUM','CHU','Busin','UA']) && $value->ResponseStatus_Code == 27){
                    $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = $value->Treatment_Code;
                    $PostArray[$Count]['Response_Date_Time'] = $value->Response_Date_Time;
                    $PostArray[$Count]['ResponseStatus_Code'] = 24;
                    $PostArray[$Count]['Response_Channel'] = $value->Response_Channel;
                    $PostArray[$Count]['Link_ID'] = $value->Link_ID;
                    $PostArray[$Count]['Link_Name'] = $value->Link_Name;
                    $PostArray[$Count]['Sub_Id'] = $value->Sub_Id;
                    $PostArray[$Count]['Sub_Id_Description'] = $value->Sub_Id_Description;
                    $PostArray[$Count]['Response_Text'] = $value->Response_Text;
                    $PostArray[$Count]['ResponseReason_Code'] = 'A08';
                    $PostArray[$Count]['Product_Offer_Code'] = 'Successful Contact';
                    $PostArray[$Count]['Forward_Count'] = $value->Forward_Count;
                    $PostArray[$Count]['Product_Offer_Desc'] = $value->Product_Offer_Desc;
                    $PostArray[$Count]['Responding_MPN'] = $value->phone_number;
                    $PostArray[$Count]['Product_Source_System'] = $value->Product_Source_System;
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';
                    $PostArray[$Count]['Status'] = $value->status;
                    
                    
                    $Count++;
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
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';
                    $PostArray[$Count]['Status'] = $value->status;
                    
                }
                
                if(in_array($value->status,['CALLBK','WEBDC','DNC','OPTOUT','OPTOU','REFER','NI','IC','HUC','DECAD','DECB','LODD','LU','MAE','NBD','NOP','O2C','PPNG','UTMUR','REFDEC','DECCC','REDPA']) && $value->ResponseStatus_Code == 29){
                   $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = $value->Treatment_Code;
                    $PostArray[$Count]['Response_Date_Time'] = $value->Response_Date_Time;
                    $PostArray[$Count]['ResponseStatus_Code'] = 24;
                    $PostArray[$Count]['Response_Channel'] = $value->Response_Channel;
                    $PostArray[$Count]['Link_ID'] = $value->Link_ID;
                    $PostArray[$Count]['Link_Name'] = $value->Link_Name;
                    $PostArray[$Count]['Sub_Id'] = $value->Sub_Id;
                    $PostArray[$Count]['Sub_Id_Description'] = $value->Sub_Id_Description;
                    $PostArray[$Count]['Response_Text'] = $value->Response_Text;
                    $PostArray[$Count]['ResponseReason_Code'] = 'A08';
                    $PostArray[$Count]['Product_Offer_Code'] = 'Successful Contact';
                    $PostArray[$Count]['Forward_Count'] = $value->Forward_Count;
                    $PostArray[$Count]['Product_Offer_Desc'] = $value->Product_Offer_Desc;
                    $PostArray[$Count]['Responding_MPN'] = $value->phone_number;
                    $PostArray[$Count]['Product_Source_System'] = $value->Product_Source_System;
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';
                    $PostArray[$Count]['Status'] = $value->status;
                    
                    $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = $value->Treatment_Code;
                    $PostArray[$Count]['Response_Date_Time'] = $value->Response_Date_Time;
                    $PostArray[$Count]['ResponseStatus_Code'] = 26;
                    $PostArray[$Count]['Response_Channel'] = $value->Response_Channel;
                    $PostArray[$Count]['Link_ID'] = $value->Link_ID;
                    $PostArray[$Count]['Link_Name'] = $value->Link_Name;
                    $PostArray[$Count]['Sub_Id'] = $value->Sub_Id;
                    $PostArray[$Count]['Sub_Id_Description'] = $value->Sub_Id_Description;
                    $PostArray[$Count]['Response_Text'] = $value->Response_Text;
                    $PostArray[$Count]['ResponseReason_Code'] = 'C02';
                    $PostArray[$Count]['Product_Offer_Code'] = 'No Sale';
                    $PostArray[$Count]['Forward_Count'] = $value->Forward_Count;
                    $PostArray[$Count]['Product_Offer_Desc'] = $value->Product_Offer_Desc;
                    $PostArray[$Count]['Responding_MPN'] = $value->phone_number;
                    $PostArray[$Count]['Product_Source_System'] = $value->Product_Source_System;
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';
                    $PostArray[$Count]['Status'] = $value->status;
                    
                    $Count++;
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
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';
                    $PostArray[$Count]['Status'] = $value->status;

                }
                
                if(in_array($value->status,['ACOnly','HS','REFSAL','SALEDE','SALEDEP','SIMODE','TCAC','TCOnly','TSALE','SALE']) && $value->ResponseStatus_Code == 28){
                    $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = $value->Treatment_Code;
                    $PostArray[$Count]['Response_Date_Time'] = $value->Response_Date_Time;
                    $PostArray[$Count]['ResponseStatus_Code'] = 24;
                    $PostArray[$Count]['Response_Channel'] = $value->Response_Channel;
                    $PostArray[$Count]['Link_ID'] = $value->Link_ID;
                    $PostArray[$Count]['Link_Name'] = $value->Link_Name;
                    $PostArray[$Count]['Sub_Id'] = $value->Sub_Id;
                    $PostArray[$Count]['Sub_Id_Description'] = $value->Sub_Id_Description;
                    $PostArray[$Count]['Response_Text'] = $value->Response_Text;
                    $PostArray[$Count]['ResponseReason_Code'] = 'A08';
                    $PostArray[$Count]['Product_Offer_Code'] = 'Successful Contact';
                    $PostArray[$Count]['Forward_Count'] = $value->Forward_Count;
                    $PostArray[$Count]['Product_Offer_Desc'] = $value->Product_Offer_Desc;
                    $PostArray[$Count]['Responding_MPN'] = $value->phone_number;
                    $PostArray[$Count]['Product_Source_System'] = $value->Product_Source_System;
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';
                    $PostArray[$Count]['Status'] = $value->status;
                    
                    $Count++;
                    $PostArray[$Count]['Customer_ID'] = $value->Customer_ID;
                    $PostArray[$Count]['Account_Id'] = $value->vendor_lead_code;
                    $PostArray[$Count]['Subscriber_ID'] = $value->Subscriber_ID;
                    $PostArray[$Count]['Campaign_Code'] = str_pad($value->Campaign_Code, 10, '0', STR_PAD_LEFT);
                    $PostArray[$Count]['Cell_Code'] = $value->Cell_Code;
                    $PostArray[$Count]['Treatment_Code'] = $value->Treatment_Code;
                    $PostArray[$Count]['Response_Date_Time'] = $value->Response_Date_Time;
                    $PostArray[$Count]['ResponseStatus_Code'] = 26;
                    $PostArray[$Count]['Response_Channel'] = $value->Response_Channel;
                    $PostArray[$Count]['Link_ID'] = $value->Link_ID;
                    $PostArray[$Count]['Link_Name'] = $value->Link_Name;
                    $PostArray[$Count]['Sub_Id'] = $value->Sub_Id;
                    $PostArray[$Count]['Sub_Id_Description'] = $value->Sub_Id_Description;
                    $PostArray[$Count]['Response_Text'] = $value->Response_Text;
                    $PostArray[$Count]['ResponseReason_Code'] = 'C01';
                    $PostArray[$Count]['Product_Offer_Code'] = 'SALE';
                    $PostArray[$Count]['Forward_Count'] = $value->Forward_Count;
                    $PostArray[$Count]['Product_Offer_Desc'] = $value->Product_Offer_Desc;
                    $PostArray[$Count]['Responding_MPN'] = $value->phone_number;
                    $PostArray[$Count]['Product_Source_System'] = $value->Product_Source_System;
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';
                    $PostArray[$Count]['Status'] = $value->status;
                    
                    
                    $Count++;
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
                    $PostArray[$Count]['custom_1'] = '';
                    $PostArray[$Count]['custom_2'] = '';
                    $PostArray[$Count]['custom_3'] = '';
                    $PostArray[$Count]['custom_4'] = '';
                    $PostArray[$Count]['custom_5'] = '';
                    $PostArray[$Count]['Status'] = $value->status;
                }
                
                
            }
//            if ($O2ReturnProcessData->save()) {
//
//            }
        }
        }
        
        return view('test.test1',compact(['PostArray']));
    }

}
