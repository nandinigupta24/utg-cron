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

class O2UNICAProcessOLD extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:O2UNICAProcessOLD';

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
        ini_set('memory_limit','2048M');
        
        $server = '109.234.196.231';
        $serverPort = 22;
        $connection = ssh2_connect($server, $serverPort);
        $serverUser = 'O2UNICA';
        $serverPassword = '569WbxXq';
        $ServerDirectory = '/O2Data/';
        $LocalDirectory = '/var/www/html/cron/storage/Automation/O2UNICA/IN/';
        $LocalImportDirectory = '/var/www/html/cron/storage/Automation/O2UNICA/';
        $newFile = [];
        $dateFileGet = '20190307';
        if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
            $sftp = ssh2_sftp($connection);
            $sftp_fd = intval($sftp);
            $files = scandir("ssh2.sftp://" . $sftp_fd . $ServerDirectory, SCANDIR_SORT_DESCENDING);

            $Count = 0;
            foreach ($files as $value) {
//                if (strpos($value, date('Ymd')) !== false) {
                if (strpos($value, $dateFileGet) !== false) {
                    $content = file_get_contents("ssh2.sftp://" . $sftp_fd . $ServerDirectory . $value);
                    $fp = fopen($LocalDirectory . $value, "w");
                    fwrite($fp, $content);
                    fclose($fp);
                    $filename = pathinfo($value, PATHINFO_FILENAME);
                    $filenameEXT = pathinfo($value, PATHINFO_EXTENSION);
                    if($filenameEXT  != 'mfst'){
                       $filename = str_replace('.dat.gz.gpg','',$value); 
                       $filenameEXT = 'dat.gz.gpg'; 
                    }
                    $FileImportLog = new FileImportLog();
                    $FileImportLog->original_filename = $value;
                    $FileImportLog->filename = $filename;
                    $FileImportLog->type = 'O2UNICA';
                    $FileImportLog->file_extension = $filenameEXT;
                    $FileImportLog->total = 0;
                    $FileImportLog->success = 0;
                    $FileImportLog->failed = 0;
                    if($FileImportLog->save()){
                        $newFile[$FileImportLog->id]['name'] = $filename.'.csv';
                        $newFile[$FileImportLog->id]['type'] = $filenameEXT;
                    }             
                    /* END */
//                    break;
                }
            }
        }
       
        shell_exec('/home/file_conv_O2UNICA.sh');
       
        foreach ($newFile as $key=>$val) {
            if($val['type'] == 'mfst'){
                $File = $LocalImportDirectory.'mfst/'.$val['name'];
                
            }else{
                $File = $LocalImportDirectory.'OUT/'. $val['name'];
            }
            
            $arrResult = array();
            $handle = fopen($File, "r");
            $Count = 1;
            if (empty($handle) === false) {
                if($val['type'] == 'mfst'){
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $arrResult[] = $data;
                        \App\Model\UTGAPI\FileImportLog::where('original_filename',$data[0])->update(['mfst_response' => serialize($data),'mfst_total'=>$data[1]]);
                    }
                }else{
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $arrResult[] = $data;
                    $O2UNICA = new O2UNICA();
                    $O2UNICA->file_import_id = $key;
                    $O2UNICA->contact_No = get_phone_numbers($data[0], '0');
                    $O2UNICA->customer_No = $data[1];
                    $O2UNICA->Customer_Id = $data[2];
                    $O2UNICA->Account_Id = $data[3];
                    $O2UNICA->Subscriber_Id = $data[4];
                    $O2UNICA->Campaign_Code = $data[5];
                    $O2UNICA->Cell_Code = $data[6];
                    $O2UNICA->Channel_Identifier = $data[7];
                    $O2UNICA->Treatment_Code = $data[8];
                    $O2UNICA->Email = $data[9];
                    $O2UNICA->Title = $data[10];
                    $O2UNICA->Forename = $data[11];
                    $O2UNICA->Surname = $data[12];
                    $O2UNICA->Address_Line1 = $data[13];
                    $O2UNICA->Address_Line2 = $data[14];
                    $O2UNICA->Address_Line3 = $data[15];
                    $O2UNICA->Address_Line4 = $data[16];
                    $O2UNICA->Address_Line5 = $data[17];
                    $O2UNICA->Postcode = $data[18];
                    $O2UNICA->Custom_Field_1 = $data[19];
                    $O2UNICA->Custom_Field_2 = $data[20];
                    $O2UNICA->Custom_Field_3 = $data[21];
                    $O2UNICA->Custom_Field_4 = $data[22];
                    $O2UNICA->Custom_Field_5 = $data[23];
                    $O2UNICA->Custom_Field_6 = $data[24];
                    $O2UNICA->Custom_Field_7 = $data[25];
                    $O2UNICA->Custom_Field_8 = $data[26];
                    $O2UNICA->Custom_Field_9 = $data[27];
                    $O2UNICA->Custom_Field_10 = $data[28];
                    $O2UNICA->Custom_Field_11 = $data[29];
                    $O2UNICA->Custom_Field_12 = $data[30];
                    $O2UNICA->Custom_Field_13 = $data[31];
                    $O2UNICA->Custom_Field_14 = $data[32];
                    $O2UNICA->Custom_Field_15 = $data[33];
                    $O2UNICA->Custom_Field_16 = $data[34];
                    $O2UNICA->Custom_Field_17 = $data[35];
                    $O2UNICA->Custom_Field_18 = $data[36];
                    $O2UNICA->Custom_Field_19 = $data[37];
                    $O2UNICA->Custom_Field_20 = $data[38];
                    $O2UNICA->Custom_Field_21 = $data[39];
                    $O2UNICA->Custom_Field_22 = $data[40];
                    $O2UNICA->Custom_Field_23 = $data[41];
                    $O2UNICA->Custom_Field_24 = $data[42];
                    $O2UNICA->Custom_Field_25 = $data[43];
                    $O2UNICA->Custom_Field_26 = $data[44];
                    $O2UNICA->Custom_Field_27 = $data[45];
                    $O2UNICA->Custom_Field_28 = $data[46];
                    $O2UNICA->Custom_Field_29 = $data[47];
                    $O2UNICA->Custom_Field_30 = $data[48];
                    $O2UNICA->Custom_Field_31 = $data[49];
                    $O2UNICA->Custom_Field_32 = $data[50];
                    $O2UNICA->Custom_Field_33 = $data[51];
                    $O2UNICA->Custom_Field_34 = $data[52];
                    $O2UNICA->Custom_Field_35 = $data[53];
                    $O2UNICA->Custom_Field_36 = $data[54];
                    $O2UNICA->Custom_Field_37 = $data[55];
                    $O2UNICA->Custom_Field_38 = $data[56];
                    $O2UNICA->Custom_Field_39 = $data[57];
                    $O2UNICA->Custom_Field_40 = $data[58];
                    $O2UNICA->Custom_Field_41 = $data[59];
                    if ($O2UNICA->save()) {
                        $Count++;
//                        \App\Model\UTGAPI\FileImportLog::where('id', $key)->update([$fieldname => $fieldvalue]);
                        DB::table('file_import_logs')->where('id', $key)->increment('total', 1);
                    }
                }
                }
                fclose($handle);
            }
        }
    }

}
