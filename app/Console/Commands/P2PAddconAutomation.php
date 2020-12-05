<?php //

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\UTGAPI\FileImportLog;
use App\Model\UTGAPI\UNICAP2PAddcon;
use DB;
use Illuminate\Support\Str;
use Mail;
use DateTime;

class P2PAddconAutomation extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:P2PAddconAutomation {yyyymmdd?}';

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

    public function validateDate($date, $format = 'd-m-Y'){
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
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

        $newFile = FileImportLog::where('type', $TypeFile)->where('file_extension', 'dat.gz.gpg')->where('created_at', '>=', date('Y-m-d') . ' 00:00:00')->get();

        foreach ($newFile as $key => $val) {
            $File = $LocalImportDirectory . 'OUT/' . $val->filename . '.csv';
            if(!file_exists($File)) continue;
            $handle = fopen($File, "r");
            $lineCount = 0;

            if (empty($handle) === false) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $lineCount++;

                    $PhoneNumber = get_phone_numbers($data[0], '0');

                    $DataExist = DB::connection($dialer)
                            ->table('list')
                            ->whereIn('list_id', $CampaignListID)
                            ->where('phone_number', $PhoneNumber)
                            ->count();

                    $DuplicateStatus = 'yes';
                    $DataSource = 'O2_ADDCONS_RECYCLED';
                    $ListdID = 30051;

                    if ($DataExist == 0) {
                        $DataExistListArchive = DB::connection($dialer)->table('list_archive')->whereIn('list_id', $CampaignListID)->where('phone_number',$PhoneNumber)->count();
                        if($DataExistListArchive == 0){
                             $DuplicateStatus = 'no';
                               $DataSource = 'O2_ADDCONS';
                               $ListdID = 3005;
                        }
                    }

                    if($data[0] == 0){
                        continue;
                    }
                    $count = 0;

                    if(!$this->validateDate($data[20])){
                        $count++;
                        for($i = 21; $i < count($data); $i++) {
                            if(!$this->validateDate($data[$i])) {
                                $count++;
                            } else{
                                break;
                            }
                        }
                        $address = '';
                        $row = '';
                        for($i = 13; $i <= (13+$count); $i++){
                            $row= $i;
                            $address .= $data[$i].' ';
                        }

                        $UNICAP2PAddcon = new UNICAP2PAddcon();
                        $UNICAP2PAddcon->file_import_log_id = $val->id;
                        $UNICAP2PAddcon->PLUS44 = $PhoneNumber;
                        $UNICAP2PAddcon->Cust_Num = $data[1];
                        $UNICAP2PAddcon->Cust_ID = $data[2];
                        $UNICAP2PAddcon->Acct_ID = $data[3];
                        $UNICAP2PAddcon->Subscr_ID = $data[4];
                        $UNICAP2PAddcon->Campaign_Code = $data[5];
                        $UNICAP2PAddcon->Cell_Code = $data[6];
                        $UNICAP2PAddcon->Channel = $data[7];
                        $UNICAP2PAddcon->Treatment_Code = $data[8];
                        $UNICAP2PAddcon->Email_Address = $data[9];
                        $UNICAP2PAddcon->Title = $data[10];
                        $UNICAP2PAddcon->Firstname = $data[11];
                        $UNICAP2PAddcon->Surname = $data[12];
                        $UNICAP2PAddcon->Address1 = $address;
                        $UNICAP2PAddcon->Address2 = '';
                        $UNICAP2PAddcon->Address3 = '';
                        $UNICAP2PAddcon->Town = $data[16+$count];
                        $UNICAP2PAddcon->County = $data[17+$count];
                        $UNICAP2PAddcon->Postcode = $data[18+$count];
                        $UNICAP2PAddcon->Propensity = $data[19+$count];
                        $UNICAP2PAddcon->Activation_Date = date('Y-m-d',strtotime($data[20+$count]));
                        $UNICAP2PAddcon->Avg_Data_Usage = $data[21+$count];
                        $UNICAP2PAddcon->DOB = $data[22+$count];
                        $UNICAP2PAddcon->Tariff_Name = $data[23+$count];
                        $UNICAP2PAddcon->DCP_Customers = $data[24+$count];
                        $UNICAP2PAddcon->Sky_BT_Flag = $data[25+$count];
                        $UNICAP2PAddcon->Customers_Monthly_Spend = $data[26+$count];
                        $UNICAP2PAddcon->Customers_In_Arrears = $data[27+$count];
                        $UNICAP2PAddcon->Number_Of_Previous_Transaction = $data[28+$count];
                        $UNICAP2PAddcon->GDPR_Bundle_1 = $data[29+$count];
                        $UNICAP2PAddcon->GDPR_Bundle_2 = $data[30+$count];
                        $UNICAP2PAddcon->GDPR_Bundle_3 = $data[31+$count];
                        $UNICAP2PAddcon->SMS_OPT_IN = $data[32+$count];
                        $UNICAP2PAddcon->Source_Partner = $data[33+$count];
                        $UNICAP2PAddcon->Device_Name = $data[34+$count];
                        $UNICAP2PAddcon->Customers_Affluence = $data[35+$count];
                        $UNICAP2PAddcon->Month_SIMO_Customers = $data[36+$count];
                        $UNICAP2PAddcon->Like_New_Model_Decile = $data[37+$count];
                        $UNICAP2PAddcon->Smartphone_Flag = $data[38+$count];
                        $UNICAP2PAddcon->list_id = $ListdID;
                        $UNICAP2PAddcon->datasource = $DataSource;
                        $UNICAP2PAddcon->duplicate_status = $DuplicateStatus;
                        $UNICAP2PAddcon->custom_1 = date('Y-m-d',strtotime($data[39+$count]));
                        $UNICAP2PAddcon->custom_2 = $data[40+$count];
                        $UNICAP2PAddcon->orbis_id = isset($data[52+$count]) ? $data[52+$count] : '';
                        if ($UNICAP2PAddcon->save()) {

                        }

                    } else {
                        $UNICAP2PAddcon = new UNICAP2PAddcon();
                        $UNICAP2PAddcon->file_import_log_id = $val->id;
                        $UNICAP2PAddcon->PLUS44 = $PhoneNumber;
                        $UNICAP2PAddcon->Cust_Num = $data[1];
                        $UNICAP2PAddcon->Cust_ID = $data[2];
                        $UNICAP2PAddcon->Acct_ID = $data[3];
                        $UNICAP2PAddcon->Subscr_ID = $data[4];
                        $UNICAP2PAddcon->Campaign_Code = $data[5];
                        $UNICAP2PAddcon->Cell_Code = $data[6];
                        $UNICAP2PAddcon->Channel = $data[7];
                        $UNICAP2PAddcon->Treatment_Code = $data[8];
                        $UNICAP2PAddcon->Email_Address = $data[9];
                        $UNICAP2PAddcon->Title = $data[10];
                        $UNICAP2PAddcon->Firstname = $data[11];
                        $UNICAP2PAddcon->Surname = $data[12];
                        $UNICAP2PAddcon->Address1 = $data[13];
                        $UNICAP2PAddcon->Address2 = $data[14];
                        $UNICAP2PAddcon->Address3 = $data[15];
                        $UNICAP2PAddcon->Town = $data[16];
                        $UNICAP2PAddcon->County = $data[17];
                        $UNICAP2PAddcon->Postcode = $data[18];
                        $UNICAP2PAddcon->Propensity = $data[19];
                        $UNICAP2PAddcon->Activation_Date = date('Y-m-d',strtotime($data[20]));
                        $UNICAP2PAddcon->Avg_Data_Usage = $data[21];
                        $UNICAP2PAddcon->DOB = $data[22];
                        $UNICAP2PAddcon->Tariff_Name = $data[23];
                        $UNICAP2PAddcon->DCP_Customers = $data[24];
                        $UNICAP2PAddcon->Sky_BT_Flag = $data[25];
                        $UNICAP2PAddcon->Customers_Monthly_Spend = $data[26];
                        $UNICAP2PAddcon->Customers_In_Arrears = $data[27];
                        $UNICAP2PAddcon->Number_Of_Previous_Transaction = $data[28];
                        $UNICAP2PAddcon->GDPR_Bundle_1 = $data[29];
                        $UNICAP2PAddcon->GDPR_Bundle_2 = $data[30];
                        $UNICAP2PAddcon->GDPR_Bundle_3 = $data[31];
                        $UNICAP2PAddcon->SMS_OPT_IN = $data[32];
                        $UNICAP2PAddcon->Source_Partner = $data[33];
                        $UNICAP2PAddcon->Device_Name = $data[34];
                        $UNICAP2PAddcon->Customers_Affluence = $data[35];
                        $UNICAP2PAddcon->Month_SIMO_Customers = $data[36];
                        $UNICAP2PAddcon->Like_New_Model_Decile = $data[37];
                        $UNICAP2PAddcon->Smartphone_Flag = $data[38];
                        $UNICAP2PAddcon->list_id = $ListdID;
                        $UNICAP2PAddcon->datasource = $DataSource;
                        $UNICAP2PAddcon->duplicate_status = $DuplicateStatus;
                        $UNICAP2PAddcon->custom_1 = date('Y-m-d',strtotime($data[39]));
                        $UNICAP2PAddcon->custom_2 = $data[40];
                        $UNICAP2PAddcon->orbis_id = isset($data[52]) ? $data[52] : '';
                        if ($UNICAP2PAddcon->save()) {

                        }
                    }
                }

            }
            FileImportLog::where('id', $val->id)->update(['total' => $lineCount]);
        }
    }

}
