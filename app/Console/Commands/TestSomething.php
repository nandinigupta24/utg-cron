<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\ScriptResponse;
use App\Model\SwitchExpertEnergy\SDSales;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;
use App\Model\O2Inbound\InboundSale;
use App\Model\UTGAPI\O2ReturnProcessData;
use App\Model\UTGAPI\O2UNICA;

class TestSomething extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:TestSomething';

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
        die('HELLO');
//        https://filetransfer.serco.com/
        $server = 'filetransfer.serco.com';
        $serverPort = 22;
        $connection = ssh2_connect($server, $serverPort);
        $serverUser = 'kanderson';
        $serverPassword = 'Intelling123!';
        $ServerDirectory = '/Intelling/';

        if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
            ssh2_scp_send($connection, "/var/www/html/cron/storage/2020-03-12-NHS-DUPLICATE.zip", $ServerDirectory . "2020-03-12-NHS-DUPLICATE.zip");
        }
        
//        $server = '217.22.12.237';
//        $serverPort = 22;
//        $connection = ssh2_connect($server, $serverPort);
//        $serverUser = 'kanderson';
//        $serverPassword = 'Intelling123!';
//        $ServerDirectory = '/Intelling/';
//        
//
//        if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
//            $sftp = ssh2_sftp($connection);
//            $sftp_fd = intval($sftp);
//            $files = scandir("ssh2.sftp://" . $sftp_fd . $ServerDirectory, SCANDIR_SORT_DESCENDING);
//            pr($files);
//        }
    }

}
