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

class O2UNICADataProcess extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:O2UNICADataProcess';

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
        $postData = [];
        $data = DB::table('O2UNICA_data_reports')->orderBy('id', 'DESC')->first();
        $dateSelection = $data->date;
//        $ArrayCode = ['A001114912', 'A001114914', 'A001114916'];
        $FileImportID = FileImportLog::where('created_at', '>=', $dateSelection . ' 00:00:00')->where('type','O2UNICA')->orderBy('id','desc')->pluck('id');

        $CountResult = DB::table('O2UNICA_data_listings')
                        ->where('created_at', '>=', $dateSelection . ' 00:00:00')
                        ->whereIn('file_import_log_id',$FileImportID)
//                        ->whereIn('Cell_Code', $ArrayCode)
                        ->count();

        $limit = round($CountResult / 5);

        $day = date('l');

        if ($day == 'Friday') {
            $DayCount = 1;
        } elseif ($day == 'Monday') {
            $DayCount = 2;
        } elseif ($day == 'Tuesday') {
            $DayCount = 3;
        } elseif ($day == 'Wednesday') {
            $DayCount = 4;
        } elseif ($day == 'Thursday') {
            $DayCount = 5;
        } elseif ($day == 'Saturday') {
            $DayCount = 6;
        } else {
            die('BYE');
        }

        $Offset = (($DayCount - 1) * $limit);
        $limit = $limit;
        $ResultResponse = DB::table('O2UNICA_data_listings')
                                ->inRandomOrder()
                                ->where('created_at', '>=', $dateSelection . ' 00:00:00')
//                                ->where('created_at', '>=', '2019-12-30 00:00:00')
//                                ->whereIn('Cell_Code', $ArrayCode)
//                ->whereIn('file_import_log_id',$FileImportID)
                                ->whereNull('lead_id')
