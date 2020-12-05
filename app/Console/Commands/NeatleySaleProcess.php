<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\ScriptResponse;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;


class NeatleySaleProcess extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:NeatleySaleProcess';

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
        ini_set('max_execution_time', 6000);
        $start = Carbon::now()->subHour();
        $end = Carbon::now();
        
        $SaleArray = [];
        $SaleArray['SwitchExpert'] = \App\Model\IntellingScriptDB\SDSales::where('lastupdated','>=',$start)
                ->where('order_provider','Neatley')
                ->whereNotIn('salemsorder',['0','00','000','0000','00000','000000','0000000','00000000','000000000'])
                ->get();
            
//        $SaleArray['RightDeal'] = DB::connection('SE_RightDea')->table('RDSales')
//                ->where('lastupdated','>=',$start)
//                ->where('order_provider','Neatley')
//                ->whereNotIn('salemsorder',['0','00','000','0000','00000','000000','0000000','00000000','000000000'])
//                ->where('report_query','RightDeal')
//                ->get();
//        $SaleArray['TalkingPeople'] = DB::connection('SE_RightDea')->table('RDSales')
//                ->where('lastupdated','>=',$start)
//                ->where('order_provider','Neatley')
//                ->whereNotIn('salemsorder',['0','00','000','0000','00000','000000','0000000','00000000','000000000'])
//                ->where('report_query','TalkingPeople')
//                ->get();
//        
//         $SaleArray['OilGenco'] = DB::connection('SE_RightDea')->table('OilSales')
//                ->where('lastupdated','>=',$start)
//                ->where('order_provider','Neatley')
//                ->whereNotIn('salemsorder',['0','00','000','0000','00000','000000','0000000','00000000','000000000'])
//                ->get();
           
       $SaleFileName = [];
       
           $filename = 'NeatleySale-'.date('YmdHis',strtotime($start)).'-'.date('YmdHis',strtotime($end));
           Excel::create($filename, function($excel) use($SaleArray) {
            $excel->setTitle('Neatley Sales');
            $excel->sheet('Sale Detail', function($sheet) use($SaleArray) {
                
                $sheet->appendRow(['First Name','Last Name','Address Line 1','Address Line 2','Postcode','Landline Phone Number','DOB','Email Address',
                    'Provider','Sale Outcome','Order Number','Package Name','Addons','Package Price','Fibre Sale','Is this sale Direct debit','Landline phone number','New Line']);

                $sheet->setOrientation('landscape');
                $Count = 0;
               foreach($SaleArray as $key=>$val){
                    if(empty($val)){
                       continue; 
                    }
                    foreach($val as $value){
                        if(empty($value->saleid)){
                           continue; 
                        }
                        $Count++;
                    $sheet->appendRow([
                        'id1' => $value->first_name,
                        'id2' => $value->last_name,
                        'id3' => $value->address1,
                        'id4' => $value->address2,
                        'id5' => $value->postal_code,
                        'id6' => $value->phone_number,
                        'id7' => $value->date_of_birth,
                        'id8' => $value->email,
                        'id9' => $value->order_provider,
                        'id10' => $value->saleoutcome,
                        'id11' => $value->salemsorder,
                        'id12' => $value->order_packagename,
                        'id13' => $value->addons,
                        'id14' => $value->order_packageprice,
                        'id15' => $value->fibre_sale,
                        'id16' => $value->direct_debit,
                        'id17' => $value->ll_phone_number,
                        'id18' => $value->new_line,
                    ]);
                
                    }
               }
               if($Count == 0){
                   die('Hello');
               }
            });
        })->store('csv', storage_path('Automation/Neatley/IN'), true);
        $data = shell_exec('/home/file_encry.sh');
        unlink("/var/www/html/cron/storage/Automation/Neatley/IN/" . $filename . '.csv');
      
       
//        $arrayMailTo = ['diallersupport@intelling.co.uk'];
        $arrayMailTo = ['SETLPROV@intelling.co.uk','neatly-provisioning@intelling.co.uk'];
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.neatley_sales';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com','Shauna.Magee@intelling.co.uk'];
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'Neatley Sales';
        $mail_data['data'] = '';

        $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data,$filename) {
                    $m->from($mail_data['from'], 'API Reports');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'API Reports');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                    $m->attach(storage_path('Automation/Neatley/OUT/').$filename.'.zip');
                });
    }

}
