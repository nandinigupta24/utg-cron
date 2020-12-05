<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\ScriptResponse;
use App\Model\SwitchExpertEnergy\SDSales;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;
use App\Model\O2Inbound\InboundSale;
use App\Model\UTGAPI\O2ReturnProcessData;
use App\Model\UTGAPI\O2UNICA;

class JLAFireSafety extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:JLAFireSafety';

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
        $FireSafetydata = DB::connection('OmniDialer')->select("select * from JLAFireSafety.customers c where c.DateSent between curdate() and curdate() +interval 1 day");
        
        $FireSafetyFileName = date('dmY').'_FireSafetyReturns';
        Excel::create($FireSafetyFileName, function($excel) use($FireSafetydata) {
            $excel->sheet('Fire Safety Returns', function($sheet) use($FireSafetydata) {
                $dataAppendArray = [];
                foreach($FireSafetydata as $key=>$value){
                    $dataAppend = [];
                    $dataAppend['lead_id'] = $value->lead_id;
                    $dataAppend['Version'] = $value->Version;
                    $dataAppend['connect'] = $value->connect;
                    $dataAppend['vendor_id'] = $value->vendor_id;
                    $dataAppend['list_id'] = $value->list_id;
                    $dataAppend['phone_number'] = $value->phone_number;
                    $dataAppend['title'] = $value->title;
                    $dataAppend['first_name'] = $value->first_name;
                    $dataAppend['last_name'] = $value->last_name;
                    $dataAppend['middle_initial'] = $value->middle_initial;
                    $dataAppend['address1'] = $value->address1;
                    $dataAppend['address2'] = $value->address2;
                    $dataAppend['address3'] = $value->address3;
                    $dataAppend['city'] = $value->city;
                    $dataAppend['postal_code'] = $value->postal_code;
                    $dataAppend['date_of_birth'] = $value->date_of_birth;
                    $dataAppend['alt_phone'] = $value->alt_phone;
                    $dataAppend['email'] = $value->email;
                    $dataAppend['comments'] = $value->comments;
                    $dataAppend['campaign'] = $value->campaign;
                    $dataAppend['fronter'] = $value->fronter;
                    $dataAppend['fullname'] = $value->fullname;
                    $dataAppend['recording_id'] = $value->recording_id;
                    $dataAppend['vendor_lead_code'] = $value->vendor_lead_code;
                    $dataAppend['province'] = $value->province;
                    $dataAppend['DateSent'] = $value->DateSent;
                    $dataAppend['last_submitted'] = $value->last_submitted;
                    $dataAppend['responsiblePerson'] = $value->responsiblePerson;
                    $dataAppend['nextSafetyCheck'] = $value->nextSafetyCheck;
                    $dataAppend['currentProvider'] = $value->currentProvider;
                    $dataAppend['serviceContract'] = $value->serviceContract;
                    $dataAppend['renewalDate'] = $value->renewalDate;
                    $dataAppend['correctPerson'] = $value->correctPerson;
                    $dataAppend['responsiblePersonName'] = $value->responsiblePersonName;
                    $dataAppend['responsiblePersonPosition'] = $value->responsiblePersonPosition;
                    $dataAppend['responsiblePersonEmail'] = $value->responsiblePersonEmail;
                    $dataAppend['responsiblePersonPhone'] = $value->responsiblePersonPhone;
                    $dataAppend['responsibleAvailable'] = $value->responsibleAvailable;
                    $dataAppend['partnerReason'] = $value->partnerReason;
                    $dataAppend['annualAssessment'] = $value->annualAssessment;
                    $dataAppend['internalExternal'] = $value->internalExternal;
                    $dataAppend['nextAssessment'] = $value->nextAssessment;
                    $dataAppendArray[$key] = $dataAppend;
                }
                $sheet->fromArray($dataAppendArray);
            });
        })->store('xls', storage_path('Automation/JLA/FireSafety'), true);
        
        
