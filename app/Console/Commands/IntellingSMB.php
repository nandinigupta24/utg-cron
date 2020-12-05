<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\UTGAPI\FileImportLog;
use App\Model\UTGAPI\UNICAP2PChurn;
use DB;
use Illuminate\Support\Str;
use Mail;

class IntellingSMB extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:IntellingSMB';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automation of P2P Churn from UNICA';

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
        
        $server = '109.234.196.231';
        $serverPort = 22;
        $connection = ssh2_connect($server, $serverPort);
        $serverUser = 'O2UNICA';
        $serverPassword = '569WbxXq';
        $ServerDirectory = '/O2Data/';
        $LocalDirectory = '/var/www/html/cron/storage/Automation/O2UNICA/IN/';
        $LocalImportDirectory = '/var/www/html/cron/storage/Automation/O2UNICA/';
        $newFile = [];
        $dateFileGet = date('Ymd');
//        $dateFileGet = '20200310';



        if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
            $sftp = ssh2_sftp($connection);
            $sftp_fd = intval($sftp);
            $files = scandir("ssh2.sftp://" . $sftp_fd . $ServerDirectory, SCANDIR_SORT_DESCENDING);
            $Count = 0;

            foreach ($files as $value) {
                if (strpos($value, 'INTELLING_SMB1to9') !== false) {
                    if (strpos($value, $dateFileGet) !== false) {
                        $content = file_get_contents("ssh2.sftp://" . $sftp_fd . $ServerDirectory . $value);
                        $fp = fopen($LocalDirectory . $value, "w");
                        fwrite($fp, $content);
                        fclose($fp);
                        $filename = str_replace('.dat.gz.gpg','',$value);
                        
                    }
                }
            }
        } else {
            die('Not Connected!!');
        }


        if(empty($filename)){
           die('File does not exist!!'); 
        }

        /* File decrypt */
        shell_exec('/home/file_conv_O2UNICA.sh');

        $server = '3.8.11.11';
        $serverPort = 22;
        $connection = ssh2_connect($server, $serverPort);
        $serverUser = 'root';
        $serverPassword = 'Utgesx0012!!';
        $ServerDirectory = '/home/WyneO2B2B/';

        if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
            ssh2_scp_send($connection, "/var/www/html/cron/storage/Automation/O2UNICA/OUT/".$filename.".csv", $ServerDirectory .$filename.".csv");
        }
    }

}
