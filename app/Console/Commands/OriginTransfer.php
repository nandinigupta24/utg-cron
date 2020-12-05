<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Mail;

class OriginTransfer extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:OriginTransfer {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Dialer Log Details';

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
        ini_set('max_execution_time', 6000);
        ini_set('memory_limit', '2048M');

            $current_date =$this->argument('date') ? : date('Y-m-d');

            $users = DB::connection('NewConnex')->select("SELECT u.call_date,left(RIGHT(u.number_dialed,11),11) AS `Number_Dialled`, u.lead_id, u.user, us.full_name, us.user_group, il.status, l.dbl_status, u.callerid
            from user_call_log u
            LEFT JOIN inbound_log il ON il.lead_id=u.lead_id
            JOIN list l ON il.lead_id=l.lead_id
            JOIN users us ON u.user=us.user
            where u.number_dialed IN ('8801302235191',
            '8801302235190',
            '8801302235189',
            '8801709803595')
            AND u.call_date between '$current_date 00:00:00' AND '$current_date 23:59:59'
            AND il.call_date between '$current_date 00:00:00' AND '$current_date 23:59:59'");

            $path = "/var/www/html/cron/assetnew/attach/";
            $filename = "userData".$current_date.".csv";
            $file = fopen('php://output', 'w');

            $file = fopen($path.$filename, 'w');

            $header = ['Call Date', 'Dial Number', 'Lead ID','User', 'Name','User Group','Status', 'DBL Status', 'Caller ID'];
            fputcsv($file, $header);
            foreach ($users as $user) {
                $arrayPost = [];
                $arrayPost[0] = $user->call_date;
                $arrayPost[1] = $user->Number_Dialled;
                $arrayPost[2] = $user->lead_id;
                $arrayPost[3] = $user->user;
                $arrayPost[4] = $user->full_name;
                $arrayPost[5] = $user->user_group;
                $arrayPost[6] = $user->status;
                $arrayPost[7] = $user->dbl_status;
                $arrayPost[8] = $user->callerid;
                fputcsv($file, $arrayPost);
            }
            fclose($file);

            sleep(10);

            /* SEND MAIL */

            $data = [];
            $data['date'] = $current_date;
            $data['file_name'] = $filename;
            $mail_data = array();
            $mail_data['to'] = ['ngupta@usethegeeks.co.uk','Originmanagers@intelling.co.uk', 'James.Playford@intelling.co.uk', 'DiallerTeam@intelling.co.uk'];
            //$mail_data['to'] = ['ngupta@usethegeeks.co.uk'];
            $mail_data['from'] = 'intellingreports@intelling.co.uk';
            $mail_data['view'] = 'emails.origin_transfer';
            $mail_data['subject'] = 'Origin Transfer';
            $mail_data['cc'] = ['akumar@usethegeeks.com'];
            $mail_data['users'] = $data;
            $mail_data['file_name'] = $path.$filename;

            $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                $m->from($mail_data['from'], 'Intelling');
                if (!empty($mail_data['cc'])) {
                    $m->cc($mail_data['cc']);
                }
                $m->attach($mail_data['file_name']);
                $m->to($mail_data['to'])->subject($mail_data['subject']);
            });
    }

}
