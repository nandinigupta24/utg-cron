<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\ScriptResponse;
use App\Model\SwitchExpertEnergy\SDSales;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;
use App\Model\UTGAPI\OctopusAPIO2LGSE;

class OctopusAPI extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:OctopusAPI';

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
        $start = date('Y-m-d');
        $data = OctopusAPIO2LGSE::where('created_at', '>=', $start . ' 00:00:00')
                ->select('inbound_group', 'duplicate_status', DB::RAW('count(*) as total'))
                ->groupBy('inbound_group', 'duplicate_status')
                ->get();
        $arrayCount = [];
        foreach ($data as $value) {
            if (!empty($arrayCount[$value->inbound_group])) {
                $arrayCount[$value->inbound_group]['total'] = $arrayCount[$value->inbound_group]['total'] + $value->total;
            } else {
                $arrayCount[$value->inbound_group]['total'] = $value->total;
            }
            if ($value->duplicate_status == 'no') {
                $arrayCount[$value->inbound_group]['Loaded'] = $value->total;
            } else {
                $arrayCount[$value->inbound_group]['Duplicate'] = $value->total;
            }
        }

        

//        $arrayMailTo = ['diallersupport@intelling.co.uk','George.Eastham@switchexperts.co.uk','Shauna.Magee@intelling.co.uk','danielle.rossall@intelling.co.uk','Harry.Morrison@intelling.co.uk','Jason.Moffett@intelling.co.uk','Collin.Alexander@intelling.co.uk','Alex.McConville@intelling.co.uk','Lee.Parry@intelling.co.uk'];
        $arrayMailTo = [env('DIALER_TEAM_EMAIL'),'George.Eastham@switchexperts.co.uk','Shauna.Magee@intelling.co.uk','danielle.rossall@intelling.co.uk','Harry.Morrison@intelling.co.uk','Jason.Moffett@intelling.co.uk','Collin.Alexander@intelling.co.uk','Alex.McConville@intelling.co.uk','Lee.Parry@intelling.co.uk'];
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.octopus_api';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'Octopus API Leads (' . date('Y-m-d') . ')';
        $mail_data['data'] = $arrayCount;

        $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                    $m->from($mail_data['from'], 'API Reports');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'API Reports');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                });
    }

}
