<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Mail; 
use Excel;     
use Illuminate\Support\Facades\Artisan;
use Log;
use App\Model\Intelling\O2DataFileLogs;
use App\Model\Intelling\O2Data;
use App\Model\Intelling\DynamicStroage;

class O2FreeSimSFTPDynamic extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:O2FreeSimSFTPDynamic';

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

        $server = '10.68.120.59';
        ini_set('memory_limit','2048M');
        $serverPort = 22;
        $connection = ssh2_connect($server, $serverPort);
        $serverUser = 'root';
        $serverPassword = '16IndiaGeeksUK';
        $O2DataFileLogsArray = O2DataFileLogs::pluck('file_name')->toArray();
        $directory = '/home/Ankit/O2PostJuly2015/O2LeadGen/';
        if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
            $sftp = ssh2_sftp($connection);
            $sftp_fd = intval($sftp);
            $files = scandir("ssh2.sftp://" . $sftp_fd . $directory, SCANDIR_SORT_DESCENDING);
            
//            $files = scandir("ssh2.sftp://" . $sftp_fd . "/home/Ankit/O2OldData/O2FreeSIM/", SCANDIR_SORT_DESCENDING);
            $newFile = [];
            $Count = 0;
            foreach ($files as $value) {
                 if (in_array($value, ['.', '..', '...', '....'],true)) {
                    continue;
                }
//                if (strpos($value, 'O2FreeSIM') !== false) {
                    if (in_array($value, $O2DataFileLogsArray,true)) {
                        continue;
                    }
                    $content = file_get_contents("ssh2.sftp://" . $sftp_fd . $directory . $value);
//                    $content = file_get_contents("ssh2.sftp://" . $sftp_fd . "/home/Ankit/O2OldData/O2FreeSIM/" . $value);
                    $fp = fopen("/var/www/html/cron/storage/Automation/O2FreeSimSFTP/" . $value, "w");
                    fwrite($fp, $content);
                    fclose($fp);
                    $O2DataFileLogs = new O2DataFileLogs();
                    $O2DataFileLogs->file_name = $value;
                    $O2DataFileLogs->total = 0;
                    $O2DataFileLogs->success = 0;
                    $O2DataFileLogs->fail = 0;
                    $O2DataFileLogs->chron_setting = 'Dynamic';
//                    $O2DataFileLogs->date = get_break_O2FreeSim($value);
                    if ($O2DataFileLogs->save()) {
                        $newFile[$O2DataFileLogs->id] = $value;
                    }
                    if (count($newFile) == 5) {
                        break;
                    }
                    /* END */
//                    break;
//                }
            }
        }
        
        $array = [];
        $array['telephone'] = ['telno','plus44','contact_no','contactno','contact_no','contact1','number','contact_1','telephone1','home_phone','homephone','work_phone','workphone','mobilephone','mobile_phone','column_11','column11','0mobile','phone', 'mobile','recphone', 'mobile_number', 'phone_number', 'mpn', 'tel', 'telephone','telephone1', 'telephone_number', 'phonenumber', 'telephonenumber', 'mobilenumber','phone_1'];
        $array['Title'] = ['nametitle','title','initial','salutation'];
        $array['Firstname'] = ['first_name','firstname','name','f_name','fname','full_name','fullname','forename','recfirstname','customer_name','first'];
        $array['Surname'] = ['last_name','lastname','l_name','recsurname','last','surname'];
        $array['Address1'] = ['address','address1','address_1','add1','add_1','street_1','street1','recaddress','addr1','ad1']; 
        $array['Address2'] = ['address2','address_2','add2','add_2','street_2','street2','recaddress2','addr2','ad2'];
        $array['Address3'] = ['address3','address_3','add3','add_3','addr3','ad3'];
        $array['Town'] = ['town','rectown','addr6','ad4'];
        $array['County'] = ['country','country_name','country_code','c_name','reccounty','addr7q','ad5'];
        $array['Postcode'] = ['postcode','postal_code','post_code','recpostcode','bpost_code','pcode'];
        $array['email'] = ['email','email_address','mail_address','emails','recemail','email_address'];
        $array['source'] = ['datasource','source','data_source','datasourcename','datasourcename'];
        $array['DOB'] = ['dateofbirth','dob'];
        $array['SupplierCode'] = ['supplier','suppliercode','supplier_code'];
        $array['LeadType'] = ['leadtype'];
        $array['broadband_provider'] = ['broadband','provider','networkprovider','bbp_provider','network','current_bb_provider'];
        $array['gas_provider'] = ['gas_provider','gasprovider','gassupplier','gas_supplier','current_energy_provider'];
        $array['electricity_provider'] = ['electricity_provider','electricityprovider','electricity_supplier','electricitysupplier'];
        $array['Reference'] = ['unique_id','uniqueid','id','lbm_diallerid','reference','recid','leadid','lead_id','vendor'];
        $array['company'] = ['company'];
        $array['Current_Provider'] = ['current_provider','currentprovider'];
        $array['Previous_Provider'] = ['previous_provider','previousprovider'];
        $array['ContractType'] = ['contracttype'];
        $array['sitename'] = ['sitename'];
        $array['gender'] = ['gender'];
        $array['age'] = ['age'];
        $array['ContactLoadID'] = ['contactloadid'];
        $array['Cust_Num'] = ['cust_num'];
        $array['Cust_ID'] = ['cust_id'];
        $array['Acct_ID'] = ['acc_id'];
        $array['Subscr_ID'] = ['subscr_id'];
        $array['Campaign_Code'] = ['campaign_code'];
        $array['Cell_Code'] = ['cell_code'];
        $array['Channel'] = ['channel'];
        $array['Treatment_Code'] = ['treatment_code'];
        $arrayPhone = $array['telephone'];
        
        foreach ($newFile as $key => $val) {
            $reader = Excel::load('/var/www/html/cron/storage/Automation/O2FreeSimSFTP/' . $val)->get();
            $newData = $reader->toArray();
            $headerRow = $reader->first()->keys()->toArray();
            
            /*Dynamic Columns START*/
            $FieldsColumn = array_keys($array);
         
            $result = [];

            foreach($FieldsColumn as $columnFields){
                $result[$columnFields] = array_values(array_intersect($headerRow,$array[$columnFields]));
            }
            /*END*/
            O2DataFileLogs::where('id', $key)->update(['file_header' => serialize($headerRow)]);
            $fail = 0;
            foreach ($arrayPhone as $phoneValue) {
                if (in_array($phoneValue, $headerRow,true)) {
                    $PhoneColumn = $phoneValue;
                    $fail = 0;
                    break;
                }else{
                    $fail = 1;
                }
            }
            
            if($fail == 1){
                O2DataFileLogs::where('id', $key)->update(['error_on_fail' => implode(',',$arrayPhone).' does not matched in header!!']);
                continue;
            }
//            error_on_fail

            if (empty($newData)) {
                continue;
            }
            $CountNewArray = 1;
           
            foreach ($newData as $value) {
                echo PHP_EOL .'-- '.$CountNewArray++.' Record of '.count($newData);
                O2DataFileLogs::where('id', $key)->update(['total' => DB::raw('total+1')]);
                
                if (empty($value[$PhoneColumn])) {
                    O2DataFileLogs::where('id', $key)->update(['fail' => DB::raw('fail+1')]);
                    continue;
                }
                if (strlen($value[$PhoneColumn]) >= 10) {
                    
                }else{
                    O2DataFileLogs::where('id', $key)->update(['fail' => DB::raw('fail+1')]);
                    continue;
                }
                $CountExist = O2Data::where('telephone',$value[$PhoneColumn])->count();
                if ($CountExist > 0) {
                    O2DataFileLogs::where('id', $key)->update(['fail' => DB::raw('fail+1')]);
                    continue;
                }
                $O2Data = new O2Data();
                $O2Data->file_log_id = $key;
//                $O2Data->campaign = $val;
                $O2Data->campaign = 'O2LeadGen';
                foreach($FieldsColumn as $columnFields){
                    $O2Data->$columnFields = @get_empty(@$value[@$result[$columnFields][0]],'');
                }
                if($O2Data->save()){
                O2DataFileLogs::where('id', $key)->update(['success' => DB::raw('success+1')]);
                }
                
            }
            echo PHP_EOL.'Finished -- '.$val;
        }
        
        echo PHP_EOL.'BYE!!';
        echo PHP_EOL;
        
        
        
        
        
//        $reader = Excel::load('/var/www/html/cron/storage/home/SPPrem_20140805.csv')->get();
//        $newData = $reader->toArray();
//        $headerRow = $reader->first()->keys()->toArray();
//        $FieldsColumn = array_keys($array);
//         
//        $result = [];
//        
//        foreach($FieldsColumn as $columnFields){
//            $result[$columnFields] = array_values(array_intersect($headerRow,$array[$columnFields]));
//        }
//       
//        
//        foreach ($newData as $phoneValue) {
//            
//            $dataInsert = [];
//             foreach($FieldsColumn as $columnFields){
//                $dataInsert[$columnFields] = get_empty(@$phoneValue[@$result[$columnFields][0]],NULL);
//            }
//            pr($dataInsert);
//            exit;
////           
//            DynamicStroage::insert($dataInsert);
//             
//        }
    }

}
