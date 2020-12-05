<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\UTGAPI\FileImportLog;
use App\Model\UTGAPI\UNICAP2PChurn;
use DB;
use Mail;

class P2PChurnProcess extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:P2PChurnProcess';

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

        $TestListID = 99999991;
        $user = 'Intelling-OmniChannel';
        $pass = '2j4VHhYYHqkTnBjJ';
        $token = '0HeIOWyak5UIdeXKmD4twR7kty030UWXQ4fiU6e6kr7cuPgTm2';

        $postData = [];

        $CampaignID = 3045;

        $CampaignListID = DB::connection('MainDialer')->table('lists')->where('campaign_id', $CampaignID)->pluck('list_id');

        $RandomKeyArray = UNICAP2PChurn::orderBy('id','desc')->first();
        $RandomKey = $RandomKeyArray->file_process_count;
        if(empty($RandomKey)){
            die('BYE-----BYE');
        }
        
        $TotalArrayData = UNICAP2PChurn::where('file_process_count',$RandomKey)->count();
        $limit = ceil($TotalArrayData/10);
        
        $dataArray = UNICAP2PChurn::whereNull('api_response')->limit($limit)->get();
        if(!empty($dataArray) && count($dataArray) == 0){
            die('BYE');
        }
        
        $DataCount = [];
        $DataCount['Loaded'] = 0;
        $DataCount['Recycled'] = 0;
        foreach ($dataArray as $arrayGet) {
            $UNICAP2PChurn = UNICAP2PChurn::find($arrayGet->id);

            $DataExist = DB::connection('MainDialer')->table('list')->whereIn('list_id', $CampaignListID)->where('phone_number', $arrayGet->Plus44)->count();

            $DuplicateStatus = 'yes';
            $DataSource = 'O2_P2P_CHURN_RECYCLED';
            $ListdID = 30451;

            if ($DataExist == 0) {
                $DataExistListArchive = DB::connection('MainDialer')
                                        ->table('list_archive')
                                        ->whereIn('list_id', $CampaignListID)
                                        ->where('phone_number',$arrayGet->Plus44)
                                        ->count();
                if($DataExistListArchive == 0){
                    $DuplicateStatus = 'no';
                    $DataSource = 'O2_P2P_CHURN';
                    $ListdID = 3045;
                    $DataCount['Loaded'] ++;
                }else{
                    $DataCount['Recycled'] ++;
                }
                
            } else {
                $DataCount['Recycled'] ++;
            }

            $UNICAP2PChurn->duplicate_status = $DuplicateStatus;
            $UNICAP2PChurn->datasource = $DataSource;
            $UNICAP2PChurn->list_id = $ListdID;
            $UNICAP2PChurn->cutome_1 = 'Yes';
            if ($UNICAP2PChurn->save()) {
                $ImportID = $arrayGet->id;
                /* ARRAY FOR POST DATA START */
                $postData[$ImportID]['import_id'] = $ImportID;

//              $postData[$ImportID]['data_list'] = $UNICAP2PChurn->list_id;
                $postData[$ImportID]['data_list'] = $ListdID;
                $postData[$ImportID]['main_phone'] = $UNICAP2PChurn->Plus44;
                $postData[$ImportID]['title'] = $UNICAP2PChurn->Cust_Title;
                $postData[$ImportID]['first_name'] = $UNICAP2PChurn->First_Name;
                $postData[$ImportID]['last_name'] = $UNICAP2PChurn->Last_Name;
                $postData[$ImportID]['source_code'] = $UNICAP2PChurn->Acct_ID;
                $postData[$ImportID]['email'] = $UNICAP2PChurn->Email_Address;
                $postData[$ImportID]['source'] = $DataSource;

                $CustomArray = [];
                $CustomArray['Tenure'] = number_format(floatval($UNICAP2PChurn->Tenure), 2);
                $CustomArray['Average monthly spend over last 3 months'] = number_format(floatval($UNICAP2PChurn->Average_monthly_spend),2);
                $CustomArray['Month 1 Spend'] = $UNICAP2PChurn->Month_1_Spend;
                $CustomArray['Month 2 Spend'] = (int) $UNICAP2PChurn->Month_2_Spend;
                $CustomArray['GDPR Bundle'] = $UNICAP2PChurn->GDPR_Bundle;
                $CustomArray['DCP flag'] = $UNICAP2PChurn->DCP_flag;
                $CustomArray['Average Top up'] = (int) $UNICAP2PChurn->Average_Top_up;
                $CustomArray['Month 1 Top Up'] = $UNICAP2PChurn->Month_1_Top_Up;
                $CustomArray['Month 2 Top Up'] = $UNICAP2PChurn->Month_2_Top_Up;
                $CustomArray['Month 3 Top Up'] = $UNICAP2PChurn->Month_3_Top_Up;
                $CustomArray['Average minutes over last 3 months'] = number_format(floatval($UNICAP2PChurn->Average_minutes),2);
                $CustomArray['Average SMS over last 3 months'] = number_format(floatval($UNICAP2PChurn->Average_SMS),2);
                $CustomArray['Average Data Consumption over last 3 months'] = $UNICAP2PChurn->Average_Data_Consumption;
                $CustomArray['Smartphone flag'] = $UNICAP2PChurn->Smartphone_flag;
                $CustomArray['Big Bundles flag'] = $UNICAP2PChurn->Big_Bundles_flag;
                $CustomArray['Active1Inactive 0'] = $UNICAP2PChurn->Active;
                $CustomArray['P2P score'] = $UNICAP2PChurn->P2P_score;
                $CustomArray['Transact Score'] = $UNICAP2PChurn->Transact_Score;
                $CustomArray['Early Life model score'] = $UNICAP2PChurn->Early_Life_model_score;
                $CustomArray['Smartphone score'] = $UNICAP2PChurn->Smartphone_score;
                $CustomArray['Current tariff'] = $UNICAP2PChurn->Current_tariff;
                $CustomArray['Phishing Email ConsumerSMB'] = $UNICAP2PChurn->Phishing_Email;
                $CustomArray['Automated Campaign Deployment Date'] = $UNICAP2PChurn->Auto_Camp_Deploy_Date;
                $CustomArray['Progressive SMS'] = $UNICAP2PChurn->Progressive_SMS;
                $CustomArray['Progressive Email'] = $UNICAP2PChurn->Progressive_Email;
                $CustomArray['Progressive Outbound Call'] = $UNICAP2PChurn->Progressive_Outbound_Call;
                $CustomArray['Progressive DM'] = $UNICAP2PChurn->Progressive_DM;
                $CustomArray['Treatment_Code'] = (int) $UNICAP2PChurn->Treatment_Code;
                $CustomArray['Channel'] = $UNICAP2PChurn->Channel;
                $CustomArray['Cell_Code'] = $UNICAP2PChurn->Cell_Code;
                $CustomArray['Campaign_Code'] = (int) $UNICAP2PChurn->Campaign_Code;
                $CustomArray['Subscr_ID'] = $UNICAP2PChurn->Subscr_ID;
                $CustomArray['Acct_ID'] = $UNICAP2PChurn->Acct_ID;
                $CustomArray['Cust_ID'] = $UNICAP2PChurn->Cust_ID;
                $CustomArray['Cust_Num'] = $UNICAP2PChurn->Cust_Num;

                $postData[$ImportID]['custom_fields'] = $CustomArray;
            }
        }

        $response = get_new_dialer_api_LeadPOST($postData);

        get_unicaP2PChurn_response($response);
        
        /* SEND MAIL */
        $mail_data = array();
        $mail_data['to'] = ['automation@usethegeeks.zendesk.com', 'Nicola.Sharrock@intelling.co.uk', 'apanwar@usethegeeks.co.uk', 'Sarah.Berry@intelling.co.uk','ssharma@usethegeeks.co.uk'];
//        $mail_data['to'] = ['apanwar@usethegeeks.co.uk'];
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.automation.p2p_churn_data';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
        $mail_data['subject'] = 'Automation - P2P Churn - 3045';
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
                DB::table('UNICA_post_results')->insert(['type'=>'P2P-CHURN-UNICA','api_response'=>json_encode($response),'count'=>$limit]);
    }

}
