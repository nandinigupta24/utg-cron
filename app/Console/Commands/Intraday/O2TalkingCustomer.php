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

class O2TalkingCustomer extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:O2TalkingCustomer';

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
      
        $date = date('Y-m-d');
        
        $data = [];
        $data['date'] = $date;
        
        if(date('H') > 20){
            exit;
        }
        
        if(date('H') >= 0 && date('H') < 10){
            exit;
        }
        $campaignId = ['TalkingCust','TalkCust2'];
        
        $data['campaignId'] = $campaignId;

        $AHT = get_talking_customer_ITVSO($date,$campaignId);
        
        $AHTarray = [];
        foreach($AHT as $value){
            if(!empty($value->lessThan15) && $value->lessThan15 >0){
                $AHTarray['Hour'][] = (int) $value->Hour;
                $InboundTeam = (($value->lessThan15/$value->total))*100;
                $AHTarray['InboundTeam'][] =(int) number_format($InboundTeam,2);
                $AHTarray['Overflow'][] = (int) (100 - $InboundTeam);
            }else{
                $AHTarray[$value->Hour] = 0;
            }
        }
        
        $data['AHT'] = $AHTarray;
        
        
        $SLA = get_talking_customer_SLA($date,$campaignId);
       
        $SLAarray = [];
        foreach($SLA as $value){
            if(!empty($value->lessThan15) && $value->lessThan15 >0){
                $SLAarray[] = (int) number_format(((($value->lessThan15/$value->total))*100),2);
            }else{
                $SLAarray[] = 0;
            }
        }
         $data['SLA'] = $SLAarray;
         
        /*Abandon Graph*/
        $abandonGet = get_talking_customer_ABANDON($date,$campaignId);
        
        $abandonGraph = [];
        foreach($abandonGet as $value){
            $abandonGraph['Hour'][] = (int) $value->Hour;
            $output = ($value->greaterthan15*100)/$value->total;
            $abandonGraph['>15'][] = (int) number_format($output,2);    
            $abandonGraph['0-15'][] = (int) (100 - $output);    
        }
        
        $data['abandonGraph'] = $abandonGraph;
        $totalOffered = [];
        $totalAbondaned = [];

//        $EmailTo = ['DiallerTeam@intelling.co.uk'];
        $EmailTo = ["aoife.o'reilly@intelling.co.uk",'danielle.rossall@intelling.co.uk','Jason.Moffett@intelling.co.uk','Harry.Morrison@intelling.co.uk','Sarah.Berry@intelling.co.uk','Nicola.Sharrock@intelling.co.uk','Mike.Oxton@intelling.co.uk','George.Eastham@switchexperts.co.uk','James.Wilson@intelling.co.uk','Collin.Alexander@intelling.co.uk'];
        $EmailCC = ['akumar@usethegeeks.com'];
        /* Start Mail */
        $mail_data = array();
        $mail_data['to'] = $EmailTo;
        $mail_data['from'] = 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = '';
        $mail_data['view'] = 'emails.intraday.o2_talking_customer';
        $mail_data['cc'] = $EmailCC;
        $mail_data['subject'] = 'Talking Customer Intraday Report';
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
