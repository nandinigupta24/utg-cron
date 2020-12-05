<?php

namespace App\Console\Commands\DataStock;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;

class CampaignReport extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CampaignReport';

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

        $start = Carbon::now()->subMonth(1)->startOfMonth();
        $end = Carbon::now()->subMonth(1)->endOfMonth();

        $dataEx = DB::connection('MainDialer')
                ->table('inbound_log')
                ->where('call_date', '>=', $start)
                ->where('call_date', '<=', $end)
                ->count();

        $fileName = 'Data_Stock_V3_IB(' . date('Y-m-d', strtotime($start)) . '-' . date('Y-m-d', strtotime($end)) . ')';

        $file = Excel::create($fileName, function($excel) use($dataEx) {
                    $excel->setTitle('Data Stock V3 IB');
                    $excel->sheet('Data Stock V3 IB', function($sheet) use($dataEx) {
                        $sheet->appendRow(['Leads', 'Campaign_Type']);
                        $sheet->setOrientation('landscape');
                        $sheet->appendRow([
                            'id1' => $dataEx,
                            'id2' => 'LEAD'
                        ]);
                    });
                })->store("xls", storage_path('DataStock/Campaign/'), true);

        $arrayMailTo = ['steve.taylor@Intelling.co.uk', 'Dan.Cooper@intelling.co.uk'];

        /* Start Mail */
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.email';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'Data Stock V3 IB';

        $result = Mail::send($mail_data['view'], $mail_data, function ($m) use ($mail_data, $fileName) {
                    $m->from($mail_data['from'], 'Intelling');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                    $m->attach(storage_path('DataStock/Campaign/') . $fileName . '.xls');
                });
        unset($_GET['mail']);
        /* End Mail */
    }

}
