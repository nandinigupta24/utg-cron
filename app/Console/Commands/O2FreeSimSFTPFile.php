<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;
use Log;
use App\Model\Intelling\O2DataFileLogs;
use App\Model\Intelling\O2Data;
use App\Model\Intelling\DynamicStroage;

class O2FreeSimSFTPFile extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:O2FreeSimSFTPFile';

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
        ini_set('memory_limit', '2048M'); 
        $server = '10.68.120.59';
        
        $serverPort = 22;
        $connection = ssh2_connect($server, $serverPort);
        $serverUser = 'root';
        $serverPassword = '16IndiaGeeksUK';
        $O2DataFileLogsArray = O2DataFileLogs::pluck('file_name')->toArray();
        if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
            $sftp = ssh2_sftp($connection);
            $sftp_fd = intval($sftp);
            $files = scandir("ssh2.sftp://" . $sftp_fd . "/home/Ankit/O2OldData/Dynamic/PreJuly2015/", SCANDIR_SORT_ASCENDING);
            $newFile = [];
            $Count = 0;
            foreach ($files as $value) {
                if (in_array($value, ['.', '..', '...', '....'], true)) {
                    continue;
                }
                if (in_array($value, $O2DataFileLogsArray, true)) {
                    continue;
                }
                echo $value.' - '.filesize("ssh2.sftp://" . $sftp_fd . "/home/Ankit/O2OldData/Dynamic/PreJuly2015/" . $value);
        exit;
                $content = file_get_contents("ssh2.sftp://" . $sftp_fd . "/home/Ankit/O2OldData/Dynamic/PreJuly2015/" . $value);
                $fp = fopen("/var/www/html/cron/storage/Automation/O2FreeSimSFTP/" . $value, "w");
                fwrite($fp, $content);
                fclose($fp);
                $O2DataFileLogs = new O2DataFileLogs();
                $O2DataFileLogs->file_name = $value;
                $O2DataFileLogs->total = 0;
                $O2DataFileLogs->success = 0;
                $O2DataFileLogs->fail = 0;
                $O2DataFileLogs->chron_setting = 'O2FreeSimSFTPFile';
                if ($O2DataFileLogs->save()) {
                    $newFile[$O2DataFileLogs->id] = $value;
                }
            }
        }
    }

}
