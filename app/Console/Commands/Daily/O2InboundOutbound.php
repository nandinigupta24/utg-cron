<?php
namespace App\Console\Commands\Daily;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;
use App\Model\Intelling\SEMobileSales;

class O2InboundOutbound extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:O2InboundOutbound';

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
        $InboundCampaignID = ['EnitreMedia', 'Grosvenor', 'Ignition', 'IPTel', 'MTA_Leadgen', 'Neatley', 'Oil_Genco', 'OilGenco', 'OutworxIn', 'RightDealIN', 'Sandra', 'SEMobSwitch', 'Switch_Expe', 'Synergy', 'Synthesis', 'Topic','ADC'];

        $start = Carbon::today()->startOfDay();
        $end = Carbon::today()->endOfDay();
        $declineOrderArray = ['MS-0', 'MS-00', 'MS-000', 'MS-0000', 'MS-00000', 'MS-000000', 'MS-0000000', 'MS-00000000', 'MS-000000000', 'MS-0000000000'];
//        $data = DB::connection('O2Inbound')->select("SELECT report_query,orderid,saleid,createddate,agentid,lead_id,phone,opt_in,customername,product_sold,url_hit_data
//                                                    FROM O2Inbound.inboundSales 
//                                                    where orderid  like 'MS-5%'
//                                                    and createddate between '" . $start . "' AND '" . $end . "'");
        $data = DB::connection('MainDialer')
                                    ->table('O2Script.customers')
                                    ->join('O2Script.sales', 'O2Script.customers.lead_id', 'O2Script.sales.lead_id')
                                    ->join('O2Script.sales_by_orig_agent', 'O2Script.sales.sale_id', 'O2Script.sales_by_orig_agent.sale_id')
                                    ->join('custom_view.sales_by_source_O2script', 'O2Script.sales_by_orig_agent.sale_id', 'custom_view.sales_by_source_O2script.sale_id')
                                    ->join('custom_view.inbound_log', 'O2Script.customers.lead_id', 'custom_view.inbound_log.lead_id')
                                    ->where('O2Script.sales_by_orig_agent.saledate', '>=', $start)
                                    ->where('O2Script.sales_by_orig_agent.saledate', '<=', $end)
                                    ->whereIn('custom_view.inbound_log.campaign_id', $InboundCampaignID)
                                    ->where('O2Script.sales.order_num', 'like', 'MS-5%')
                                    ->select('O2Script.sales.sale_id','O2Script.customers.bb_switch_call','O2Script.customers.lead_id','O2Script.customers.first_name','O2Script.customers.last_name','O2Script.sales_by_orig_agent.saledate', 'O2Script.customers.phone_number', 'O2Script.sales_by_orig_agent.fullname', 'O2Script.sales_by_orig_agent.user_group', 'O2Script.sales.product_type', 'O2Script.sales.make', 'O2Script.sales.model', 'O2Script.sales.order_num', 'O2Script.sales.campaign_sold_on', 'O2Script.sales.tariff_type', 'O2Script.sales.upfront_cost', 'O2Script.customers.vendor_id', 'O2Script.customers.list_id', 'custom_view.sales_by_source_O2script.source_id', 'custom_view.inbound_log.campaign_id')
                                    ->get();
       
      
