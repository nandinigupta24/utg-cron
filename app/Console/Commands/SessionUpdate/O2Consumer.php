<?php

namespace App\Console\Commands\SessionUpdate;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;

class O2Consumer extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:O2Consumer';

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
        
        $start = Carbon::now()->subHours(2)->toDateTimeString();
        $end = Carbon::now()->toDateTimeString();
        
        if (date('H') > 20) {
            die('BYE');
        }

        if (date('H') >= 0 && date('H') < 10) {
            die('BYE');
        }
        $newArray = get_group_sales_O2Consumer($start, $end);

        $SPATarget = 2.3;
        $SessionUpdateTime = date('H');
        $SessionUpdateName = date('h A');

        $FTE1 = get_calculate_FTE($start, $end);

        $data = [];
        $data['newArray'] = $newArray;
        $data['SPATarget'] = $SPATarget;
        $data['SessionUpdateTime'] = $SessionUpdateTime;
        $data['SessionUpdateName'] = $SessionUpdateName;
        $data['FTE1'] = $FTE1;

        $arrayMailTo = ['O2Managers@intelling.co.uk'];
//        $arrayMailTo = ['apanwar@usethegeeks.co.uk'];
        $arrayMailCC = ['dialerteam@usethegeeks.zendesk.com',
            'Mike.Oxton@intelling.co.uk',
            'George.Eastham@switchexperts.co.uk',
            'James.Wilson@intelling.co.uk',
            'Anthony.Monks@intelling.co.uk',
            'danielle.rossall@intelling.co.uk',
            'Kelly.McNeill@intelling.co.uk',
            'annie.seisay@intelling.co.uk',
            'akumar@usethegeeks.com',
            'developers@usethegeeks.co.uk'];
        /* Start Mail */
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.session_update.o2_consumer';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : $arrayMailCC;
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'O2 Consumer Session Update (' . $SessionUpdateName . ')';
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
    }

}
