<?php
namespace App\Console\Commands\Intraday;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;

class O2InboundReportNew extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:O2InboundReportNew';

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

        if (date('H') > 20) {
            exit;
        }

        if (date('H') >= 0 && date('H') < 10) {
            exit;
        }

        $campaignId = [
                       'O2_Sales'=>'O2 BAU',
                       'O2_Sales_1'=>'O2 Customer Services',
                       'O2_Sales_2'=>'O2 Homepage Existing Customer',
                       'O2_Sales_3'=>'O2 Checkout Page',
                       'O2_Sales_4'=>'O2 No label',
                       'O2_Sales_5'=>'O2 Sky Movies Mag',
                       'O2_Sales_6'=>'O2 Inbound Main',
                       'O2_Sales_7'=>'O2 Pontis SMS Low Spend',
                       'O2_Sales_8'=>'O2 Handset Co-op Spare Number',
                       'O2_Sales_9'=>'O2 Pontis SMS High Spend',
                       'O2_Sales_10'=>'O2 Talk Talk',
                       'O2_Sales_11'=>'O2 Pre Pay Retention Warm Tran',
                       'O2_Sales_12'=>'O2 Pontis SMS Low Spend',
                       'O2_Sales_13'=>'O2 Pontis SMS Mid Spend',
                       'O2_Sales_14'=>'O2 Pontis SMS Mid Spend',
                       'O2_Sales_15'=>'O2 Pontis SMS High Spend',
                       'O2_Sales_16'=>'O2 Homepage New Customer',
                       'O2_Sales_17'=>'O2 Easter DM Campaign 2020',
                       'O2_Sales_18'=>'O2 Pontis SMS High Spend'
                        ];

        $data['campaignId'] = $campaignId;

        $AHT = get_o2inbound_intraday_ITVSO($date,$campaignId);

        $AHTarray = [];
        foreach ($AHT as $value) {
            if (!empty($value->lessThan15) && $value->lessThan15 > 0) {
                $AHTarray['Hour'][] = (int) $value->Hour;
                $InboundTeam = (($value->lessThan15 / $value->total)) * 100;
                $AHTarray['InboundTeam'][] = (int) number_format($InboundTeam, 2);
                $AHTarray['Overflow'][] = (int) (100 - $InboundTeam);
            } else {
                $AHTarray[$value->Hour] = 0;
            }
        }

        $data['AHT'] = $AHTarray;


        $SLA = get_o2inbound_intraday_SLA($date, $campaignId);

        $SLAarray = [];
        foreach ($SLA as $value) {
            if (!empty($value->lessThan15) && $value->lessThan15 > 0) {
                $SLAarray[] = (int) number_format(((($value->lessThan15 / $value->total)) * 100), 2);
            } else {
                $SLAarray[] = 0;
            }
        }
        $data['SLA'] = $SLAarray;
        /* Abandon Graph */
        $abandonGet = get_o2inbound_intraday_ABANDON($date, $campaignId);

        $abandonGraph = [];
        foreach ($abandonGet as $value) {
            $abandonGraph['Hour'][] = (int) $value->Hour;
            $output = ($value->greaterthan15 * 100) / $value->total;
            $abandonGraph['>15'][] = (int) number_format($output, 2);
            $abandonGraph['0-15'][] = (int) (100 - $output);
        }

        $data['abandonGraph'] = $abandonGraph;
        $totalOffered = [];
        $totalAbondaned = [];

//        $FirstEmailTo = ["Collin.Alexander@intelling.co.uk", "aoife.o'reilly@intelling.co.uk", 'shahid.ramzan@intelling.co.uk', 'danielle.rossall@intelling.co.uk', 'jason.topping@intelling.co.uk', 'anthony.monks@intelling.co.uk', 'Liam.Radford@intelling.co.uk', 'Kelly.McNeill@intelling.co.uk', 'annie.seisay@intelling.co.uk', 'Rupal.Ahmed@intelling.co.uk', 'Mike.Oxton@intelling.co.uk', 'Mike.Hoye@intelling.co.uk','kerry.rowe@intelling.co.uk'];
//        $FirstEmailCC = ['Sarah.Berry@intelling.co.uk', 'Nicola.Sharrock@intelling.co.uk', 'akumar@usethegeeks.com', 'apanwar@usethegeeks.co.uk'];
        $FirstEmailTo = ['apanwar@usethegeeks.co.uk','Stephen.Philbin@intelling.co.uk','Sarah.Berry@intelling.co.uk'];
        /* Start Mail */
        $mail_data = array();
        $mail_data['to'] = $FirstEmailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.intraday.o2_inbound_report_new';
      $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
//        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : $FirstEmailCC;
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'O2 Sales Inbound Intraday Report';
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
    }

}
