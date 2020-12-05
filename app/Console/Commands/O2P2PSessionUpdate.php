<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Mail;
use Excel;
use App\Model\O2Combine\O2AddconSales;

class O2P2PSessionUpdate extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:O2P2PSessionUpdate';

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

        $start = Carbon::now()->subHours(2)->subMinutes(4)->toDateTimeString();
        $end = Carbon::now()->subMinutes(4)->toDateTimeString();

        if (date('H') > 20) {
            die('BYE');
        }

        if (date('H') >= 0 && date('H') < 10) {
            die('BYE');
        }
        
        $userGroup = ['SLM' =>[
                               'Name'=>'SLM',
                               'CampaignID'=>[3001],
                               'Search'=>['slm']
                              ],
                     'Out'=>[
                              'Name'=>'Outworx',
                              'CampaignID'=>[1330],
                              'Search'=>['out']
                            ],
                     'Belfast'=>[
                              'Name'=>'Belfast',
                              'CampaignID'=>[1330,3001],
                              'Search'=>['blf','belfast']
                            ]
                    ];

        $newArray = [];
        foreach ($userGroup as $k=>$group) {
            if($k == 'Belfast'){
                $groups1 = DB::connection('OmniDialer')
                            ->table('user_groups')
                            ->where('allowed_campaigns', 'LIKE', '%3001%')
                            ->where('user_group', 'like', '%blf%')
                            ->pluck('user_group')->toArray();
                $groups2 = DB::connection('OmniDialer')
                            ->table('user_groups')
                            ->where('allowed_campaigns', 'LIKE', '%1330%')
                            ->where('user_group', 'like', '%blf%')
                            ->pluck('user_group')->toArray();
                $groups = array_merge($groups1,$groups2);
               
            }else{
            $groups = DB::connection('OmniDialer')
                            ->table('user_groups')
                            ->where('allowed_campaigns', 'LIKE', '%'.implode(',',$group['CampaignID']).'%')
                            ->where('user_group', 'like', '%' . implode(',',$group['Search']) . '%')
                            ->pluck('user_group')->toArray();
            }
            
            $query = "select

s.sale_id,

s.lead_id,

s.sale_date,

c.phone_number,

s.sold_by as fullname,

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
c.first_name,
c.last_name,
c.postal_code,
 

s.campaign_sold_on,

coalesce(al.campaign_id, ol.campaign_id) as campaign_id,

il.campaign_id as inbound_group

 

from O2Script.sales s

join O2Script.customers c on s.lead_id = c.lead_id

JOIN custom_view.sales_by_source_O2script ss ON ss.sale_id = s.sale_id

left join

(

select lead_id , from_unixtime(talk_epoch) as log_start, from_unixtime(dispo_epoch + dispo_sec)  as log_end, u.full_name as agent_name, campaign_id,

ifnull(al.user_group, u.user_group) as user_group

from custom_view.agent_log al

join custom_view.users u on al.user = u.user

) al on s.lead_id = al.lead_id and s.sale_date between al.log_start and al.log_end and s.sold_by = al.agent_name

 

left join (select lead_id, ol.call_date as call_start, from_unixtime(ol.end_epoch) as call_end, u.full_name as agent_name, ifnull(ol.user_group, u.user_group) as user_group,

campaign_id

from custom_view.inbound_log ol

join custom_view.users u on ol.user = u.user

) il on s.lead_id = il.lead_id and s.sale_date between il.call_start and il.call_end and s.sold_by = il.agent_name

left join  (select lead_id, ol.call_date as call_start, from_unixtime(ol.end_epoch) as call_end, u.full_name as agent_name, ifnull(ol.user_group, u.user_group) as user_group,

campaign_id

from custom_view.outbound_log ol

join custom_view.users u on ol.user = u.user

) ol

on s.lead_id = ol.lead_id and s.sale_date between ol.call_start and ol.call_end and s.sold_by = ol.agent_name

 

where s.sale_date between '".$start."' and '".$end."' AND s.campaign_sold_on in ('".implode("','",$group['CampaignID'])."') AND s.team IN ('".implode("','",$groups)."') AND s.order_num LIKE '{{SaleType}}'";
                 
            $Accept = count(DB::connection('OmniDialer')->select(str_replace('{{SaleType}}','MS-5%', $query)));

            $Decline = count(DB::connection('OmniDialer')->select(str_replace('{{SaleType}}','MS-0%', $query)));

            $newArray[$group['Name']]['Accept'] = (!empty($Accept)) ? ($Accept) : 0 ;
            $newArray[$group['Name']]['Decline'] = (!empty($Decline)) ? ($Decline) : 0;
        }
      
        $SPATarget = 1;
        $SessionUpdateTime = date('H');
        $SessionUpdateName = date('h A');
        $FTE1 = [];
        $FTE1['SLM'] = get_calculate_FTE_3001($start, $end,[3001],'slm');
        $FTE1['Outworx'] = get_calculate_FTE_3001($start, $end,[1330],'out');
        $FTE1['Belfast'] = get_calculate_FTE_3001($start, $end,[1330,3001],'blf');
        
        
        $data = [];
        $data['newArray'] = $newArray;
        $data['SPATarget'] = $SPATarget;
        $data['SessionUpdateTime'] = $SessionUpdateTime;
        $data['SessionUpdateName'] = $SessionUpdateName;
        $data['FTE1'] = $FTE1;


//        $arrayMailCC = ['dialerteam@usethegeeks.zendesk.com', 'Phil.Morgan@intelling.co.uk', 'craig.winnard@intelling.co.uk', 'Mike.Oxton@intelling.co.uk', 'George.Eastham@switchexperts.co.uk', 'James.Wilson@intelling.co.uk', 'Anthony.Monks@intelling.co.uk', 'danielle.rossall@intelling.co.uk', 'Kelly.McNeill@intelling.co.uk', 'annie.seisay@intelling.co.uk', 'akumar@usethegeeks.com', 'developers@usethegeeks.co.uk'];
        $arrayMailCC = ['akumar@usethegeeks.com','apanwar@usethegeeks.co.uk'];
        $arrayMailTo = ['Sarah.Berry@intelling.co.uk','Nicola.Sharrock@intelling.co.uk'];
//        $arrayMailTo = ['apanwar@usethegeeks.co.uk'];

        /* Start Mail */
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.session_update.o2_p2_p';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : $arrayMailCC;
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'O2 Pre To Post Session Update (' . $SessionUpdateName . ')';
        $mail_data['data'] = $data;
        $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                    $m->from($mail_data['from'], 'Intelling');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                });

        unset($_GET['mail']);
        exit;
    }

}
