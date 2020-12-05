<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendDiallerOperationMail;
use DB;

class DiallerOperationsNew extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:DiallerOperationsNew';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email Cron to Dialler Team and Operations';

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

        $day = date('l');
        $arraySearch = [];
        /*Records Per Head*/
        $recordsPerHead = [];

        $recordsPerHead[001] = 70;
        $recordsPerHead[1308] = 100;
        $recordsPerHead[1307] = 135;
        $recordsPerHead[3045] = 120;
        $recordsPerHead[3003] = 60;
        $recordsPerHead[3040] = 70;
        $recordsPerHead[3011] = 70;

        $recordsPerHead[3005] = 120;
        $recordsPerHead[3013] = 100;
        $recordsPerHead[3002] = 65;
        $recordsPerHead[3001] = 100;
        $recordsPerHead[3009] = 50;
        $recordsPerHead[1330] = 170;
        $recordsPerHead[4007] = 50;
        $recordsPerHead[4006] = 50;
        $recordsPerHead[4008] = 50;
        $recordsPerHead[4010] = 120;

        $recordsPerHead[3043] = 70;
        $recordsPerHead[4011] = 25;

        if(($day == 'Monday')){
            $start = date("Y-m-d", strtotime('-3 days'));
            $end = date("Y-m-d", strtotime('-1 days'));
        }else{
            $start = $end = date("Y-m-d", strtotime('-1 days'));
        }


        $CurrentStart = $CurrentEnd = date('Y-m-d');


        $arrayCountFromFriday = ['001',1308,1307,3003,3040,3011,3043];
        $MondayStart = [3045];

        $query1 = "SELECT c.campaign_id,c.campaign_name AS `CampaignName`,count(*) AS `LeadsLoaded`
from custom_view.`list` l
JOIN lists ls ON l.list_id=ls.list_id
JOIN campaigns c ON c.campaign_id=ls.campaign_id
WHERE l.entry_date BETWEEN '".$start." 00:00:00' and '".$end." 23:59:59'
and l.source_id not in('VDCL') AND c.campaign_id IN ('".implode("','",$arrayCountFromFriday)."')
group BY c.campaign_name";

        $MainDialer1 = DB::connection('MainDialer')->select($query1);

        $query2 = "SELECT c.campaign_id,c.campaign_name AS `CampaignName`,count(*) AS `LeadsLoaded`
from custom_view.`list` l
JOIN lists ls ON l.list_id=ls.list_id
JOIN campaigns c ON c.campaign_id=ls.campaign_id
WHERE l.entry_date BETWEEN '".$CurrentStart." 00:00:00' and '".$CurrentEnd." 23:59:59'
and l.source_id not in('VDCL') AND c.campaign_id IN ('".implode("','",$MondayStart)."')
group BY c.campaign_name";

        $MainDialer2 = DB::connection('MainDialer')->select($query2);
        $MainDialer = array_merge($MainDialer1,$MainDialer2);


        foreach($MainDialer as $dialer2){

            $arraySearch[$dialer2->campaign_id]['Campaign_Id'] = $dialer2->campaign_id;
            $arraySearch[$dialer2->campaign_id]['Campaign_Name'] = $dialer2->CampaignName;
            $arraySearch[$dialer2->campaign_id]['Leads_Loaded'] = $dialer2->LeadsLoaded;
            $arraySearch[$dialer2->campaign_id]['Records_Per_Head'] = $recordsPerHead[$dialer2->campaign_id];
            $arraySearch[$dialer2->campaign_id]['FTE_Required'] = round(($dialer2->LeadsLoaded/$recordsPerHead[$dialer2->campaign_id]),1);
        }

        $arrayCountFromFriday = [3002,3009,4007,4006,4008,3013,4011];
        $MondayStart = [1330,3001,3005,4010];


        $query1 = "SELECT c.campaign_id,c.campaign_name AS `CampaignName`,count(*) AS `LeadsLoaded`
from custom_view.`list` l
JOIN lists ls ON l.list_id=ls.list_id
JOIN campaigns c ON c.campaign_id=ls.campaign_id
WHERE l.entry_date BETWEEN '".$start." 00:00:00' and '".$end." 23:59:59'
and l.source_id not in('VDCL') AND c.campaign_id IN ('".implode("','",$arrayCountFromFriday)."') AND l.list_id NOT IN ('3011')
group BY c.campaign_name";

        $OmniDialer1 = DB::connection('OmniDialer')->select($query1);

        $query2 = "SELECT c.campaign_id,c.campaign_name AS `CampaignName`,count(*) AS `LeadsLoaded`
