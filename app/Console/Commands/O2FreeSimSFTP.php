<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;
use App\Model\Intelling\O2Data;
use App\Model\Intelling\NicolaFileLog;
use App\Model\Intelling\NicolaDialerData;

class O2FreeSimSFTP extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:O2FreeSimSFTP';

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
        $directory = '/home/Ankit/Dialer_data/O2/';
        $O2DataFileLogsArray = NicolaFileLog::pluck('file_name')->toArray();
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
                    $fp = fopen("/var/www/html/cron/storage/Automation/DialerData/" . $value, "w");
                    fwrite($fp, $content);
                    fclose($fp);
                    $O2DataFileLogs = new NicolaFileLog();
                    $O2DataFileLogs->file_name = $value;
                    $O2DataFileLogs->total = 0;
                    $O2DataFileLogs->success = 0;
                    $O2DataFileLogs->fail = 0;
//                    $O2DataFileLogs->date = get_break_O2FreeSim($value);
                    if ($O2DataFileLogs->save()) {
                        $newFile[$O2DataFileLogs->id] = $value;
                    }
                    if (count($newFile) == 10) {
                        break;
                    }
                    /* END */
//                    break;
//                }
            }
        }
       $array = [];
        $array['phone'] = ['plus44','contact_no','contactno','contact_no','contact1','number','contact_1','telephone1','home_phone','homephone','work_phone','workphone','mobilephone','mobile_phone','column_11','column11','0mobile','phone', 'mobile','recphone', 'mobile_number', 'phone_number', 'mpn', 'tel', 'telephone','telephone1', 'telephone_number', 'phonenumber', 'telephonenumber', 'mobilenumber'];
        $array['title'] = ['nametitle','title','initial','salutation'];
        $array['firstname'] = ['first_name','firstname','name','f_name','fname','full_name','fullname','forename','recfirstname','customer_name','first'];
        $array['lastname'] = ['last_name','lastname','l_name','recsurname','last'];
        $array['address1'] = ['address','address1','address_1','add1','add_1','street_1','street1','recaddress','addr1']; 
        $array['address2'] = ['address2','address_2','add2','add_2','street_2','street2','recaddress2','addr2'];
        $array['address3'] = ['address3','address_3','add3','add_3','addr3'];
        $array['city'] = ['town','rectown','addr6'];
        $array['county'] = ['country','country_name','country_code','c_name','reccounty','addr7q'];
        $array['postcode'] = ['postcode','postal_code','post_code','recpostcode','bpost_code','pcode'];
        $array['email'] = ['email','email_address','mail_address','emails','recemail','email_address'];
        $array['source'] = ['datasource','source','data_source','datasourcename','datasourcename'];
        $array['dob'] = ['dateofbirth','dob'];
//        $array['SupplierCode'] = ['supplier','suppliercode','supplier_code'];
//        $array['LeadType'] = ['leadtype'];
//        $array['broadband_provider'] = ['broadband','provider','networkprovider','bbp_provider','network','current_bb_provider'];
//        $array['gas_provider'] = ['gas_provider','gasprovider','gassupplier','gas_supplier','current_energy_provider'];
//        $array['electricity_provider'] = ['electricity_provider','electricityprovider','electricity_supplier','electricitysupplier'];
        $array['reference'] = ['unique_id','uniqueid','id','lbm_diallerid','reference','recid','leadid','lead_id','vendor'];
//        $array['company'] = ['company'];
//        $array['Current_Provider'] = ['current_provider','currentprovider'];
//        $array['Previous_Provider'] = ['previous_provider','previousprovider'];
//        $array['ContractType'] = ['contracttype'];
        $array['sitename'] = ['sitename'];
        $array['gender'] = ['gender'];
        $array['age'] = ['age'];
//        $array['ContactLoadID'] = ['contactloadid'];
//        $array['Cust_Num'] = ['cust_num'];
//        $array['Cust_ID'] = ['cust_id'];
//        $array['Acct_ID'] = ['acc_id'];
//        $array['Subscr_ID'] = ['subscr_id'];
//        $array['Campaign_Code'] = ['campaign_code'];
//        $array['Cell_Code'] = ['cell_code'];
//        $array['Channel'] = ['channel'];
//        $array['Treatment_Code'] = ['treatment_code'];
        $array['timestamp'] = ['timestamp'];
        $array['prize'] = ['prize'];
        $arrayPhone = $array['phone'];
//        $arrayPhone = ['phone', 'mobile','mobile_number','phone_number', 'mpn','tel', 'telephone', 'telephone_number','phonenumber','telephonenumber','mobilenumber'];
        foreach ($newFile as $key => $val) {
            $reader = Excel::load('/var/www/html/cron/storage/Automation/DialerData/' . $val)->get();
            $newData = $reader->toArray();
            $headerRow = $reader->first()->keys()->toArray();
           /*Dynamic Columns START*/
            $FieldsColumn = array_keys($array);
         
            $result = [];

            foreach($FieldsColumn as $columnFields){
                $result[$columnFields] = array_values(array_intersect($headerRow,$array[$columnFields]));
            }
            /*END*/
            NicolaFileLog::where('id', $key)->update(['file_header' => serialize($headerRow)]);
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
                NicolaFileLog::where('id', $key)->update(['total' => DB::raw('total+1')]);

                 if (empty($value[$PhoneColumn])) {
                    NicolaFileLog::where('id', $key)->update(['fail' => DB::raw('fail+1')]);
                    continue;
                }
                if (strlen($value[$PhoneColumn]) >= 10) {
                    
                }else{
                    NicolaFileLog::where('id', $key)->update(['fail' => DB::raw('fail+1')]);
                    continue;
                }
                $CountExist = O2Data::where('telephone',$value[$PhoneColumn])->count();
                if ($CountExist > 0) {
                    NicolaFileLog::where('id', $key)->update(['fail' => DB::raw('fail+1')]);
                    continue;
                }
                $O2Data = new NicolaDialerData();
                $O2Data->file_log_id = $key;
//                $O2Data->campaign = $val;
//                $O2Data->campaign = 'P2PPreOCT';
                foreach($FieldsColumn as $columnFields){
                    $O2Data->$columnFields = @get_empty(@$value[@$result[$columnFields][0]],'');
                }
                if($O2Data->save()){
                NicolaFileLog::where('id', $key)->update(['success' => DB::raw('success+1')]);
                }
            }
            echo PHP_EOL.'Finished -- '.$val;
        }
        
        echo PHP_EOL.'BYE!!';
        echo PHP_EOL;
    }

}
