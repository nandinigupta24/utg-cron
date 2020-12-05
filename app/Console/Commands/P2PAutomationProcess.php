<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\UTGAPI\FileImportLog;
use App\Model\UTGAPI\UNICAP2PCore;
use DB;
use Mail;

class P2PAutomationProcess extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:P2PAutomationProcess';

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

        $TestListID = 99999991;
        $user = 'Intelling-OmniChannel';
        $pass = '2j4VHhYYHqkTnBjJ';
        $token = '0HeIOWyak5UIdeXKmD4twR7kty030UWXQ4fiU6e6kr7cuPgTm2';

        $postData = [];

        $newFile = FileImportLog::where('type', 'P2P-CORE-UNICA')
                ->where('file_extension', 'dat.gz.gpg')
                ->orderBy('id', 'DESC')
                ->first();
        $FileID = $newFile->id;
        $start = date('Y-m-d', strtotime($newFile->created_at));

        $CampaignID = 3001;

        $CampaignListID = DB::connection('OmniDialer')->table('lists')->where('campaign_id', $CampaignID)->pluck('list_id');

        $Year = date('Y', strtotime('+1 month', strtotime($start)));
        $Month = date('F', strtotime('+1 month', strtotime($start)));
        $end = date('Y-m-d', strtotime('first thursday of ' . strtolower($Month) . ' ' . $Year));
        $start = date('Y-m-d', strtotime('+1 day', strtotime($start)));
        $days = get_count_WorkingDays($start, $end);
        $totalCount = UNICAP2PCore::where('file_import_log_id', $FileID)->count();
        $Limit = round(($totalCount / $days));

        $dataArray = UNICAP2PCore::where('file_import_log_id', $FileID)->whereNULL('custom')->limit($Limit)->get();

        $DataCount = [];
        $DataCount['Loaded'] = 0;
        $DataCount['Recycled'] = 0;
        foreach ($dataArray as $arrayGet) {
            $UNICAP2PCore = UNICAP2PCore::find($arrayGet->id);

            $DataExist = DB::connection('OmniDialer')->table('list')->whereIn('list_id', $CampaignListID)->where('phone_number', $arrayGet->plus44)->count();

            $DuplicateStatus = 'yes';
            $DataSource = 'O2_PRETOPOST_RECYCLED';
            $ListdID = 30011;

            if ($DataExist == 0) {
                $DataExistListArchive = DB::connection('OmniDialer')
                        ->table('list_archive')
                        ->whereIn('list_id', $CampaignListID)
                        ->where('phone_number', $arrayGet->plus44)
                        ->count();
                if ($DataExistListArchive == 0) {
                    $DuplicateStatus = 'no';
                    $DataSource = 'O2_PRETOPOST';
                    $ListdID = 3001;
                    $DataCount['Loaded'] ++;
                } else {
                    $DataCount['Recycled'] ++;
                }
            } else {
                $DataCount['Recycled'] ++;
            }

            $UNICAP2PCore->duplicate_status = $DuplicateStatus;
            $UNICAP2PCore->datasource = $DataSource;
            $UNICAP2PCore->list_id = $ListdID;
            $UNICAP2PCore->custom = 'Yes';
            if ($UNICAP2PCore->save()) {
                $ImportID = $arrayGet->id;
                /* ARRAY FOR POST DATA START */
                $postData[$ImportID]['import_id'] = $ImportID;

                $postData[$ImportID]['data_list'] = $UNICAP2PCore->list_id;
                $postData[$ImportID]['main_phone'] = $UNICAP2PCore->plus44;
                $postData[$ImportID]['title'] = $UNICAP2PCore->Cust_Title;
                $postData[$ImportID]['first_name'] = $UNICAP2PCore->First_Name;
                $postData[$ImportID]['last_name'] = $UNICAP2PCore->Surname;
                $postData[$ImportID]['source_code'] = $UNICAP2PCore->df_AccountID;
                $postData[$ImportID]['email'] = $UNICAP2PCore->df_Email;
                $postData[$ImportID]['source'] = $DataSource;

                $CustomArray = [];
                $CustomArray['CustNum'] = $UNICAP2PCore->Cust_Num;
                $CustomArray['O2ClientID'] = $UNICAP2PCore->Cust_Id;
                $CustomArray['O2SubscriptionID'] = $UNICAP2PCore->Subscription_ID;
                $CustomArray['O2CampaignCode'] = (int) $UNICAP2PCore->CampaignCode;
                $CustomArray['O2CellCode'] = $UNICAP2PCore->CellCode;
                $CustomArray['O2Channel'] = $UNICAP2PCore->df_Channel;
                $CustomArray['O2TreatmentCode'] = (int) $UNICAP2PCore->TreatmentCode;
                $CustomArray['O2BigBundleFlag'] = $UNICAP2PCore->df_BigBundle_Flag;
                $CustomArray['O2P2PScore'] = $UNICAP2PCore->df_p2p_score;
                $CustomArray['O2TransactScore'] = $UNICAP2PCore->df_TransactScore;
                $CustomArray['O2EarlyLifeModel'] = $UNICAP2PCore->df_EarlyLifeModelScore;
                $CustomArray['O2CurrentTariff'] = $UNICAP2PCore->CURRENT_TARRIFF;

                $CustomArray['AboutCust1a'] = 'TENURE';
                $CustomArray['AboutCust1b'] = (!empty($UNICAP2PCore->df_Tenure) && $UNICAP2PCore->df_Tenure) ? number_format(floatval($UNICAP2PCore->df_Tenure), 2) : $UNICAP2PCore->df_Tenure;

                $CustomArray['AboutCust2a'] = 'AVG SPEND';
                $CustomArray['AboutCust2b'] = (!empty($UNICAP2PCore->df_Avg_Spend_3M) && $UNICAP2PCore->df_Avg_Spend_3M) ? number_format(floatval($UNICAP2PCore->df_Avg_Spend_3M), 2) : $UNICAP2PCore->df_Avg_Spend_3M;

                $CustomArray['AboutCust3a'] = 'LAST MONTH TOP UP';
                $CustomArray['AboutCust3b'] = (!empty($UNICAP2PCore->df_Month3_TopUp) && $UNICAP2PCore->df_Month3_TopUp) ? number_format(floatval($UNICAP2PCore->df_Month3_TopUp), 2) : $UNICAP2PCore->df_Month3_TopUp;

                $CustomArray['AboutCust4a'] = 'AVG TOP UP 3M';
                $CustomArray['AboutCust4b'] = (!empty($UNICAP2PCore->df_Avg_TopUp) && $UNICAP2PCore->df_Avg_TopUp) ? number_format(floatval($UNICAP2PCore->df_Avg_TopUp), 2) : $UNICAP2PCore->df_Avg_TopUp;

                $CustomArray['AboutCust5a'] = 'AVG MIN 3M';
                $CustomArray['AboutCust5b'] = (!empty($UNICAP2PCore->Avg_Mins_Last3M) && $UNICAP2PCore->Avg_Mins_Last3M) ? number_format(floatval($UNICAP2PCore->Avg_Mins_Last3M), 2) : $UNICAP2PCore->Avg_Mins_Last3M;

                $CustomArray['AboutCust6a'] = 'AVG SMS 3M';
                $CustomArray['AboutCust6b'] = (!empty($UNICAP2PCore->Avg_SMS_Last3M) && $UNICAP2PCore->Avg_SMS_Last3M) ? number_format(floatval($UNICAP2PCore->Avg_SMS_Last3M), 2) : $UNICAP2PCore->Avg_SMS_Last3M;

                $CustomArray['AboutCust7a'] = 'AVG DATA 3M';
                $CustomArray['AboutCust7b'] = (!empty($UNICAP2PCore->Avg_Data_Last3M) && $UNICAP2PCore->Avg_Data_Last3M) ? number_format(floatval($UNICAP2PCore->Avg_Data_Last3M), 2) : $UNICAP2PCore->Avg_Data_Last3M;

                $CustomArray['AboutCust8a'] = 'CURRENT TARRIFF';
                $CustomArray['AboutCust8b'] = $UNICAP2PCore->df_Tariff;

                $postData[$ImportID]['custom_fields'] = $CustomArray;
            }
        }

        if (count($postData) == 0) {
            die('You dont have more data to post!!');
        }

        /* Data POST ON API */
        $postData1 = [];
        $postData1['token'] = $token;
        $postData1['customers'] = $postData;

        $NewData = get_omni_api_curl_test($user, $pass, $token, $postData1);

        get_unicaP2PCore_response($NewData);

        /* SEND MAIL */
        $mail_data = array();
        $mail_data['to'] = ['automation@usethegeeks.zendesk.com', 'Nicola.Sharrock@intelling.co.uk', 'developers@usethegeeks.co.uk', 'Sarah.Berry@intelling.co.uk','ssharma@usethegeeks.co.uk'];
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.automation.p2p_core_data';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
        $mail_data['subject'] = 'P2P CORE - 3001 - Automation';
        $mail_data['data'] = $DataCount;

        $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                    $m->from($mail_data['from'], 'Intelling');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                });

        DB::table('UNICA_post_results')->insert(['type' => 'P2P-CORE-UNICA', 'api_response' => json_encode($NewData), 'count' => $Limit]);
    }

}
