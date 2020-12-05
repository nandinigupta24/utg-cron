<?php

namespace App\Console\Commands\TalkTalk;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;

class IntradayReport extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:IntradayReport';

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
        $data = [];
        $data['date'] = $date;
        
        if(date('H') > 20){
            exit;
        }
        
        if(date('H') >= 0 && date('H') <= 8){
            exit;
        }
        $arrayChron = array();
        $arrayChron['title'] = 'TalkTalkIntradayReport';
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


        $FirstEmailTo = ['andy@synergycontactcentre.com','des@synergycontactcentre.com','joechem@synergycontactcentre.com','ashtique@synergycontactcentre.com','ingrid.brackley@intelling.co.uk','anthony.monks@intelling.co.uk'];
//        $FirstEmailCC = ['akumar@usethegeeks.com','Emma.Eeles@intelling.co.uk','developers@usethegeeks.co.uk','conor.henry@intelling.co.uk'];
//        $FirstEmailTo = ['apanwar@usethegeeks.co.uk','pkumar@usethegeeks.co.uk'];
        $FirstEmailCC = ['akumar@usethegeeks.com','Emma.Eeles@intelling.co.uk','developers@usethegeeks.co.uk','conor.henry@intelling.co.uk'];
    
        /* Start Mail */
        $mail_data = array();
        $mail_data['to'] = $FirstEmailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.talktalk.intraday_report';
//        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : $FirstEmailCC;
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'INTERNAL TALKTALK INTRADAY';
        $mail_data['data'] = $data;

        $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                    $m->from($mail_data['from'], 'Intelling');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                });
        
    }

}