//        $AgentsData = DB::connection('O2Inbound')->table('Agents')->pluck('full_name', 'user')->toArray();
//        $SiteData = DB::connection('O2Inbound')->table('Agents')->pluck('user_group', 'user')->toArray();
        $AgentsData = \App\Model\Intelling\AgentTableCombined::pluck('full_name','user')->toArray();

       $SiteData = \App\Model\Intelling\AgentTableCombined::pluck('user_group','user')->toArray();
        $dataO2Combine = DB::connection('O2Combine')->select("SELECT salemsorder,createddate,agentid,saleid,phone_number,campaign_name,source_id,first_name,middle_initial,saletype,user_group,last_name,saleoutcome,source_id
                                            FROM O2Combine.O2Sales 
                                            where salemsorder like 'MS-5%'
                                            and createddate between '" . $start . "' AND '" . $end . "'");

        $AgentsDataO2Combine = DB::connection('Intelling')->table('AgentTableCombined')->pluck('full_name', 'user')->toArray();

        /* AdditionalData */
        $dataO2CombineAdditional = DB::connection('O2Combine')->select("SELECT a.salemsorder,createddate,agentid,o.saleid,phone_number,campaign_name,source_id,first_name,middle_initial,a.saletype,o.user_group,last_name,a.saleoutcome,o.source_id 
                                                            FROM O2Combine.O2Sales o
                                                            Join O2Combine.O2Sales_additional a
                                                            ON o.saleid  = a.saleid 
                                                            where o.salemsorder like 'MS-5%'
                                                            and o.createddate between '" . $start . "' AND '" . $end . "'");

        $campiagnData = DB::connection('MainDialer')->table('campaigns')->pluck('campaign_name', 'campaign_id');

        $dataConsumer = DB::connection('NewDialer')->select("SELECT c.lead_id
                            ,o.saledate
                            ,c.phone_number
                            ,c.first_name
                            ,c.last_name
                            ,o.fullname
                            ,o.user_group
                            ,s.product_type
                            ,s.make
                            ,s.model
                            ,s.order_num
                            ,s.campaign_sold_on
                            ,s.tariff_type
                            ,s.upfront_cost
                            ,s.sale_id
                            ,c.decline_reason
                            ,c.vendor_id
                            ,c.list_id
                            ,ss.source_id
                            FROM Intelling.customers c
                            INNER JOIN Intelling.sales s ON c.lead_id = s.lead_id
                            JOIN Intelling.sales_by_orig_agent o ON s.sale_id = o.sale_id
                            JOIN custom_view.sales_by_source ss ON ss.sale_id = o.sale_id
                             WHERE s.order_num is not NULL AND s.product_type <> 'Accessory'
                            AND o.saledate >= '" . $start . "' AND o.saledate <= '" . $end . "'");
        
        
         $O2WelcomePAYG = DB::connection('IntellingScriptDB')
                                        ->table('O2WelcomePAYG')
                                        ->where('created_at', '>=', $start)
                                        ->where('created_at', '<=', $end)
                                        ->where('salemsorder','NOT LIKE','000%')
                                        ->whereNotNull('agent_id')
                                        ->get();
         
         $SEMobileData = SEMobileSales::where('createddate', '>=', $start)
                                        ->where('createddate', '<=', $end)
                                         ->where('salemsorder','like','MS-5%')
                                         ->whereNotNull('user_group')
                                        ->get();
         
         $MTASales = DB::connection('MainDialerIntelling')
                ->table('customers')
                ->join('sales','customers.lead_id','sales.lead_id')
                ->join('sales_by_orig_agent','sales.sale_id','sales_by_orig_agent.sale_id')
                ->where('sales_by_orig_agent.saledate','>=',$start)
                ->where('sales_by_orig_agent.saledate','<=',$end)
                ->where('sales.product_type','<>','Accessory')
                ->where('sales.campaign_sold_on',3038)
                ->where('sales.order_num','like','ms-5%')
                ->select('customers.lead_id','sales.sale_id','customers.first_name','customers.last_name','sales_by_orig_agent.saledate','customers.phone_number','sales_by_orig_agent.fullname','sales_by_orig_agent.user_group','sales.product_type','sales.make','sales.model','sales.order_num','sales.campaign_sold_on','sales.tariff_type','sales.upfront_cost','customers.decline_reason','customers.vendor_id','customers.list_id')
                ->get();
         
         
         
         $O2PremiumSale = DB::connection('MainDialer')->select("SELECT c.first_name, c.last_name,
c.lead_id
,o.saledate
,c.phone_number
,o.fullname
,o.user_group
,s.product_type
,s.make
,s.model
,s.order_num
,s.campaign_sold_on
,s.tariff_type
,s.upfront_cost
,c.vendor_id
,c.list_id
,ss.source_id
,s.sale_id
#i.campaign_id
FROM O2Script.customers c
INNER JOIN O2Script.sales s
ON c.lead_id = s.lead_id
JOIN O2Script.sales_by_orig_agent o
ON s.sale_id = o.sale_id
JOIN custom_view.sales_by_source_O2script ss
ON ss.sale_id = o.sale_id
where o.saledate between '".$start."' and '".$end."'
AND s.campaign_sold_on IN (3003,3040,3042,3043)");
         
         
         
         $query = "select
s.sale_id,
s.lead_id,
s.sale_date,
c.phone_number,
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
s.campaign_sold_on,
coalesce(al.campaign_id, ol.campaign_id) as campaign_id,
il.campaign_id as inbound_group,
c.first_name as FirstName,
c.last_name as LastName,
c.title as Title,
c.postal_code as PostalCode
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
where s.sale_date between  '".$start."' and '".$end."' AND s.campaign_sold_on IN (3003,3040,3042,3043,1304,1307,1308)";
         
         
         $MainDialerReport = DB::connection('MainDialer')->select($query);
         
         $query1 = "select
s.sale_id,
s.lead_id,
s.sale_date,
c.phone_number,
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
s.campaign_sold_on,
coalesce(al.campaign_id, ol.campaign_id) as campaign_id,
il.campaign_id as inbound_group,
c.first_name as FirstName,
c.last_name as LastName,
c.title as Title,
c.postal_code as PostalCode
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
where s.sale_date between  '".$start."' and '".$end."' AND s.campaign_sold_on IN (1330,3001,3002,3005)";
         
         
         $OmniDialerReport = DB::connection('OmniDialer')->select($query1);
         
         
         
        $CampaignArray = DB::connection('MainDialer')->table("campaigns")->pluck('campaign_name', 'campaign_id')->toArray();
        $CampaignArrayOMNI = DB::connection('OmniDialer')->table("campaigns")->pluck('campaign_name', 'campaign_id')->toArray();
        
        $fileName = 'Daily_Report(' . date('Y-m-d') . ')';
        $file = Excel::create($fileName, function($excel) use($data, $AgentsData, $dataO2Combine, $AgentsDataO2Combine, $dataO2CombineAdditional, $campiagnData, $dataConsumer, $SiteData, $declineOrderArray, $CampaignArray,$O2WelcomePAYG,$SEMobileData,$MTASales,$O2PremiumSale,$MainDialerReport,$OmniDialerReport,$CampaignArrayOMNI) {
                    $excel->sheet('Daily Order ID Report(Inbound)', function($sheet) use($data, $AgentsData, $declineOrderArray, $SiteData) {
                        $sheet->setOrientation('landscape');
                        $sheet->appendRow(['Sale ID', 'Order ID', 'SIMO/HANDSET', 'Sale Outcome', 'Time of Sale', 'Site', 'Agent ID', 'Lead Id', 'Phone', 'OPTIN', 'Customer Name', 'Report Query', 'Source Id']);
                        foreach ($data as $value) {
                            $sheet->appendRow([
                                'id1' => @$value->sale_id,
                                'id2' => @$value->order_num,
                                'id3' => @$value->product_type,
                                'id4' => 'Accept',
                                'id5' => @$value->saledate,
                                'id6' => @$value->user_group,
                                'id7' => @$value->fullname,
                                'id8' => @$value->lead_id,
                                'id9' => @$value->phone_number,
                                'id10' => @$value->bb_switch_call,
                                'id11' => @$value->first_name.' '.@$value->last_name,
                                'id12' => @$value->campaign_id,
                                'id13' => @$value->source_id,
                            ]);
                        }
                    });
                    $excel->sheet('Daily Order ID Report(Outbound)', function($sheet) use($dataO2Combine, $AgentsDataO2Combine, $dataO2CombineAdditional, $campiagnData, $dataConsumer, $declineOrderArray, $CampaignArray,$O2WelcomePAYG,$SEMobileData,$SiteData,$MTASales,$O2PremiumSale,$MainDialerReport,$OmniDialerReport,$CampaignArrayOMNI) {
                        $sheet->setOrientation('landscape');
                        $sheet->appendRow(['Sale ID', 'Order ID', 'SIMO/HANDSET', 'Sale Outcome', 'Time of Sale', 'Site', 'Agent ID', 'Phone', 'Campaign', 'Campaign Name', 'Source ID', 'First Name', 'Last Name']);
                        foreach ($dataO2Combine as $value) {
                            
                            $sheet->appendRow([
                                'id1' => @$value->saleid,
                                'id2' => @$value->salemsorder,
                                'id3' => @$value->saletype,
                                'id4' => @$value->saleoutcome,
                                'id5' => @$value->createddate,
                                'id6' => @$value->user_group,
                                'id7' => (!empty($value->agentid)) ? (!empty(@$AgentsDataO2Combine[strtoupper($value->agentid)])) ? @$AgentsDataO2Combine[strtoupper($value->agentid)] : 'NO AGENT ASSIGNED' : 'NO AGENT ASSIGNED',
                                'id8' => @$value->phone_number,
                                'id9' => @$value->campaign_name,
                                'id10' => (!empty($value->campaign_name)) ? (!empty($campiagnData[$value->campaign_name])) ? @$campiagnData[$value->campaign_name] : 'NO CAMPAIGN ID' : 'NO CAMPAIGN ID',
                                'id11' => @$value->source_id,
                                'id12' => @$value->first_name,
                                'id13' => @$value->last_name,
                            ]);
                        }
                        if (!empty($dataO2CombineAdditional)) {
                            foreach ($dataO2CombineAdditional as $value) {

                                $sheet->appendRow([
                                    'id1' => @$value->saleid,
                                    'id2' => @$value->salemsorder,
                                    'id3' => @$value->saletype,
                                    'id4' => @$value->saleoutcome,
                                    'id5' => @$value->createddate,
                                    'id6' => @$value->user_group,
                                    'id7' => (!empty($value->agentid)) ? (!empty(@$AgentsDataO2Combine[strtoupper($value->agentid)])) ? @$AgentsDataO2Combine[strtoupper($value->agentid)] : 'NO AGENT ASSIGNED' : 'NO AGENT ASSIGNED',
                                    'id8' => @$value->phone_number,
                                    'id9' => @$value->campaign_name,
                                    'id10' => (!empty($value->campaign_name)) ? (!empty($campiagnData[$value->campaign_name])) ? @$campiagnData[$value->campaign_name] : 'NO CAMPAIGN ID' : 'NO CAMPAIGN ID',
                                    'id11' => @$value->source_id,
                                    'id12' => @$value->first_name,
                                    'id13' => @$value->last_name,
                                ]);
                            }
                        }
//                        if (!empty($dataConsumer)) {
//                            foreach ($dataConsumer as $value) {
//                                $sheet->appendRow([
//                                    'id1' => @$value->saleid,
//                                    'id2' => @$value->order_num,
//                                    'id3' => @$value->product_type,
//                                    'id4' => (!empty($value->order_num) && in_array($value->order_num, $declineOrderArray)) ? 'Decline' : 'Accept',
//                                    'id5' => @$value->saledate,
//                                    'id6' => @$value->user_group,
//                                    'id7' => (!empty($value->fullname)) ? @$value->fullname : 'NO AGENT ASSIGNED',
//                                    'id8' => @$value->phone_number,
//                                    'id9' => @$value->campaign_sold_on,
//                                    'id10' => (!empty($CampaignArray[$value->campaign_sold_on])) ? $CampaignArray[$value->campaign_sold_on] : '',
//                                    'id11' => @$value->source_id,
//                                    'id12' => @$value->first_name,
//                                    'id13' => @$value->last_name
//                                ]);
//                            }
//                        }
//                        if (!empty($O2PremiumSale)) {
//                            foreach ($O2PremiumSale as $value) {
//                                $sheet->appendRow([
//                                    'id1' => @$value->sale_id,
//                                    'id2' => @$value->order_num,
//                                    'id3' => @$value->product_type,
//                                    'id4' => (!empty($value->order_num) && in_array($value->order_num, $declineOrderArray)) ? 'Decline' : 'Accept',
//                                    'id5' => @$value->saledate,
//                                    'id6' => @$value->user_group,
//                                    'id7' => (!empty($value->fullname)) ? @$value->fullname : 'NO AGENT ASSIGNED',
//                                    'id8' => @$value->phone_number,
//                                    'id9' => 3003,
//                                    'id10' => 'O2 Premium',
//                                    'id11' => @$value->source_id,
//                                    'id12' => @$value->first_name,
//                                    'id13' => @$value->last_name
//                                ]);
//                            }
//                        }
                        if (!empty($SEMobileData)) {
                            foreach ($SEMobileData as $value) {
                                $sheet->appendRow([
                                    'id1' => @$value->saleid,
                                    'id2' => @$value->salemsorder,
                                    'id3' => @$value->saletype,
                                    'id4' => (!empty($value->salemsorder) && in_array($value->salemsorder, $declineOrderArray)) ? 'Decline' : 'Accept',
                                    'id5' => @$value->saletime,
                                    'id6' => @$value->user_group,
                                    'id7' => (!empty($value->agentid)) ? (!empty(@$AgentsDataO2Combine[strtoupper($value->agentid)])) ? @$AgentsDataO2Combine[strtoupper($value->agentid)] : 'NO AGENT ASSIGNED' : 'NO AGENT ASSIGNED',
                                    'id8' => @$value->phone_number,
                                    'id9' => 3011,
                                    'id10' => 'SE Mobile',
                                    'id11' => @$value->source_id,
                                    'id12' => @$value->first_name,
                                    'id13' => @$value->last_name
                                ]);
                            }
                        }
                        if (!empty($O2WelcomePAYG)) {
                            foreach ($O2WelcomePAYG as $value) {
                                if(!empty($value->saved_url)){
                                            $serialiseData = getUnserialize($value->saved_url);
                                        }
                                $sheet->appendRow([
                                    'id1' => @$value->sale_id,
                                    'id2' => @$value->salemsorder,
                                    'id3' => @$value->sale_type,
                                    'id4' => (!empty($value->salemsorder) && in_array($value->salemsorder, $declineOrderArray)) ? 'Decline' : 'Accept',
                                    'id5' => @$value->created_at,
                                    'id6' => (!empty($SiteData[strtoupper($value->agent_id)])) ? $SiteData[strtoupper($value->agent_id)] : '',
                                    'id7' => (!empty($value->agent_id)) ? (!empty(@$AgentsDataO2Combine[strtoupper($value->agent_id)])) ? @$AgentsDataO2Combine[strtoupper($value->agent_id)] : 'NO AGENT ASSIGNED' : 'NO AGENT ASSIGNED',
                                    'id8' => @$value->phone_number,
                                    'id9' => 3004,
                                    'id10' => 'O2 Welcome PAYG',
                                    'id11' => @$serialiseData['source_id'],
                                    'id12' => @$value->first_name,
                                    'id13' => @$value->last_name
                                ]);
                            }
                        }
                        if (!empty($MTASales)) {
                            foreach ($MTASales as $value) {
                                $sheet->appendRow([
                                    'id1' => @$value->saleid,
                                    'id2' => @$value->order_num,
                                    'id3' => @$value->product_type,
                                    'id4' => 'Accept',
                                    'id5' => @$value->saledate,
                                    'id6' => @$value->user_group,
                                    'id7' => (!empty($value->fullname)) ? @$value->fullname : 'NO AGENT ASSIGNED',
                                    'id8' => @$value->phone_number,
                                    'id9' => 3038,
                                    'id10' => 'MTA Consumer',
                                    'id11' => @$value->source_id,
                                    'id12' => @$value->first_name,
                                    'id13' => @$value->last_name
                                ]);
                            }
                        }
                        
                        if(!empty($MainDialerReport) && count($MainDialerReport) > 0){
                            foreach ($MainDialerReport as $value) {
                                $sheet->appendRow([
                                    'id1' => @$value->sale_id,
                                    'id2' => @$value->order_num,
                                    'id3' => @$value->product_type,
                                    'id4' => 'Accept',
                                    'id5' => @$value->sale_date,
                                    'id6' => @$value->user_group,
                                    'id7' => @$value->sold_by,
                                    'id8' => @$value->phone_number,
                                    'id9' => @$value->campaign_sold_on,
                                    'id10' => @$CampaignArray[$value->campaign_sold_on],
                                    'id11' => @$value->source_id,
                                    'id12' => @$value->FirstName,
                                    'id13' => @$value->LastName
                                ]);
                            }
                        }
                        if(!empty($OmniDialerReport) && count($OmniDialerReport) > 0){
                            foreach ($OmniDialerReport as $value) {
                                $sheet->appendRow([
                                    'id1' => @$value->sale_id,
                                    'id2' => @$value->order_num,
                                    'id3' => @$value->product_type,
                                    'id4' => 'Accept',
                                    'id5' => @$value->sale_date,
                                    'id6' => @$value->user_group,
                                    'id7' => @$value->sold_by,
                                    'id8' => @$value->phone_number,
                                    'id9' => @$value->campaign_sold_on,
                                    'id10' => @$CampaignArrayOMNI[$value->campaign_sold_on],
                                    'id11' => @$value->source_id,
                                    'id12' => @$value->FirstName,
                                    'id13' => @$value->LastName
                                ]);
                            }
                        }
                        
                        
                    });
                })->store("xls", storage_path('Daily/O2InboundOutbound/'), true);

        $arrayMailTo = ['Andy.Hughes@intelling.co.uk', 'Kelly.McNeill@intelling.co.uk', 'sarah.berry@intelling.co.uk', 'emma.eeles@intelling.co.uk', 'liz.mckie@intelling.co.uk', 'mark.burgess@intelling.co.uk', 'anna.rickers@intelling.co.uk','apanwar@usethegeeks.co.uk'];
//        $arrayMailTo = ['apanwar@usethegeeks.co.uk'];
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.email';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'Daily O2 Order Id Report';

        $result = Mail::send($mail_data['view'], $mail_data, function ($m) use ($mail_data, $fileName) {
                    $m->from($mail_data['from'], 'Intelling');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                    $m->attach(storage_path('Daily/O2InboundOutbound/') . $fileName . '.xls');
                });
        /* End Mail */
        
    }

}
