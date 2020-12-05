<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendDiallerOperationMail;
use DB;

class DiallerOperations extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DiallerOperations';

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

        if (in_array($day, ['Saturday', 'Sunday'])) {   
            die('BYE');
        } elseif ($day == 'Monday') {
            $start = date("Y-m-d", strtotime('-3 days'));
            $end = date("Y-m-d", strtotime('-1 days'));
        } else {
            $start = $end = date("Y-m-d", strtotime('-1 days'));
        }
        
        $data = [];
        $recordsPerHead = [];

        $recordsPerHead[3001] = 100;
        $recordsPerHead[3002] = 65;
        $recordsPerHead[3005] = 120;
        $recordsPerHead[3006] = 250;
        $recordsPerHead[1330] = 170;

        $recordsPerHead[3042] = 70;
        $recordsPerHead[3043] = 70;
        $recordsPerHead[3011] = 70;
        $recordsPerHead[3040] = 70;
        $recordsPerHead[1307] = 135;
        $recordsPerHead[3003] = 65;
        $recordsPerHead[3045] = 120;
        $recordsPerHead[3009] = 50;
        $recordsPerHead[4010] = 120;


        $result = DB::connection('OmniDialer')->table('list')
                        ->join('lists', 'list.list_id', '=', 'lists.list_id')
                        ->join('campaigns', 'campaigns.campaign_id', '=', 'lists.campaign_id')
//                        ->whereIn('campaigns.campaign_id', [1307, 3003, 3040, 3042, 3043, 3011, 3001, 3002, 3005, 3006, 3045])
                        ->whereIn('campaigns.campaign_id', [1307, 3003, 3040, 3042, 3043, 3011, 3002, 3006,3009,4010])
                        ->whereBetween('list.entry_date', [$start . ' 00:00:00', $end . ' 23:59:59'])
                        ->where('list.source_id', '!=', 'VDCL')
                        ->select(DB::raw('count(*) as leads_count,campaigns.campaign_name,campaigns.campaign_id'))
                        ->groupBy('campaigns.campaign_name')
                        ->get()->toArray();

        $result2 = DB::connection('MainDialer')->table('list')
                        ->join('lists', 'list.list_id', '=', 'lists.list_id')
                        ->join('campaigns', 'campaigns.campaign_id', '=', 'lists.campaign_id')
                        ->whereIn('campaigns.campaign_id', [1307, 3003, 3040, 3042, 3043, 3011, 3002, 3006])
                        ->whereBetween('list.entry_date', [$start . ' 00:00:00', $end . ' 23:59:59'])
                        ->where('list.source_id', '!=', 'VDCL')
                        ->select(DB::raw('count(*) as leads_count,campaigns.campaign_name,campaigns.campaign_id'))
                        ->groupBy('campaigns.campaign_name')
                        ->get()->toArray();

        $result3 = DB::connection('OmniDialer')->table('list')
                        ->join('lists', 'list.list_id', '=', 'lists.list_id')
                        ->join('campaigns', 'campaigns.campaign_id', '=', 'lists.campaign_id')
                        ->whereIn('campaigns.campaign_id', [1330,3001,3005])
                        ->whereBetween('list.entry_date', [date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:59'])
                        ->where('list.source_id', '!=', 'VDCL')
                        ->select(DB::raw('count(*) as leads_count,campaigns.campaign_name,campaigns.campaign_id'))
                        ->groupBy('campaigns.campaign_name')
                        ->get()->toArray();
        
        $result4 = DB::connection('MainDialer')->table('list')
                        ->join('lists', 'list.list_id', '=', 'lists.list_id')
                        ->join('campaigns', 'campaigns.campaign_id', '=', 'lists.campaign_id')
                        ->whereIn('campaigns.campaign_id', [3045])
                        ->whereBetween('list.entry_date', [date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:59'])
                        ->where('list.source_id', '!=', 'VDCL')
                        ->select(DB::raw('count(*) as leads_count,campaigns.campaign_name,campaigns.campaign_id'))
                        ->groupBy('campaigns.campaign_name')
                        ->get()->toArray();


        $resultData = array_merge($result, $result2, $result3, $result4);
            
        $data['recordsPerHead'] = $recordsPerHead;
        $data['result'] = $resultData;

//        $MailTo = ["sarah.berry@intelling.co.uk","nicola.rooney@intelling.co.uk","mike.oxton@intelling.co.uk","annie.seisay@intelling.co.uk","Anthony.Monks@intelling.co.uk","Aoife.O'Reilly@intelling.co.uk","dialerteam@usethegeeks.zendesk.com","jamie.taylor@intelling.co.uk","mike.hoye@intelling.co.uk","kerry.anderson@intelling.co.uk"];
        $MailTo = ["sarah.berry@intelling.co.uk","nicola.rooney@intelling.co.uk","mike.oxton@intelling.co.uk","Anthony.Monks@intelling.co.uk","Aoife.O'Reilly@intelling.co.uk","dialerteam@usethegeeks.zendesk.com","mike.hoye@intelling.co.uk","kerry.anderson@intelling.co.uk","min.bonugli@intelling.co.uk"];
        $MailCC = ['akumar@usethegeeks.com'];
        Mail::to($MailTo)
                ->cc($MailCC)
                ->send(new SendDiallerOperationMail($data));
    }

}
