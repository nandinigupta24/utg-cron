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

class SynergyDupesProcess extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:SynergyDupesProcess';

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
        $server = 'usethegeeks.ftp.uk';
        $serverPort = 22;
        $serverUser = 'akumar1';
        $serverPassword = '16IndiaGeeksUK';
        $connection = ssh2_connect($server, $serverPort);
        if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
           echo 'connected';
           
           $sftp = ssh2_sftp($connection);
            $sftp_fd = intval($sftp);


            $files = scandir("ssh2.sftp://$sftp_fd/Synergy/TTDupesCheck/", SCANDIR_SORT_DESCENDING);
            $newFile = [];
            
            foreach ($files as $keyval=>$value) {
                 $filename = pathinfo($value, PATHINFO_FILENAME);
                
            $date = date("Y-m-d", filemtime("ssh2.sftp://".$sftp_fd."/Synergy/TTDupesCheck/" . $value));
                
//                    $FileImportLog = new FileImportLog();
//                    $FileImportLog->filename = $value;
//                    $FileImportLog->type = 'TalkTalk';
                    $SaveFileName = date('Y-m-d').'-'.$keyval.'-' . $value;
                    $content = file_get_contents("ssh2.sftp://$sftp_fd/Synergy/TTDupesCheck/".$value);
                    $fp = fopen("/var/www/html/cron/storage/Automation/SynergyDupesCheck/In" . $SaveFileName, "w");
                    fwrite($fp, $content);
                    fclose($fp);
//                    $FileImportLog->save_filename = $SaveFileName;
//                    if($FileImportLog->save()){
                    
                        $newFile[$filename] = '/var/www/html/cron/storage/Automation/SynergyDupesCheck/In'.$SaveFileName;
//                        break;
//                    }
            }
            
            
            $exportFile = [];
             foreach ($newFile as $k=>$val) {
                 
                $reader = Excel::load($val)->get();
                $dataLoaded = $reader->toArray();
                
                $toalArray = [];
                foreach($dataLoaded as $value){
                    if(!empty($value['phone_number'])){
                        $toalArray[] = $value['phone_number'];
                    }
                }
                
                
                $dupesArray = DB::connection('Intelling')
                        ->table('SynergyTTAPI')
                        ->whereIn('phone_number',$toalArray)
                        ->pluck('phone_number')
                        ->toArray();
                
//                pr($dataLoaded);
                $arrayLastInsert = [];
                foreach($dataLoaded as $key=>$value){
                    if(empty($value['phone_number'])){
                        continue;
                    }
                    $arrayLastInsert[$key]['phone_number'] = $value['phone_number'];
                    if(in_array($value['phone_number'],$dupesArray)){
                        $arrayLastInsert[$key]['dupes_phone'] = 'Yes';
                    }else{
                        $arrayLastInsert[$key]['dupes_phone'] = 'No';
                    }
                    $arrayLastInsert[$key]['list_id'] = $value['list_id'];
                    $arrayLastInsert[$key]['title'] = $value['title'];
                    $arrayLastInsert[$key]['first_name'] = $value['first_name'];
                    $arrayLastInsert[$key]['last_name'] = $value['last_name'];
                    $arrayLastInsert[$key]['address_1'] = $value['address_1'];
                    $arrayLastInsert[$key]['address_2'] = $value['address_2'];
                    $arrayLastInsert[$key]['address_3'] = $value['address_3'];
                    $arrayLastInsert[$key]['city'] = $value['city'];
                    $arrayLastInsert[$key]['post_code'] = $value['post_code'];
                    $arrayLastInsert[$key]['county'] = $value['county'];
                    $arrayLastInsert[$key]['email'] = $value['email'];
                    $arrayLastInsert[$key]['security_phrase'] = $value['security_phrase'];
                }
                    
                
                Excel::create($k, function($excel) use($arrayLastInsert,$val) {
                    $excel->sheet('Dupes Check', function($sheet) use($arrayLastInsert,$val) {
                        $sheet->setOrientation('landscape');
                        $sheet->fromArray($arrayLastInsert);
                    });
                })->store('xls', storage_path('Automation/SynergyDupesCheck/Out'), true);
               
             }
             
             
             
             
        $arrayMailTo = ['apanwar@usethegeeks.co.uk'];
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.email';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'SynergyTalkTalk Phone Dupes - ' . date('Y-m-d');
        $mail_data['pdf_type'] = !empty($data['pdf_type']) ? $data['pdf_type'] : "Pdf Attachment";
        $mail_data['pdf_attachment'] = !empty($data['pdf_attachment']) ? $data['pdf_attachment'] : null;


        $result = Mail::send($mail_data['view'], $mail_data, function ($m) use ($mail_data,$newFile) {
                    $m->from($mail_data['from'], 'API Reports');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'API Reports');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                    foreach($newFile as $k=>$v){
                        $m->attach(storage_path('Automation/SynergyDupesCheck/Out/').$k.'.xls');
                    }
                    
                }); 
             
        }
        
    }

}
