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
use App\Model\UTGAPI\O2FreeSimLoadedRecord;
use App\Model\UTGAPI\O2FreeSimFileImport;

class O2FreeSimProcessCPAYG extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:O2FreeSimProcessCPAYG';

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

        $listId = 30031;
//        $listId = 989898;
        $user = 'Intelling-OmniChannel';
        $pass = '2j4VHhYYHqkTnBjJ';
        $token = '0HeIOWyak5UIdeXKmD4twR7kty030UWXQ4fiU6e6kr7cuPgTm2';

        $date = date('Ymd');

        $FileImportLog = O2FreeSimFileImport::orderBy('id','desc')->first();
        $SaveFileName = $FileImportLog->save_file_name;
        $UpdatedID = $FileImportLog->id;
        $FILEname = '/var/www/html/cron/storage/Automation/O2FreeSim/In/' . $SaveFileName;
        $file = fopen($FILEname, "r");
        
        $dataLoaded = [];
        $Count = 0;
        while (($getData = fgetcsv($file, 10000, ",")) !== FALSE) {
            $Count++;
            if($Count == 1){
                continue;
            }
            $array = [];
            $array['NAME'] = $getData[0];
            $array['ADDR1'] = $getData[1];
            $array['ADDR2'] = $getData[2];
            $array['ADDR3'] = $getData[3];
            $array['ADDR4'] = $getData[4];
            $array['ADDR5'] = $getData[5];
            $array['ADDR6'] = $getData[6];
            $array['ADDR7'] = $getData[7];
            $array['MPN'] = $getData[8];
            $array['EMAIL'] = $getData[9];
            $array['TEMPLATE'] = $getData[10];
            $array['SIMTYPE'] = $getData[11];
            $array['OPTIN'] = $getData[12];
            $array['DATE_PROCESSED'] = $getData[13];
            $array['MAILDATE'] = $getData[14];
            $array['PACKDESC'] = iconv( "Windows-1252", "UTF-8",$getData[15]);
            $array['FILECODE'] = $getData[16];
            $array['RDI_MSISDN'] = $getData[17];
            $dataLoaded[] = $array;
            
        }
        
        $phoneNumber = [];
        $newArray = [];
        $totalArray = [];
        $totalArray['Total'] = count($dataLoaded);
        $packdescArray = ['International Bundle 10', 'Classic Pay As You Go'];
        $filecodeArray = ['Leadgen'];
        $TotalArray = [];
        $TotalArray['number_of_records'] = 0;
        $TotalArray['loaded_records'] = 0;
        $TotalArray['duplicate_on_loaded'] = 0;
        $TotalArray['failed'] = 0;
        $CountLoaded = 1;
        $PACKDESCArray = [];
        $FILECODEArray = [];
        foreach ($dataLoaded as $key => $val) {
            
            if(!empty($PACKDESCArray[$val['PACKDESC']])){
                $PACKDESCArray[$val['PACKDESC']] = $PACKDESCArray[$val['PACKDESC']] + 1;
            }else{
                $PACKDESCArray[$val['PACKDESC']] = 1;
            }
            if(!empty($FILECODEArray[$val['FILECODE']])){
                $FILECODEArray[$val['FILECODE']] = $FILECODEArray[$val['FILECODE']] + 1;
            }else{
                $FILECODEArray[$val['FILECODE']] = 1;
            }
            
            $TotalArray['number_of_records'] ++;
            $TotalArray['count_of_PACKDESC'][$val['PACKDESC']] = $val['PACKDESC'];
            $TotalArray['count_of_FILECODE'][$val['FILECODE']] = $val['FILECODE'];
            if (empty($val['MPN'])) {
                $TotalArray['failed'] ++;
                continue;
            }

            if (strpos(strtolower($val['PACKDESC']), 'international') !== false) {
                continue;
            }
//            if (strpos(strtolower($val['PACKDESC']), 'classic') !== false) {
//                continue;
//            }
            if (strpos(strtolower($val['FILECODE']), 'leadgen') !== false) {
                continue;
            }
            if (strpos(strtolower($val['PACKDESC']), 'classic') !== false) {
                if(strpos(strtolower($val['PACKDESC']), 'pay as') !== false) {
                    
                }else{
                    continue;
                }   
            }else{
                continue;
            }
            
            $key = get_phone_numbers($val['MPN'], 0);
            $phoneNumber[] = $key;
            $newArray[$key]['Vendor'] = 'O2_FREESIM-' . date('Ymd') . '-' . date('His') . '-' . $CountLoaded;
            $newArray[$key]['Datasource'] = 'O2_FREESIM_RD';
            $newArray[$key]['name'] = $val['NAME'];
            $newArray[$key]['addr1'] = $val['ADDR1'];
            $newArray[$key]['addr2'] = $val['ADDR2'];
            $newArray[$key]['addr3'] = $val['ADDR3'];
            $newArray[$key]['addr4'] = $val['ADDR4'];
            $newArray[$key]['addr5'] = $val['ADDR5'];
            $newArray[$key]['addr6'] = $val['ADDR6'];
            $newArray[$key]['addr7'] = $val['ADDR7'];
            $newArray[$key]['mpn'] = $key;
            $newArray[$key]['email'] = $val['EMAIL'];
            $newArray[$key]['template'] = $val['TEMPLATE'];
            $newArray[$key]['simtype'] = $val['SIMTYPE'];
            $newArray[$key]['optin'] = $val['OPTIN'];
            $newArray[$key]['date_processed'] = $val['DATE_PROCESSED'];
            $newArray[$key]['maildate'] = $val['MAILDATE'];
            $newArray[$key]['packdesc'] = $val['PACKDESC'];
            $newArray[$key]['filecode'] = $val['FILECODE'];
            $newArray[$key]['rdi_msisdn'] = $val['RDI_MSISDN'];
            $newArray[$key]['DATE PROCESSED'] = 'DATE PROCESSED';
            $newArray[$key]['MAIL DATE'] = 'MAIL DATE';
            $newArray[$key]['CURRENT TARIFF'] = 'CURRENT TARIFF';
            if (!empty($newArray[$key])) {
                $newArray[$key]['Total SIMS Ordered'] = @$newArray[$key]['Total SIMS Ordered'] + 1;
            } else {
                $newArray[$key]['Total SIMS Ordered'] = 1;
            }
            $newArray[$key]['TOTAL SIMS ORDERED'] = 'TOTAL SIMS ORDERED';
            $CountLoaded++;
        }



        $postData = [];
        foreach ($newArray as $key => $value) {
            $O2FreeSimLoadedRecord = new O2FreeSimLoadedRecord();
            $O2FreeSimLoadedRecord->type = 'CPAYG';
            $O2FreeSimLoadedRecord->file_id_import = $UpdatedID;
            $O2FreeSimLoadedRecord->Vendor = $value['Vendor'];
            $O2FreeSimLoadedRecord->Datasource = $value['Datasource'];
            $O2FreeSimLoadedRecord->NAME = $value['name'];
            $O2FreeSimLoadedRecord->ADDR1 = $value['addr1'];
            $O2FreeSimLoadedRecord->ADDR2 = $value['addr2'];
            $O2FreeSimLoadedRecord->ADDR3 = $value['addr3'];
            $O2FreeSimLoadedRecord->ADDR4 = $value['addr4'];
            $O2FreeSimLoadedRecord->ADDR5 = $value['addr5'];
            $O2FreeSimLoadedRecord->ADDR6 = $value['addr6'];
            $O2FreeSimLoadedRecord->ADDR7 = $value['addr7'];
            $O2FreeSimLoadedRecord->MPN = $value['mpn'];
            $O2FreeSimLoadedRecord->EMAIL = $value['email'];
            $O2FreeSimLoadedRecord->TEMPLATE = $value['template'];
            $O2FreeSimLoadedRecord->SIMTYPE = $value['simtype'];
            $O2FreeSimLoadedRecord->OPTIN = $value['optin'];
            $O2FreeSimLoadedRecord->DATE_PROCESSED = $value['date_processed'];
            $O2FreeSimLoadedRecord->MAILDATE = $value['maildate'];
            $O2FreeSimLoadedRecord->PACKDESC = $value['packdesc'];
            $O2FreeSimLoadedRecord->FILECODE = $value['filecode'];
            $O2FreeSimLoadedRecord->RDI_MSISDN = $value['rdi_msisdn'];
            $O2FreeSimLoadedRecord->DATEPROCESSED = $value['DATE PROCESSED'];
            $O2FreeSimLoadedRecord->MAIL_DATE = $value['MAIL DATE'];
            $O2FreeSimLoadedRecord->CURRENT_TARIFF = $value['CURRENT TARIFF'];
            $O2FreeSimLoadedRecord->TOTAL_SIMS_ORDERED = $value['TOTAL SIMS ORDERED'];
            $dataExistDialer = DB::connection('OmniDialer')
                    ->table('list')
                    ->where('list_id', $listId)
                    ->where('phone_number', str_replace('+44', 0, $value['mpn']))
                    ->count();
            if ($dataExistDialer > 0) {
                $TotalArray['duplicate_on_loaded'] ++;
                $O2FreeSimLoadedRecord->dupes_status = 'yes';
                if ($O2FreeSimLoadedRecord->save()) {
                    
                }
                continue;
            }
            $O2FreeSimLoadedRecord->dupes_status = 'no';
            if ($O2FreeSimLoadedRecord->save()) {
                
            }
            $TotalArray['loaded_records'] ++;
            $postData[$key]['import_id'] = $O2FreeSimLoadedRecord->id;
            $postData[$key]['data_list'] = $listId;
            $postData[$key]['main_phone'] = str_replace('+44', 0, $value['mpn']);
            $postData[$key]['first_name'] = @$value['name'];
            $postData[$key]['address1'] = @$value['addr1'];
            $postData[$key]['address2'] = @$value['addr2'];
            $postData[$key]['address3'] = @$value['addr3'];
            $postData[$key]['city'] = @$value['addr3'];
            $postData[$key]['state'] = @$value['addr4'];
            $postData[$key]['province'] = '';
            $postData[$key]['postal_code'] = @$value['addr5'];
            $postData[$key]['source_code'] = 'O2_FREESIM_CLASSIC_RD-' . date('Ymd') . '-' . date('His');
            $postData[$key]['email'] = @$value['email'];
            $postData[$key]['source'] = @$value['Datasource'];
            $postData[$key]['custom_fields'] = ['O2Template' => @$value['template'],
                'O2SIMType' => @$value['simtype'],
                'O2OptInFlag' => @$value['optin'],
                'O2FileCode' => @$value['filecode'],
                'O2RDIMSISDN' => @$value['rdi_msisdn'],
                'AboutCust1a' => @$value['DATE PROCESSED'],
                'AboutCust1b' => @$value['date_processed'],
                'AboutCust2a' => @$value['MAIL DATE'],
                'AboutCust2b' => @$value['maildate'],
                'AboutCust3a' => @$value['CURRENT TARIFF'],
                'AboutCust3b' => @$value['packdesc'],
                'AboutCust4a' => @$value['TOTAL SIMS ORDERED'],
                'AboutCust4b' => @$value['Total SIMS Ordered']
            ];
        }

        
        $postData1 = [];
        $postData1['token'] = $token;
        $postData1['customers'] = $postData;
        
        $NewData = get_omni_api_curl_test($user, $pass, $token, $postData1);
        
        $O2FreeSimFileImport = O2FreeSimFileImport::find($UpdatedID);
        $O2FreeSimFileImport->CPAYG_loaded_records = $TotalArray['loaded_records'];
        $O2FreeSimFileImport->CPAYG_duplicate_on_loaded = $TotalArray['duplicate_on_loaded'];

        if ($O2FreeSimFileImport->save()) {
            get_response_update($NewData);
        }
        $dataEmail = [];
        $dataEmail['number_of_records'] = $TotalArray['number_of_records'];
        $dataEmail['loaded_records'] = $TotalArray['loaded_records'];
        $dataEmail['duplicate_on_loaded'] = $TotalArray['duplicate_on_loaded'];
        $dataEmail['count_of_PACKDESC'] = count($TotalArray['count_of_PACKDESC']);
        $dataEmail['count_of_FILECODE'] = count($TotalArray['count_of_FILECODE']);
        $dataEmail['PACKDESK'] = $PACKDESCArray;
        $dataEmail['FILECODE'] = $FILECODEArray;


//        $arrayMailTo = ['apanwar@usethegeeks.co.uk','Jamie.Taylor@intelling.co.uk','Nicola.Sharrock@intelling.co.uk','diallersupport@intelling.co.uk'];
        $arrayMailTo = ['apanwar@usethegeeks.co.uk','Jamie.Taylor@intelling.co.uk','Nicola.Sharrock@intelling.co.uk',env('DIALER_TEAM_EMAIL')];
//        $arrayMailTo = ['apanwar@usethegeeks.co.uk'];
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.o2_free_sim';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'O2FreeSim Classic Leads';
        $mail_data['data'] = @$dataEmail;

        $result = Mail::send($mail_data['view'], ['data' => $dataEmail], function ($m) use ($mail_data) {
                    $m->from($mail_data['from'], 'API Reports');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'API Reports');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                });
    }

}
