<?php

namespace App\Console\Commands\TalkTalk;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;

class AlertReverse extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:AlertReverse';

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
        $currentDateTime = date("Y-m-d H:i:s", strtotime("now"));
        $Before10MDateTime = date("Y-m-d H:i:s", strtotime("-5 minutes"));

        $o2CampaignAgentLog = [3001, 3002, 3003, 3004, 3005, 3006, 3007, 1330, 3023, 3650, 3010,'oilgenco', 'Wanted_Media', 'Emedia3', 'Emerging', 'EntireMedia', 'CCI_hotKey', 'Enitremedia', '01618147471', 'synergy', 'My_Offers', 'O2_RightDea', 'SE_MOB', 'SEMobSwitch', 'TalkingPeop'];

        $agentsData = DB::connection('TalkTalkO2Inbound')->table('Agents')->whereNotIn('user', [1777])->pluck('user');
        
        /*function for main dialer*/
        $data = DB::connection('NewDialer')->table('agent_log')
                ->whereIn('agent_log.user', $agentsData)
                ->where('agent_log.sub_status', 'Login')
                ->where('event_time', '>=', $Before10MDateTime)
                ->where('event_time', '<', $currentDateTime)
                ->whereIn('campaign_id',$o2CampaignAgentLog)
                ->get();
      
        if (!empty($data) && count((array)$data) > 0) {
            foreach ($data as $key => $value) {
                
                $userData = DB::connection('NewDialer')->table('agent_log')
                        ->where('agent_log.user', $value->user)
                        ->where('agent_log.sub_status', 'Login')
                        ->where('agent_log.event_time', '<', $value->event_time)
                        ->join('users', 'users.user', '=', 'agent_log.user')
                        ->orderBy('agent_log.agent_log_id', 'desc')
                        ->first();
               
                if (!empty($userData) && count((array)$userData) > 0) {
                    if (in_array($userData->campaign_id,[3027])) {
                        $time = $userData->event_time;
                        $campaignId = $userData->campaign_id;
                        
                        $dataSale1 = DB::connection('NewDialer')->table('inbound_log')
                                ->where('user', $value->user)
                                ->where('call_date', '>=', $time)
                                ->where('call_date', '<', $value->event_time)
                                ->where('status', 'SALE')
                                ->get();
                        
                       
                        if(!empty($dataSale1) && count((array)$dataSale1) > 0){
                            if (!empty($dataSale1->campaign_id) && in_array($dataSale1->campaign_id, [3027])) {
                            /* Send Mail */
                            $dataAlert = [];
                            $dataAlert['user'] = $value->user;
//                            $dataAlert['campaign'] = $dataSale1->campaign_id;
                            $dataAlert['campaign'] = $value->campaign_id;
                            $dataAlert['login_time'] = $userData->event_time;
                            $dataAlert['sale_time'] = $dataSale1->call_date;

                            $arrayMailTo = ['Collin.Alexander@intelling.co.uk','Mike.Oxton@intelling.co.uk','George.Eastham@switchexperts.co.uk'];
                            $arrayMailCC = ['Sarah.Berry@intelling.co.uk','Nicola.Sharrock@intelling.co.uk','Dannielle.Harris@intelling.co.uk','akumar@usethegeeks.com'];
                             
                            /* Start Mail */
                            $mail_data = array();
                            $mail_data['to'] = $arrayMailTo;
                            $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
                            $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
                            $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.talk_talk_alert';
                          $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : $arrayMailCC;
                            $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'Talk Talk Alert';
                            $mail_data['moved_agents'] = @$dataAlert;


                            $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                                        $m->from($mail_data['from'], 'Intelling');
                                        if (!empty($mail_data['cc'])) {
                                            $m->cc($mail_data['cc']);
                                        }
                                        $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                                        $m->to($mail_data['to'])->subject($mail_data['subject']);
                                    });
                                    die('BYE');
                        }
                            
                        }else{
                            
                           $dataSale2 = DB::connection('NewDialer')->table('outbound_log')
                                ->where('user', $value->user)
                                ->where('call_date', '>=', $time)
                                ->where('call_date', '<', $value->event_time)
                                ->where('status', 'SALE')
                                ->first();
                          
                           if (!empty($dataSale2->campaign_id) && in_array($dataSale2->campaign_id,[3027])) {
                            /* Send Mail */
                            $dataAlert = [];
                            $dataAlert['user'] = $value->user;
                            $dataAlert['campaign'] = $value->campaign_id;
                            $dataAlert['login_time'] = $userData->event_time;
                            $dataAlert['sale_time'] = $dataSale2->call_date;
                            
                            $arrayMailTo = ['Collin.Alexander@intelling.co.uk','Mike.Oxton@intelling.co.uk','George.Eastham@switchexperts.co.uk'];
                            $arrayMailCC = ['Sarah.Berry@intelling.co.uk','Nicola.Sharrock@intelling.co.uk','Dannielle.Harris@intelling.co.uk','akumar@usethegeeks.com'];
                            
                            /* Start Mail */
                            $mail_data = array();
                            $mail_data['to'] = $arrayMailTo;
                            $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
                            $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
                            $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.talk_talk_alert';
                          $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : $arrayMailCC;
                            $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'Talk Talk Alert';
                            $mail_data['moved_agents'] = @$dataAlert;

                            $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                                        $m->from($mail_data['from'], 'Intelling');
                                        if (!empty($mail_data['cc'])) {
                                            $m->cc($mail_data['cc']);
                                        }
                                        $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                                        $m->to($mail_data['to'])->subject($mail_data['subject']);
                                    });
                                    die('BYE');
                        }
                        }
                    }
                }
            }
        }
        
        /*function for omni dialer*/
        $data = DB::connection('OmniDialer')->table('agent_log')
                ->whereIn('agent_log.user', $agentsData)
                ->where('agent_log.sub_status', 'Login')
                ->where('event_time', '>=', $Before10MDateTime)
                ->where('event_time', '<', $currentDateTime)
                ->whereIn('campaign_id', $o2CampaignAgentLog)
                ->get();
      
        if (!empty($data) && count((array)$data) > 0) {
            foreach ($data as $key => $value) {
                
                $userData = DB::connection('OmniDialer')->table('agent_log')
                        ->where('agent_log.user', $value->user)
                        ->where('agent_log.sub_status', 'Login')
                        ->where('agent_log.event_time', '<', $value->event_time)
                        ->join('users', 'users.user', '=', 'agent_log.user')
                        ->orderBy('agent_log.agent_log_id', 'desc')
                        ->first();
                
                if (!empty($userData) && count((array)$userData) > 0) {
                    if (in_array($userData->campaign_id,[3027])) {
                        $time = $userData->event_time;
                        $campaignId = $userData->campaign_id;
                        
                        $dataSale1 = DB::connection('OmniDialer')->table('inbound_log')
                                ->where('user', $value->user)
                                ->where('call_date', '>=', $time)
                                ->where('call_date', '<', $value->event_time)
                                ->where('status', 'SALE')
                                ->get();
                        
                       
                        if(!empty($dataSale1) && count((array)$dataSale1) > 0){
                            if (!empty($dataSale1->campaign_id) && in_array($dataSale1->campaign_id, [3027])) {
                            /* Send Mail */
                            $dataAlert = [];
                            $dataAlert['user'] = $value->user;
//                            $dataAlert['campaign'] = $dataSale1->campaign_id;
                            $dataAlert['campaign'] = $value->campaign_id;
                            $dataAlert['login_time'] = $userData->event_time;
                            $dataAlert['sale_time'] = $dataSale1->call_date;

                            $arrayMailTo = ['Collin.Alexander@intelling.co.uk','Mike.Oxton@intelling.co.uk','George.Eastham@switchexperts.co.uk'];
                            $arrayMailCC = ['Sarah.Berry@intelling.co.uk','Nicola.Sharrock@intelling.co.uk','Dannielle.Harris@intelling.co.uk','akumar@usethegeeks.com'];
                             
                            /* Start Mail */
                            $mail_data = array();
                            $mail_data['to'] = $arrayMailTo;
                            $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
                            $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
                            $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.talk_talk_alert';
                          $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : $arrayMailCC;
                            $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'Talk Talk Alert';
                            $mail_data['moved_agents'] = @$dataAlert;

                            $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                                        $m->from($mail_data['from'], 'Intelling');
                                        if (!empty($mail_data['cc'])) {
                                            $m->cc($mail_data['cc']);
                                        }
                                        $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                                        $m->to($mail_data['to'])->subject($mail_data['subject']);
                                    });
                                    die('BYE');
                        }
                            
                        }else{
                            
                           $dataSale2 = DB::connection('OmniDialer')->table('outbound_log')
                                ->where('user', $value->user)
                                ->where('call_date', '>=', $time)
                                ->where('call_date', '<', $value->event_time)
                                ->where('status', 'SALE')
                                ->first();
                          
                           if (!empty($dataSale2->campaign_id) && in_array($dataSale2->campaign_id,[3027])) {
                            /* Send Mail */
                            $dataAlert = [];
                            $dataAlert['user'] = $value->user;
//                            $dataAlert['campaign'] = $dataSale2->campaign_id;
                            $dataAlert['campaign'] = $value->campaign_id;
                            $dataAlert['login_time'] = $userData->event_time;
                            $dataAlert['sale_time'] = $dataSale2->call_date;

                             $arrayMailTo = ['Collin.Alexander@intelling.co.uk','Mike.Oxton@intelling.co.uk','George.Eastham@switchexperts.co.uk'];
                            $arrayMailCC = ['Sarah.Berry@intelling.co.uk','Nicola.Sharrock@intelling.co.uk','Dannielle.Harris@intelling.co.uk','akumar@usethegeeks.com'];
                             
                            
                            /* Start Mail */
                            $mail_data = array();
                            $mail_data['to'] = $arrayMailTo;
                            $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
                            $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
                            $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.talk_talk_alert';
                          $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : $arrayMailCC;
                            $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'Talk Talk Alert';
                            $mail_data['moved_agents'] = @$dataAlert;

                            $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                                        $m->from($mail_data['from'], 'Intelling');
                                        if (!empty($mail_data['cc'])) {
                                            $m->cc($mail_data['cc']);
                                        }
                                        $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                                        $m->to($mail_data['to'])->subject($mail_data['subject']);
                                    });
                                    die('BYE');
                        }
                        }
                    }
                }
            }
        }
        
        
        /* End Mail */
        exit;
    }

}
