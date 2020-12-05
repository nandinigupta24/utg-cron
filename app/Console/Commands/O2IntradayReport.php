<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendDiallerOperationMail;
use DB;
use Excel;
use ZipArchive;
use File;

class O2IntradayReport extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:O2IntradayReport';

    /**
     * The console command description.
     * 
     * @var string
     */
    protected $description = 'Email Cron to Dialler Team and Operations';

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

        $start = $end = date('Y-m-d');
//        $start = $end = '2020-03-13';

//        $query = "Select CallDate,DROP_CALL,Calls, Connects, DMCs, ROUND(Connects/Calls*100,2) as PCA,ROUND(DROP_CALL/Calls*100,2) as Abandon,ROUND(DMCs/Connects*100,2) as DMCRate,Sales,Completed,ROUND(Sales/DMCs*100,2) as ConversionRate,ManDials
//from
// (select HOUR(call_date) as CallDate,
//  sum(case when status is not null and (val.comments NOT IN ('CHAT','EMAIL') OR val.comments IS NULL) then 1 else 0 end) as Calls,
//  sum(case when val.status in (select status from status_combined where human_answered = 'Y') then 1 else 0 end) as Connects,
//  sum(case when val.status in (select status from status_combined where customer_contact = 'Y') then 1 else 0 end) as DMCs,
//  sum(case when val.status in (select status from status_combined where Sale = 'Y') then 1 else 0 end) as Sales,
//  sum(case when val.status in (select status from status_combined where completed = 'Y') then 1 else 0 end) as Completed,
//  sum(case when val.comments='MANUAL' then 1 else 0 end) as ManDials,
//  sum(case when val.status='DROP' then 1 else 0 end) as DROP_CALL
//  from inbound_log val
//  WHERE call_date >= '" . $start . " 00:00:00' AND campaign_id IN ('O2_Sales','O2_Sales_Pr') group by HOUR(call_date)) a WHERE CallDate >= 10 ORDER BY CallDate";
        $query = "Select CallDate AS `Hour`,
Calls AS `Calls_Offered`,
ANSWERED AS `Calls_Answered`,
ABANDON AS `Calls_Abandoned`,
ROUND(ANSWERED/Calls*100,2) AS `PCA`,
Sales
from
(select HOUR(call_date) as CallDate,
sum(case when status is not null and (val.comments NOT IN ('CHAT','EMAIL') OR val.comments IS NULL) then 1 else 0 end) as Calls,
sum(case when val.status in (select status from status_combined where Sale = 'Y') then 1 else 0 end) as Sales,
sum(case when val.term_reason ='ABANDON' then 1 else 0 end) as ABANDON,
sum(case when val.term_reason IN ('AGENT','CALLER','NONE') then 1 else 0 end) as ANSWERED
from custom_view.inbound_log val
WHERE call_date >= '".$start." 10:00:00' AND campaign_id IN ('O2_Sales') group by HOUR(call_date)) a WHERE CallDate >= 10 ORDER BY CallDate";



        $data = DB::connection('MainDialer')->select($query);

//        $query = "Select CallDate,DROP_CALL,Calls, Connects, DMCs, ROUND(Connects/Calls*100,2) as PCA,ROUND(DROP_CALL/Calls*100,2) as Abandon,ROUND(DMCs/Connects*100,2) as DMCRate,Sales,Completed,ROUND(Sales/DMCs*100,2) as ConversionRate,ManDials
//from
// (select HOUR(call_date) as CallDate,
//  sum(case when status is not null and (val.comments NOT IN ('CHAT','EMAIL') OR val.comments IS NULL) then 1 else 0 end) as Calls,
//  sum(case when val.status in (select status from status_combined where human_answered = 'Y') then 1 else 0 end) as Connects,
//  sum(case when val.status in (select status from status_combined where customer_contact = 'Y') then 1 else 0 end) as DMCs,
//  sum(case when val.status in (select status from status_combined where Sale = 'Y') then 1 else 0 end) as Sales,
//  sum(case when val.status in (select status from status_combined where completed = 'Y') then 1 else 0 end) as Completed,
//  sum(case when val.comments='MANUAL' then 1 else 0 end) as ManDials,
//  sum(case when val.status='DROP' then 1 else 0 end) as DROP_CALL
//  from inbound_log val
//  WHERE call_date >= '" . $start . " 00:00:00' AND campaign_id IN ('O2_Retentio','O2_Ret_Pro') group by HOUR(call_date)) a WHERE CallDate >= 10 ORDER BY CallDate";
        $query = "Select CallDate AS `Hour`,
Calls AS `Calls_Offered`,
ANSWERED AS `Calls_Answered`,
ABANDON AS `Calls_Abandoned`,
ROUND(ANSWERED/Calls*100,2) AS `PCA`,
Sales
from
(select HOUR(call_date) as CallDate,
sum(case when status is not null and (val.comments NOT IN ('CHAT','EMAIL') OR val.comments IS NULL) then 1 else 0 end) as Calls,
sum(case when val.status in (select status from status_combined where Sale = 'Y') then 1 else 0 end) as Sales,
sum(case when val.term_reason ='ABANDON' then 1 else 0 end) as ABANDON,
sum(case when val.term_reason IN ('AGENT','CALLER','NONE') then 1 else 0 end) as ANSWERED
from custom_view.inbound_log val
WHERE call_date >= '".$start." 10:00:00' AND campaign_id IN ('O2_Retentio') group by HOUR(call_date)) a WHERE CallDate >= 10 ORDER BY CallDate";



        $data1 = DB::connection('MainDialer')->select($query);


//        $arrayMailTo = ['Nicola.Sharrock@intelling.co.uk', 'Sarah.Berry@intelling.co.uk', 'Mike.Oxton@intelling.co.uk','apanwar@usethegeeks.co.uk'];
        $arrayMailTo = ['Nicola.Sharrock@intelling.co.uk', 'Sarah.Berry@intelling.co.uk', 'Mike.Oxton@intelling.co.uk','Stephen.Philbin@intelling.co.uk','annie.seisay@intelling.co.uk','Phil.Morgan@intelling.co.uk',"Aoife.O'Reilly@intelling.co.uk",'apanwar@usethegeeks.co.uk'];
//        $arrayMailTo = ['apanwar@usethegeeks.co.uk'];
//        $arrayMailTo = ['apickett@usethegeeks.co.uk'];
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.email_demo';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'O2 Intraday Report';
        $mail_data['data1'] = $data;
        $mail_data['data2'] = $data1;    

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
