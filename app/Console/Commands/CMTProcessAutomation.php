<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Excel;
use Storage;
use Mail;

class CMTProcessAutomation extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CMTProcessAutomation';

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
        $date = Carbon::now()->toDateString(); 
//        $date = Carbon::yesterday()->toDateString();
        $dateFormat = date('Ymd', strtotime($date));
        $content = 'M|intelling|psoptin_intelling_' . $dateFormat . '-000000_' . $dateFormat . '235959.dat|' . $dateFormat . 'T00:00:00Z|' . $dateFormat . 'T23:59:59Z
H|intelling|service_user_id|0|update_date|update_source|B1|B2|B3|CP_Text|CP_E-mail|CP_Phone|CP_Post';
     
//        $query = "SELECT cl.status as OPTOUT,
//cl.phone_number as MPN,
//cl.last_local_call_time as UPDATE_DATE,
//ct.custom_1 as B1
//FROM custom_view.list cl
//join custom_view.custom_fields_data ct
//on cl.lead_id = ct.lead_id
//where cl.list_id in (1218,1422,3012,36502,4000,4003,30032,3655,3003,30032)
//and cl.status like 'OPTOUT'
//and cl.last_local_call_time between '" . $date . " 00:00:00' and '" . $date . " 23:59:59'";
        
        $query = "SELECT cl.status as OPTOUT,cl.phone_number as MPN,cl.last_local_call_time as UPDATE_DATE,ct.custom_1 as B1 
FROM custom_view.list cl
join custom_view.lists ls
on cl.list_id = ls.list_id
join custom_view.custom_fields_data ct
on cl.lead_id = ct.lead_id
where ls.campaign_id = '3045'
and cl.status like 'OPTOUT'
and cl.last_local_call_time between '".$date." 00:00:00' and '".$date." 23:59:59'";
        
        $query1 = "SELECT cl.status as OPTOUT,cl.phone_number as MPN,cl.last_local_call_time as UPDATE_DATE,ct.custom_1 as B1 
FROM custom_view.list cl
join custom_view.lists ls
on cl.list_id = ls.list_id
join custom_view.custom_fields_data ct
on cl.lead_id = ct.lead_id
where ls.campaign_id IN ('1330','3001','3002','3004','3005')
and cl.status like 'OPTOUT'
and cl.last_local_call_time between '".$date." 00:00:00' and '".$date." 23:59:59'";
        
        $data = DB::connection('NewDialer')->select($query);
        
        $data1 = DB::connection('OmniDialer')->select($query1);
        
        foreach ($data as $val) {
            $content .= PHP_EOL . 'I|mobile|' . get_phone_numbers($val->MPN, 44) . '|0|' . str_replace(' ', 'T', $val->UPDATE_DATE) . '|74|N|N|N|N|N|N|N';
        }
        
        foreach ($data1 as $valu) {
            $content .= PHP_EOL . 'I|mobile|' . get_phone_numbers($valu->MPN, 44) . '|0|' . str_replace(' ', 'T', $valu->UPDATE_DATE) . '|74|N|N|N|N|N|N|N';
        }

        
//        $MainDialerQuery = "SELECT cl.status as OPTOUT,
//cl.phone_number as MPN,
//cl.last_local_call_time as UPDATE_DATE,
//ct.Bundle as B1
//FROM custom_view.list cl
//join custom_view.custom_3655 ct
//on cl.lead_id = ct.lead_id
//where cl.list_id in (30032)
//and cl.status like 'OPTOUT'
//and cl.last_local_call_time between '" . $date . " 00:00:00' and '" . $date . " 23:59:59'";
//        $MainDialerData = DB::connection('MainDialer')->select($MainDialerQuery);
//        if(!empty($MainDialerData) && $MainDialerData){
//            foreach ($MainDialerData as $val) {
//                $content .= PHP_EOL . 'I|mobile|' . get_phone_numbers($val->MPN, 44) . '|0|' . str_replace(' ', 'T', $val->UPDATE_DATE) . '|74|N|N|N|N|N|N|N';
//            }
//        }
        $CountTotalData = count(json_decode(json_encode($data), true)) + count(json_decode(json_encode($data1), true));
//        $CountTotalData = count(json_decode(json_encode($data), true));
        
        $content .= PHP_EOL . 'F|' . $CountTotalData;

        $fileName = 'psoptin_intelling_' . $dateFormat . '-000000_' . $dateFormat . '235959';
        $fp = fopen("/var/www/html/cron/storage/Automation/CMT/In/" . $fileName . ".txt", "w");
        fwrite($fp, $content);
        fclose($fp);
        
        $dataExecute = shell_exec('/home/file_conv_CMT.sh');
      
        
        
        $server = '3.8.11.11';
        $serverPort = 22;
        $connection = ssh2_connect($server, $serverPort);
        $serverUser = 'root';
        $serverPassword = 'Utgesx0012!!';
        if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
                ssh2_scp_send($connection, "/var/www/html/cron/storage/Automation/CMT/Out/" . $fileName . '.dat', "/home/CMT/" . $fileName . '.dat');   
        }
        $dataEmail = [];
        $dataEmail['filename'] = $fileName.'.dat';
        $dataEmail['Count'] = $CountTotalData;
        
        $arrayMailTo = ['apanwar@usethegeeks.co.uk','Andy.Hughes@intelling.co.uk','Nicola.Sharrock@intelling.co.uk',env('DIALER_TEAM_EMAIL')];
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.cmt';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'CMT Process (' . $date . ')';
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
