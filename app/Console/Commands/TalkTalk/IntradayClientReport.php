<?php

namespace App\Console\Commands\TalkTalk;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;

class IntradayClientReport extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:IntradayClientReport';

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

        /* Send Mail */
        $date = date('Y-m-d');    
//        $date = '2018-08-31';
        $data = [];
        $data['date'] = $date;
        
        if(date('H') > 20){
            exit;
        }
        
        if(date('H') >= 0 && date('H') <= 8){
            exit;
        }
        $arrayChron = array();
        $arrayChron['title'] = 'TalkTalkClientUpdate';
        $arrayChron['running_time'] = date('Y-m-d H:i:s');
        $arrayChron['start'] = $date;
        $arrayChron['end'] = $date;
        DB::connection('Intelling')->table('chron_log')->insert($arrayChron);
        
        $AHT = get_intraday_average_LIS($date);
        $AHTarray = [];
        foreach ($AHT as $value) {
            $AHTarray[$value->Hour] = $value->average;
        }
        $data['AHT'] = $AHTarray;
        
        $QIS = get_intraday_average_QIS($date);
        $QISarray = [];
        foreach ($QIS as $value) {
            $QISarray[$value->Hour] = $value->average;
        }
        $data['QIS'] = $QISarray;
        
        $IWS = get_intraday_IWS($date);
        $IWSarray = [];
        foreach ($IWS as $value) {
            $IWSarray[$value->Hour] = $value->sale;
        }
        $data['IWS'] = $IWSarray;
        
        $SLA = get_intraday_SLA($date);

        $SLAarray = [];
        foreach ($SLA as $value) {
            if (!empty($value->lessThan15) && $value->lessThan15 > 0) {
                $SLAarray[$value->Hour] = (($value->lessThan15 / $value->total)) * 100;
            } else {
                $SLAarray[$value->Hour] = 0;
            }
        }
        $data['SLA'] = $SLAarray;

        $totalOffered = [];
        $totalAbondaned = [];
        
        /*Second Email*/
        $secondEmailTo = ['annie.seisay@intelling.co.uk','annemarie.bond@telefonica.com','Jason.Hearne@telefonica.com','richard.palmer@telefonica.com','DiallerTeam@intelling.co.uk','Jo.Cornish@telefonica.com','anna.rickers@intelling.co.uk','ingrid.brackley@intelling.co.uk'];
        $secondEmailCC = ['Emma.Eeles@intelling.co.uk','akumar@usethegeeks.com','Dannielle.Harris@intelling.co.uk','Mike.Oxton@intelling.co.uk','danielle.rossall@intelling.co.uk','Collin.Alexander@intelling.co.uk','ciaran.gargan@intelling.co.uk','Liam.Radford@intelling.co.uk','Mark.Ritchie@intelling.co.uk','Mike.Oxton@intelling.co.uk','Nicola.Sharrock@intelling.co.uk','rosie.crosby@intelling.co.uk','Sarah.Berry@intelling.co.uk','developers@usethegeeks.co.uk','conor.henry@intelling.co.uk'];
        /* Start Mail */
        $mail_data = array();
        $mail_data['to'] = $secondEmailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.talktalk.intraday_report';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : $secondEmailCC;
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'CLIENT UPDATE – TALK TALK INTRADAY';
        $mail_data['pdf_type'] = !empty($data['pdf_type']) ? $data['pdf_type'] : "Pdf Attachment";
        $mail_data['pdf_attachment'] = !empty($data['pdf_attachment']) ? $data['pdf_attachment'] : null;
        $mail_data['data'] = $data;


        $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                    $m->from($mail_data['from'], 'Intelling');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                });
        /* End Mail */
        exit;
    }

}