from custom_view.`list` l
JOIN lists ls ON l.list_id=ls.list_id
JOIN campaigns c ON c.campaign_id=ls.campaign_id
WHERE l.entry_date BETWEEN '".$CurrentStart." 00:00:00' and '".$CurrentEnd." 23:59:59'
and l.source_id not in('VDCL') AND c.campaign_id IN ('".implode("','",$MondayStart)."')
group BY c.campaign_name";

        $OmniDialer2 = DB::connection('OmniDialer')->select($query2);
        $OmniDialer = array_merge($OmniDialer1,$OmniDialer2);

        foreach($OmniDialer as $dialer1){

            $arraySearch[$dialer1->campaign_id]['Campaign_Id'] = $dialer1->campaign_id;
            $arraySearch[$dialer1->campaign_id]['Campaign_Name'] = $dialer1->CampaignName;
            $arraySearch[$dialer1->campaign_id]['Leads_Loaded'] = $dialer1->LeadsLoaded;
            $arraySearch[$dialer1->campaign_id]['Records_Per_Head'] = $recordsPerHead[$dialer1->campaign_id];
            $arraySearch[$dialer1->campaign_id]['FTE_Required'] = round(($dialer1->LeadsLoaded/$recordsPerHead[$dialer1->campaign_id]),1);
        }


        $query4 = "SELECT c.campaign_id,c.campaign_name AS `CampaignName`,count(*) AS `LeadsLoaded`
from custom_view.`list` l
JOIN lists ls ON l.list_id=ls.list_id
JOIN campaigns c ON c.campaign_id=ls.campaign_id
WHERE l.entry_date BETWEEN '".$start." 00:00:00' and '".$end." 23:59:59'
and l.source_id not in('VDCL') AND l.list_id IN ('3011')
group BY c.campaign_name";

        $OmniDialer4 = DB::connection('OmniDialer')->select($query4);
        foreach($OmniDialer4 as $dialer4){

            $arraySearch[3013]['Campaign_Id'] = 3013;
            $arraySearch[3013]['Campaign_Name'] = 'Octopus Marketing Punch';
            $arraySearch[3013]['Leads_Loaded'] = $dialer4->LeadsLoaded;
            $arraySearch[3013]['Records_Per_Head'] = $recordsPerHead[3013];
            $arraySearch[3013]['FTE_Required'] = round(($dialer4->LeadsLoaded/$recordsPerHead[3013]),1);
        }


         $query3 = "SELECT c.campaign_id,c.campaign_name AS `CampaignName`,l.source_id,count(*) AS `LeadsLoaded`
from custom_view.`list` l
JOIN lists ls ON l.list_id=ls.list_id
JOIN campaigns c ON c.campaign_id=ls.campaign_id
WHERE l.entry_date BETWEEN '".$start." 00:00:00' and '".$end." 23:59:59'
and l.source_id not in('VDCL') AND c.campaign_id = 3003
group BY l.source_id";

        $MainDialer3 = DB::connection('MainDialer')->select($query3);

        $query5 = "SELECT c.campaign_id,c.campaign_name AS `CampaignName`,l.source_id,count(*) AS `LeadsLoaded`
from custom_view.`list` l
JOIN lists ls ON l.list_id=ls.list_id
JOIN campaigns c ON c.campaign_id=ls.campaign_id
WHERE l.entry_date BETWEEN '".$start." 00:00:00' and '".$end." 23:59:59'
and l.source_id not in('VDCL') AND c.campaign_id = 4011
group BY l.source_id";

       $MainDialer5 = DB::connection('OmniDialer')->select($query5);

        $data = [];
        $data['Campaign'] = $arraySearch;
        $data['Source'] = $MainDialer3;
        $data['Source1'] = $MainDialer5;

        $MailTo = ["sarah.berry@intelling.co.uk","nicola.rooney@intelling.co.uk","mike.oxton@intelling.co.uk","Anthony.Monks@intelling.co.uk","Aoife.O'Reilly@intelling.co.uk","dialerteam@usethegeeks.zendesk.com","mike.hoye@intelling.co.uk","kerry.anderson@intelling.co.uk","min.bonugli@intelling.co.uk","Brett.Bailey@intelling.co.uk","Bryony.Thomson@intelling.co.uk"];
//        $MailTo = ["ngupta@usethegeeks.co.uk"];
        $MailCC = ['akumar@usethegeeks.com'];
        Mail::to($MailTo)
                ->cc($MailCC)
                ->send(new SendDiallerOperationMail($data));
    }

}
