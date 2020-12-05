<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;

class PublisherReport extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:PublisherReport';

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

        $dataSource = ['OILSID58', 'SID16', 'SID200', 'SID216', 'SID92', 'SID46', 'SID34', 'SID48', 'SID8R', 'SID130', 'SID34U', 'SID36', 'SID20',
            'SID74', 'SID100', 'SID30', 'OILSID46', 'SID2', 'SID192', '1581', '1396', 'SID80', 'OILSID62', 'SID32', '1397', 'SID26',
            'SID128', 'SID196', 'SID62', 'SID72', 'SID184', 'SID98R', 'SID54', 'SID76', 'OILSID34', 'SID1', 'SID19', 'SID14', 'SID98',
            'SID15', 'SID8', 'OILSID12', 'SID12', 'OILSID32', 'SID56', 'SID38', 'SID58', 'SID28', 'SID1R', 'SID42', 'SID6', 'SID66',
            'SID88', 'SID20U', 'OIL68', 'OILSID15', 'OILSID19', 'OILSID24', 'OILSID38', 'OILSID6', 'OILSID68', '34U', 'SID210', 'SID218', 'SID44',
            '1595', 'SID150', 'SID234', 'OILSID52'];
        $start = Carbon::now()->startOfWeek()->toDateTimeString();
        $end = Carbon::now()->endOfWeek()->toDateTimeString();


        $MainDialerListIds = [3655, 3012, 36502, 1307, 3003];
        $query = get_publisher_report($start, $end, $MainDialerListIds);
        $data = DB::connection('MainDialer')->select($query);


        $CampaignArray = [];
        $CampaignArray['MainDialer'] = DB::connection('MainDialer')->table("campaigns")->pluck('campaign_name', 'campaign_id')->toArray();
        $CampaignArray['OmniDialer'] = DB::connection('OmniDialer')->table("campaigns")->pluck('campaign_name', 'campaign_id')->toArray();


        $totalArray = [];
        foreach ($data as $key => $value) {
            $totalArray['MainDialer'][$key]['CampaignID'] = $value->CampaignId;
            $totalArray['MainDialer'][$key]['CampaigName'] = (!empty($CampaignArray['MainDialer'][$value->CampaignId])) ? $CampaignArray['MainDialer'][$value->CampaignId] : '';
            $totalArray['MainDialer'][$key]['SID'] = $value->sid;
            $totalArray['MainDialer'][$key]['Leads Loaded'] = $value->LeadsLoaded;
            $totalArray['MainDialer'][$key]['Agent Calls'] = $value->Agent_Calls;
            $totalArray['MainDialer'][$key]['Connects'] = $value->Connects;
            $totalArray['MainDialer'][$key]['Connect Rate'] = $value->ConnectRate;
            $totalArray['MainDialer'][$key]['Average Calls'] = $value->Average_Calls;
            $totalArray['MainDialer'][$key]['DMCs'] = $value->DMCs;
            $totalArray['MainDialer'][$key]['DMC Rate'] = $value->DMCRate;
            $totalArray['MainDialer'][$key]['Answering Machine'] = $value->AnsweringMachine;
            $totalArray['MainDialer'][$key]['Drop'] = $value->Drop;
            $totalArray['MainDialer'][$key]['Completed'] = $value->Completed;
        }

        /* Omni Dialer */
        $OmniDialerListIds = [3008, 3006, 1003];
        $query = get_publisher_report($start, $end, $OmniDialerListIds);
        $OmniDialerdata = DB::connection('OmniDialer')->select($query);

        foreach ($OmniDialerdata as $key => $value) {
            $totalArray['OmniDialer'][$key]['CampaignID'] = $value->CampaignId;
            $totalArray['OmniDialer'][$key]['CampaigName'] = (!empty($CampaignArray['OmniDialer'][$value->CampaignId])) ? $CampaignArray['OmniDialer'][$value->CampaignId] : '';
            $totalArray['OmniDialer'][$key]['SID'] = $value->sid;
            $totalArray['OmniDialer'][$key]['Leads Loaded'] = $value->LeadsLoaded;
            $totalArray['OmniDialer'][$key]['Agent Calls'] = $value->Agent_Calls;
            $totalArray['OmniDialer'][$key]['Connects'] = $value->Connects;
            $totalArray['OmniDialer'][$key]['Connect Rate'] = $value->ConnectRate;
            $totalArray['OmniDialer'][$key]['Average Calls'] = $value->Average_Calls;
            $totalArray['OmniDialer'][$key]['DMCs'] = $value->DMCs;
            $totalArray['OmniDialer'][$key]['DMC Rate'] = $value->DMCRate;
            $totalArray['OmniDialer'][$key]['Answering Machine'] = $value->AnsweringMachine;
            $totalArray['OmniDialer'][$key]['Drop'] = $value->Drop;
            $totalArray['OmniDialer'][$key]['Completed'] = $value->Completed;
        }

        $fileName = 'Publisher_Report(' . date('Y-m-d', strtotime($start)) . ' - ' . date('Y-m-d', strtotime($end)) . ')';
        $file = Excel::create($fileName, function($excel) use($totalArray, $CampaignArray) {
                    $excel->sheet('MainDialer', function($sheet) use($totalArray, $CampaignArray) {
                        $sheet->setOrientation('landscape');
                        $sheet->fromArray($totalArray['MainDialer']);
                    });
                    $excel->sheet('OmniDialer', function($sheet) use($totalArray, $CampaignArray) {
                        $sheet->setOrientation('landscape');
                        if (!empty($totalArray['OmniDialer']) && count($totalArray['OmniDialer']) > 0) {
                            $sheet->fromArray($totalArray['OmniDialer']);
                        }
                    });
                })->store("xls", storage_path('/PublisherReport/'), true);

        $arrayMailTo = ['Sarah.Berry@intelling.co.uk', 'Nicola.Sharrock@intelling.co.uk', 'Kelly.McNeill@intelling.co.uk'];
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.email';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'Publisher Report';
        $mail_data['pdf_type'] = !empty($data['pdf_type']) ? $data['pdf_type'] : "Pdf Attachment";
        $mail_data['pdf_attachment'] = !empty($data['pdf_attachment']) ? $data['pdf_attachment'] : null;


        $result = Mail::send($mail_data['view'], $mail_data, function ($m) use ($mail_data, $fileName) {
                    $m->from($mail_data['from'], 'Intelling');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                    $m->attach(storage_path('/PublisherReport/') . $fileName . '.xls');
                });
        /* End Mail */
    }

}
