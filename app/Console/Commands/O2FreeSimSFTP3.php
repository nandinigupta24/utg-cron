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
use App\Model\Intelling\O2DataFileLogs;

class O2FreeSimSFTP3 extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:O2FreeSimSFTP3';

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
        if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
            $sftp = ssh2_sftp($connection);
            $sftp_fd = intval($sftp);
            $files = scandir("ssh2.sftp://" . $sftp_fd . "/home/Ankit/O2OldData/OtherCampaign/FinishedSources/Scottish Power/SP ILD Base/", SCANDIR_SORT_DESCENDING);
            
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
                    $content = file_get_contents("ssh2.sftp://" . $sftp_fd . "/home/Ankit/O2OldData/OtherCampaign/FinishedSources/Scottish Power/SP ILD Base/" . $value);
//                    $content = file_get_contents("ssh2.sftp://" . $sftp_fd . "/home/Ankit/O2OldData/O2FreeSIM/" . $value);
                    $fp = fopen("/var/www/html/cron/storage/Automation/O2FreeSimSFTP/" . $value, "w");
                    fwrite($fp, $content);
                    fclose($fp);
                    $O2DataFileLogs = new O2DataFileLogs();
                    $O2DataFileLogs->file_name = $value;
                    $O2DataFileLogs->total = 0;
                    $O2DataFileLogs->success = 0;
                    $O2DataFileLogs->fail = 0;
//                    $O2DataFileLogs->date = get_break_O2FreeSim($value);
                    if ($O2DataFileLogs->save()) {
                        $newFile[$O2DataFileLogs->id] = $value;
                    }
                    if (count($newFile) == 1) {
                        break;
                    }
                    /* END */
//                    break;
//                }
            }
        }
        $arrayPhone = ['phone', 'mobile','mobile_number','phone_number', 'mpn','tel', 'telephone', 'telephone_number','phonenumber','telephonenumber','mobilenumber'];
        foreach ($newFile as $key => $val) {
            $reader = Excel::load('/var/www/html/cron/storage/Automation/O2FreeSimSFTP/' . $val)->get();
            $newData = $reader->toArray();
            $headerRow = $reader->first()->keys()->toArray();
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
                O2DataFileLogs::where('id', $key)->update(['error_on_fail' => 'phone,mobile,phone_number,mpn,telephone,telephone_number does not matched in header!!']);
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
                $CountExist = O2Data::where('telephone',$value[$PhoneColumn])->count();
                if ($CountExist > 0) {
                    O2DataFileLogs::where('id', $key)->update(['fail' => DB::raw('fail+1')]);
                    continue;
                }
                $O2Data = new O2Data();
//                if ($PhoneColumn == 'telephone') {
                    $O2Data->file_log_id = $key;
                    $O2Data->Reference = (!empty($value['urn'])) ? $value['urn'] : '';
                    $O2Data->Title = (!empty($value['title'])) ? $value['title'] : '';
                    $O2Data->Firstname = (!empty($value['firstname'])) ? $value['firstname'] : '';
                    $O2Data->Surname = (!empty($value['lastname'])) ? $value['lastname'] : '';
                    $O2Data->Address1 = (!empty($value['address1'])) ? $value['address1'] : '';
                    $O2Data->Address2 = (!empty($value['address2'])) ? $value['address2'] : '';
                    $O2Data->address3 = (!empty($value['address3'])) ? $value['address3'] : '';
                    $O2Data->Town = (!empty($value['town'])) ? $value['town'] : '';
                    $O2Data->County = (!empty($value['county'])) ? $value['county'] : '';
                    $O2Data->Postcode = (!empty($value['postcode'])) ? $value['postcode'] : '';
                    $O2Data->Telephone = (!empty($value[$PhoneColumn])) ? $value[$PhoneColumn] : '';
                    $O2Data->source = (!empty($value['datasource'])) ? $value['datasource'] : '';
                    $O2Data->DOB = (!empty($value['dob'])) ? $value['dob'] : '';
                    $O2Data->timestamp = (!empty($value['timestamp'])) ? $value['timestamp'] : '';
                    $O2Data->sitename = (!empty($value['sitename'])) ? $value['sitename'] : '';
                    $O2Data->prize = (!empty($value['prize'])) ? $value['prize'] : '';
                    $O2Data->email = (!empty($value['email'])) ? $value['email'] : '';
                    $O2Data->gas_provider = (!empty($value['gas_provider'])) ? $value['gas_provider'] : '';
                    $O2Data->electricity_provider = (!empty($value['electricity_provider'])) ? $value['electricity_provider'] : '';
                    $O2Data->age = (!empty($value['age'])) ? $value['age'] : '';
                    $O2Data->gender = (!empty($value['gender'])) ? $value['gender'] : '';
                    $O2Data->campaign = 'SPILDBASE';
                    
//                    $O2Data->Priority = (!empty($value['priority'])) ? $value['priority'] : '';
//                    $O2Data->DOB = (!empty($value['dob'])) ? $value['dob'] : '';
//                    $O2Data->SupplierCode = (!empty($value['suppliercode'])) ? $value['suppliercode'] : '';
//                    $O2Data->Current_Provider = (!empty($value['current_provider'])) ? $value['current_provider'] : '';
//                    $O2Data->Previous_Provider = (!empty($value['previous_provider'])) ? $value['previous_provider'] : '';
//                    $O2Data->LeadType = (!empty($value['leadtype'])) ? $value['leadtype'] : '';
//                    $O2Data->ContactLoadID = (!empty($value['contactloadid'])) ? $value['contactloadid'] : '';
//                    $O2Data->ContractType = (!empty($value['contracttype'])) ? $value['contracttype'] : '';
//                    $O2Data->SumbissionDate = (!empty($value['sumbissiondate'])) ? $value['sumbissiondate'] : '';
                    $O2Data->save_full_data = serialize($value);
//                } else {
//
//                    $O2Data->file_log_id = $key;
//                    $O2Data->Reference = (!empty($value['type'])) ? $value['type'] : '';
//                    $O2Data->Firstname = (!empty($value['name'])) ? $value['name'] : '';
//                    $O2Data->Address1 = (!empty($value['add1'])) ? $value['add1'] : '';
//                    $O2Data->Address2 = (!empty($value['add2'])) ? $value['add2'] : '';
//                    $O2Data->Telephone = (!empty($value[$PhoneColumn])) ? $value[$PhoneColumn] : '';
//                    $O2Data->save_full_data = serialize($value);
//                }
                if ($O2Data->save()) {
                    O2DataFileLogs::where('id', $key)->update(['success' => DB::raw('success+1')]);
                }
            }
            echo PHP_EOL.'Finished -- '.$val;
        }
        
        echo PHP_EOL.'BYE!!';
        echo PHP_EOL;
    }

}
