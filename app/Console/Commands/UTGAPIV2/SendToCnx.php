<?php

namespace App\Console\Commands\UTGAPIV2;

use Illuminate\Console\Command;
use App\Model\UTGAPI\FileImportData;
use App\Model\UTGAPI\FileImportLog;
use DB;
use DateTime;
use Mail;

class SendToCnx extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'utgapiv2:sendtocnx {--id=} {--filetype=} {--date=} {--code=} {--debug=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send data to cnx api for debug add --debug=1 for testing mode
                              Optional params --id=1 for particular file --filetype=P2P-SMARTPHONE-UNICA , --date=2020-01-01 for file date --code=A******
                              option for file types: P2P-SMARTPHONE-UNICA, P2P-CHURN-UNICA, P2P-CORE-UNICA, P2P-ADDCON-UNICA, O2UNICA';

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
        $id = $this->option('id');
        $filetype = $this->option('filetype');
        $code = $this->option('code');
        $debug = $this->option('debug');
        $date = $this->option('date');

        $files = FileImportLog::orderBy('id', 'desc');
        if($id && is_numeric($id)) {
          $files->where('id', $id);
        }else {
          $files->where('is_transfer', 1);
        }
        if($filetype) {
          $files->where('type', $filetype);
        }
        if($date) {
          $dateFileGet =  date('Ymd', strtotime($date));
          $files->where('filename', 'like', "%$dateFileGet%");
        } else {
          $log_addcon_file = FileImportLog::selectRaw('*, SUBSTR(filename, 9, 8) AS DateOrder');
          if($filetype) {
            $log_addcon_file->where('type', $filetype);
          }
          $log_addcon_file = $log_addcon_file->orderBy('DateOrder','desc')->first();
          $files->where('filename', 'like', "%$log_addcon_file->DateOrder%");
        }
        if($code) {
          $files->where('filename', 'like', "%$code%");
        }
        $dataFiles = $files->get();
        if($dataFiles){
          $loopFilesType =  [
              'P2P-SMARTPHONE-UNICA' => '400000010',
              'P2P-CHURN-UNICA' => '999999999',
              'P2P-CORE-UNICA' => '9999999',
              'P2P-ADDCON-UNICA' => '300000051',
              'O2UNICA' => '9999999'
          ];
        $fileUpdatedValue = [];
          foreach($dataFiles as $logFile){
              $datas = FileImportData::where('list_id','!=', '0')->where('is_transfered', '=', '0')->where('file_type', $logFile->type)->where('import_file_id', $logFile->id)->take($logFile->daily_limit)->get(); //$logFile->daily_limit
              utgapilog($logFile->type."Data Sending Start - ".$logFile->id.' with limit -'.$logFile->daily_limit);
              if($datas){
                  if($logFile->type=='P2P-SMARTPHONE-UNICA') {
                    $fileUpdatedValue[$logFile->id] = $this->smartPhoneUnica($debug, $loopFilesType, $logFile, $datas);
                  }
                  if($logFile->type=='O2UNICA') {
                    $fileUpdatedValue[$logFile->id] = $this->O2Unica($debug, $loopFilesType, $logFile, $datas);
                  }
                  if($logFile->type=='P2P-ADDCON-UNICA') {
                    $fileUpdatedValue[$logFile->id] = $this->P2PAddconUnica($debug, $loopFilesType, $logFile, $datas);
                  }
                  if($logFile->type=='P2P-CHURN-UNICA') {
                    $fileUpdatedValue[$logFile->id] = $this->P2PChurnUnica($debug, $loopFilesType, $logFile, $datas);
                  }
                  if($logFile->type=='P2P-CORE-UNICA') {
                    $fileUpdatedValue[$logFile->id] = $this->P2PCoreUnica($debug, $loopFilesType, $logFile, $datas);
                  }
              }
          }
          if(!$debug) {
            $this->connexMail($fileUpdatedValue, $logFile);
          }
      }else{
          utgapilog("No Data Send");
      }
    }

    protected function connexMail($fileUpdatedValue, $logFile) {
      $helpers = mailTemplateHelperConnex();
      $data['file'] = $logFile;
      $data['fileData'] = $fileUpdatedValue;
      $mail_data = array();
      $mail_data['to'] = ['ngupta@usethegeeks.co.uk', 'dialerteam@usethegeeks.zendesk.com', 'Nicola.Sharrock@intelling.co.uk', 'Sarah.Berry@intelling.co.uk', 'ssharma@usethegeeks.co.uk'];
      //$mail_data['to'] = ['ngupta@usethegeeks.co.uk'];
      $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
      $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
      $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.automation.utgapi_email';
      $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
      $mail_data['subject'] = $helpers[$logFile->type]['subject'];
      $mail_data['data'] = $data;

      $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                  $m->from($mail_data['from'], 'Intelling');
                  if (!empty($mail_data['cc'])) {
                      $m->cc($mail_data['cc']);
                  }
                  $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                  $m->to($mail_data['to'])->subject($mail_data['subject']);
              });
    }

    protected function connexFailMail($data) {
      $mail_data = array();
      $mail_data['to'] = ['ngupta@usethegeeks.co.uk', 'akumar@usethegeeks.com'];
      //$mail_data['to'] = ['ngupta@usethegeeks.co.uk'];
      $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
      $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
      $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.automation.utgapi_email_fail';
      $mail_data['subject'] = 'Data sent to connex is failed';
      $mail_data['data'] = $data;

      $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                  $m->from($mail_data['from'], 'Intelling');
                  if (!empty($mail_data['cc'])) {
                      $m->cc($mail_data['cc']);
                  }
                  $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                  $m->to($mail_data['to'])->subject($mail_data['subject']);
              });
    }

    protected function smartPhoneUnica($debug, $loopFilesType, $logFile, $datas) {
      $postData = [];
      $debugListId =  $debug ? $loopFilesType[$logFile->type] : false;
      foreach($datas as $row){
          $data = json_decode($row->data, true);

          $postData[$row->id]['import_id'] = $row->id;
          $postData[$row->id]['data_list'] = $debugListId ? :$row->list_id;
          $postData[$row->id]['source'] = $row->datasource;
          $postData[$row->id]['main_phone'] = get_phone_numbers($data[0], '0');
          $postData[$row->id]['source_code'] = $data[3];
          $postData[$row->id]['email'] = $data[9];
          $postData[$row->id]['title'] = $data[10];
          $postData[$row->id]['first_name'] = $data[11];
          $postData[$row->id]['last_name'] = $data[13];
          $postData[$row->id]['address1'] = $data[14];
          $postData[$row->id]['address2'] = $data[15];
          $postData[$row->id]['address3'] = $data[16];
          $postData[$row->id]['city'] = $data[17];
          $postData[$row->id]['postcode'] = $data[18];

          $CustomArray = [];
          $CustomArray['CustNum'] = $data[1];
          $CustomArray['O2ClientID'] = $data[2];
          $CustomArray['O2SubscriptionID'] = $data[4];
          $CustomArray['O2CampaignCode'] = $data[5];
          $CustomArray['O2CellCode'] = $data[6];
          $CustomArray['O2Channel'] = $data[7];
          $CustomArray['O2TreatmentCode'] = $data[8];
          $CustomArray['Tenure with O2'] = $data[19];
          $CustomArray['Avg 3 month spend'] = $data[20];
          $CustomArray['Smartphone Flag'] = $data[21];
          $CustomArray['Feature to smartphone decile'] = $data[22];
          $CustomArray['smartphone to smartphone decile'] = $data[23];
          $CustomArray['Phishing Email (Consumer/SMB)'] = $data[25];
          $CustomArray['Automated Campaign Deployment Date'] = $data[26];
          $CustomArray['Progressive SMS'] = $data[27];

          $postData[$row->id]['custom_fields'] = $CustomArray;
          if(!$debug) {
            $row->is_transfered =1;
            $row->save();
          }
      }
      $response = get_OMNI_api_LeadPOST($postData);
      utgapilog($logFile->type."API Response - ".json_encode($response));
      if(!$debug) {
        return get_connex_response($response);
      }
    }

    protected function O2Unica($debug, $loopFilesType, $logFile, $datas) {
      $postData = [];
      $debugListId =  $debug ? $loopFilesType[$logFile->type] : false;
      foreach($datas as $row){
          $data = json_decode($row->data, true);
          $ImportID = $row->id;
          $postData[$ImportID]['import_id'] = $row->id;
          $postData[$ImportID]['data_list'] = $debugListId ? :$row->list_id;
          $postData[$ImportID]['main_phone'] = get_phone_numbers($data[0], 0);
          $postData[$ImportID]['title'] = $data[12];
          $postData[$ImportID]['first_name'] = $data[13];
          $postData[$ImportID]['last_name'] = $data[14];
          $postData[$ImportID]['source_code'] = $data[3];
          $postData[$ImportID]['email'] = $data[9];
          $postData[$ImportID]['source'] = $row->datasource;
          $postData[$ImportID]['security_phrase'] = (!empty($data[20]) && $data[20]) ? number_format(floatval($data[20]), 2) : $data[20];

          $CustomArray = [];
          $CustomArray['CustNum'] = $data[1];
          $CustomArray['O2ClientID'] = $data[2];
          $CustomArray['O2SubscriptionID'] = $data[4];
          $CustomArray['O2CampaignCode'] = $data[5];
          $CustomArray['O2CellCode'] = $data[6];
          $CustomArray['O2Channel'] = $data[7];
          $CustomArray['O2TreatmentCode'] = $data[8];
          $CustomArray['O2DeploymentDate'] = NULL;
          $CustomArray['O2BigBundleFlag'] = $data[28];
          $CustomArray['O2P2PScore'] = $data[30];
          $CustomArray['O2TransactScore'] = $data[31];
          $CustomArray['O2EarlyLifeModel'] = $data[32];
          $CustomArray['O2CurrentTariff'] = $data[35];

          $CustomArray['AboutCust1a'] = 'TENURE';
          $CustomArray['AboutCust1b'] = (!empty($data[47]) && $data[47]) ? number_format(floatval($data[47]), 2) : $data[47];

          $CustomArray['AboutCust2a'] = 'AVG SPEND';
          $CustomArray['AboutCust2b'] = (!empty($data[16]) && $data[16]) ? number_format(floatval($data[16]), 2) : $data[16];

          $CustomArray['AboutCust3a'] = 'LAST MONTH TOP UP';
          $CustomArray['AboutCust3b'] = (!empty($data[23]) && $data[23]) ? number_format(floatval($data[23]), 2) : $data[23];

          $CustomArray['AboutCust4a'] = 'AVG TOP UP 3M';
          $CustomArray['AboutCust4b'] = (!empty($data[20]) && $data[20]) ? number_format(floatval($data[20]), 2) : $data[20];

          $CustomArray['AboutCust5a'] = 'AVG MIN 3M';
          $CustomArray['AboutCust5b'] = (!empty($data[24]) && $data[24]) ? number_format(floatval($data[24]), 2) : $data[24];

          $CustomArray['AboutCust6a'] = 'AVG SMS 3M';
          $CustomArray['AboutCust6b'] = (!empty($data[25]) && $data[25]) ? number_format(floatval($data[25]), 2) : $data[25];

          $CustomArray['AboutCust7a'] = 'AVG DATA 3M';
          $CustomArray['AboutCust7b'] = (!empty($data[26]) && $data[26]) ? number_format(floatval($data[26]), 2) : $data[26];

          $CustomArray['AboutCust8a'] = 'CURRENT TARRIFF';
          $CustomArray['AboutCust8b'] = $data[35];

          $postData[$ImportID]['custom_fields'] = $CustomArray;
          if(!$debug) {
            $row->is_transfered =1;
            $row->save();
          }
          //dd($postData);
      }
      $response = get_OMNI_api_LeadPOST($postData);
      utgapilog($logFile->type."API Response - ".json_encode($response));
      if(!$debug) {
        return get_connex_response($response);
      }
    }

    protected function P2PAddconUnica($debug, $loopFilesType, $logFile, $datas) {
      $postData = [];
      $debugListId =  $debug ? $loopFilesType[$logFile->type] : false;
      foreach($datas as $row){
        $data = json_decode($row->data, true);
        $count = 0;
        $ImportID = $row->id;
        $postData[$ImportID]['import_id'] = $row->id;
        $postData[$ImportID]['data_list'] = $debugListId ? :$row->list_id;
        $postData[$ImportID]['main_phone'] = get_phone_numbers($data[0], 0);
        if(!$this->validateDate($data[33])){
          $count++;
          for($i = 34; $i < count($data); $i++) {
            if(!$this->validateDate($data[$i])) {
              $count++;
            } else{
              break;
            }
          }
          $address = '';
          for($i = 13; $i <= (13+$count); $i++){
            $address .= $data[$i].' ';
          }
          $postData[$ImportID]['address1'] = $address;
          $postData[$ImportID]['address2'] = '';
          $postData[$ImportID]['address3'] = '';
        } else {
          $postData[$ImportID]['address1'] = $data[13];
          $postData[$ImportID]['address2'] = $data[14];
          $postData[$ImportID]['address3'] = $data[15];
        }
        $postData[$ImportID]['title'] = $data[10];
        $postData[$ImportID]['first_name'] = $data[11];
        $postData[$ImportID]['last_name'] = $data[12];
        $postData[$ImportID]['city'] = $data[16+$count];
        $postData[$ImportID]['province'] = $data[17+$count];
        $postData[$ImportID]['postcode'] = $data[18+$count];
        $postData[$ImportID]['date_of_birth'] = date('Y-m-d',strtotime($data[19+$count]));
        $postData[$ImportID]['source_code'] = $data[3];
        $postData[$ImportID]['email'] = $data[9];
        $postData[$ImportID]['source'] = $row->datasource;
        $postData[$ImportID]['security_phrase'] = $data[6];

        $CustomArray = [];
        $CustomArray['Cust_Num'] = $data[1];
        $CustomArray['Cust_ID'] = $data[2];
        $CustomArray['Subscr_ID'] = $data[4];
        $CustomArray['Campaign_Code'] = $data[5];
        $CustomArray['Cell_Code'] = $data[6];
        $CustomArray['Channel'] = $data[7];
        $CustomArray['Treatment_Code'] = $data[8];
        $CustomArray['Propensity'] = '';
        $CustomArray['Avg_Data_Usage'] = isset($data[35+$count]) ? $data[35+$count] : '';
        $CustomArray['Tariff_Name'] = isset($data[36+$count]) ? $data[36+$count] : '';
        $CustomArray['DCP Customers'] = isset($data[37+$count]) ? $data[37+$count] : '';
        $CustomArray['Sky/BT Flag'] = isset($data[38+$count]) ? $data[38+$count] : '';
        $CustomArray['Customers monthly spend'] = isset($data[39+$count]) ? $data[39+$count] : '';
        $CustomArray['Customers In Arrears'] = isset($data[40+$count]) ? $data[40+$count] : '';
        $CustomArray['Number of previous Transaction applications in last 12 months '] = isset($data[41+$count]) ? $data[41+$count] : '';
        $CustomArray['GDPR Bundle 1'] = isset($data[42+$count]) ? $data[42+$count] : '';
        $CustomArray['GDPR Bundle 2'] = isset($data[43+$count]) ? $data[43+$count] : '';
        $CustomArray['GDPR Bundle 3'] = isset($data[44+$count]) ? $data[44+$count] : '';
        $CustomArray['Source / Partner ID '] = isset($data[47+$count]) ? $data[47+$count] : '';
        $CustomArray['Device Name '] = isset($data[48+$count]) ? $data[48+$count] : '';
        $CustomArray['Customers Affluence'] = $data[31+$count];
        $CustomArray['12 Month SIMO Customers'] = isset($data[51+$count]) ? $data[51+$count] : '';
        $CustomArray['Like New Model Decile'] = $data[32+$count];
        $CustomArray['Smartphone Flag'] = isset($data[53+$count]) ? $data[53+$count] : '';
        $CustomArray['Orbis'] = isset($data[52+$count]) ? $data[52+$count] : '';
        $CustomArray['Activation date'] = date('dmY',strtotime($data[33+$count]));
        $CustomArray['Contract End Date'] = date('Y-m-d',strtotime($data[34+$count]));
        $CustomArray['SMS Opt In'] = isset($data[46+$count]) ? $data[46+$count] : '';
        $CustomArray['Voucher code'] = isset($data[20+$count]) ? $data[20+$count] : '';
        $CustomArray['Voucher expiry date'] = isset($data[21+$count]) ? $data[21+$count] : '';
        $CustomArray['Voucher value'] = isset($data[22+$count]) ? $data[22+$count] : '';
        $CustomArray['Buyout flag'] = isset($data[23+$count]) ? $data[23+$count] : '';
        $CustomArray['Recommended handset name 1'] = isset($data[24+$count]) ? $data[24+$count] : '';
        $CustomArray['Recommended handset name 2'] = isset($data[25+$count]) ? $data[25+$count] : '';
        $CustomArray['Recommended handset name 3'] = isset($data[26+$count]) ? $data[26+$count] : '';
        $CustomArray['Recommended tariff'] = isset($data[27+$count]) ? $data[27+$count] : '';
        $CustomArray['Add Conns Voice Model Score'] = isset($data[28+$count]) ? $data[28+$count] : '';
        $CustomArray['Add Conns Tablet Model Score'] = isset($data[29+$count]) ? $data[29+$count] : '';
        $CustomArray['Experien Family Model Score'] = isset($data[30+$count]) ? $data[30+$count] : '';
        $CustomArray['Soft Opt In Flag'] = isset($data[45+$count]) ? $data[45+$count] : '';
        $CustomArray['Operating system'] = isset($data[49+$count]) ? $data[49+$count] : '';
        $CustomArray['Recycle: Trade-in Value'] = isset($data[50+$count]) ? $data[50+$count] : '';
        $CustomArray['Phishing Email (Consumer/SMB)'] = isset($data[54+$count]) ? $data[54+$count] : '';
        $CustomArray['Progressive SMS'] = isset($data[56+$count]) ? $data[56+$count] : '';
        $CustomArray['Progressive Email'] = isset($data[57+$count]) ? $data[57+$count] : '';
        $CustomArray['Progressive Outbound Call'] = isset($data[58+$count]) ? $data[58+$count] : '';
        $CustomArray['Progressive DM'] = isset($data[59+$count]) ? $data[59+$count] : '';
        $CustomArray['Automated Campaign Deployment Date'] = isset($data[55+$count]) ? $data[55+$count] : '';

        $postData[$ImportID]['custom_fields'] = $CustomArray;

        if(!$debug) {
          $row->is_transfered =1;
          $row->save();
        }
      }

      if(count($postData) > 0) {
        $response = get_OMNI_api_LeadPOST($postData);
        if(is_null($response)){
          $data = [];
          $data['filetype'] = $logFile->type;
          $data['filename'] = $logFile->filename;
          $data['fileid'] = $logFile->id;
          $this->connexFailMail($data);
        }
        utgapilog($logFile->type."API Response - ".json_encode($response));
        if(!$debug) {
          return get_connex_response($response);
        }
      } else {
        utgapilog($logFile->type."No data for API");
      }

    }

    protected function P2PChurnUnica($debug, $loopFilesType, $logFile, $datas) {
      $postData = [];
      $debugListId =  $debug ? $loopFilesType[$logFile->type] : false;
      foreach($datas as $row){
        $data = json_decode($row->data, true);
        $ImportID = $row->id;
        $postData[$ImportID]['import_id'] = $row->id;
        $postData[$ImportID]['data_list'] = $debugListId ? :$row->list_id;
        $postData[$ImportID]['main_phone'] = get_phone_numbers($data[0], 0);
        $postData[$ImportID]['title'] = $data[10];
        $postData[$ImportID]['first_name'] = $data[11];
        $postData[$ImportID]['last_name'] = $data[12];
        $postData[$ImportID]['source_code'] = $data[3];
        $postData[$ImportID]['email'] = $data[9];
        $postData[$ImportID]['source'] = $row->datasource;

        $CustomArray = [];
        $CustomArray['Tenure'] = number_format(floatval($data[13]), 2);
        $CustomArray['Average monthly spend over last 3 months'] = number_format(floatval($data[14]),2);
        $CustomArray['Month 1 Spend'] = $data[15];
        $CustomArray['Month 2 Spend'] = (int) $data[16];
        $CustomArray['GDPR Bundle'] = $data[17];
        $CustomArray['DCP flag'] = $data[18];
        $CustomArray['Average Top up'] = (int) $data[19];
        $CustomArray['Month 1 Top Up'] = $data[20];
        $CustomArray['Month 2 Top Up'] = $data[21];
        $CustomArray['Month 3 Top Up'] = $data[22];
        $CustomArray['Average minutes over last 3 months'] = number_format(floatval($data[23]),2);
        $CustomArray['Average SMS over last 3 months'] = number_format(floatval($data[24]),2);
        $CustomArray['Average Data Consumption over last 3 months'] = $data[25];
        $CustomArray['Smartphone flag'] = $data[26];
        $CustomArray['Big Bundles flag'] = $data[27];
        $CustomArray['Active1Inactive 0'] = $data[28];
        $CustomArray['P2P score'] = $data[29];
        $CustomArray['Transact Score'] = $data[30];
        $CustomArray['Early Life model score'] = $data[31];
        $CustomArray['Smartphone score'] = $data[33];
        $CustomArray['Current tariff'] = $data[34];
        $CustomArray['Phishing Email ConsumerSMB'] = $data[35];
        $CustomArray['Automated Campaign Deployment Date'] = $data[36];
        $CustomArray['Progressive SMS'] = $data[37];
        $CustomArray['Progressive Email'] = $data[38];
        $CustomArray['Progressive Outbound Call'] = $data[39];
        $CustomArray['Progressive DM'] = $data[40];
        $CustomArray['Treatment_Code'] = (int) $data[8];
        $CustomArray['Channel'] = $data[7];
        $CustomArray['Cell_Code'] = $data[6];
        $CustomArray['Campaign_Code'] = (int) $data[5];
        $CustomArray['Subscr_ID'] = $data[4];
        $CustomArray['Acct_ID'] = $data[3];
        $CustomArray['Cust_ID'] = $data[2];
        $CustomArray['Cust_Num'] = $data[1];

        $postData[$ImportID]['custom_fields'] = $CustomArray;

        if(!$debug) {
          $row->is_transfered =1;
          $row->save();
        }
      }
      $response = get_new_dialer_api_LeadPOST($postData);
      utgapilog($logFile->type."API Response - ".json_encode($response));
      if(!$debug) {
        return get_connex_response($response);
      }
    }

    protected function P2PCoreUnica($debug, $loopFilesType, $logFile, $datas) {
      $postData = [];
      $debugListId =  $debug ? $loopFilesType[$logFile->type] : false;
      foreach($datas as $row){
        $data = json_decode($row->data, true);
        $ImportID = $row->id;
        $postData[$ImportID]['import_id'] = $row->id;
        $postData[$ImportID]['data_list'] = $debugListId ? :$row->list_id;
        $postData[$ImportID]['main_phone'] = get_phone_numbers($data[0], 0);
        $postData[$ImportID]['title'] = $data[10];
        $postData[$ImportID]['first_name'] = $data[11];
        $postData[$ImportID]['last_name'] = $data[12];
        $postData[$ImportID]['source_code'] = $data[3];
        $postData[$ImportID]['email'] = $data[9];
        $postData[$ImportID]['source'] = $row->datasource;

        $CustomArray = [];
        $CustomArray['CustNum'] = $data[1];
        $CustomArray['O2ClientID'] = $data[2];
        $CustomArray['O2SubscriptionID'] = $data[4];
        $CustomArray['O2CampaignCode'] = (int) $data[5];
        $CustomArray['O2CellCode'] = $data[6];
        $CustomArray['O2Channel'] = $data[7];
        $CustomArray['O2TreatmentCode'] = (int) $data[8];
        $CustomArray['O2BigBundleFlag'] = $data[26];
        $CustomArray['O2P2PScore'] = $data[28];
        $CustomArray['O2TransactScore'] = $data[29];
        $CustomArray['O2EarlyLifeModel'] = $data[30];
        $CustomArray['O2CurrentTariff'] = $data[41];

        $CustomArray['AboutCust1a'] = 'TENURE';
        $CustomArray['AboutCust1b'] = (!empty($data[13]) && $data[13]) ? number_format(floatval($data[13]), 2) : $data[13];

        $CustomArray['AboutCust2a'] = 'AVG SPEND';
        $CustomArray['AboutCust2b'] = (!empty($data[14]) && $data[14]) ? number_format(floatval($data[14]), 2) : $data[14];

        $CustomArray['AboutCust3a'] = 'LAST MONTH TOP UP';
        $CustomArray['AboutCust3b'] = (!empty($data[21]) && $data[21]) ? number_format(floatval($data[21]), 2) : $data[21];

        $CustomArray['AboutCust4a'] = 'AVG TOP UP 3M';
        $CustomArray['AboutCust4b'] = (!empty($data[18]) && $data[18]) ? number_format(floatval($data[18]), 2) : $data[18];

        $CustomArray['AboutCust5a'] = 'AVG MIN 3M';
        $CustomArray['AboutCust5b'] = (!empty($data[22]) && $data[22]) ? number_format(floatval($data[22]), 2) : $data[22];

        $CustomArray['AboutCust6a'] = 'AVG SMS 3M';
        $CustomArray['AboutCust6b'] = (!empty($data[23]) && $data[23]) ? number_format(floatval($data[23]), 2) : $data[23];

        $CustomArray['AboutCust7a'] = 'AVG DATA 3M';
        $CustomArray['AboutCust7b'] = (!empty($data[24]) && $data[24]) ? number_format(floatval($data[24]), 2) : $data[24];

        $CustomArray['AboutCust8a'] = 'CURRENT TARRIFF';
        $CustomArray['AboutCust8b'] = $data[33];

        $postData[$ImportID]['custom_fields'] = $CustomArray;

        if(!$debug) {
          $row->is_transfered =1;
          $row->save();
        }
      }

      $response = get_OMNI_api_LeadPOST($postData);
      utgapilog($logFile->type."API Response - ".json_encode($response));
      if(!$debug) {
        return get_connex_response($response);
      }
    }

    public function validateDate($date, $format = 'd-m-Y'){
      $d = DateTime::createFromFormat($format, $date);
      return $d && $d->format($format) === $date;
    }
}
