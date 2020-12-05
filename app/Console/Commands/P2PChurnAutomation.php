<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\UTGAPI\FileImportLog;
use App\Model\UTGAPI\UNICAP2PChurn;
use DB;
use Illuminate\Support\Str;
use Mail;

class P2PChurnAutomation extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:P2PChurnAutomation';

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
        $newFile = [];
        $dateFileGet = date('Ymd');
          $dateFileGet = date('20200706');

        $testListID = 9999999;
        $user = 'Intelling-OmniChannel'; 
        $pass = '2j4VHhYYHqkTnBjJ';
        $token = '0HeIOWyak5UIdeXKmD4twR7kty030UWXQ4fiU6e6kr7cuPgTm2';

        $randomKey = Str::random(32);
        $randomKey = 'r2CbK0njStCVdFc3dyv5iRsCQtJIMA2m';

        $CampaignID = 3045;

        $CampaignListID = DB::connection('MainDialer')->table('lists')->where('campaign_id', $CampaignID)->pluck('list_id');


        $ArrayCodeProcess = ['A001594207', 'A001644919', 'A001645526', 'A001645530', 'A001645534'];

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
                        $FileImportLog->type = 'P2P-CHURN-UNICA';
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

        /* START NO -FILE */
        if (empty($newFile) && count($newFile) == 0) {
            $mail_data = array();
            $mail_data['to'] = ['automation@usethegeeks.zendesk.com', 'Nicola.Sharrock@intelling.co.uk', 'apanwar@usethegeeks.co.uk'];
//            $mail_data['to'] = ['apanwar@usethegeeks.co.uk'];
            $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
            $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
            $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.automation.P2PChurnAlert';
            $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
            $mail_data['subject'] = 'P2P CHURN - 3045 - No File Alert';

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
        /* END NO - FILE */

        /* File decrypt */
        shell_exec('/home/file_conv_O2UNICA.sh');

        $newFile = FileImportLog::where('type', 'P2P-CHURN-UNICA')->where('file_extension', 'dat.gz.gpg')->where('created_at', '>=', date('Y-m-d') . ' 00:00:00')->get();

        foreach ($newFile as $key => $val) {
            $File = $LocalImportDirectory . 'OUT/' . $val->filename . '.csv';
            $handle = fopen($File, "r");
            $Count = 0;

            if (empty($handle) === false) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $Count++;
                    $PhoneNumber = get_phone_numbers($data[0], '0');

                    $DataExist = DB::connection('MainDialer')
                            ->table('list')
                            ->whereIn('list_id', $CampaignListID)
                            ->where('phone_number', $PhoneNumber)
                            ->count();

                    $DuplicateStatus = 'yes';
                    $DataSource = 'O2_P2P_CHURN_RECYCLED';
                    $ListdID = 30451;

                    if ($DataExist == 0) {
                        $DuplicateStatus = 'no';
                        $DataSource = 'O2_P2P_CHURN';
                        $ListdID = 3045;
                    }
                    $UNICAP2PChurn = new UNICAP2PChurn();
                    $UNICAP2PChurn->file_import_log_id = $val->id;
                    $UNICAP2PChurn->plus44 = $PhoneNumber;
                    $UNICAP2PChurn->Cust_Num = $data[1];
                    $UNICAP2PChurn->Cust_Id = $data[2];
                    $UNICAP2PChurn->Acct_ID = $data[3];
                    $UNICAP2PChurn->Subscr_ID = $data[4];
                    $UNICAP2PChurn->Campaign_Code = (int) $data[5];
                    $UNICAP2PChurn->Cell_Code = $data[6];
                    $UNICAP2PChurn->Channel = $data[7];
                    $UNICAP2PChurn->Treatment_Code = (int) $data[8];
                    $UNICAP2PChurn->Email_Address = $data[9];
                    $UNICAP2PChurn->Cust_Title = $data[10];
                    $UNICAP2PChurn->First_Name = $data[11];
                    $UNICAP2PChurn->Last_Name = $data[12];
                    $UNICAP2PChurn->Tenure = $data[13];
                    $UNICAP2PChurn->Average_monthly_spend = $data[14];
                    $UNICAP2PChurn->Month_1_Spend = $data[15];
                    $UNICAP2PChurn->Month_2_Spend = $data[16];
                    $UNICAP2PChurn->GDPR_Bundle = $data[17];
                    $UNICAP2PChurn->DCP_flag = $data[18];
                    $UNICAP2PChurn->Average_Top_up = $data[19];
                    $UNICAP2PChurn->Month_1_Top_Up = $data[20];
                    $UNICAP2PChurn->Month_2_Top_Up = $data[21];
                    $UNICAP2PChurn->Month_3_Top_Up = $data[22];
                    $UNICAP2PChurn->Average_minutes = $data[23];
                    $UNICAP2PChurn->Average_SMS = $data[24];
                    $UNICAP2PChurn->Average_Data_Consumption = $data[25];
                    $UNICAP2PChurn->Smartphone_flag = $data[26];
                    $UNICAP2PChurn->Big_Bundles_flag = $data[27];
                    $UNICAP2PChurn->Active = $data[28];
                    $UNICAP2PChurn->P2P_score = $data[29];
                    $UNICAP2PChurn->Transact_Score = $data[30];
                    $UNICAP2PChurn->Early_Life_model_score = $data[31];
                    $UNICAP2PChurn->In_Life_Churn_model_score = $data[32];
                    $UNICAP2PChurn->Smartphone_score = $data[33];
                    $UNICAP2PChurn->Current_tariff = $data[34];
                    $UNICAP2PChurn->Phishing_Email = $data[35];
                    $UNICAP2PChurn->Auto_Camp_Deploy_Date = $data[36];
                    $UNICAP2PChurn->Progressive_SMS = $data[37];
                    $UNICAP2PChurn->Progressive_Email = $data[38];
                    $UNICAP2PChurn->Progressive_Outbound_Call = $data[39];
                    $UNICAP2PChurn->Progressive_DM = $data[40];
                    $UNICAP2PChurn->list_id = $ListdID;
                    $UNICAP2PChurn->datasource = $DataSource;
                    $UNICAP2PChurn->duplicate_status = $DuplicateStatus;
                    $UNICAP2PChurn->file_process_count = $DuplicateStatus;
                    $UNICAP2PChurn['file_process_count'] = $randomKey;
                    if ($UNICAP2PChurn->save()) {
//                        $ImportID = $UNICAP2PChurn->id; 
                    }
                }
            }
            FileImportLog::where('id', $val->id)->update(['total' => $Count, 'file_process_key' => $randomKey]);
        }
    }

}
