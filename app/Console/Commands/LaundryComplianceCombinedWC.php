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

class LaundryComplianceCombinedWC extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:LaundryComplianceCombinedWC';

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
        
//        $start = $end = '2019-05-10';
        $start = $end = date('Y-m-d');
        $status = ['Declined - UH Service JLA',
                   'Declined - Use a Competitor',
                   'Declined - Cost / Value',
                   'Declined - Included',
                   'No Opportunity',
                   'Passed To JLA',
                   'Order',
                   'Quoted',
                   'Unavailable'];
        $query = "select c.lead_id,c.Version,c.connect,c.vendor_id,c.list_id,c.phone_number,c.title,c.first_name,c.last_name,c.address1,c.address2,c.address3,c.city,c.postal_code,c.date_of_birth,c.alt_phone,c.email,c.comments,c.campaign,c.fronter,c.fullname,c.vendor_lead_code,c.DateSent,c.last_submitted,c.pencilWorkIn,c.preferableData,c.okPrice,c.responsiblePersonName,c.responsiblePersonPosition,c.responsiblePersonEmail,c.responsiblePersonPhone,c.responsibleAvailable,c.businessRole,c.authoriseCosts,(select s.status from custom_view.status_combined s where s.status = l.status group by status)as LastOutcome,(select custom_2 from custom_view.custom_fields_data cfd where l.lead_id=cfd.lead_id)as PivotalNumber,(select custom_3 from custom_view.custom_fields_data cfd where l.lead_id=cfd.lead_id)as TotalOrderValue,(select custom_6 from custom_view.custom_fields_data cfd where l.lead_id=cfd.lead_id) as PMVVolume,(select custom_7 from custom_view.custom_fields_data cfd where l.lead_id=cfd.lead_id) as PMVUnitValue,(select custom_8 from custom_view.custom_fields_data cfd where l.lead_id=cfd.lead_id) as TotalPMVvalue,(select custom_9 from custom_view.custom_fields_data cfd where l.lead_id=cfd.lead_id) as DuctVolume,(select custom_10 from custom_view.custom_fields_data cfd where l.lead_id=cfd.lead_id)as DuctunitValue,(select custom_11 from custom_view.custom_fields_data cfd where l.lead_id = cfd.lead_id)as TotalDuctValue from  JLA.customers c
 
inner join custom_view.list l on c.lead_id=l.lead_id

where c.DateSent between '".$start." 00:00:00' and '".$end." 23:59:59'";
    
       $data = DB::connection('OmniDialer')->select($query);
       $StatusCombined = DB::connection('OmniDialer')->table('status_combined')->pluck('status_name','status')->toArray();
       
        $NewArray = [];
        foreach($data as $key=>$value){
            
            if(!in_array($StatusCombined[$value->LastOutcome],$status)){
                continue;
            }
            if(!empty($NewArray[$value->LastOutcome]['Status'])){
                $NewArray[$value->LastOutcome]['PivotalNumber'] = ($NewArray[$value->LastOutcome]['PivotalNumber'] + $value->PivotalNumber);
                $NewArray[$value->LastOutcome]['PivotalNumberCount'] = ($NewArray[$value->LastOutcome]['PivotalNumberCount'] + 1);
                $NewArray[$value->LastOutcome]['TotalOrderValue'] = ($NewArray[$value->LastOutcome]['TotalOrderValue']+ $value->TotalOrderValue);
                $NewArray[$value->LastOutcome]['Status'] = $StatusCombined[$value->LastOutcome];
            }else{
                $NewArray[$value->LastOutcome]['PivotalNumber'] = $value->PivotalNumber;
                $NewArray[$value->LastOutcome]['PivotalNumberCount'] = 1;
                $NewArray[$value->LastOutcome]['TotalOrderValue'] = $value->TotalOrderValue;
                $NewArray[$value->LastOutcome]['Status'] = $StatusCombined[$value->LastOutcome];
            }
        }
        array_sort_by_column($NewArray,'Status');
        $filename = 'Laundry_Compliance_Combined_WC-'.$start;
        Excel::create($filename, function($excel) use($NewArray){
                $excel->sheet('Laundry Compliance', function($sheet) use($NewArray){
                    $sheet->appendRow(['LastOutcome', 'Value Presented', 'Unique Companies', 'Value Secured']);
                    $dataArray = [];
                    $dataArray['Yellow'] = ['Quoted','Unavailable'];
                    $dataArray['Green'] = ['Order','Passed To JLA'];
                    $keyCount = 2;
                foreach($NewArray as $key=>$value){
                    if($value['Status'] == 'Order'){
                        $sheet->appendRow([
                                    'id1' => @$value['Status'],
                                    'id2' => '£'.number_format($value['TotalOrderValue'],2),
                                    'id3' => $value['PivotalNumberCount'],
                                    'id4' => '£'.number_format($value['TotalOrderValue'],2)
                                ]);
                    }else{
                        $sheet->appendRow([
                                    'id1' => @$value['Status'],
                                    'id2' => '£'.number_format($value['TotalOrderValue'],2),
                                    'id3' => $value['PivotalNumberCount'],
                                    'id4' => ''
                                ]);
                    }
                    $ColorCodeRed = '#FF0000';
                    $ColorCodeGreen = '#92D050';
                    $ColorCodeYellow = '#FFC000';
                    $ColorCodeGrey = '#A6A6A6';
                    if(in_array($value['Status'],$dataArray['Yellow'])){
                        $sheet->cells('A'.$keyCount.':D'.$keyCount, function ($cells) use($ColorCodeYellow){
                             $cells->setBackground($ColorCodeYellow);
                         });
                    }elseif(in_array($value['Status'],$dataArray['Green'])){
                        $sheet->cells('A'.$keyCount.':D'.$keyCount, function ($cells) use($ColorCodeGreen){
                             $cells->setBackground($ColorCodeGreen);
                         });
                    }else{
                         $sheet->cells('A'.$keyCount.':C'.$keyCount, function ($cells) use($ColorCodeRed){
                             $cells->setBackground($ColorCodeRed);
                         });
                         $sheet->cells('D'.$keyCount, function ($cells) use($ColorCodeGrey){
                             $cells->setBackground($ColorCodeGrey);
                         });
                    }
                     $keyCount++;
                }
               });
            })->store('xls',storage_path('Automation/LaundryComplianceCombinedWC'), true);
        
        
        $arrayMailTo = ['Mike.Oxton@intelling.co.uk','Nicola.Sharrock@intelling.co.uk','Sarah.Berry@intelling.co.uk','apanwar@usethegeeks.co.uk'];
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.email';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com','SSwain@jla.com'];
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'Laundry Compliance Combined WC (' . date('Y-m-d',strtotime($start)) . ')';
        $mail_data['data'] = @$dataEmail;

        $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data,$filename) {
                    $m->from($mail_data['from'], 'API Reports');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'API Reports');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                    $m->attach(storage_path('Automation/LaundryComplianceCombinedWC/').$filename.'.xls');
                });
    }

}
