<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Excel;
use Storage;
use App\Model\UTGAPI\O2ReturnProcessFile;
use Mail;

class O2ReturnProcessSFTP extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:O2ReturnProcessSFTP';

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
        ini_set('max_execution_time', 6000);
        $server = '158.230.101.193';
//        $server = '10.68.120.59';
        $serverPort = 22;
        $connection = ssh2_connect($server, $serverPort);
        $serverUser = 'int2nuc';
//        $serverUser = 'root';
        $serverPassword = 'qwerty123';
//        $serverPassword = '16IndiaGeeksUK';
        $data = O2ReturnProcessFile::orderBy('id', 'desc')->first();
        $fileName = $data->File_name;
        $MSTfileName = $data->mfst_file_name;

        if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
            if (!empty($fileName)) {
                ssh2_scp_send($connection, "/var/www/html/cron/storage/Automation/O2ReturnProcess/export/1330/" . $fileName . '.dat.gz.gpg', "/export/home/int2nuc/export/home/int2nuc/received/" . $fileName . '.dat.gz.gpg');
//                ssh2_scp_send($connection, "/var/www/html/cron/storage/Automation/O2ReturnProcess/export/1330/" . $fileName . '.dat.gz.gpg', "/home/Ankit/" . $fileName . '.dat.gz.gpg');
            }
            if (!empty($MSTfileName)) {
                ssh2_scp_send($connection, "/var/www/html/cron/storage/Automation/O2ReturnProcess/export/1330/" . $MSTfileName . '.mfst', "/export/home/int2nuc/export/home/int2nuc/received/" . $MSTfileName . '.mfst');
//                ssh2_scp_send($connection, "/var/www/html/cron/storage/Automation/O2ReturnProcess/export/1330/" . $MSTfileName . '.mfst', "/home/Ankit/" . $MSTfileName . '.mfst');
            }
        }


//        $arrayMailTo = ['apanwar@usethegeeks.co.uk','Andy.Hughes@intelling.co.uk','Nicola.Sharrock@intelling.co.uk','diallersupport@intelling.co.uk'];
        $arrayMailTo = ['apanwar@usethegeeks.co.uk','Andy.Hughes@intelling.co.uk','Nicola.Sharrock@intelling.co.uk',env('DIALER_TEAM_EMAIL')];
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.o2_return_process';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'O2 Return Process (' . date('Y-m-d') . ')';
        $mail_data['data'] = @$data;

        $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                    $m->from($mail_data['from'], 'API Reports');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'API Reports');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                });
                 if (!empty($fileName)) {
                unlink("/var/www/html/cron/storage/Automation/O2ReturnProcess/export/1330/" . $fileName . '.dat.gz.gpg');
                 }
                  if (!empty($MSTfileName)) {
                unlink("/var/www/html/cron/storage/Automation/O2ReturnProcess/export/1330/" . $MSTfileName . '.mfst');
                  }
    }

}