//        $ComplianceLaundrydata = DB::connection('OmniDialer')->select("select * from JLA.customers c where c.DateSent between curdate() and curdate() + interval 1 day");
        $ComplianceLaundrydata = DB::connection('OmniDialer')->select("select c.lead_id,c.Version,c.connect,c.vendor_id,c.list_id,c.phone_number,c.title,c.first_name,c.last_name,c.address1,c.address2,c.address3,c.city,c.postal_code,c.date_of_birth,c.alt_phone,c.email,c.comments,c.campaign,c.fronter,c.fullname,c.vendor_lead_code,c.DateSent,c.last_submitted,c.pencilWorkIn,c.preferableData,c.okPrice,c.responsiblePersonName,c.responsiblePersonPosition,c.responsiblePersonEmail,c.responsiblePersonPhone,c.responsibleAvailable,c.businessRole,c.authoriseCosts,(select s.status from custom_view.status_combined s where s.`status` = l.`status` group by s.status) as LastOutcome,(select custom_2 from custom_view.custom_fields_data cfd where l.lead_id = cfd.lead_id) as PivotalNumber,(select custom_3 from custom_view.custom_fields_data cfd where l.lead_id = cfd.lead_id) as TotalOrderValue,(select custom_6 from custom_view.custom_fields_data cfd where l.lead_id = cfd.lead_id) as PMVVolume,(select custom_7 from custom_view.custom_fields_data cfd where l.lead_id = cfd.lead_id) as PMVUnitValue,(select custom_8 from custom_view.custom_fields_data cfd where l.lead_id = cfd.lead_id) as TotalPMVvalue,(select custom_9 from custom_view.custom_fields_data cfd where l.lead_id = cfd.lead_id) as DuctVolume,(select custom_10 from custom_view.custom_fields_data cfd where l.lead_id = cfd.lead_id) as DuctunitValue,(select custom_11 from custom_view.custom_fields_data cfd where l.lead_id = cfd.lead_id) as TotalDuctValue from JLA.customers c 
inner join custom_view.`list` l on c.lead_id = l.lead_id
where c.DateSent between curdate() and curdate() + interval 1 day");
        $ComplianceLaundryFileName = date('dmY').'_ComplianceLaundryReturns';       
        Excel::create($ComplianceLaundryFileName, function($excel) use($ComplianceLaundrydata) {
            $excel->sheet('Compliance Laundry Returns', function($sheet) use($ComplianceLaundrydata) {
                $dataAppendArray = [];
                foreach($ComplianceLaundrydata as $key=>$value){
                    $dataAppend = [];
                    $dataAppend['lead_id'] = @$value->lead_id;
                    $dataAppend['Version'] = @$value->Version;
                    $dataAppend['connect'] = @$value->connect;
                    $dataAppend['vendor_id'] = @$value->vendor_id;
                    $dataAppend['list_id'] = @$value->list_id;
                    $dataAppend['phone_number'] = @$value->phone_number;
                    $dataAppend['title'] = @$value->title;
                    $dataAppend['first_name'] = @$value->first_name;
                    $dataAppend['last_name'] = @$value->last_name;
//                    $dataAppend['middle_initial'] = @$value->middle_initial;
                    $dataAppend['address1'] = @$value->address1;
                    $dataAppend['address2'] = @$value->address2;
                    $dataAppend['address3'] = @$value->address3;
                    $dataAppend['city'] = @$value->city;
                    $dataAppend['postal_code'] = @$value->postal_code;
                    $dataAppend['date_of_birth'] = @$value->date_of_birth;
                    $dataAppend['alt_phone'] = @$value->alt_phone;
                    $dataAppend['email'] = @$value->email;
                    $dataAppend['comments'] = @$value->comments;
                    $dataAppend['campaign'] = @$value->campaign;
                    $dataAppend['fronter'] = @$value->fronter;
                    $dataAppend['fullname'] = @$value->fullname;
//                    $dataAppend['recording_id'] = @$value->recording_id;
                    $dataAppend['vendor_lead_code'] = @$value->vendor_lead_code;
//                    $dataAppend['province'] = @$value->province;
                    $dataAppend['DateSent'] = @$value->DateSent;
                    $dataAppend['last_submitted'] = @$value->last_submitted;
                    $dataAppend['pencilWorkIn'] = @$value->pencilWorkIn;
                    $dataAppend['preferableData'] = @$value->preferableData;
                    $dataAppend['okPrice'] = @$value->okPrice;
                    $dataAppend['responsiblePersonName'] = @$value->responsiblePersonName;
                    $dataAppend['responsiblePersonPosition'] = @$value->responsiblePersonPosition;
                    $dataAppend['responsiblePersonEmail'] = @$value->responsiblePersonEmail;
                    $dataAppend['responsiblePersonPhone'] = @$value->responsiblePersonPhone;
                    $dataAppend['responsibleAvailable'] = @$value->responsibleAvailable;
                    $dataAppend['businessRole'] = @$value->businessRole;
                    $dataAppend['authoriseCosts'] = @$value->authoriseCosts;
                    $dataAppend['LastOutcome'] = @$value->LastOutcome;
                    $dataAppend['PivotalNumber'] = @$value->PivotalNumber;
                    $dataAppend['TotalOrderValue'] = @$value->TotalOrderValue;
                    $dataAppend['PMVVolume'] = @$value->PMVVolume;
                    $dataAppend['PMVUnitValue'] = @$value->PMVUnitValue;
                    $dataAppend['TotalPMVvalue'] = @$value->TotalPMVvalue;
                    $dataAppend['DuctVolume'] = @$value->DuctVolume;
                    $dataAppend['DuctunitValue'] = @$value->DuctunitValue;
                    $dataAppend['TotalDuctValue'] = @$value->TotalDuctValue;
                    $dataAppendArray[$key] = $dataAppend;
                }
                $sheet->fromArray($dataAppendArray);
            });
        })->store('xls', storage_path('Automation/JLA/ComplianceLaundry'), true);
        
        $server = '3.8.11.11';
        $serverPort = 22;
        $connection = ssh2_connect($server, $serverPort);
        $serverUser = 'root';
        $serverPassword = 'Utgesx0012!!';
        $ServerDirectory = '/home/JLAsftp/Returns/';
//        $ServerDirectory = '/home/TestDevelopment/';
        $LocalDirectory = '/var/www/html/cron/storage/Automation/JLA';
       
        if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
                ssh2_scp_send($connection, $LocalDirectory."/FireSafety/" . $FireSafetyFileName.'.xls', $ServerDirectory.$FireSafetyFileName.'.xls');   
                ssh2_scp_send($connection, $LocalDirectory."/ComplianceLaundry/" . $ComplianceLaundryFileName.'.xls', $ServerDirectory.$ComplianceLaundryFileName.'.xls');   
        }
        
        $dataEmail = [];
        $dataEmail['FSFile'] = $FireSafetyFileName.'.xls';
        $dataEmail['CLFile'] = $ComplianceLaundryFileName.'.xls';
        
        
        $arrayMailTo = ['SSwain@jla.com','apanwar@usethegeeks.co.uk'];
//        $arrayMailTo = ['diallersupport@intelling.co.uk'];
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.JLA';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'JLA Fire Safety (' . date('Y-m-d') . ')';
        $mail_data['data'] = @$dataEmail;

        $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                    $m->from($mail_data['from'], 'API Reports');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'API Reports');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                });
    }

}
