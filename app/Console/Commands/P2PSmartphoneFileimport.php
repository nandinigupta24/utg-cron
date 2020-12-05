<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Illuminate\Support\Str;
use Mail;
use App\Model\UTGAPI\FileImportLog;

class P2PSmartphoneFileimport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:P2PSmartphoneFileimport {yyyymmdd?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automation of P2P Smartphone from UNICA File import command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set('max_execution_time', 6000);
        ini_set('memory_limit', '2048M');

        $server             = '109.234.196.231';
        $serverPort         = 22;
        $connection         = ssh2_connect($server, $serverPort);
        $serverUser         = 'O2UNICA';
        $serverPassword     = '569WbxXq';
        $ServerDirectory    = '/O2Data/';
        $LocalDirectory     = '/var/www/html/cron/storage/Automation/O2UNICA/IN/';
        $LocalImportDirectory = '/var/www/html/cron/storage/Automation/O2UNICA/';
        $TypeFile           = 'P2P-SMARTPHONE-UNICA';
        $dialer             = 'OmniDialer';
        $dateFileGet        = $this->argument('yyyymmdd') ? : date('Ymd');
        $testListID         = 9999999;
        $user               = 'Intelling-OmniChannel';
        $pass               = '2j4VHhYYHqkTnBjJ';
        $token              = '0HeIOWyak5UIdeXKmD4twR7kty030UWXQ4fiU6e6kr7cuPgTm2';
        $randomKey          = Str::random(32);
        $CampaignID         = 3005;
        $newFile = $postData = [];

        if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
            $sftp = ssh2_sftp($connection);
            $sftp_fd = intval($sftp);
            $files = scandir("ssh2.sftp://" . $sftp_fd . $ServerDirectory.'UNI_105_'.$dateFileGet.'*_SMP_OBC_*.dat.gz.gpg', SCANDIR_SORT_DESCENDING);
            // dd($files);
            $Count = 0;
            $ArrayCodeProcess = ['A001554730','A000996975'];
            foreach ($files as $value) {
                $FileSourceCode = get_file_break($value);
                // dd($FileSourceCode);
                if (in_array($FileSourceCode, $ArrayCodeProcess)) {
                    if (strpos($value, $dateFileGet) !== false) {
                        $content = file_get_contents("ssh2.sftp://" . $sftp_fd . $ServerDirectory . $value);
                        $fp = fopen($LocalDirectory . $value, "w");
                        fwrite($fp, $content);
                        fclose($fp);
                        $filename = pathinfo($value, PATHINFO_FILENAME);
                        $filenameEXT = pathinfo($value, PATHINFO_EXTENSION);
                        if ($filenameEXT != 'mfst') {
                            $filename = str_replace('.dat.gz.gpg', '', $value);
                            $filenameEXT = 'dat.gz.gpg';
                        }
                        $FileImportLog = new FileImportLog();
                        $FileImportLog->original_filename = $value;
                        $FileImportLog->filename = $filename;
                        $FileImportLog->type = $TypeFile;
                        $FileImportLog->file_extension = $filenameEXT;
                        $FileImportLog->total = 0;
                        $FileImportLog->success = 0;
                        $FileImportLog->failed = 0;
                        if ($FileImportLog->save()) {
                            $newFile[$FileImportLog->id]['name'] = $filename . '.csv';
                            $newFile[$FileImportLog->id]['type'] = $filenameEXT;
                        }
                    }
                }
            }
        } else {
            die('Not Connected!!');
        }

        /*START NO -FILE*/
        if(empty($newFile) && count($newFile) == 0){
            $mail_data = array();
            $mail_data['to'] = ['automation@usethegeeks.zendesk.com', 'Nicola.Sharrock@intelling.co.uk', 'apanwar@usethegeeks.co.uk'];
           // $mail_data['to'] = ['ngupta@usethegeeks.co.uk'];
            $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
            $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
            $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.automation.P2PAddconAlert';
            $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
            $mail_data['subject'] = 'P2P SMARTPHONE - 4010 - No File Alert';

            $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                        $m->from($mail_data['from'], 'Intelling');
                        if (!empty($mail_data['cc'])) {
                            $m->cc($mail_data['cc']);
                        }
                        $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                        $m->to($mail_data['to'])->subject($mail_data['subject']);
                    });
                    dd('BYE');
        }
        shell_exec('/home/file_conv_O2UNICA.sh');
    }
}
