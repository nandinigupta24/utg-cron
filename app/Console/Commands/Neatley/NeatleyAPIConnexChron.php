<?php

namespace App\Console\Commands\Neatley;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;
use App\Model\Intelling\AgentTableCombined;
use App\Model\Intelling\NeatleyAPIConnex;
class NeatleyAPIConnexChron extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:NeatleyAPIConnexChron';

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

        $start = Carbon::now()->startOfDay()->toDateTimeString();
        $end = Carbon::now()->toDateTimeString();

        $startMessage = date('Y-m-d', strtotime($start));
        $endMessage = date('Y-m-d', strtotime($end));
        
        if ($startMessage == $endMessage) {
            $subjectMesage = '(' . $startMessage . ')';
        } else {
            $subjectMesage = '(' . $startMessage . ' - ' . $endMessage . ')';
        }


        $Agents = AgentTableCombined::where('dialer_name', 'main')->pluck('full_name', 'user');
        $data = DB::connection('O2Combine')
                            ->table('O2Sales')
                            ->where('campaign_name', 3003)
                            ->where('saletype', 'SIMO')
                            ->where('salemsorder', 'LIKE', 'MS-0%')
                            ->where('createddate', '>=', $start)
                            ->where('createddate', '<=', $end)
                            ->get();
       
        $Unique = 'NeatleyMobile-' . date('Ymd');
        
        $arrayCheck = [];
        $arrayCheck['success'] = 0;
        $arrayCheck['duplicate'] = 0;
        $arrayCheck['total'] = 0;
        foreach ($data as $key => $value) {
            $arrayCheck['total'] ++;
            $exist = NeatleyAPIConnex::where('phone_number', $value->phone_number)->count();
            if (!empty($exist) && $exist > 0) {
                $arrayCheck['duplicate'] ++;
                continue;
            }
            $dataArray = [];
            $dataArray['list_id'] = 3032;
            $dataArray['phone_number'] = @$value->phone_number;
            $dataArray['title'] = @$value->title;
            $dataArray['first_name'] = @$value->first_name;
            $dataArray['last_name'] = @$value->last_name;
            $dataArray['postal_code'] = @$value->postal_code;
            $dataArray['email'] = $value->email;
            $dataArray['vendor_lead_code'] = $Unique . '-' . $value->saleid;
            $dataArray['source_id'] = 'NeatleyMobile';
            $dataArray['DateofSale'] = $value->saletime;
            $dataArray['SaleType'] = $value->saletype;
            $dataArray['AgentName'] = (!empty($value->agentid)) ? @$Agents[strtoupper(@$value->agentid)] : '';
            $dataArray['custom_fields'] = 'Y';
            $queryString = http_build_query($dataArray);

            $NeatleyAPIConnex = new NeatleyAPIConnex();
            $NeatleyAPIConnex->vendor_lead_code = $Unique . '-' . $value->saleid;
            $NeatleyAPIConnex->source_id = 'NeatleyMobile';
            $NeatleyAPIConnex->phone_number = @$value->phone_number;
            $NeatleyAPIConnex->first_name = @$value->first_name;
            $NeatleyAPIConnex->last_name = @$value->last_name;
            $NeatleyAPIConnex->postal_code = @$value->postal_code;
            $NeatleyAPIConnex->AgentName = (!empty($value->agentid)) ? @$Agents[strtoupper(@$value->agentid)] : '';
            $NeatleyAPIConnex->DateofSale = $value->saletime;
            $NeatleyAPIConnex->SaleType = $value->saletype;
            $NeatleyAPIConnex->response = NULL;
            $NeatleyAPIConnex->status = 'open';
            if ($NeatleyAPIConnex->save()) {
                $data = @file_get_contents('http://'.env('NewDialerIP').'/Admin/.functions/non_agent_api.php?source=Test&user=APIUSER&pass=APIUSER&function=add_lead&' . $queryString);
                $NeatleyAPIConnexUpdate = $NeatleyAPIConnex::find($NeatleyAPIConnex->id);
                $NeatleyAPIConnexUpdate->response = $data;
                $NeatleyAPIConnexUpdate->status = 'success';
                if ($NeatleyAPIConnexUpdate->save()) {
                    $arrayCheck['success'] ++;
                }
            }
        }
        
        /*diallersupport@intelling.co.uk*/
        $arrayMailTo = [env('DIALER_TEAM_EMAIL')];
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.connex_api';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com', 'Colm.Corran@intelling.co.uk'];
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'Neatley Leads ' . $subjectMesage;
        $mail_data['data'] = @$arrayCheck;
        $mail_data['timeFormat'] = array('start' => $start, 'end' => $end);

        $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                    $m->from($mail_data['from'], 'Intelling');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                });
    }

}
