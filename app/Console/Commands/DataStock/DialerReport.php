<?php

namespace App\Console\Commands\DataStock;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\ScriptResponse;
use App\Model\SwitchExpertEnergy\SDSales;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;

class DialerReport extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:DialerReport';

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
        
        $strQuery = "select l.source_id,count(*) as 'record_count' from custom_view.`list` l
where l.entry_date between '" . $start . "' and '" . $end . "'
and l.source_id not in('VDCL') group by l.source_id";

        $arrayDialer = ['MainDialer', 'OmniDialer'];
        $dataEx = [];

        foreach ($arrayDialer as $dialer) {
            $dataDialer = DB::connection($dialer)->select($strQuery);
            foreach ($dataDialer as $value) {
                $dialerCat = get_cat_dialer($dialer, $value->source_id);
                if (!empty($dataEx[$dialerCat])) {
                    $dataEx[$dialerCat] += $value->record_count;
                } else {
                    $dataEx[$dialerCat] = $value->record_count;
                }
            }
        }
        
        $fileName = 'Data_Stock_V2('.date('Y-m-d',strtotime($start)).'-'.date('Y-m-d',strtotime($end)).')';
        
        $file = Excel::create($fileName, function($excel) use($dataEx) {
                    $excel->setTitle('Data Stock V2');
                    $excel->sheet('Data Stock V2', function($sheet) use($dataEx) {
                        $sheet->appendRow(['Leads', 'Campaign_Type']);
                        $sheet->setOrientation('landscape');
                        foreach ($dataEx as $key => $value) {
                            $sheet->appendRow([
                                'id1' => $value,
                                'id2' => $key
                            ]);
                        }
                    });
                })->store("xls",storage_path('DataStock/Dialer/'), true);
        $arrayMailTo = ['steve.taylor@Intelling.co.uk', 'Dan.Cooper@intelling.co.uk'];

        /* Start Mail */
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.email';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'Data Stock V2 Report';

        $result = Mail::send($mail_data['view'], $mail_data, function ($m) use ($mail_data, $fileName) {
                    $m->from($mail_data['from'], 'Intelling');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                    $m->attach(storage_path('DataStock/Dialer/').$fileName.'.xls');
                });
        unset($_GET['mail']);
    }

}
