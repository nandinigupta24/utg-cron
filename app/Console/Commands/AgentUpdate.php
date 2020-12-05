<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;
//use App\Model\Intelling\AgentTableCombined;
use App\Model\IntellingScriptDB\AgentTableCombined; 

class AgentUpdate extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:AgentUpdate';

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
        $dialer = ['Main' => 'MainDialer', 'Omni' => 'OmniDialer','NewConnex'=>'NewConnex'];
//        $missGrouped = ['O2EzPhone', 'O2GradBayBelfast', 'O2AddConBLF'];
        $missGrouped = ['O2EzPhone', 'O2GradBayBelfast'];

        foreach ($dialer as $key => $val) {
            $data = DB::connection($val)
                    ->table('users')
                    ->join('user_groups', 'users.user_group', '=', 'user_groups.user_group')
                    ->get();

            foreach ($data as $value) {
                $exist = AgentTableCombined::where('user', $value->user)->where('dialer_name', $key)->count();
                if (!empty($exist) && $exist > 0) {
                    $exist = AgentTableCombined::where('user', $value->user)->where('dialer_name', $key)->first();
                    if (in_array($exist->user_group, $missGrouped)) {
                        $AgentTableCombined = AgentTableCombined::find($exist->id);
                        $AgentTableCombined->full_name = $value->full_name;
                        $AgentTableCombined->group_name = $value->group_name;
                        $AgentTableCombined->allowed_campaigns = $value->allowed_campaigns;
                        $AgentTableCombined->updated_at = date('Y-m-d H:i:s');
                    } else {
                        $AgentTableCombined = AgentTableCombined::find($exist->id);
                        $AgentTableCombined->full_name = $value->full_name;
                        $AgentTableCombined->user_group = $value->user_group;
                        $AgentTableCombined->script_allowed = 'Yes';
                        $AgentTableCombined->group_name = $value->group_name;
                        $AgentTableCombined->allowed_campaigns = $value->allowed_campaigns;
                        $AgentTableCombined->updated_at = date('Y-m-d H:i:s');
                    }
                } else {
                    $AgentTableCombined = new AgentTableCombined();
                    $AgentTableCombined->user = $value->user;
                    $AgentTableCombined->full_name = $value->full_name;
                    $AgentTableCombined->user_group = $value->user_group;
                    $AgentTableCombined->group_name = $value->group_name;
                    $AgentTableCombined->allowed_campaigns = $value->allowed_campaigns;
                    $AgentTableCombined->dialer_name = $key;
                    $AgentTableCombined->created_at = date('Y-m-d H:i:s');
                    $AgentTableCombined->updated_at = date('Y-m-d H:i:s');
                }
                if ($AgentTableCombined->save()) {
                    
                }
            }
        }
    }

}
