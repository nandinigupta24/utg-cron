<?php

namespace App\Console\Commands\Hourly;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\ScriptResponse;
use App\Model\SwitchExpertEnergy\SDSales;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;
use App\Model\Intelling\CampaignTableCombined;
use App\Model\Intelling\WebSetting;

class CampaignHourlyReport extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CampaignHourlyReport';

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
        $lastHour = Carbon::now()->subHour(1)->format('H');

        $start = date('Y-m-d') . ' 00:00:00';
        $end = date('Y-m-d') . ' ' . $lastHour . ':59:59';
        
//        $start = '2019-06-03 00:00:00';
//        $end = '2019-06-03 23:59:59';

        $dynamicNumber = date('Y-m-d') . '-' . $lastHour . '-00-00';

        $H = date('H');

        if (date('H') > 20) {
            die('BYE');
        }

        if (date('H') >= 0 && date('H') < 10) {
            die('BYE');
        }

        $data = [];
        $data['DynamicNumber'] = $dynamicNumber;

        
        $data['Omni']['HideCampaign'] = [1004];
        $data['Main']['HideCampaign'] = [1404, 3027, 1308, 3008, 3041];

        $mainDialer = get_campaign_hourly_report('MainDialer', $start, $end);

        $omniDialer = get_campaign_hourly_report('OmniDialer', $start, $end);

        $neyArray = [];

        $data['Main']['CampaignID'] = DB::connection('MainDialer')->table('campaigns')->whereNotIn('campaign_id',$data['Main']['HideCampaign'])->where('active', 'Y')->pluck('campaign_id')->toArray();
        $data['Omni']['CampaignID'] = DB::connection('OmniDialer')->table('campaigns')->whereNotIn('campaign_id',$data['Omni']['HideCampaign'])->where('active', 'Y')->pluck('campaign_id')->toArray();
        
        foreach ($mainDialer as $value) {
            if (in_array($value->campaign_id, $data['Main']['HideCampaign'])) {
                continue;
            }
            $data['Table']['Values'][$value->campaign_id] = @$value->campaign_name;
            $data['Table']['ConnectRate'][$value->campaign_id] = number_format(@$value->Total_Connect_Rate, 2);
            $data['Table']['DMCRate'][$value->campaign_id] = number_format(@$value->Total_DMCrate, 2);
            $data['Table']['DMCDelivered'][$value->campaign_id] = number_format(@$value->DMC_Productive, 2);
            $data['Table']['Calls_to_Agents'][$value->campaign_id] = @$value->CallsToAgent;
            $data['Table']['Droped_Call'][$value->campaign_id] = @$value->Dropped;
            $data['Table']['Drop'][$value->campaign_id] = number_format(@$value->DroppedRate, 2);
        }
        $data['Omni']['CampaignID'] = [];
        foreach ($omniDialer as $value) {
            if (in_array($value->campaign_id, $data['Omni']['HideCampaign'])) {
                continue;
            }
            $data['Omni']['CampaignID'][] = $value->campaign_id;
            $data['Table']['Values'][$value->campaign_id] = @$value->campaign_name;
            $data['Table']['ConnectRate'][$value->campaign_id] = number_format(@$value->Total_Connect_Rate, 2);
            $data['Table']['DMCRate'][$value->campaign_id] = number_format(@$value->Total_DMCrate, 2);
            $data['Table']['DMCDelivered'][$value->campaign_id] = number_format(@$value->DMC_Productive, 2);
            $data['Table']['Calls_to_Agents'][$value->campaign_id] = @$value->CallsToAgent;
            $data['Table']['Droped_Call'][$value->campaign_id] = @$value->Dropped;
            $data['Table']['Drop'][$value->campaign_id] = number_format(@$value->DroppedRate, 2);
        }
        if(!empty($data['Table']) && count($data['Table']) > 0){
            
        }else{
            die('BYE!!');
        }
//        $FirstEmailTo = ['Sarah.Berry@intelling.co.uk','Nicola.Sharrock@intelling.co.uk','apickett@usethegeeks.co.uk','Craig.Winnard@intelling.co.uk'];
//        $FirstEmailCC = ['akumar@usethegeeks.com','developers@usethegeeks.co.uk'];
//        
//        $FirstEmailTo = ["Collin.Alexander@intelling.co.uk", "aoife.o'reilly@intelling.co.uk", 'shahid.ramzan@intelling.co.uk', 'danielle.rossall@intelling.co.uk', 'jason.topping@intelling.co.uk', 'anthony.monks@intelling.co.uk', 'Liam.Radford@intelling.co.uk', 'craig.winnard@intelling.co.uk', 'Kelly.McNeill@intelling.co.uk', 'annie.seisay@intelling.co.uk', 'Rupal.Ahmed@intelling.co.uk', 'Mike.Oxton@intelling.co.uk', 'Mike.Hoye@intelling.co.uk','stephen.philbin@intelling.co.uk'];
        $FirstEmailTo = ["Collin.Alexander@intelling.co.uk", "aoife.o'reilly@intelling.co.uk", 'shahid.ramzan@intelling.co.uk', 'danielle.rossall@intelling.co.uk', 'jason.topping@intelling.co.uk', 'anthony.monks@intelling.co.uk', 'Liam.Radford@intelling.co.uk', 'Kelly.McNeill@intelling.co.uk', 'annie.seisay@intelling.co.uk', 'Rupal.Ahmed@intelling.co.uk', 'Mike.Oxton@intelling.co.uk', 'Mike.Hoye@intelling.co.uk','stephen.philbin@intelling.co.uk'];
//        $FirstEmailTo = ['apanwar@usethegeeks.co.uk'];
        $FirstEmailCC = ['Sarah.Berry@intelling.co.uk', 'Nicola.Sharrock@intelling.co.uk', 'akumar@usethegeeks.com', 'developers@usethegeeks.co.uk'];
//      
        /* Start Mail */
        $mail_data = array();
        $mail_data['to'] = $FirstEmailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.hourly.campaign_report';
//      $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : $FirstEmailCC;
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'Campaign Hourly Report';
        $mail_data['data'] = $data;


        $result = Mail::send($mail_data['view'], ['data' => $data], function ($m) use ($mail_data) {
                    $m->from($mail_data['from'], 'Intelling');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                });


        /* End Mail */
        exit;
    }

}
