<?php

namespace App\Console\Commands\SessionUpdate;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;

class O2Premium extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:O2Premium';

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
//        $start = date('Y-m-d').' 10:00:00';
//        $end = date('Y-m-d').' 16:04:00';
//        $start = Carbon::now()->subHours(2)->subMinutes(4)->toDateTimeString();
//        $end = Carbon::now()->subMinutes(4)->toDateTimeString();
        $start = Carbon::now()->startOfDay()->toDateTimeString();

        $end = Carbon::now()->toDateTimeString();

        if (date('H') > 20) {
            die('BYE');
        }

        if (date('H') >= 0 && date('H') < 10) {
            die('BYE');
        }

//        $userGroup = ['Belfast','Burnley','Southmoor','Synergy','SLM'];
        $userGroup = ['Belfast', 'Burnley', 'Southmoor', 'Synergy', 'SLM'];
        $newArray = [];
        foreach ($userGroup as $group) {
            $groups = DB::connection('NewConnex')
                            ->table('user_groups')
                            ->where('allowed_campaigns', 'LIKE', '%3003%')
                            ->where('group_name', 'like', '%' . $group . '%')
                            ->pluck('user_group')->toArray();

            $Accept = DB::connection('MainDialer')
                            ->table('O2Script.customers')
                            ->join('O2Script.sales','O2Script.customers.lead_id','O2Script.sales.lead_id')
                            ->join('O2Script.sales_by_orig_agent','O2Script.sales.sale_id','O2Script.sales_by_orig_agent.sale_id')
                            ->join('custom_view.sales_by_source_O2script','O2Script.sales_by_orig_agent.sale_id','custom_view.sales_by_source_O2script.sale_id')
                            ->where('O2Script.sales.sale_date','>=',$start)
                            ->where('O2Script.sales.sale_date','<=',$end)
                            ->where('O2Script.sales.campaign_sold_on',3003)
                            ->where('O2Script.sales.order_num','LIKE','MS-5%')
                            ->whereIn('O2Script.sales.team',$groups)
                            ->count();
            

            $Decline = DB::connection('MainDialer')
                                ->table('O2Script.customers')
                                ->join('O2Script.sales','O2Script.customers.lead_id','O2Script.sales.lead_id')
                                ->join('O2Script.sales_by_orig_agent','O2Script.sales.sale_id','O2Script.sales_by_orig_agent.sale_id')
                                ->join('custom_view.sales_by_source_O2script','O2Script.sales_by_orig_agent.sale_id','custom_view.sales_by_source_O2script.sale_id')
                                ->where('O2Script.sales.sale_date','>=',$start)
                                ->where('O2Script.sales.sale_date','<=',$end)
                                ->where('O2Script.sales.campaign_sold_on',3003)
                                ->where('O2Script.sales.order_num','LIKE','MS-0%')
                                ->whereIn('O2Script.sales.team',$groups)
                                ->count();

            

            $newArray[$group]['Accept'] = (!empty($Accept)) ? $Accept : 0 ;
            $newArray[$group]['Decline'] = (!empty($Decline)) ? $Decline : 0;
        }
//        pr($newArray);
//        exit;
        $SPATarget = 5.5;
        $SessionUpdateTime = date('H');
        $SessionUpdateName = date('h A');
        $FTE1 = get_calculate_FTE_3003($start, $end);
        $data = [];
        $data['newArray'] = $newArray;
        $data['SPATarget'] = $SPATarget;
        $data['SessionUpdateTime'] = $SessionUpdateTime;
        $data['SessionUpdateName'] = $SessionUpdateName;
        $data['FTE1'] = $FTE1;


        $arrayMailCC = ['dialerteam@usethegeeks.zendesk.com', 'craig.winnard@intelling.co.uk', 'Mike.Oxton@intelling.co.uk', 'George.Eastham@switchexperts.co.uk', 'James.Wilson@intelling.co.uk', 'Anthony.Monks@intelling.co.uk', 'danielle.rossall@intelling.co.uk', 'Kelly.McNeill@intelling.co.uk', 'annie.seisay@intelling.co.uk', 'akumar@usethegeeks.com', 'developers@usethegeeks.co.uk'];
        $arrayMailTo = ['O2Managers@intelling.co.uk'];
//        $arrayMailTo = ['apanwar@usethegeeks.co.uk'];
        
        /* Start Mail */
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.session_update.o2_premium';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : $arrayMailCC;
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'O2 Premium Session Update (' . $SessionUpdateName . ')';
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
