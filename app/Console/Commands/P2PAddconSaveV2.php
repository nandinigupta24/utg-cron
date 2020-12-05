<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\UTGAPI\FileImportLog;
use App\Model\UTGAPI\UNICAP2PAddcon;
use DB;
use Illuminate\Support\Str;
use Mail;
use DateTime;

class P2PAddconSaveV2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:P2PAddconSaveV2 {file_log_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automation of P2P Addcon from UNICA Version 2';

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
        $TypeFile = 'P2P-ADDCON-UNICA';
        if($file_log_id = $this->argument('file_log_id')) {
          $newFile = FileImportLog::where('id', $file_log_id)->where('type', $TypeFile)->limit(1)->get();
        }else {
          $newFile = FileImportLog::where('is_transfer', '=', '0')->where('type', $TypeFile)->limit(1)->get();
        }
        $start = time();
        echo 'Start - '.$start;
        if($newFile && count($newFile)>0){
          $LocalImportDirectory = '/var/www/html/cron/storage/Automation/O2UNICA/';
          $dialer = 'OmniDialer';
          $CampaignID = 3005;
          $CampaignListID = DB::connection($dialer)->table('lists')->where('campaign_id', $CampaignID)->pluck('list_id');
          foreach ($newFile as $key => $val) {

              $File = $LocalImportDirectory . 'OUT/' . $val->filename . '.csv';

              if(!file_exists($File)) continue;
              $totalRows = count(file($File));
              $val->total = $totalRows;
              $val->save();


              $handle = fopen($File, "r");
              $lineCount = 0;
              if (empty($handle) === false) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                    if($data[0] == 0){
                        continue;
                    }
                    $lineCount++;
                    $DuplicateStatus  = 'yes';
                    $DataSource       = 'O2_ADDCONS_RECYCLED';
                    $ListdID          = 30051;
                    $count            = 0;

                    $PhoneNumber = get_phone_numbers($data[0], '0');

                    $DataExist = DB::connection($dialer)
                            ->table('list')
                            ->whereIn('list_id', $CampaignListID)
                            ->where('phone_number', $PhoneNumber)
                            ->count();
                    if ($DataExist == 0) {
                        $DataExistListArchive = DB::connection($dialer)->table('list_archive')->whereIn('list_id', $CampaignListID)->where('phone_number',$PhoneNumber)->count();
                        if($DataExistListArchive == 0){
                               $DuplicateStatus = 'no';
                               $DataSource = 'O2_ADDCONS';
                               $ListdID = 3005;
                        }
                    }

                    if(!$this->validateDate($data[33])){
                      $count++;
                      for($i = 34; $i < count($data); $i++) {
                          if($this->validateDate($data[$i])) {
                              break;
                          }
                          $count++;
                      }
                    }
                    $address = '';
                    for($i = 13; $i <= (13+$count); $i++) {
                        $address .= $data[$i].' ';
                    }


                    $UNICAP2PAddcon = new UNICAP2PAddcon();
                    $UNICAP2PAddcon->Propensity                           = '';
                    $UNICAP2PAddcon->file_import_log_id                   = $val->id;
                    $UNICAP2PAddcon->PLUS44                               = $PhoneNumber;
                    $UNICAP2PAddcon->Cust_Num                             = $data[1];
                    $UNICAP2PAddcon->Cust_ID                              = $data[2];
                    $UNICAP2PAddcon->Acct_ID                              = $data[3];
                    $UNICAP2PAddcon->Subscr_ID                            = $data[4];
                    $UNICAP2PAddcon->Campaign_Code                        = $data[5];
                    $UNICAP2PAddcon->Cell_Code                            = $data[6];
                    $UNICAP2PAddcon->Channel                              = $data[7];
                    $UNICAP2PAddcon->Treatment_Code                       = $data[8];
                    $UNICAP2PAddcon->Email_Address                        = $data[9];
                    $UNICAP2PAddcon->Title                                = $data[10];
                    $UNICAP2PAddcon->Firstname                            = $data[11];
                    $UNICAP2PAddcon->Surname                              = $data[12];
                    $UNICAP2PAddcon->Address1                             = $address;
                    $UNICAP2PAddcon->Address2                             = $data[14+$count];
                    $UNICAP2PAddcon->Address3                             = $data[15+$count];
                    $UNICAP2PAddcon->Town                                 = $data[16+$count];
                    $UNICAP2PAddcon->County                               = $data[17+$count];
                    $UNICAP2PAddcon->Postcode                             = $data[18+$count];
                    $UNICAP2PAddcon->DOB                                  = date('Y-m-d',strtotime($data[19+$count]));
                    $UNICAP2PAddcon->Voucher_code                         = $data[20+$count];
                    $UNICAP2PAddcon->Voucher_expiry_date                  = $data[21+$count];
                    $UNICAP2PAddcon->Voucher_value                        = $data[22+$count];
                    $UNICAP2PAddcon->Buyout_flag                          = $data[23+$count];
                    $UNICAP2PAddcon->Recommended_handset_name_1           = $data[24+$count];
                    $UNICAP2PAddcon->Recommended_handset_name_2           = $data[25+$count];
                    $UNICAP2PAddcon->Recommended_handset_name_3           = $data[26+$count];
                    $UNICAP2PAddcon->Recommended_tariff                   = $data[27+$count];
                    $UNICAP2PAddcon->AddConns_Voice_Model_Score           = $data[28+$count];
                    $UNICAP2PAddcon->AddConns_Tablet_Model_Score          = $data[29+$count];
                    $UNICAP2PAddcon->Experien_Family_Model_Score          = $data[30+$count];
                    $UNICAP2PAddcon->Customers_Affluence                  = $data[31+$count];
                    $UNICAP2PAddcon->Like_New_Model_Decile                = $data[32+$count];
                    $UNICAP2PAddcon->Activation_Date                      = date('Y-m-d',strtotime($data[33+$count]));
                    $UNICAP2PAddcon->custom_1                             = date('Y-m-d',strtotime($data[34+$count]));
                    $UNICAP2PAddcon->Avg_Data_Usage                       = isset($data[35+$count]) ? $data[35+$count] : '';
                    $UNICAP2PAddcon->Tariff_Name                          = isset($data[36+$count]) ? $data[36+$count] : '';
                    $UNICAP2PAddcon->DCP_Customers                        = isset($data[37+$count]) ? $data[37+$count] : '';
                    $UNICAP2PAddcon->Sky_BT_Flag                          = isset($data[38+$count]) ? $data[38+$count] : '';
                    $UNICAP2PAddcon->Customers_Monthly_Spend              = isset($data[39+$count]) ? $data[39+$count] : '';
                    $UNICAP2PAddcon->Customers_In_Arrears                 = isset($data[40+$count]) ? $data[40+$count] : '';
                    $UNICAP2PAddcon->Number_Of_Previous_Transaction       = isset($data[41+$count]) ? $data[41+$count] : '';
                    $UNICAP2PAddcon->GDPR_Bundle_1                        = isset($data[42+$count]) ? $data[42+$count] : '';
                    $UNICAP2PAddcon->GDPR_Bundle_2                        = isset($data[43+$count]) ? $data[43+$count] : '';
                    $UNICAP2PAddcon->GDPR_Bundle_3                        = isset($data[44+$count]) ? $data[44+$count] : '';
                    $UNICAP2PAddcon->Soft_Opt_In_Flag                     = isset($data[45+$count]) ? $data[45+$count] : '';
                    $UNICAP2PAddcon->SMS_OPT_IN                           = isset($data[46+$count]) ? $data[46+$count] : '';
                    $UNICAP2PAddcon->Source_Partner                       = isset($data[47+$count]) ? $data[47+$count] : '';
                    $UNICAP2PAddcon->Device_Name                          = isset($data[48+$count]) ? $data[48+$count] : '';
                    $UNICAP2PAddcon->Operating_system                     = isset($data[49+$count]) ? $data[49+$count] : '';
                    $UNICAP2PAddcon->RecycleTrade_in_Value                = isset($data[50+$count]) ? $data[50+$count] : '';
                    $UNICAP2PAddcon->Month_SIMO_Customers                 = isset($data[51+$count]) ? $data[51+$count] : '';
                    $UNICAP2PAddcon->orbis_id                             = isset($data[52+$count]) ? $data[52+$count] : '';
                    $UNICAP2PAddcon->Smartphone_Flag                      = isset($data[53+$count]) ? $data[53+$count] : '';
                    $UNICAP2PAddcon->Phishing_Email                       = isset($data[54+$count]) ? $data[54+$count] : '';
                    $UNICAP2PAddcon->Automated_Campaign_Deployment_Date   = isset($data[55+$count]) ? $data[55+$count] : '';
                    $UNICAP2PAddcon->Progressive_SMS                      = isset($data[56+$count]) ? $data[56+$count] : '';
                    $UNICAP2PAddcon->Progressive_Email                    = isset($data[57+$count]) ? $data[57+$count] : '';
                    $UNICAP2PAddcon->Progressive_OutboundCall             = isset($data[58+$count]) ? $data[58+$count] : '';
                    $UNICAP2PAddcon->Progressive_DM                       = isset($data[59+$count]) ? $data[59+$count] : '';
                    $UNICAP2PAddcon->list_id                              = $ListdID;
                    $UNICAP2PAddcon->datasource                           = $DataSource;
                    $UNICAP2PAddcon->duplicate_status                     = $DuplicateStatus;
                    $UNICAP2PAddcon->custom_2                             = '';
                    if (!$UNICAP2PAddcon->save()) {
                        echo "No able to save data";
                    }
                }
              }

              if($lineCount!=$val->total) {
                $val->total = $lineCount;
              }
              $val->is_transfer=1;
              $val->save();
              // FileImportLog::where('id', $val->id)->update(['total' => $lineCount, 'is_transfer' => '1']);
          }
        }
        echo 'end - '.time();
        echo "Total Seconds ".(time()-$start);
    }
    // Validate Date
    public function validateDate($date, $format = 'd-m-Y'){
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}
