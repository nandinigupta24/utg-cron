<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\UTGAPI\FileImportLog;
use App\Model\UTGAPI\UNICAP2PCore;
use DB;
use Mail;

class P2PAutomation extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:P2PAutomation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automation of P2P Core from UNICA';

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
//        $dateFileGet = date('20200402');

        $testListID = 9999999;
        $user = 'Intelling-OmniChannel';
        $pass = '2j4VHhYYHqkTnBjJ';
        $token = '0HeIOWyak5UIdeXKmD4twR7kty030UWXQ4fiU6e6kr7cuPgTm2';
        
        $CampaignID = 3001;
        
        $CampaignListID = DB::connection('OmniDialer')->table('lists')->where('campaign_id',$CampaignID)->pluck('list_id');
       
        $ArrayCodeProcess = ['A000980064'];
        

        if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
            $sftp = ssh2_sftp($connection);
            $sftp_fd = intval($sftp);
            $files = scandir("ssh2.sftp://" . $sftp_fd . $ServerDirectory, SCANDIR_SORT_DESCENDING);

            $Count = 0;

            foreach ($files as $value) {
                if (strpos($value, $dateFileGet) !== false) {
                    if (strpos($value, 'A000980064') !== false) {
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
                        $FileImportLog->type = 'P2P-CORE-UNICA';
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
            $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.automation.P2PCoreAlert';
            $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
            $mail_data['subject'] = 'P2P CORE - 3001 - No File Alert';

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
        
        /*File decrypt*/
        shell_exec('/home/file_conv_O2UNICA.sh');
        
        $newFile = FileImportLog::where('type','P2P-CORE-UNICA')->where('file_extension','dat.gz.gpg')->where('created_at','>=',date('Y-m-d').' 00:00:00')->get();
        
        $ArrayCount = [];
        $ArrayCount['total'] = 0;
        $ArrayCount['duplicate'] = 0;
        
        foreach ($newFile as $key=>$val) {
            $File = $LocalImportDirectory.'OUT/'. $val->filename.'.csv';
            $handle = fopen($File, "r");
            $Count = 1;
            
            if (empty($handle) === false) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $PhoneNumber = $PhoneNumber = get_phone_numbers($data[0], '0');
                    
                    $DataExist = DB::connection('OmniDialer')->table('list')->whereIn('list_id',$CampaignListID)->where('phone_number',$PhoneNumber)->count();
                     $ArrayCount['total']++;
                    $DuplicateStatus = 'yes';
                    $DataSource = 'O2_PRETOPOST_RECYCLED';
                    $ListdID = 30011;
                    
                    if($DataExist == 0){
                        $DuplicateStatus = 'no';
                        $DataSource = 'O2_PRETOPOST';
                        $ListdID = 3001;
                        $ArrayCount['duplicate']++;
                    }
                    $UNICAP2PCore = new UNICAP2PCore();
                    $UNICAP2PCore->file_import_log_id = $val->id;
                    $UNICAP2PCore->plus44 = $PhoneNumber;
                    $UNICAP2PCore->Cust_Num = $data[1];
                    $UNICAP2PCore->Cust_Id = $data[2];
                    $UNICAP2PCore->df_AccountID = $data[3];
                    $UNICAP2PCore->Subscription_ID = $data[4];
                    $UNICAP2PCore->CampaignCode = $data[5];
                    $UNICAP2PCore->CellCode = $data[6];
                    $UNICAP2PCore->df_Channel = $data[7];
                    $UNICAP2PCore->TreatmentCode = $data[8];
                    $UNICAP2PCore->df_Email = $data[9];
                    $UNICAP2PCore->Cust_Title = $data[10];
                    $UNICAP2PCore->First_Name = $data[11];
                    $UNICAP2PCore->Surname = $data[12];
                    $UNICAP2PCore->df_Tenure = $data[13];
                    $UNICAP2PCore->df_Avg_Spend_3M = $data[14];
                    $UNICAP2PCore->df_Month1_Spend = $data[15];
                    $UNICAP2PCore->df_Month2_Spend = $data[16];
                    $UNICAP2PCore->df_Month3_Spend = $data[17];
                    $UNICAP2PCore->df_Avg_TopUp = $data[18];
                    $UNICAP2PCore->df_Month1_TopUp = $data[19];
                    $UNICAP2PCore->df_Month2_TopUp = $data[20];
                    $UNICAP2PCore->df_Month3_TopUp = $data[21];
                    $UNICAP2PCore->Avg_Mins_Last3M = $data[22];
                    $UNICAP2PCore->Avg_SMS_Last3M = $data[23];
                    $UNICAP2PCore->Avg_Data_Last3M = $data[24];
                    $UNICAP2PCore->df_Smartphone = $data[25];
                    $UNICAP2PCore->df_BigBundle_Flag = $data[26];
                    $UNICAP2PCore->df_Status = $data[27];
                    $UNICAP2PCore->df_p2p_score = $data[28];
                    $UNICAP2PCore->df_TransactScore = $data[29];
                    $UNICAP2PCore->df_EarlyLifeModelScore = $data[30];
                    $UNICAP2PCore->df_InLifeModelScore = $data[31];
                    $UNICAP2PCore->df_SmartphoneScore = $data[32];
                    $UNICAP2PCore->df_Tariff = $data[33];
                    $UNICAP2PCore->TENURE = $data[34];
                    $UNICAP2PCore->AVG_SPEND = $data[35];
                    $UNICAP2PCore->LAST_MONTH_TOP_UP = $data[36];
                    $UNICAP2PCore->AVG_TOP_UP_3M = $data[37];
                    $UNICAP2PCore->AVG_MIN_3M = $data[38];
                    $UNICAP2PCore->AVG_SMS_3M = $data[39];
                    $UNICAP2PCore->AVG_DATA_3M = $data[40];
                    $UNICAP2PCore->CURRENT_TARRIFF = $data[41];
                    $UNICAP2PCore->list_id = $ListdID;
                    $UNICAP2PCore->datasource = $DataSource;
                    $UNICAP2PCore->duplicate_status = $DuplicateStatus;
                    if($UNICAP2PCore->save()){
                        
                        $ImportID = $UNICAP2PCore->id;
                         
                        /*ARRAY FOR POST DATA START*/
//                        $postData[$ImportID]['import_id'] = $ImportID;
//                        
//                        $postData[$ImportID]['data_list'] = $UNICAP2PCore->list_id;
//                        $postData[$ImportID]['main_phone'] = $PhoneNumber;
//                        $postData[$ImportID]['title'] = $UNICAP2PCore->Cust_Title;
//                        $postData[$ImportID]['first_name'] = $UNICAP2PCore->First_Name;
//                        $postData[$ImportID]['last_name'] = $UNICAP2PCore->Surname;
//                        $postData[$ImportID]['source_code'] = $UNICAP2PCore->df_AccountID;
//                        $postData[$ImportID]['email'] = $UNICAP2PCore->df_Email;
//                        $postData[$ImportID]['source'] = $UNICAP2PCore->datasource;
//
//                        $CustomArray = [];
//                        $CustomArray['CustNum'] = $UNICAP2PCore->Cust_Num;
//                        $CustomArray['O2ClientID'] = $UNICAP2PCore->Cust_Id;
//                        $CustomArray['O2SubscriptionID'] = $UNICAP2PCore->Subscription_ID;
//                        $CustomArray['O2CampaignCode'] = $UNICAP2PCore->CampaignCode;
//                        $CustomArray['O2CellCode'] =  $UNICAP2PCore->CellCode;
//                        $CustomArray['O2Channel'] = $UNICAP2PCore->df_Channel;
//                        $CustomArray['O2TreatmentCode'] = $UNICAP2PCore->TreatmentCode;
//                        $CustomArray['O2BigBundleFlag'] = $UNICAP2PCore->df_BigBundle_Flag;
//                        $CustomArray['O2P2PScore'] = $UNICAP2PCore->df_p2p_score;
//                        $CustomArray['O2TransactScore'] = $UNICAP2PCore->df_TransactScore;
//                        $CustomArray['O2EarlyLifeModel'] = $UNICAP2PCore->df_EarlyLifeModelScore;
//                        $CustomArray['O2CurrentTariff'] = $UNICAP2PCore->CURRENT_TARRIFF;
//
//                        $CustomArray['AboutCust1a'] = 'TENURE';
//                        $CustomArray['AboutCust1b'] = (!empty($UNICAP2PCore->df_Tenure) && $UNICAP2PCore->df_Tenure) ? number_format(floatval($UNICAP2PCore->df_Tenure),2) : $UNICAP2PCore->df_Tenure;
//
//                        $CustomArray['AboutCust2a'] = 'AVG SPEND';
//                        $CustomArray['AboutCust2b'] = (!empty($UNICAP2PCore->df_Avg_Spend_3M) && $UNICAP2PCore->df_Avg_Spend_3M) ? number_format(floatval($UNICAP2PCore->df_Avg_Spend_3M),2) : $UNICAP2PCore->df_Avg_Spend_3M;
//
//                        $CustomArray['AboutCust3a'] = 'LAST MONTH TOP UP';
//                        $CustomArray['AboutCust3b'] = (!empty($UNICAP2PCore->df_Month3_TopUp) && $UNICAP2PCore->df_Month3_TopUp) ? number_format(floatval($UNICAP2PCore->df_Month3_TopUp),2) : $UNICAP2PCore->df_Month3_TopUp;
//
//                        $CustomArray['AboutCust4a'] = 'AVG TOP UP 3M';
//                        $CustomArray['AboutCust4b'] = (!empty($UNICAP2PCore->df_Avg_TopUp) && $UNICAP2PCore->df_Avg_TopUp) ? number_format(floatval($UNICAP2PCore->df_Avg_TopUp),2) : $UNICAP2PCore->df_Avg_TopUp;
//
//                        $CustomArray['AboutCust5a'] = 'AVG MIN 3M';
//                        $CustomArray['AboutCust5b'] = (!empty($UNICAP2PCore->Avg_Mins_Last3M) && $UNICAP2PCore->Avg_Mins_Last3M) ? number_format(floatval($UNICAP2PCore->Avg_Mins_Last3M),2) : $UNICAP2PCore->Avg_Mins_Last3M ;
//
//                        $CustomArray['AboutCust6a'] = 'AVG SMS 3M';
//                        $CustomArray['AboutCust6b'] = (!empty($UNICAP2PCore->Avg_SMS_Last3M) && $UNICAP2PCore->Avg_SMS_Last3M) ? number_format(floatval($UNICAP2PCore->Avg_SMS_Last3M),2) : $UNICAP2PCore->Avg_SMS_Last3M;
//
//                        $CustomArray['AboutCust7a'] = 'AVG DATA 3M';
//                        $CustomArray['AboutCust7b'] = (!empty($UNICAP2PCore->Avg_Data_Last3M) && $UNICAP2PCore->Avg_Data_Last3M) ? number_format(floatval($UNICAP2PCore->Avg_Data_Last3M), 2) : $UNICAP2PCore->Avg_Data_Last3M;
//
//                        $CustomArray['AboutCust8a'] = 'CURRENT TARRIFF';
//                        $CustomArray['AboutCust8b'] = $UNICAP2PCore->df_Tariff;
//
//                        $postData[$ImportID]['custom_fields'] = $CustomArray;
//                        break;
                        /*ARRAY FOR DATA POST END*/ 
                         
                         
                    }
                }
            }
        }
        
        
        
        /*Data POST ON API*/
//        $postData1 = [];
//        $postData1['token'] = $token;
//        $postData1['customers'] = $postData;
//         
//        $NewData = get_omni_api_curl_test($user, $pass, $token, $postData1);
//        
//        get_unicaP2PCore_response($NewData);

        
        
        FileImportLog::where('type','P2P-CORE-UNICA')->where('file_extension','dat.gz.gpg')->where('created_at','>=',date('Y-m-d').' 00:00:00')->update(['total'=>$ArrayCount['total'],'success'=>($ArrayCount['total']-$ArrayCount['duplicate']),'duplicate'=>$ArrayCount['duplicate']]);
        
//        $FileDetail = FileImportLog::where('type','P2P-CORE-UNICA')->where('file_extension','dat.gz.gpg')->where('created_at','>=',date('Y-m-d').' 00:00:00')->first();
        
//        $FileImportLogID = $FileDetail->id;
        
        
        
        
    }

}
