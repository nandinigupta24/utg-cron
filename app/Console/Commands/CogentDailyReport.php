<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;

class CogentDailyReport extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CogentDailyReport';

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

        $start = Carbon::now()->startOfDay();
//        $start = '2019-03-18 00:00:00';
        $end = Carbon::now()->endOfDay();
        $CampaignArray = ['CogentHubin', 'OutworxIn', 'TouchstonIn'];
        $data = DB::connection('MainDialer')
                ->table('inbound_log')
                 ->join('list','inbound_log.lead_id','list.lead_id')
                ->whereIn('campaign_id', $CampaignArray)
                ->where('call_date', '>=', $start)
                ->where('call_date', '<=', $end)
                ->select('list.list_id','inbound_log.closecallid','inbound_log.call_date','inbound_log.length_in_sec','inbound_log.status','inbound_log.phone_number','inbound_log.campaign_id','inbound_log.queue_seconds','inbound_log.user','inbound_log.lead_id','inbound_log.term_reason')
                ->get();

        $fileName = 'SKIN-IMAGE-Daily-Report(' . date('Y-m-d', strtotime($start)).')';

        $file = Excel::create($fileName, function($excel) use($data) {
                    $excel->setTitle('Skin Image');
                    $excel->sheet('SkinImage', function($sheet) use($data) {
                        $sheet->appendRow(['Serial', 'DATE/TIME','LENGTH','STATUS','PHONE','CAMPAIGN','WAIT(S)','AGENT(S)','LIST','LEAD','HANGUP REASON']);
                        $sheet->setOrientation('landscape');
                        foreach($data as $value){
                            $sheet->appendRow([
                                'id1' => $value->closecallid,
                                'id2' => $value->call_date,
                                'id3' => $value->length_in_sec,
                                'id4' => $value->status,
                                'id5' => $value->phone_number,
                                'id6' => $value->campaign_id,
                                'id7' => $value->queue_seconds,
                                'id8' => $value->user,
                                'id9' => $value->list_id,
                                'id10' => $value->lead_id,
                                'id11' => $value->term_reason,
                            ]);
                        }
                    });
                })->store("xls", storage_path('CogentHUB/'), true);

//        $arrayMailTo = ['steve.taylor@Intelling.co.uk', 'Dan.Cooper@intelling.co.uk'];
        $arrayMailTo = ['apanwar@usethegeeks.co.uk'];

        /* Start Mail */
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.email';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'Skin Image Daily Report';

        $result = Mail::send($mail_data['view'], $mail_data, function ($m) use ($mail_data, $fileName) {
                    $m->from($mail_data['from'], 'Intelling');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                    $m->attach(storage_path('CogentHUB/') . $fileName . '.xls');
                });
        unset($_GET['mail']);
        /* End Mail */
    }

}
