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
use App\Model\UTGAPI\O2UNICACount;
use App\Model\UTGAPI\O2UNICAManifest;
use App\Model\UTGAPI\FileImportLog;
use App\Model\O2UNICA\DataListing;
use App\Model\O2UNICA\DataReport;

class O2UNICAProcess extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:O2UNICAProcess';

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
        ini_set('memory_limit','2048M');

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
        $dateFileGet = date('ymd');
        $CampaignID = 1330;

        $CampaignListID = DB::connection('OmniDialer')->table('lists')->where('campaign_id',$CampaignID)->pluck('list_id');

        $testListID = 9999999;
        $user = 'Intelling-OmniChannel';
        $pass = '2j4VHhYYHqkTnBjJ';
        $token = '0HeIOWyak5UIdeXKmD4twR7kty030UWXQ4fiU6e6kr7cuPgTm2';

        $FileCodeArray = [];
        $FileCodeArray['A001114912'] = ['Tarrif'=>['CLASSICPAYG'],'Datasource'=>'O2_P2PADDITIONAL_CLASSIC','List'=>1330,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_CLASSIC_REC','Duplicate_List'=>13302];
        $FileCodeArray['A001114914'] = ['Tarrif'=>['INTERNATIONALSIM'],'Datasource'=>'O2_P2PADDITIONAL_INTERNATIONAL','List'=>1330,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_INTERNATIONAL_REC','Duplicate_List'=>13302];
        $FileCodeArray['A001114916'] = ['Tarrif'=>['CLASSICPAYG','INTERNATIONALSIM'],'Datasource'=>'O2_P2PADDITIONAL','List'=>1330,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_REC','Duplicate_List'=>13302];
        $FileCodeArray['A001114910'] = ['Tarrif'=>['CLASSICPAYG'],'Datasource'=>'O2_P2PADDITIONAL_SUB10_CLASSIC','List'=>13303,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_SUB10_CLASSIC_REC','Duplicate_List'=>13305];
        $FileCodeArray['A001732657'] = ['Tarrif'=>['CLASSICPAYG'],'Datasource'=>'O2_P2PADDITIONAL_SUB10_CLASSIC','List'=>1330,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_SUB10_CLASSIC_REC','Duplicate_List'=>13302];
        $FileCodeArray['A001113235'] = ['Tarrif'=>['INTERNATIONALSIM'],'Datasource'=>'O2_P2PADDITIONAL_SUB10_INTERNATIONAL','List'=>13303,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_SUB10_INTERNATIONAL_REC','Duplicate_List'=>13305];
        $FileCodeArray['A001114899'] = ['Tarrif'=>['CLASSICPAYG','INTERNATIONALSIM'],'Datasource'=>'O2_P2PADDITIONAL_SUB10','List'=>13303,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_SUB10_REC','Duplicate_List'=>13305];
        $FileCodeArray['A001376530'] = ['Tarrif'=>['CLASSICPAYG','INTERNATIONALSIM'],'Datasource'=>'O2_P2PADDITIONAL_SUB10','List'=>13303,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_SUB10_REC','Duplicate_List'=>13305];
        $FileCodeArray['A001732655'] = ['Tarrif'=>['CLASSICPAYG','INTERNATIONALSIM'],'Datasource'=>'O2_P2PADDITIONAL_SUB10','List'=>13303,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_SUB10_REC','Duplicate_List'=>13305];

        $ArrayCodeProcess = ['A001114899','A001113235','A001114910','A001114912','A001114914','A001732655','A001732657','A001376530'];

        $FileCodeNewArray = [];
        $FileCodeNewArray[1330]['CLASSICPAYG'] = ['Datasource'=>'O2_P2PADDITIONAL_CLASSIC','List'=>1330,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_CLASSIC_REC','Duplicate_List'=>13302];
        $FileCodeNewArray[1330]['INTERNATIONALSIM'] = ['Datasource'=>'O2_P2PADDITIONAL_INTERNATIONAL','List'=>1330,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_INTERNATIONAL_REC','Duplicate_List'=>13302];
        $FileCodeNewArray[1330]['CLASSICPAYG-INTERNATIONALSIM'] = ['Datasource'=>'O2_P2PADDITIONAL','List'=>1330,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_REC','Duplicate_List'=>13302];

        $FileCodeNewArray[13303]['CLASSICPAYG'] = ['Datasource'=>'O2_P2PADDITIONAL_SUB10_CLASSIC','List'=>13303,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_SUB10_REC','Duplicate_List'=>13305];
        $FileCodeNewArray[13303]['INTERNATIONALSIM'] = ['Datasource'=>'O2_P2PADDITIONAL_SUB10_INTERNATIONAL','List'=>13303,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_SUB10_INTERNATIONAL_REC','Duplicate_List'=>13305];
        $FileCodeNewArray[13303]['CLASSICPAYG-INTERNATIONALSIM'] = ['Datasource'=>'O2_P2PADDITIONAL_SUB10','List'=>13303,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_SUB10_REC','Duplicate_List'=>13305];


        $NotEqualArray = ['A001114916','A001114999'];
        $ArrayCountUpdate = [];

        $ReportID = DataReport::insertGetId(['date'=>date('Y-m-d')]);

        $DataSourceArray = [];
        $Count_International_Classic = [];
        $Count_International_Classic['Classic'] = 0;
        $Count_International_Classic['International'] = 0;

        if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
            $sftp = ssh2_sftp($connection);
            $sftp_fd = intval($sftp);
            $files = scandir("ssh2.sftp://" . $sftp_fd . $ServerDirectory, SCANDIR_SORT_DESCENDING);

            $Count = 0;
            foreach ($files as $value) {
//                if (strpos($value, date('Ymd')) !== false) {
                $FileSourceCode = get_file_break($value);
                if (in_array($FileSourceCode, array_keys($FileCodeArray))) {
                if (strpos($value, $dateFileGet) !== false) {
                    $content = file_get_contents("ssh2.sftp://" . $sftp_fd . $ServerDirectory . $value);
                    $fp = fopen($LocalDirectory . $value, "w");
                    fwrite($fp, $content);
                    fclose($fp);
                    $filename = pathinfo($value, PATHINFO_FILENAME);
                    $filenameEXT = pathinfo($value, PATHINFO_EXTENSION);
                    if($filenameEXT  != 'mfst'){
                       $filename = str_replace('.dat.gz.gpg','',$value);
                       $filenameEXT = 'dat.gz.gpg';
                    }
                    $FileImportLog = new FileImportLog();
                    $FileImportLog->original_filename = $value;
                    $FileImportLog->filename = $filename;
                    $FileImportLog->type = 'O2UNICA';
                    $FileImportLog->file_extension = $filenameEXT;
                    $FileImportLog->total = 0;
                    $FileImportLog->success = 0;
                    $FileImportLog->failed = 0;
                    if($FileImportLog->save()){
                        $newFile[$FileImportLog->id]['name'] = $filename.'.csv';
                        $newFile[$FileImportLog->id]['type'] = $filenameEXT;
                    }
                    /* END */
//                    break;
                }
                }
            }
        }else{
            die('Not Connected!!');
        }


        if(empty($newFile) && count($newFile) == 0){
            $mail_data = array();
            $mail_data['to'] = ['automation@usethegeeks.zendesk.com', 'Nicola.Sharrock@intelling.co.uk', 'apanwar@usethegeeks.co.uk'];
//            $mail_data['to'] = ['apanwar@usethegeeks.co.uk'];
            $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
            $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
            $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.automation.P2PAdditionalAlert';
            $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
            $mail_data['subject'] = 'P2P Additional - 1330 - No File Alert';

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

        shell_exec('/home/file_conv_O2UNICA.sh');
//
        $newFile = FileImportLog::where('type','O2UNICA')->where('file_extension','dat.gz.gpg')->where('created_at','>=',date('Y-m-d').' 00:00:00')->get();

        foreach ($newFile as $key=>$val) {
////            if($val['type'] == 'mfst'){
////                $File = $LocalImportDirectory.'mfst/'.$val['name'];
////            }else{
                $File = $LocalImportDirectory.'OUT/'. $val->filename.'.csv';
////            }
            $FileName = explode('_',$val->filename);
            if(empty($FileName[8])){
               continue;
            }
            $FileCode = $FileName[8];
               $ArrayCountUpdate[$FileCode]['Useable'] = 0 ;
               $ArrayCountUpdate[$FileCode]['NonUseable'] = 0 ;
               $ArrayCountUpdate[$FileCode]['Loaded'] = 0 ;
               $ArrayCountUpdate[$FileCode]['Recycled'] = 0 ;
            if(empty($FileCodeArray[$FileCode])){
               continue;
            }
            $CodeArray = $FileCodeArray[$FileCode];
            $FileCodeNewArrayResult = $FileCodeNewArray[$CodeArray['List']];

            $arrResult = array();
            $handle = fopen($File, "r");
            $Count = 1;
            if (empty($handle) === false) {
                if($val['type'] == 'mfst'){
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $arrResult[] = $data;
                        \App\Model\UTGAPI\FileImportLog::where('original_filename',$data[0])->update(['mfst_response' => serialize($data),'mfst_total'=>$data[1]]);
                    }
                }else{
                    $ArrayCount = [];
                    $ArrayCount['failed'] = 0;
                    $ArrayCount['success'] = 0;
                    $ArrayCount['duplicate'] = 0;
                    $ArrayCount['total'] = 0;
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                        $ArrayCount['total']++;
                        if(empty($data[35])){
                            $datasource = $FileCodeNewArrayResult['CLASSICPAYG-INTERNATIONALSIM']['Datasource'];
                            $ListID = $CodeArray['List'];

                            $DuplicateListID = $FileCodeNewArrayResult['CLASSICPAYG-INTERNATIONALSIM']['Duplicate_List'];
                            $DuplicateDatasource = $FileCodeNewArrayResult['CLASSICPAYG-INTERNATIONALSIM']['Duplicate_Datasource'];
                        }else{
                            if($data[35] == 'CLASSICPAYG'){
                                $datasource = $FileCodeNewArrayResult['CLASSICPAYG']['Datasource'];
                                $ListID = $CodeArray['List'];

                                $DuplicateListID = $FileCodeNewArrayResult['CLASSICPAYG']['Duplicate_List'];
                                $DuplicateDatasource = $FileCodeNewArrayResult['CLASSICPAYG']['Duplicate_Datasource'];
                            }elseif($data[35] == 'INTERNATIONALSIM'){
                                $datasource = $FileCodeNewArrayResult['INTERNATIONALSIM']['Datasource'];
                                $ListID = $CodeArray['List'];

                                $DuplicateListID = $FileCodeNewArrayResult['INTERNATIONALSIM']['Duplicate_List'];
                                $DuplicateDatasource = $FileCodeNewArrayResult['INTERNATIONALSIM']['Duplicate_Datasource'];
                            }else{
                                $datasource = $FileCodeNewArrayResult['CLASSICPAYG-INTERNATIONALSIM']['Datasource'];
                                $ListID = $CodeArray['List'];

                                $DuplicateListID = $FileCodeNewArrayResult['CLASSICPAYG-INTERNATIONALSIM']['Duplicate_List'];
                                $DuplicateDatasource = $FileCodeNewArrayResult['CLASSICPAYG-INTERNATIONALSIM']['Duplicate_Datasource'];
                            }

                        }
                        $PhoneNumber = get_phone_numbers($data[0], '0');

                        $DialerExist = DB::connection('OmniDialer')->table('list')->where('phone_number',$PhoneNumber)->where('list_id',$ListID)->count();
                        $duplicateStatus = 'no';
//                        $datasource = $CodeArray['Datasource'];
//                        $listID = $CodeArray['List'];
                        $listID = $testListID;
                        if($DialerExist > 0){
                            $duplicateStatus = 'yes';
                            $datasource = $DuplicateDatasource;
//                            $listID = $CodeArray['Duplicate_List'];
                            $listID = $DuplicateListID;
                            $ListID = $DuplicateListID;
                            $ArrayCountUpdate[$FileCode]['Recycled']++;
                            $DataSourceArray[$CodeArray['Duplicate_Datasource']] = $CodeArray['Duplicate_Datasource'];
                        }else{
                             $DataExistListArchive = DB::connection('OmniDialer')
                                                    ->table('list_archive')
                                                    ->where('list_id', $ListID)
                                                    ->where('phone_number',$PhoneNumber)
                                                    ->count();

                                if($DataExistListArchive > 0){
                                    $duplicateStatus = 'yes';
                                    $datasource = $DuplicateDatasource;
                                    $listID = $DuplicateListID;
                                    $ListID = $DuplicateListID;
                                    $ArrayCountUpdate[$FileCode]['Recycled']++;
                                    $DataSourceArray[$CodeArray['Duplicate_Datasource']] = $CodeArray['Duplicate_Datasource'];

                                }else{
                                     $DataSourceArray[$CodeArray['Datasource']] = $CodeArray['Datasource'];
                                     $ArrayCountUpdate[$FileCode]['Loaded']++;
                                }


                        }
                    $O2UNICA = new DataListing();
                    $O2UNICA->file_import_log_id = $val->id;
                    $O2UNICA->phone_number = $PhoneNumber;
                    $O2UNICA->Cust_Num = @$data[1];
                    $O2UNICA->Cust_ID = @$data[2];
                    $O2UNICA->Acct_ID = @$data[3];
                    $O2UNICA->Subscr_ID = @$data[4];
                    $O2UNICA->Campaign_Code = @$data[5];
                    $O2UNICA->Cell_Code = @$data[6];
                    $O2UNICA->Channel = @$data[7];
                    $O2UNICA->Treatment_Code = @$data[8];
                    $O2UNICA->Email_Address = @$data[9];
                    $O2UNICA->Column_K = @$data[10];
                    $O2UNICA->Column_L = @$data[11];
                    $O2UNICA->Title = @$data[12];
                    $O2UNICA->Firstname = @$data[13];
                    $O2UNICA->Surname = @$data[14];
                    $O2UNICA->Tenure = @$data[15];
                    $O2UNICA->Average_Monthly_Spend = @$data[16];
                    $O2UNICA->Tarriff_Type = @$data[17];
                    $O2UNICA->Existing_Handset = @$data[18];
                    $O2UNICA->Column_T = @$data[19];
                    $O2UNICA->Average_TopUp = @$data[20];
                    $O2UNICA->Month1_TopUp = @$data[21];
                    $O2UNICA->Month2_TopUp = @$data[22];
                    $O2UNICA->Month3_TopUp = @$data[23];
                    $O2UNICA->Average_Minutes = @$data[24];
                    $O2UNICA->Average_SMS = @$data[25];
                    $O2UNICA->Average_Data_Consumption = @$data[26];
                    $O2UNICA->Smartphone_Flag = @$data[27];
                    $O2UNICA->Big_Bundles_Flag = @$data[28];
                    $O2UNICA->Active_Inactive = @$data[29];
                    $O2UNICA->P2P_Model_Score = @$data[30];
                    $O2UNICA->Transact_Score = @$data[31];
                    $O2UNICA->Early_Life_Churn_Model_Score = @$data[32];
                    $O2UNICA->In_Life_Churn_Model_Score = @$data[33];
                    $O2UNICA->Smartphone_Score = @$data[34];
                    $O2UNICA->Current_Tariff = @$data[35];
                    $O2UNICA->Column_AK = @$data[36];
                    $O2UNICA->Column_AL = @$data[37];
                    $O2UNICA->Column_AM = @$data[38];
                    $O2UNICA->Column_AN = @$data[39];
                    $O2UNICA->Column_AO = @$data[40];
                    $O2UNICA->Column_AP = @$data[41];
                    $O2UNICA->Column_AQ = @$data[42];
                    $O2UNICA->Column_AR = @$data[43];
                    $O2UNICA->Column_AS = @$data[44];
                    $O2UNICA->Column_AT = @$data[45];
                    $O2UNICA->Column_AU = @$data[46];
                    $O2UNICA->Column_TENURE = @$data[47];
                    $O2UNICA->Column_AVG_SPEND = @$data[48];
                    $O2UNICA->Column_LAST_MONTH_TOP_UP = @$data[49];
                    $O2UNICA->Column_AVG_TOP_UP_3M = @$data[50];
                    $O2UNICA->Column_AVG_MIN_3M = @$data[51];
                    $O2UNICA->Column_AVG_SMS_3M = @$data[51];
                    $O2UNICA->Column_AVG_DATA_3M = @$data[53];
                    $O2UNICA->Column_CURRENT_TARRIFF = @$data[54];
//                    $O2UNICA->Column_TENURE = 'TENURE';
//                    $O2UNICA->Column_AVG_SPEND = 'AVG_SPEND';
//                    $O2UNICA->Column_LAST_MONTH_TOP_UP = 'LAST_MONTH_TOP_UP';
//                    $O2UNICA->Column_AVG_TOP_UP_3M = 'AVG_TOP_UP_3M';
//                    $O2UNICA->Column_AVG_MIN_3M = 'AVG_MIN_3M';
//                    $O2UNICA->Column_AVG_SMS_3M = 'AVG_SMS_3M';
//                    $O2UNICA->Column_AVG_DATA_3M = 'AVG_DATA_3M';
//                    $O2UNICA->Column_CURRENT_TARRIFF = 'CURRENT_TARRIFF';
                    $O2UNICA->Column_DATASOURCE = $datasource;
                    $O2UNICA->duplicate_status = $duplicateStatus;
                    $O2UNICA->list_id = $ListID;
                    $O2UNICA->data_report_id = $ReportID;
                    if ($O2UNICA->save()) {
                    $ArrayCountUpdate[$FileCode]['Useable']++;

//                    if(in_array($FileCode,['A001114912','A001114910'])){
//                        $Count_International_Classic['Classic']++;
//                    }elseif(in_array($FileCode,['A001114914','A001114935'])){
//                        $Count_International_Classic['International']++;
//                    }else{
//
//                    }
                    if($data[35] == 'CLASSICPAYG'){
                        $Count_International_Classic['Classic']++;
                    }elseif($data[35] == 'INTERNATIONALSIM'){
                        $Count_International_Classic['International']++;
                    }else{

                    }


                    if(in_array(@$data[6],$ArrayCodeProcess)){
                        $ImportID = $O2UNICA->id;
                        $postData[$ImportID]['import_id'] = $ImportID;
                        $postData[$ImportID]['data_list'] = $O2UNICA->list_id;
                        $postData[$ImportID]['main_phone'] = get_phone_numbers($O2UNICA->phone_number, 0);
                        $postData[$ImportID]['title'] = @$O2UNICA->Title;
                        $postData[$ImportID]['first_name'] = @$O2UNICA->Firstname;
                        $postData[$ImportID]['last_name'] = @$O2UNICA->Surname;
                        $postData[$ImportID]['source_code'] = @$O2UNICA->Acct_ID;
                        $postData[$ImportID]['email'] = @$O2UNICA->Email_Address;
                        $postData[$ImportID]['source'] = @$O2UNICA->Column_DATASOURCE;

                        $CustomArray = [];
                        $CustomArray['CustNum'] = @$O2UNICA->Cust_ID;
                        $CustomArray['O2ClientID'] = @$O2UNICA->Cust_ID;
                        $CustomArray['O2SubscriptionID'] = @$O2UNICA->Subscr_ID;
                        $CustomArray['O2CampaignCode'] = @$O2UNICA->Campaign_Code;
                        $CustomArray['O2CellCode'] = @$O2UNICA->Cell_Code;
                        $CustomArray['O2Channel'] = @$O2UNICA->Channel;
                        $CustomArray['O2TreatmentCode'] = @$O2UNICA->Treatment_Code;
                        $CustomArray['O2DeploymentDate'] = NULL;
                        $CustomArray['O2BigBundleFlag'] = @$O2UNICA->Big_Bundles_Flag;
                        $CustomArray['O2P2PScore'] = @$O2UNICA->P2P_Model_Score;
                        $CustomArray['O2TransactScore'] = @$O2UNICA->Transact_Score;
                        $CustomArray['O2EarlyLifeModel'] = @$O2UNICA->Early_Life_Churn_Model_Score;
                        $CustomArray['O2CurrentTariff'] = @$O2UNICA->Current_Tariff;

                        $CustomArray['AboutCust1a'] = 'TENURE';
                        $CustomArray['AboutCust1b'] = (!empty($O2UNICA->Tenure) && $O2UNICA->Tenure) ? number_format(floatval($O2UNICA->Tenure), 2) : $O2UNICA->Tenure;

                        $CustomArray['AboutCust2a'] = 'AVG SPEND';
                        $CustomArray['AboutCust2b'] = (!empty($O2UNICA->Average_Monthly_Spend) && $O2UNICA->Average_Monthly_Spend) ? number_format(floatval($O2UNICA->Average_Monthly_Spend), 2) : $O2UNICA->Average_Monthly_Spend;

                        $CustomArray['AboutCust3a'] = 'LAST MONTH TOP UP';
                        $CustomArray['AboutCust3b'] = (!empty($O2UNICA->Month3_TopUp) && $O2UNICA->Month3_TopUp) ? number_format(floatval($O2UNICA->Month3_TopUp), 2) : $O2UNICA->Month3_TopUp;

                        $CustomArray['AboutCust4a'] = 'AVG TOP UP 3M';
                        $CustomArray['AboutCust4b'] = (!empty($O2UNICA->Average_TopUp) && $O2UNICA->Average_TopUp) ? number_format(floatval($O2UNICA->Average_TopUp), 2) : $O2UNICA->Average_TopUp;

                        $CustomArray['AboutCust5a'] = 'AVG MIN 3M';
                        $CustomArray['AboutCust5b'] = (!empty($O2UNICA->Average_Minutes) && $O2UNICA->Average_Minutes) ? number_format(floatval($O2UNICA->Average_Minutes), 2) : $O2UNICA->Average_Minutes;

                        $CustomArray['AboutCust6a'] = 'AVG SMS 3M';
                        $CustomArray['AboutCust6b'] = (!empty($O2UNICA->Average_SMS) && $O2UNICA->Average_SMS) ? number_format(floatval($O2UNICA->Average_SMS), 2) : $O2UNICA->Average_SMS;

                        $CustomArray['AboutCust7a'] = 'AVG DATA 3M';
                        $CustomArray['AboutCust7b'] = @(!empty($O2UNICA->Average_Data_Consumption) && $O2UNICA->Average_Data_Consumption) ? number_format(floatval($O2UNICA->Average_Data_Consumption), 2) : $O2UNICA->Average_Data_Consumption;

                        $CustomArray['AboutCust8a'] = 'CURRENT TARRIFF';
                        $CustomArray['AboutCust8b'] = @$O2UNICA->Current_Tariff;

                        $postData[$ImportID]['custom_fields'] = $CustomArray;
                    }
                        $ArrayCount['success']++;
                        $Count++;
                    }
                }

                $ArrayUpdate = [];
                $ArrayUpdate[$FileCode.'_useable'] = $ArrayCountUpdate[$FileCode]['Useable'];
                $ArrayUpdate[$FileCode.'_non_useable'] = $ArrayCountUpdate[$FileCode]['NonUseable'];
                $ArrayUpdate[$FileCode.'_loaded'] = $ArrayCountUpdate[$FileCode]['Loaded'];
                $ArrayUpdate[$FileCode.'_recycled'] = $ArrayCountUpdate[$FileCode]['Recycled'];

                DataReport::where('id',$ReportID)->update($ArrayUpdate);
                FileImportLog::where('id',$val->id)->update(['failed'=>$ArrayCount['failed'],'success'=>$ArrayCount['success'],'duplicate'=>$ArrayCount['duplicate'],'total'=>$ArrayCount['total']]);
                }
                fclose($handle);
            }
        }

        DataReport::where('id',$ReportID)->update(['count_of_datasource'=>count($DataSourceArray),'count_of_classic'=>$Count_International_Classic['Classic'],'count_of_international'=>$Count_International_Classic['International']]);


        $postData1 = [];
        $postData1['token'] = $token;
        $postData1['customers'] = $postData;

//        $NewData = get_omni_api_curl_test($user, $pass, $token, $postData1);

//        get_O2UNICA_response_update($NewData);




//        $ReportID
//        die('BYE');
        $ReportStatus = DataReport::find($ReportID);

        $mail_data = array();
        $mail_data['to'] = ['ngupta@usethegeeks.co.uk'];
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.P2P_additional';
//        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['apanwar@usethegeeks.co.uk'];
        $mail_data['subject'] = 'P2P Additional';
        $mail_data['data'] = $ReportStatus;

        $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                    $m->from($mail_data['from'], 'Intelling');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                });

    }

}
