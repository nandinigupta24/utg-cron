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
use App\Model\O2Inbound\InboundSale;

class OutworxOwnerUpdate extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:OutworxOwnerUpdate';

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
        ini_set('max_execution_time', 10000);
        
        $start = Carbon::today()->startOfDay();
        $end = Carbon::today()->endOfDay();
         
        $data = InboundSale::where('createddate', '>=', $start)
                ->where('report_query', 'Outworx')
                ->whereNull('inbound_group')
//                ->where('orderid','like','MS-%')
                ->pluck('lead_id');
        
        $LeadData = DB::connection('MainDialer')
                ->table('list')
                ->join('inbound_log', 'list.lead_id', 'inbound_log.lead_id')
                ->where('inbound_log.campaign_id', 'OutworxIn')
                ->where('list.list_id', 140410)
                ->where('inbound_log.call_date', '>=', $start)
                ->where('inbound_log.call_date', '<=', $end)
                ->whereIn('list.lead_id', $data)
                ->select('list.owner', 'list.lead_id')
                ->get();

        foreach ($LeadData as $leadInfo) {
            $detais = InboundSale::where('lead_id', $leadInfo->lead_id)->update(['inbound_group' => $leadInfo->owner]);
        }
    }

}
