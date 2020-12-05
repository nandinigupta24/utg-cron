<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\UTGAPI\FileImportLog;
use App\Model\UTGAPI\UNICAP2PAddcon;
use DB;
use Mail;

class P2PAddconProcess extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:P2PAddconProcess';

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

        // $TestListID = 9999999992;
        // $user = 'Intelling-OmniChannel';
        // $pass = '2j4VHhYYHqkTnBjJ';
        // $token = '0HeIOWyak5UIdeXKmD4twR7kty030UWXQ4fiU6e6kr7cuPgTm2';
        $dialer = 'OmniDialer';
        $TypeFile = 'P2P-ADDCON-UNICA';

        $postData = [];

        $CampaignID = 3005;

        $CampaignListID = DB::connection($dialer)->table('lists')->where('campaign_id', $CampaignID)->pluck('list_id');

        $FileImportLog = FileImportLog::where('type', $TypeFile)->orderBy('id', 'desc')->first();

        if (!empty($FileImportLog->created_at) && $FileImportLog->created_at) {
            $LastDateUpdated = date('Y-m-d', strtotime($FileImportLog->created_at));
            $END = date('Y-m-d', strtotime('+1 month', strtotime($FileImportLog->created_at)));
            $WorkingDays = get_count_WorkingDays($LastDateUpdated, $END);
        }
        $WorkingDays = 10;

        $FileImportLogID = FileImportLog::where('is_complete', '0')->pluck('id');

        $dataCount = UNICAP2PAddcon::whereIn('file_import_log_id', $FileImportLogID)->count();

        $limit = ceil($dataCount / $WorkingDays);
        if ($limit <= 0) {
            die('Limit does not exist!!');
        }

        $GroupByData = FileImportLog::where('is_complete', '0')
                ->select(DB::RAW('id as file_import_log_id,total'))
                ->get();

        $DataCount = [];
        $DataCount['Loaded'] = 0;
        $DataCount['Recycled'] = 0;

        foreach ($GroupByData as $DataGroupBy) {
            $limit = ceil($DataGroupBy->total / $WorkingDays);

            //For Production
             $dataArray = UNICAP2PAddcon::whereNull('api_response')
                    ->where('file_import_log_id', $DataGroupBy->file_import_log_id)
                    ->limit($limit)
                    ->get();

            //For Testing Purpose
            // $dataArray = UNICAP2PAddcon::whereNull('api_response')
            //         ->where('file_import_log_id', 575)
            //         ->limit(1)
            //         ->get();

            $sucess_count = 0;

            foreach ($dataArray as $arrayGet) {
                $sucess_count++;
                $UNICAP2PAddcon = UNICAP2PAddcon::find($arrayGet->id);
                $PhoneNumber = get_phone_numbers($arrayGet->PLUS44, 0);
                $DataExist = DB::connection($dialer)->table('list')->whereIn('list_id', $CampaignListID)->where('phone_number', $PhoneNumber)->count();

                $DuplicateStatus = 'yes';
                $DataSource = 'O2_ADDCONS_RECYCLED';
                // $ListdID = 300000051; //Development Mode
                $ListdID = 30051; // Production Mode

                if ($DataExist == 0) {
                    $DataExistListArchive = DB::connection($dialer)->table('list_archive')->whereIn('list_id', $CampaignListID)->where('phone_number', $PhoneNumber)->count();

                    if ($DataExistListArchive == 0) {
                        $DuplicateStatus = 'no';
                        $DataSource = 'O2_ADDCONS';
                        // $ListdID = 300000051; //Development Mode
                        $ListdID = 3005; //Product Mode
                        $DataCount['Loaded']++;
                    } else {
                        $DataCount['Recycled']++;
                    }
                } else {
                    $DataCount['Recycled']++;
                }

                $UNICAP2PAddcon->duplicate_status = $DuplicateStatus;
                $UNICAP2PAddcon->datasource = $DataSource;
                $UNICAP2PAddcon->list_id = $ListdID;
                $UNICAP2PAddcon->custom_3 = 'Yes';
                if ($UNICAP2PAddcon->save()) {
                    $ImportID = $arrayGet->id;
                    /* ARRAY FOR POST DATA START */
                    $postData[$ImportID]['import_id'] = $ImportID;

                    $postData[$ImportID]['data_list'] = $ListdID;
                    $postData[$ImportID]['main_phone'] = $PhoneNumber;
                    $postData[$ImportID]['title'] = $UNICAP2PAddcon->Title;
                    $postData[$ImportID]['first_name'] = $UNICAP2PAddcon->Firstname;
                    $postData[$ImportID]['last_name'] = $UNICAP2PAddcon->Surname;
                    $postData[$ImportID]['address1'] = $UNICAP2PAddcon->Address1;
                    $postData[$ImportID]['address2'] = $UNICAP2PAddcon->Address2;
                    $postData[$ImportID]['address3'] = $UNICAP2PAddcon->Address3;
                    $postData[$ImportID]['city'] = $UNICAP2PAddcon->Town;
                    $postData[$ImportID]['province'] = $UNICAP2PAddcon->County;
                    $postData[$ImportID]['postcode'] = $UNICAP2PAddcon->Postcode;
                    $postData[$ImportID]['date_of_birth'] = $UNICAP2PAddcon->DOB;
                    $postData[$ImportID]['source_code'] = $UNICAP2PAddcon->Acct_ID;
                    $postData[$ImportID]['email'] = $UNICAP2PAddcon->Email_Address;
                    $postData[$ImportID]['source'] = $DataSource;
                    $postData[$ImportID]['security_phrase'] = $UNICAP2PAddcon->Cell_Code;

                    $CustomArray = [];
                    $CustomArray['Cust_Num'] = $UNICAP2PAddcon->Cust_Num;
                    $CustomArray['Cust_ID'] = $UNICAP2PAddcon->Cust_ID;
                    $CustomArray['Subscr_ID'] = $UNICAP2PAddcon->Subscr_ID;
                    $CustomArray['Campaign_Code'] = $UNICAP2PAddcon->Campaign_Code;
                    $CustomArray['Cell_Code'] = $UNICAP2PAddcon->Cell_Code;
                    $CustomArray['Channel'] = $UNICAP2PAddcon->Channel;
                    $CustomArray['Treatment_Code'] = $UNICAP2PAddcon->Treatment_Code;
                    $CustomArray['Propensity'] = $UNICAP2PAddcon->Propensity;
                    $CustomArray['Avg_Data_Usage'] = $UNICAP2PAddcon->Avg_Data_Usage;
                    $CustomArray['Tariff_Name'] = $UNICAP2PAddcon->Tariff_Name;
                    $CustomArray['DCP Customers'] = $UNICAP2PAddcon->DCP_Customers;
                    $CustomArray['Sky/BT Flag'] = $UNICAP2PAddcon->Sky_BT_Flag;
                    $CustomArray['Customers monthly spend'] = $UNICAP2PAddcon->Customers_Monthly_Spend;
                    $CustomArray['Customers In Arrears'] = $UNICAP2PAddcon->Customers_In_Arrears;
                    $CustomArray['Number of previous Transaction applications in last 12 months '] = $UNICAP2PAddcon->Number_Of_Previous_Transaction;
                    $CustomArray['GDPR Bundle 1'] = $UNICAP2PAddcon->GDPR_Bundle_1;
                    $CustomArray['GDPR Bundle 2'] = $UNICAP2PAddcon->GDPR_Bundle_2;
                    $CustomArray['GDPR Bundle 3'] = $UNICAP2PAddcon->GDPR_Bundle_3;
                    $CustomArray['Source / Partner ID '] = $UNICAP2PAddcon->Source_Partner;
                    $CustomArray['Device Name '] = $UNICAP2PAddcon->Device_Name;
                    $CustomArray['Customers Affluence'] = $UNICAP2PAddcon->Customers_Affluence;
                    $CustomArray['12 Month SIMO Customers'] = $UNICAP2PAddcon->Month_SIMO_Customers;
                    $CustomArray['Like New Model Decile'] = $UNICAP2PAddcon->Like_New_Model_Decile;
                    $CustomArray['Smartphone Flag'] = $UNICAP2PAddcon->Smartphone_Flag;
                    $CustomArray['Orbis'] = $UNICAP2PAddcon->orbis_id;
                    $CustomArray['Activation date'] = $UNICAP2PAddcon->Activation_Date;
                    $CustomArray['Contract End Date'] = $UNICAP2PAddcon->custom_1;
                    $CustomArray['SMS Opt In'] = $UNICAP2PAddcon->SMS_OPT_IN;
                    $CustomArray['Voucher code'] = $UNICAP2PAddcon->SMS_OPT_IN;
                    $CustomArray['Voucher expiry date'] = $UNICAP2PAddcon->SMS_OPT_IN;
                    $CustomArray['Voucher value'] = $UNICAP2PAddcon->SMS_OPT_IN;
                    $CustomArray['Buyout flag'] = $UNICAP2PAddcon->SMS_OPT_IN;
                    $CustomArray['Recommended handset name 1'] = $UNICAP2PAddcon->SMS_OPT_IN;
                    $CustomArray['Recommended handset name 2'] = $UNICAP2PAddcon->SMS_OPT_IN;
                    $CustomArray['Recommended handset name 3'] = $UNICAP2PAddcon->SMS_OPT_IN;
                    $CustomArray['Recommended tariff'] = $UNICAP2PAddcon->SMS_OPT_IN;
                    $CustomArray['Add Conns Voice Model Score'] = $UNICAP2PAddcon->SMS_OPT_IN;
                    $CustomArray['Add Conns Tablet Model Score'] = $UNICAP2PAddcon->SMS_OPT_IN;
                    $CustomArray['Experien Family Model Score'] = $UNICAP2PAddcon->SMS_OPT_IN;
                    $CustomArray['Soft Opt In Flag'] = $UNICAP2PAddcon->SMS_OPT_IN;
                    $CustomArray['Operating system'] = $UNICAP2PAddcon->SMS_OPT_IN;
                    $CustomArray['Recycle: Trade-in Value'] = $UNICAP2PAddcon->SMS_OPT_IN;
                    $CustomArray['Phishing Email (Consumer/SMB)'] = $UNICAP2PAddcon->SMS_OPT_IN;
                    $CustomArray['Progressive SMS'] = $UNICAP2PAddcon->SMS_OPT_IN;
                    $CustomArray['Progressive Email'] = $UNICAP2PAddcon->SMS_OPT_IN;
                    $CustomArray['Progressive Outbound Call'] = $UNICAP2PAddcon->SMS_OPT_IN;
                    $CustomArray['Progressive DM'] = $UNICAP2PAddcon->SMS_OPT_IN;

                    $postData[$ImportID]['custom_fields'] = $CustomArray;
                }
            }

            //Remove comment while production
           $File_import_log = FileImportLog::find($DataGroupBy->file_import_log_id);
            if($File_import_log->total_transfer + $sucess_count >= $File_import_log->total) {
                $File_import_log->is_complete = 1;
            }
            $File_import_log->total_transfer = $File_import_log->total_transfer + $sucess_count;
            $File_import_log->save();
        }
        $response = get_OMNI_api_LeadPOST($postData);

        // dd($response);


        //Remove comment while production
        get_unicaP2PAddcon_response($response);

        /* SEND MAIL */
        $mail_data = array();
        $mail_data['to'] = ['dialerteam@usethegeeks.zendesk.com', 'Nicola.Sharrock@intelling.co.uk', 'apanwar@usethegeeks.co.uk', 'Sarah.Berry@intelling.co.uk', 'ssharma@usethegeeks.co.uk'];
//        $mail_data['to'] = ['apanwar@usethegeeks.co.uk'];
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.automation.p2p_addcon_data';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
        $mail_data['subject'] = 'Automation - P2P Addcon - 3005';
        $mail_data['data'] = $DataCount;

        $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                    $m->from($mail_data['from'], 'Intelling');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                });

//            echo $MonthDate .' - '.$Limit.PHP_EOL;
//        }
    }

}
