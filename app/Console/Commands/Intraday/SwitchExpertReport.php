<?php

namespace App\Console\Commands\Intraday;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\ScriptResponse;
use App\Model\SwitchExpertEnergy\SDSales;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;

class SwitchExpertReport extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:SwitchExpertReport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description SwitchExpert';

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

        $date = date('Y-m-d');
        $data = [];
        $data['date'] = $date;

        if(date('H') > 21){
            exit;
        }

        if(date('H') >= 0 && date('H') <= 8){
            exit;
        }
        $CampaignID = ['TC_1'=>'TC_1','TC_2'=>'TC_2','TC_3'=>'TC_3','TC_4'=>'TC_4','SwitchExper'=>'SwitchExper'];
        $data['Campaign'] = $CampaignID;

//        $AHT = get_intraday_average_LIS_SEIR($date,$CampaignID);
//        $AHTarray = [];
//        foreach ($AHT as $value) {
//            $AHTarray[$value->Hour] = (int) $value->average;
//        }
//        $data['AHT'] = $AHTarray;
//
//        $QIS = get_intraday_average_QIS_SEIR($date,$CampaignID);
//        $QISarray = [];
//        foreach ($QIS as $value) {
//            $QISarray[$value->Hour] = (int) $value->average;
//        }
//        $data['QIS'] = $QISarray;
//
//        $IWS = get_intraday_IWS_SEIR($date,$CampaignID);
//        $IWSarray = [];
//        foreach ($IWS as $value) {
//            $IWSarray[$value->Hour] = (int) $value->sale;
//        }
//        $data['IWS'] = $IWSarray;
//
//        $SLA = get_intraday_SLA_SEIR($date,$CampaignID);
//
//        $SLAarray = [];
//        foreach ($SLA as $value) {
//            if (!empty($value->lessThan15) && $value->lessThan15 > 0) {
//                $SLAarray[$value->Hour] = (int) ((($value->lessThan15 / $value->total)) * 100);
//            } else {
//                $SLAarray[$value->Hour] = 0;
//            }
//        }
//        $data['SLA'] = $SLAarray;

        $totalOffered = [];
        $totalAbondaned = [];

//        $FirstEmailTo = ["aoife.o'reilly@intelling.co.uk",'danielle.rossall@intelling.co.uk','Jason.Moffett@intelling.co.uk','Harry.Morrison@intelling.co.uk','Sarah.Berry@intelling.co.uk','Nicola.Sharrock@intelling.co.uk','Mike.Oxton@intelling.co.uk','George.Eastham@switchexperts.co.uk','James.Wilson@intelling.co.uk','Shauna.Magee@intelling.co.uk','Collin.Alexander@intelling.co.uk','jason.mcconnell@intelling.co.uk','paul.ryan@intelling.co.uk'];
        $FirstEmailTo = ["aoife.o'reilly@intelling.co.uk",'Jason.Moffett@intelling.co.uk','Harry.Morrison@intelling.co.uk','Sarah.Berry@intelling.co.uk','Nicola.Sharrock@intelling.co.uk','Mike.Oxton@intelling.co.uk','Shauna.Magee@intelling.co.uk','Collin.Alexander@intelling.co.uk','jason.mcconnell@intelling.co.uk','Kelly.McNeill@intelling.co.uk','nicola.rooney@intelling.co.uk'];
//        $FirstEmailTo = ['apanwar@usethegeeks.co.uk'];
        /* Start Mail */
        $mail_data = array();
        $mail_data['to'] = $FirstEmailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.intraday.SwitchExper';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
//        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : $FirstEmailCC;
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'SwitchExper Intraday Report';
        $mail_data['data'] = $data;

        $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                    $m->from($mail_data['from'], 'Intelling');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                });


        exit;
    }

}
