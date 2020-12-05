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

class SwitchExpertSalesConnex extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:SwitchExpertSalesConnex';

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
        $start = Carbon::now()->startOfDay()->toDateTimeString();
        $end = Carbon::now()->endOfDay()->toDateTimeString();

        
//        $CampaignArray1 = DB::connection('MainDialer')->table('campaigns')->select('campaign_name','campaign_id')->get()->toArray();
//        $CampaignArray2 = DB::connection('OmniDialer')->table('campaigns')->select('campaign_name','campaign_id')->get()->toArray();
//        $CampaignArray = array_merge_recursive($CampaignArray1,$CampaignArray2);

//        $SESales = SDSales::where('createddate', '>=', $start)
//                                ->where('createddate', '<=', $end)
//                                ->where('saleoutcome', 'Sale')
//                                ->whereNotNull('salemsorder')
//                                ->get();
        
        $query = "select
date(s.sale_date) as `sale_date`,
time(s.sale_date) as `sale_time`,
s.lead_id,
ifnull(l.date_inserted, l.entry_date) as `lead_entry_date`,
l.source_id,
s.sold_by as `associate_name`,
l.status as `sale_outcome`,
s.product_type as `sale_type`,
s.make as `provider`,
s.model as `package_name`,
s.cost as `package_price`,
s.order_num as `order_reference`,
c.first_name,
c.last_name,
c.postal_code,
c.phone_number as `telephone`,
(select group_concat(make) from SwitchExperts.sales where lead_id = s.lead_id and product_type = 'Addon') as `add_ons`,
c.campaign_name,
c.campaign_id,
l.security_phrase as traffic_id,
#case when c.list_id = '4006' then cfd.custom_8 else '' end as traffic_id,
case when c.list_id = '4006' then cfd.custom_9 else '' end as form_id,
s.sale_id
from SwitchExperts.sales s
join SwitchExperts.customers c on s.lead_id = c.lead_id
join custom_view.list l on s.lead_id = l.lead_id
left join custom_view.campaigns c on s.campaign_sold_on = c.campaign_id
left join custom_view.custom_fields_data cfd on s.lead_id = cfd.lead_id
where sale_date between '" . $start . "' and '" . $end . "'
and product_type <> 'Addon'";
        $SESales = DB::connection('OmniDialer')->select($query);
       
        $array = [];
        $Count = 0;
        foreach ($SESales as $val) {
            $Count++;
            $array[$Count]['Campaign ID'] = @$val->campaign_id;
            $array[$Count]['Campaign'] = @$val->campaign_name;
            $array[$Count]['Date Of Sale'] = @$val->sale_date;
            $array[$Count]['Time Of Sale'] = @$val->sale_time;
            $array[$Count]['saleid'] = @$val->sale_id;
            $array[$Count]['agentid'] = @$val->associate_name;
            $array[$Count]['Source'] = @$val->source_id;
            $array[$Count]['Provider'] = @$val->provider;
            $array[$Count]['Package Name'] = @$val->package_name;
            $array[$Count]['Package Price'] = @$val->package_price;
            $array[$Count]['Order ID'] = @$val->order_reference;
            $array[$Count]['First Name'] = @$val->first_name;
            $array[$Count]['Surname'] = @$val->last_name;
            $array[$Count]['Postcode'] = @$val->postal_code;
            $array[$Count]['Telephone'] = @$val->telephone;
            $array[$Count]['Lead ID'] = @$val->lead_id;
            $array[$Count]['Addons'] = @$val->add_ons;
        }

        $filename = date('Y-m-d') . '-SESales';
        Excel::create($filename, function($excel) use($array) {
            $excel->setTitle('SE Sales');
            $excel->sheet('SE Sales', function($sheet) use($array) {
                $sheet->fromArray($array);
            });
        })->store('xls', storage_path('Email/SESales'), true);
        
        
        $arrayMailTo = ['Jason.Moffett@intelling.co.uk','anna.rickers@intelling.onmicrosoft.com','James.Wilson@intelling.co.uk','Harry.Morrison@intelling.co.uk','Emma.Eeles@intelling.co.uk','Collin.Alexander@intelling.co.uk','Lizzie.Mckie@intelling.co.uk','justin.naidoo@outworx.co.za','Fanie.olivier@outworx.co.za','Kerov.govender@outworx.co.za','Siyabonga.masinga@outworx.co.za'];
//        $arrayMailTo = ['apanwar@usethegeeks.co.uk'];
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.email';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'SwitchExpertInbound Sales - ' . date('Y-m-d');
        $mail_data['pdf_type'] = !empty($data['pdf_type']) ? $data['pdf_type'] : "Pdf Attachment";
        $mail_data['pdf_attachment'] = !empty($data['pdf_attachment']) ? $data['pdf_attachment'] : null;


        $result = Mail::send($mail_data['view'], $mail_data, function ($m) use ($mail_data,$filename) {
                    $m->from($mail_data['from'], 'API Reports');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'API Reports');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                        $m->attach(storage_path('Email/SESales/').$filename.'.xls');
                    
                }); 
    }

}
