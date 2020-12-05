<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Mail;
use Excel;
use App\Model\O2Combine\O2AddconSales;

class O2AddconSessionUpdate extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:O2AddconSessionUpdate';

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

        if (date('H') > 20) {
            die('BYE');
        }

        if (date('H') >= 0 && date('H') < 10) {
            die('BYE');
        }
        
        $dateForSaleTarget = date('d-m-Y',strtotime("last Monday"));
        
        $SaleTarget = [
            '21-01-2019'=>['SLM'=>0.75,'Synergy'=>0.5,'Belfast'=>1.2,'TP'=>1.2],
            '28-01-2019'=>['SLM'=>0.75,'Synergy'=>0.5,'Belfast'=>1.2,'TP'=>1.2],
            '04-02-2019'=>['SLM'=>1,'Synergy'=>0.5,'Belfast'=>1.2,'TP'=>1.2],
            '11-02-2019'=>['SLM'=>1.2,'Synergy'=>0.75,'Belfast'=>1.2,'TP'=>1.2],
            '18-02-2019'=>['SLM'=>1.2,'Synergy'=>1,'Belfast'=>1.2,'TP'=>1.2],
            '25-02-2019'=>['SLM'=>1.2,'Synergy'=>1.2,'Belfast'=>1.2,'TP'=>1.2],
            '04-03-2019'=>['SLM'=>1.2,'Synergy'=>1.2,'Belfast'=>1.2,'TP'=>1.2,'Outworx'=>.5]
            ];
        
        $SaleTargetGroup = $SaleTarget['04-03-2019'];
        
        $userGroup = ['BLF' => 'Belfast','SYN' => 'Synergy', 'SLM' => 'SLM', 'TEL' => 'Teleperformance','Out'=>'Outworx'];

        $newArray = [];
        foreach ($userGroup as $k => $group) {
            $sales = O2AddconSales::where('agentid', 'LIKE', $k . '%')
                    ->where('createddate', '>=', $start)
                    ->where('createddate', '<=', $end)
                    ->where('salemsorder', 'like', 'MS-5%')
                    ->count();
            $decline = O2AddconSales::where('agentid', 'LIKE', $k . '%')
                    ->where('createddate', '>=', $start)
                    ->where('createddate', '<=', $end)
                    ->where('salemsorder', 'like', 'MS-0%')
                    ->count();
            $newArray[$group]['Accept'] = $sales;
            $newArray[$group]['Decline'] = $decline;
        }

        $SPATarget = 5.5;
        $SessionUpdateTime = date('H');
        $SessionUpdateName = date('h A');
        $FTE1 = get_calculate_FTE_3005($start, $end);

        $data = [];
        $data['newArray'] = $newArray;
        $data['SPATarget'] = $SPATarget;
        $data['SessionUpdateTime'] = $SessionUpdateTime;
        $data['SessionUpdateName'] = $SessionUpdateName;
        $data['FTE1'] = $FTE1;
        $data['SaleTargetGroup'] = $SaleTargetGroup;


         $arrayMailBCC  = ['bill@slmconnect.co.uk','chris@slmconnect.co.uk','Catherine.Hopkins@teleperformance.com','Sam.Golding@teleperformance.com','DiallerTeam@intelling.co.uk','Phil.Morgan@intelling.co.uk','Mike.Oxton@intelling.co.uk','George.Eastham@switchexperts.co.uk','James.Wilson@intelling.co.uk','Anthony.Monks@intelling.co.uk','danielle.rossall@intelling.co.uk','Kelly.McNeill@intelling.co.uk','annie.seisay@intelling.co.uk','akumar@usethegeeks.com','developers@usethegeeks.co.uk','amir@slmconnect.co.uk','chris@slmconnect.co.uk','zabed@slmconnect.co.uk'];
         $arrayMailTo  = ['O2Managers@intelling.co.uk','akumar@usethegeeks.com'];
//           $arrayMailBCC  = ['gnegi@usethegeeks.co.uk','rbansal@usethegeeks.co.uk'];
//           $arrayMailTo  = ['akumar@usethegeeks.com'];

        /* Start Mail */
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.o2_addcon';
        $mail_data['bcc'] = !empty($data['bcc']) ? $data['bcc'] : $arrayMailBCC;
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'O2 Addcon Session Update (' . $SessionUpdateName . ')';
        $mail_data['pdf_type'] = !empty($data['pdf_type']) ? $data['pdf_type'] : "Pdf Attachment";
        $mail_data['pdf_attachment'] = !empty($data['pdf_attachment']) ? $data['pdf_attachment'] : null;
        $mail_data['data'] = $data;
        $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                    $m->from($mail_data['from'], 'Intelling');
                    if (!empty($mail_data['bcc'])) {
                        $m->bcc($mail_data['bcc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                });

        unset($_GET['mail']);
        exit;
    }

}