//                                ->offset($Offset)
                                ->limit($limit)
                                ->get();

        if(!empty($ResultResponse) && count($ResultResponse) == 0){
            die('BYE');
        }

        $DataSourceArray = [];

        foreach ($ResultResponse as $response) {
            if (!empty($DataSourceArray[$response->Column_DATASOURCE])) {
                $DataSourceArray[$response->Column_DATASOURCE] ++;
            } else {
                $DataSourceArray[$response->Column_DATASOURCE] = 1;
            }

            $ImportID = $response->id;
            $postData[$ImportID]['import_id'] = $response->id;
            $postData[$ImportID]['data_list'] = $response->list_id;
            $postData[$ImportID]['main_phone'] = get_phone_numbers($response->phone_number, 0);
            $postData[$ImportID]['title'] = @$response->Title;
            $postData[$ImportID]['first_name'] = @$response->Firstname;
            $postData[$ImportID]['last_name'] = @$response->Surname;
            $postData[$ImportID]['source_code'] = @$response->Acct_ID;
            $postData[$ImportID]['email'] = @$response->Email_Address;
            $postData[$ImportID]['source'] = @$response->Column_DATASOURCE;
            $postData[$ImportID]['security_phrase'] = (!empty($response->Average_TopUp) && $response->Average_TopUp) ? number_format(floatval($response->Average_TopUp), 2) : $response->Average_TopUp;

            $CustomArray = [];
            $CustomArray['CustNum'] = @$response->Cust_ID;
            $CustomArray['O2ClientID'] = @$response->Cust_ID;
            $CustomArray['O2SubscriptionID'] = @$response->Subscr_ID;
            $CustomArray['O2CampaignCode'] = @$response->Campaign_Code;
            $CustomArray['O2CellCode'] = @$response->Cell_Code;
            $CustomArray['O2Channel'] = @$response->Channel;
            $CustomArray['O2TreatmentCode'] = @$response->Treatment_Code;
            $CustomArray['O2DeploymentDate'] = NULL;
            $CustomArray['O2BigBundleFlag'] = @$response->Big_Bundles_Flag;
            $CustomArray['O2P2PScore'] = @$response->P2P_Model_Score;
            $CustomArray['O2TransactScore'] = @$response->Transact_Score;
            $CustomArray['O2EarlyLifeModel'] = @$response->Early_Life_Churn_Model_Score;
            $CustomArray['O2CurrentTariff'] = @$response->Current_Tariff;

            $CustomArray['AboutCust1a'] = 'TENURE';
            $CustomArray['AboutCust1b'] = (!empty($response->Tenure) && $response->Tenure) ? number_format(floatval($response->Tenure), 2) : $response->Tenure;

            $CustomArray['AboutCust2a'] = 'AVG SPEND';
            $CustomArray['AboutCust2b'] = (!empty($response->Average_Monthly_Spend) && $response->Average_Monthly_Spend) ? number_format(floatval($response->Average_Monthly_Spend), 2) : $response->Average_Monthly_Spend;

            $CustomArray['AboutCust3a'] = 'LAST MONTH TOP UP';
            $CustomArray['AboutCust3b'] = (!empty($response->Month3_TopUp) && $response->Month3_TopUp) ? number_format(floatval($response->Month3_TopUp), 2) : $response->Month3_TopUp;

            $CustomArray['AboutCust4a'] = 'AVG TOP UP 3M';
            $CustomArray['AboutCust4b'] = (!empty($response->Average_TopUp) && $response->Average_TopUp) ? number_format(floatval($response->Average_TopUp), 2) : $response->Average_TopUp;

            $CustomArray['AboutCust5a'] = 'AVG MIN 3M';
            $CustomArray['AboutCust5b'] = (!empty($response->Average_Minutes) && $response->Average_Minutes) ? number_format(floatval($response->Average_Minutes), 2) : $response->Average_Minutes;

            $CustomArray['AboutCust6a'] = 'AVG SMS 3M';
            $CustomArray['AboutCust6b'] = (!empty($response->Average_SMS) && $response->Average_SMS) ? number_format(floatval($response->Average_SMS), 2) : $response->Average_SMS;

            $CustomArray['AboutCust7a'] = 'AVG DATA 3M';
            $CustomArray['AboutCust7b'] = @(!empty($response->Average_Data_Consumption) && $response->Average_Data_Consumption) ? number_format(floatval($response->Average_Data_Consumption), 2) : $response->Average_Data_Consumption;

            $CustomArray['AboutCust8a'] = 'CURRENT TARRIFF';
            $CustomArray['AboutCust8b'] = @$response->Current_Tariff;

            $postData[$ImportID]['custom_fields'] = $CustomArray;
        }


        /* API Credentials */
        $user = 'Intelling-OmniChannel';
        $pass = '2j4VHhYYHqkTnBjJ';
        $token = '0HeIOWyak5UIdeXKmD4twR7kty030UWXQ4fiU6e6kr7cuPgTm2';


        $postData1 = [];
        $postData1['token'] = $token;
        $postData1['customers'] = $postData;

        $NewData = get_omni_api_curl_test($user, $pass, $token, $postData1);

        get_O2UNICA_response_update($NewData);

        $mail_data = array();
        $mail_data['to'] = ['ngupta@usethegeeks.co.uk','automation@usethegeeks.zendesk.com', 'Nicola.Sharrock@intelling.co.uk', 'apanwar@usethegeeks.co.uk','ssharma@usethegeeks.co.uk'];
        $mail_data['from'] = 'intellingreports@intelling.co.uk';
        $mail_data['cc'] = 'akumar@usethegeeks.com';
        $mail_data['msg'] = '';
//        $mail_data['view'] = 'emails.P2P_Additional_data';
        $mail_data['view'] = 'emails.automation.p2p_additional_data';
        $mail_data['subject'] = 'P2P Additional';
        $mail_data['data'] = $DataSourceArray;

        $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                    $m->from($mail_data['from'], 'Intelling');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                });

        DB::table('UNICA_post_results')->insert(['type'=>'P2P-CORE-UNICA','api_response'=>json_encode($NewData),'count'=>$limit]);
    }

}
