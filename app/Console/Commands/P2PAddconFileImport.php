<?php //

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\UTGAPI\FileImportLog;
use DB;
use Illuminate\Support\Str;
use Mail;

class P2PAddconFileImport extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:P2PAddconFileImport {yyyymmdd?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automation of P2P Addcon from UNICA';

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

        $postData = [];
        $server = '109.234.196.231';
        $serverPort = 22;
        $connection = ssh2_connect($server, $serverPort);
        $serverUser = 'O2UNICA';
        $serverPassword = '569WbxXq';
        $ServerDirectory = '/O2Data/';
        $LocalDirectory = '/var/www/html/cron/storage/Automation/O2UNICA/IN/';
        $LocalImportDirectory = '/var/www/html/cron/storage/Automation/O2UNICA/';
        $TypeFile = 'P2P-ADDCON-UNICA';
        $dialer = 'OmniDialer';
        $newFile = [];
//        $dateFileGet = date('Ymd');
        // $dateFileGet = date('20200922');
        $dateFileGet = $this->argument('yyyymmdd') ? : date('Ymd');    

        $testListID = 9999999;
        $user = 'Intelling-OmniChannel';
        $pass = '2j4VHhYYHqkTnBjJ';
        $token = '0HeIOWyak5UIdeXKmD4twR7kty030UWXQ4fiU6e6kr7cuPgTm2';

        $randomKey = Str::random(32);

        $CampaignID = 3005;

        $CampaignListID = DB::connection($dialer)->table('lists')->where('campaign_id', $CampaignID)->pluck('list_id');

        $ArrayCodeProcess = ['A001855533','A001855527','A001855521','A001855515','A001647417','A001647427','A001647437','A001647451','A001647453','A001647455','A001647461','A001647433','A001647457','A001706754','A001706738','A001706549','A001706539'];

        if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
            $sftp = ssh2_sftp($connection);
            $sftp_fd = intval($sftp);
            $files = scandir("ssh2.sftp://" . $sftp_fd . $ServerDirectory, SCANDIR_SORT_DESCENDING);
            $Count = 0;

            foreach ($files as $value) {
                $FileSourceCode = get_file_break($value);
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
//            $mail_data['to'] = ['apanwar@usethegeeks.co.uk'];
            $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
            $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
            $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.automation.P2PAddconAlert';
            $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
            $mail_data['subject'] = 'P2P ADDCON - 3005 - No File Alert';

            $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                        $m->from($mail_data['from'], 'Intelling');
                        if (!empty($mail_data['cc'])) {
                            $m->cc($mail_data['cc']);
                        }
                        $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                        $m->to($mail_data['to'])->subject($mail_data['subject']);
                    });

                    die('BYE');
        }

        /*END NO - FILE*/

//        die('HEllo');
        /* File decrypt */
        shell_exec('/home/file_conv_O2UNICA.sh');

    }
}
