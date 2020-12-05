<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;
use App\Model\Intelling\CampaignTableCombined;

class CampaignUpdate extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CampaignUpdate';

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
        $dialer = ['Main' => 'MainDialer', 'Omni' => 'OmniDialer'];

        foreach ($dialer as $key => $val) {
            $data = DB::connection($val)
                    ->table('campaigns')
                    ->where('active', 'Y')
                    ->select('campaign_id', 'campaign_name')
                    ->get();

            foreach ($data as $value) {
                $exist = CampaignTableCombined::where('campaign_id', $value->campaign_id)->where('dialler_name', $key)->count();
                if (!empty($exist) && $exist > 0) {
                    $exist = CampaignTableCombined::where('campaign_id', $value->campaign_id)->where('dialler_name', $key)->first();
                    if (trim($value->campaign_name) == trim($exist->campaign_name)) {
                        continue;
                    }
                    $CampaignTableCombined = CampaignTableCombined::find($exist->id);
                    $CampaignTableCombined->campaign_id = $value->campaign_id;
                    $CampaignTableCombined->campaign_name = $value->campaign_name;
                    $CampaignTableCombined->dialler_name = $key;
                    $CampaignTableCombined->updated_at = date('Y-m-d H:i:s');
                } else {
                    $CampaignTableCombined = new CampaignTableCombined();
                    $CampaignTableCombined->campaign_id = $value->campaign_id;
                    $CampaignTableCombined->campaign_name = $value->campaign_name;
                    $CampaignTableCombined->dialler_name = $key;
                    $CampaignTableCombined->created_at = date('Y-m-d H:i:s');
                    $CampaignTableCombined->updated_at = date('Y-m-d H:i:s');
                }
                if ($CampaignTableCombined->save()) {
                    
                }
            }
        }
    }

}
