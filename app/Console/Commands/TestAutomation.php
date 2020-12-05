<?php //

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\UTGAPI\FileImportLog;
use App\Model\UTGAPI\UNICAP2PAddcon;
use DB;
use Illuminate\Support\Str;
use Mail;
use DateTime;

class TestAutomation extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:TestAutomation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automation of P2P Addcon from UNICA';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    public function validateDate($date, $format = 'd-m-Y'){
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        ini_set('max_execution_time', 6000);
        ini_set('memory_limit', '2048M');

        $LocalImportDirectory = '/var/www/html/cron/storage/Automation/O2UNICA/';

        $newFile = DB::table('temp_csv')->where('status', '0')->limit(1)->get();

        echo date('Y-m-d H:i:s');

        //foreach ($newFile as $val) {
            $File = $LocalImportDirectory . 'Temp_Data/' . $newFile[0]->filename . '.csv';
            //if(!file_exists($File)) continue;
            $handle = fopen($File, "r");
            $Count = 0;
            if (empty($handle) === false) {
                while (($line = fgetcsv($handle, 1000, ",")) !== false) {
                    $data = DB::table('UNICA_P2P_Addcon')->where([['Activation_Date', '=', '1970-01-01'], ['created_at', '>=', '2020-08-24'], ['Cust_ID', '=', $line[2]], ['Acct_ID','=',$line[3]]])->count();

                    $line_count = count($line);

                    if($data == 0){
                        continue;
                    }
                        $count = 0;

                        $UNICAP2PAddcon = UNICAP2PAddcon::where([['Activation_Date', '=', '1970-01-01'], ['created_at', '>=', '2020-08-24'], ['Cust_ID', '=', $line[2]], ['Acct_ID','=',$line[3]]])->first()?:new UNICAP2PAddcon();

                        if(!$this->validateDate($line[20])){
                            $count++;
                            for($i = 21; $i < count($line); $i++) {
                                if(!$this->validateDate($line[$i])) {
                                   $count++;
                                } else{
                                break;
                                }
                            }
                            $address = '';
                            $row = '';
                            for($i = 13; $i <= (13+$count); $i++){
                                $row= $i;
                                $address .= $line[$i].' ';
                            }

                            $PhoneNumber = get_phone_numbers($line[0], '0');

                            $UNICAP2PAddcon->file_import_log_id = $newFile[0]->file_import_id;
                            $UNICAP2PAddcon->PLUS44 = $PhoneNumber;
                            $UNICAP2PAddcon->Cust_Num = $line[1];
                            $UNICAP2PAddcon->Cust_ID = $line[2];
                            $UNICAP2PAddcon->Acct_ID = $line[3];
                            $UNICAP2PAddcon->Subscr_ID = $line[4];
                            $UNICAP2PAddcon->Campaign_Code = $line[5];
                            $UNICAP2PAddcon->Cell_Code = $line[6];
                            $UNICAP2PAddcon->Channel = $line[7];
                            $UNICAP2PAddcon->Treatment_Code = $line[8];
                            $UNICAP2PAddcon->Email_Address = $line[9];
                            $UNICAP2PAddcon->Title = $line[10];
                            $UNICAP2PAddcon->Firstname = $line[11];
                            $UNICAP2PAddcon->Surname = $line[12];
                            $UNICAP2PAddcon->Address1 = $address;
                            $UNICAP2PAddcon->Address2 = '';
                            $UNICAP2PAddcon->Address3 = '';
                            $UNICAP2PAddcon->Town = $line[16+$count];
                            $UNICAP2PAddcon->County = $line[17+$count];
                            $UNICAP2PAddcon->Postcode = $line[18+$count];
                            $UNICAP2PAddcon->Propensity = $line[19+$count];
                            $UNICAP2PAddcon->Activation_Date = date('Y-m-d',strtotime($line[20+$count]));
                            $UNICAP2PAddcon->Avg_Data_Usage = $line[21+$count];
                            $UNICAP2PAddcon->DOB = $line[22+$count];
                            $UNICAP2PAddcon->Tariff_Name = $line[23+$count];
                            $UNICAP2PAddcon->DCP_Customers = $line[24+$count];
                            $UNICAP2PAddcon->Sky_BT_Flag = $line[25+$count];
                            $UNICAP2PAddcon->Customers_Monthly_Spend = $line[26+$count];
                            $UNICAP2PAddcon->Customers_In_Arrears = $line[27+$count];
                            $UNICAP2PAddcon->Number_Of_Previous_Transaction = $line[28+$count];
                            $UNICAP2PAddcon->GDPR_Bundle_1 = $line[29+$count];
                            $UNICAP2PAddcon->GDPR_Bundle_2 = $line[30+$count];
                            $UNICAP2PAddcon->GDPR_Bundle_3 = $line[31+$count];
                            $UNICAP2PAddcon->SMS_OPT_IN = $line[32+$count];
                            $UNICAP2PAddcon->Source_Partner = $line[33+$count];
                            $UNICAP2PAddcon->Device_Name = $line[34+$count];
                            $UNICAP2PAddcon->Customers_Affluence = $line[35+$count];
                            $UNICAP2PAddcon->Month_SIMO_Customers = $line[36+$count];
                            $UNICAP2PAddcon->Like_New_Model_Decile = $line[37+$count];
                            $UNICAP2PAddcon->Smartphone_Flag = $line[38+$count];
                            $UNICAP2PAddcon->custom_1 = date('Y-m-d',strtotime($line[39+$count]));
                            $UNICAP2PAddcon->custom_2 = $line[40+$count];
                            if ($UNICAP2PAddcon->save()) {

                            }
                        }

                }
                fclose($f);
            }
            DB::table('temp_csv')->where('file_import_id', $newFile[0]->file_import_id)->update(['status' => 1]);
        //}

        echo date('Y-m-d H:i:s');
    }

}
