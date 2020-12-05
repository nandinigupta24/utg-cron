<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Excel;
use Storage;
use App\Model\UTGAPI\O2ReturnProcessData;
use App\Model\UTGAPI\O2ReturnProcessFile;
use Mail;

class O2InboundSaleHourlyReport extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:O2InboundSaleHourlyReport';

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

        $CurrentStart = Carbon::now()->startOfDay();
        $CurrentEnd = Carbon::now()->endOfDay();

        $query = "SELECT
c.lead_id
,o.saledate
,c.phone_number
,o.fullname
#,s.team
#,o.user_group
,s.product_type
,s.make
,s.model
,s.order_num
,s.campaign_sold_on
,s.tariff_type
,s.upfront_cost
#,c.vendor_id
#,c.list_id
,ss.source_id
#i.campaign_id
FROM O2Script.customers c
INNER JOIN O2Script.sales s
ON c.lead_id = s.lead_id
JOIN O2Script.sales_by_orig_agent o
ON s.sale_id = o.sale_id
JOIN custom_view.sales_by_source_O2script ss
ON ss.sale_id = o.sale_id
join custom_view.inbound_log i
on c.lead_id = i.lead_id
#where i.campaign_id in ('EnitreMedia','Grosvenor','Ignition','IPTel','ADC','MTA_Leadgen','Neatley','Oil_Genco','OilGenco','OutworxIn','RightDealIN','Sandra','SEMobSwitch','Switch_Expe','Synergy','Synthesis','Topic')
#and s.order_num = 'MS-513195186'
where o.saledate between curdate() and curdate() + interval 1 day
and s.order_num like ('ms-5%')";
        $fileName = 'O2Inbound-SaleHourlyReport-' . date('Y-m-d');
        $data = DB::connection('MainDialer')->select($query);
        $file = Excel::create($fileName, function($excel) use($data) {
                    $excel->sheet('Daily Order ID Report(Inbound)', function($sheet) use($data) {
                        $sheet->setOrientation('landscape');
                        $sheet->appendRow(['Lead ID', 'Sale Date', 'Phone Number', 'Full Name', 'Product Type', 'Make', 'Model', 'Order Number', 'Campaign', 'Tarrif Type', 'Upfront Cost', 'Source ID']);
                        foreach ($data as $value) {
                            $sheet->appendRow([
                                'id1' => @$value->lead_id,
                                'id2' => @$value->saledate,
                                'id3' => @$value->phone_number,
                                'id4' => @$value->fullname,
                                'id5' => @$value->product_type,
                                'id6' => @$value->make,
                                'id7' => @$value->model,
                                'id8' => @$value->order_num,
                                'id9' => @$value->campaign_sold_on,
                                'id10' => @$value->tariff_type,
                                'id11' => @$value->upfront_cost,
                                'id12' => @$value->source_id
                            ]);
                        }
                    });
                })->store("xls", storage_path('Daily/O2InboundOutbound/'), true);

//        $arrayMailTo = ['Andy.Hughes@intelling.co.uk', 'Kelly.McNeill@intelling.co.uk', 'sarah.berry@intelling.co.uk', 'emma.eeles@intelling.co.uk', 'liz.mckie@intelling.co.uk', 'mark.burgess@intelling.co.uk', 'anna.rickers@intelling.co.uk','apanwar@usethegeeks.co.uk'];
        $arrayMailTo = ['apanwar@usethegeeks.co.uk'];
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.email';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'O2Inbound Hourly Report';

        $result = Mail::send($mail_data['view'], $mail_data, function ($m) use ($mail_data, $fileName) {
                    $m->from($mail_data['from'], 'Intelling');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                    $m->attach(storage_path('Daily/O2InboundOutbound/') . $fileName . '.xls');
                });
    }

}
