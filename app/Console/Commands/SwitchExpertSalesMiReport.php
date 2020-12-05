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

class SwitchExpertSalesMiReport extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:SwitchExpertSalesMiReport {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command is used for SwitchExpertSalesMiReport with optional parameter date ex: 2020-11-26';

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
        //$start = Carbon::now()->startOfDay()->toDateTimeString();
        //$end = Carbon::now()->endOfDay()->toDateTimeString();

        if($date = $this->argument('date')) {
          $start = date('Y-m-d 00:00:00' , strtotime($date));
          $end = date('Y-m-d 23:59:59' , strtotime($date));
        }else {
          $start = Carbon::now()->startOfDay()->toDateTimeString();
          $end = Carbon::now()->endOfDay()->toDateTimeString();
        }

        // $start = '2020-09-29 00:00:00';
        // $end = '2020-09-29 23:59:59';

        $query = "select

s.sale_id,

s.lead_id,

s.sale_date,

c.phone_number,

-- c.alt_phone AS 'alternative number',

s.sold_by,

coalesce(al.user_group, ol.user_group, il.user_group) as user_group,

s.product_type,

s.make,

s.model,

s.order_num,

s.campaign_sold_on,

s.tariff_type,

s.upfront_cost,

c.vendor_id,

c.list_id,

ss.source_id,

coalesce(al.campaign_id, ol.campaign_id) as campaign_id,

il.campaign_id as inbound_group,
c.first_name,
c.last_name,
c.postal_code,
s.cost,
CAM.campaign_name,
coalesce(al.user, ol.user) as user,
LISTCAM.security_phrase,
(select group_concat(make) from SwitchExperts.sales where lead_id = s.lead_id and product_type = 'Addon') as `add_ons`
from SwitchExperts.sales s
left join custom_view.campaigns CAM on s.campaign_sold_on = CAM.campaign_id
left join custom_view.list LISTCAM on s.lead_id = LISTCAM.lead_id
join SwitchExperts.customers c on s.lead_id = c.lead_id

JOIN custom_view.sales_by_source_se ss ON ss.sale_id = s.sale_id

left join

  (

select lead_id , from_unixtime(talk_epoch) as log_start, from_unixtime(dispo_epoch + dispo_sec)  as log_end, u.user,u.full_name as agent_name, campaign_id,

ifnull(al.user_group, u.user_group) as user_group

from custom_view.agent_log al

join custom_view.users u on al.user = u.user

) al on s.lead_id = al.lead_id and s.sale_date between al.log_start and al.log_end and s.sold_by = al.agent_name

left join

(select lead_id, ol.call_date as call_start, from_unixtime(ol.end_epoch) as call_end, u.user, u.full_name as agent_name, ifnull(ol.user_group, u.user_group) as user_group,

campaign_id

from custom_view.inbound_log ol

join custom_view.users u on ol.user = u.user

) il on s.lead_id = il.lead_id and s.sale_date between il.call_start and il.call_end and s.sold_by = il.agent_name

left join

(select lead_id, ol.call_date as call_start, from_unixtime(ol.end_epoch) as call_end, u.user, u.full_name as agent_name, ifnull(ol.user_group, u.user_group) as user_group, campaign_id

from custom_view.outbound_log ol

join custom_view.users u on ol.user = u.user

) ol

on s.lead_id = ol.lead_id and s.sale_date between ol.call_start and ol.call_end and s.sold_by = ol.agent_name

where

s.sale_date BETWEEN '".$start."' and '".$end."'";
        $SESales = DB::connection('OmniDialer')->select($query);

        $array = [];
        $Count = 0;
        foreach ($SESales as $val) {
            $Count++;
            $array[$Count]['CampaignID'] = @$val->campaign_id;
            $array[$Count]['Campaign'] = @$val->campaign_name;
            $array[$Count]['Date_Of_Sale'] = date('Y-m-d',strtotime($val->sale_date));
            $array[$Count]['Time_Of_Sale'] = date('H:i:s',strtotime($val->sale_date));
            $array[$Count]['SaleID'] = @$val->sale_id;
            $array[$Count]['AgentID'] = @$val->sold_by;
            $array[$Count]['Source'] = @$val->source_id;
            $array[$Count]['Provider'] = @$val->make;
            $array[$Count]['Package_Name'] = @$val->model;
            $array[$Count]['Package_Price'] = @$val->cost;
            $array[$Count]['OrderID'] = @$val->order_num;
            $array[$Count]['Sale/Decline'] = ($val->order_num == '000000000') ? 'DECLINE' : 'SALE';
            $array[$Count]['FirstName'] = $val->first_name;
            $array[$Count]['Surname'] = $val->last_name;
            $array[$Count]['Postcode'] = $val->postal_code;
            $array[$Count]['Telephone'] = @$val->phone_number;
            $array[$Count]['LeadID'] = @$val->lead_id;
            $array[$Count]['Security_Phrase'] = @$val->security_phrase;
            $array[$Count]['Addons'] = @$val->add_ons;
        }

        $filename = date('Y-m-d') . '-SwitchExpertsMIReport';
        Excel::create($filename, function($excel) use($array) {
            $excel->setTitle('SE Sales');
            $excel->sheet('SE Sales', function($sheet) use($array) {
                $sheet->fromArray($array);
            });
        })->store('xls', storage_path('Email/SESales'), true);


        $arrayMailTo = ['ngupta@usethegeeks.co.uk', 'Jason.Moffett@intelling.co.uk','anna.rickers@intelling.onmicrosoft.com','James.Wilson@intelling.co.uk','Harry.Morrison@intelling.co.uk','Emma.Eeles@intelling.co.uk','Collin.Alexander@intelling.co.uk','Lizzie.Mckie@intelling.co.uk','justin.naidoo@outworx.co.za','Fanie.olivier@outworx.co.za','Kerov.govender@outworx.co.za','Siyabonga.masinga@outworx.co.za'];
//        $arrayMailTo = ['akumar@usethegeeks.co.uk'];
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.email';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'Switch Experts - MI report - ' . date('Y-m-d');
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
