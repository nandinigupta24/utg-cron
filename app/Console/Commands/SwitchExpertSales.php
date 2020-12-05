<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;
use App\Model\IntellingScriptDB\SDSales;

class SwitchExpertSales extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:SwitchExpertSales {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command is used for SwitchExpertSales with optional parameter date ex: 2020-11-26';

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
        if($date = $this->argument('date')) {
          $start = date('Y-m-d 00:00:00' , strtotime($date));
          $end = date('Y-m-d 23:59:59' , strtotime($date));
        }else {
          $start = Carbon::now()->startOfDay()->toDateTimeString();
          $end = Carbon::now()->endOfDay()->toDateTimeString();
        }

        $CampaignArray1 = DB::connection('MainDialer')->table('campaigns')->select('campaign_name', 'campaign_id')->get()->toArray();
        $CampaignArray2 = DB::connection('OmniDialer')->table('campaigns')->select('campaign_name', 'campaign_id')->get()->toArray();
        $CampaignArray = array_merge_recursive($CampaignArray1, $CampaignArray2);

        $SESales = SDSales::where('createddate', '>=', $start)
                ->where('createddate', '<=', $end)
                ->where('saleoutcome', 'Sale')
                ->whereNotNull('salemsorder')
                ->get();


        $array = [];
        $Count = 0;
        foreach ($SESales as $val) {
            $Count++;
            $key = array_search($val->campaign_name, array_column($CampaignArray, 'campaign_id'));
            $array[$Count]['Campaign ID'] = $val->campaign_name;
            $array[$Count]['Campaign'] = (!empty($CampaignArray[$key]->campaign_name) && $CampaignArray[$key]->campaign_name && $key > 1) ? $CampaignArray[$key]->campaign_name : 'Not Available';
            $array[$Count]['Date Of Sale'] = date("Y-m-d", strtotime($val->createddate));
            $array[$Count]['Time Of Sale'] = date("h:i:sa", strtotime($val->createddate));
            $array[$Count]['saleid'] = $val->saleid;
            $array[$Count]['agentid'] = $val->agentid;
            $array[$Count]['Source'] = $val->source_id;
            $array[$Count]['Provider'] = $val->order_provider;
            $array[$Count]['Package Name'] = $val->order_packagename;
            $array[$Count]['Package Price'] = $val->order_packageprice;
            $array[$Count]['Order ID'] = $val->salemsorder;
            $array[$Count]['First Name'] = $val->first_name;
            $array[$Count]['Surname'] = $val->last_name;
            $array[$Count]['Postcode'] = $val->postal_code;
            $array[$Count]['Telephone'] = $val->phone_number;
            $array[$Count]['Lead ID'] = $val->lead_id;
            $array[$Count]['Security Phrase'] = $val->security_phrase;
        }


        $filename = date('Y-m-d') . '-SESales';
        Excel::create($filename, function($excel) use($array) {
            $excel->setTitle('SE Sales');
            $excel->sheet('SE Sales', function($sheet) use($array) {
                $sheet->fromArray($array);
            });
        })->store('xls', storage_path('Email/SESales'), true);


        $arrayMailTo = ['ngupta@usethegeeks.co.uk', 'Jason.Moffett@intelling.co.uk', 'anna.rickers@intelling.onmicrosoft.com', 'James.Wilson@intelling.co.uk', 'Harry.Morrison@intelling.co.uk', 'Emma.Eeles@intelling.co.uk', 'Collin.Alexander@intelling.co.uk', 'Lizzie.Mckie@intelling.co.uk', 'justin.naidoo@outworx.co.za', 'Fanie.olivier@outworx.co.za', 'Kerov.govender@outworx.co.za', 'Siyabonga.masinga@outworx.co.za'];
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.email';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'Switch Expert Sales - ' . date('Y-m-d');
        $mail_data['pdf_type'] = !empty($data['pdf_type']) ? $data['pdf_type'] : "Pdf Attachment";
        $mail_data['pdf_attachment'] = !empty($data['pdf_attachment']) ? $data['pdf_attachment'] : null;


        $result = Mail::send($mail_data['view'], $mail_data, function ($m) use ($mail_data, $filename) {
                    $m->from($mail_data['from'], 'API Reports');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'API Reports');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                    $m->attach(storage_path('Email/SESales/') . $filename . '.xls');
                });
    }

}
