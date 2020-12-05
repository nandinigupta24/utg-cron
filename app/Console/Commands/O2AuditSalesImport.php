<?php //

namespace App\Console\Commands;

use App\Exceptions\CustomException;
use Illuminate\Console\Command;
use App\Model\O2Audit\O2AuditSale;
use DB;
use Illuminate\Support\Str;
use Mail;

class O2AuditSalesImport extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:O2AuditSalesImport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'O2AuditSales Imported from server';

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

        $LocalDirectory = '/mnt';
        $date = date('Ymd');
        $file_name = $date.'_VTFR_Intelling_Orders_.csv';

        $File = $LocalDirectory . '/'.$file_name;

        if(file_exists($File)) {
            $handle = fopen($File, "r");
            $row = 0;
                if (empty($handle) === false) {
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) { 
                        $row++;
                        if ($row == 1) continue;
                        $O2AuditSale = new O2AuditSale();
                        $O2AuditSale->OID = $data[0];
                        $O2AuditSale->BasicOID = $data[1];
                        $O2AuditSale->OrderType = $data[2];
                        $O2AuditSale->OrderDate = $data[3];
                        $O2AuditSale->Weekending = $data[4];
                        $O2AuditSale->LTEAddressableOpp = $data[5];
                        $O2AuditSale->LTEAddressableStore = $data[6];
                        $O2AuditSale->CHANNEL = $data[7];
                        $O2AuditSale->ChannelL2 = $data[8];
                        $O2AuditSale->CSAID = $data[9];
                        $O2AuditSale->WFBoundaryState = $data[10];
                        $O2AuditSale->UpgradeType = $data[11];
                        $O2AuditSale->FJCategory = $data[12];
                        $O2AuditSale->FJGroupID = $data[13];
                        $O2AuditSale->FJProductID = $data[14];
                        $O2AuditSale->FJProductName = $data[15];
                        $O2AuditSale->LJCategory = $data[16];
                        $O2AuditSale->LJProdID = $data[17];
                        $O2AuditSale->LJHansetModel = $data[18];
                        $O2AuditSale->FJVATExcelPrice = $data[19];
                        $O2AuditSale->FJVAT = $data[20];
                        $O2AuditSale->FJCatalogPrice = $data[21];
                        $O2AuditSale->ClickCollectIndicator = $data[22];
                        $O2AuditSale->RefreshIndicator = $data[23];
                        $O2AuditSale->LTEIndicator = $data[24];
                        $O2AuditSale->CategoryClassification = $data[25];
                        $O2AuditSale->HandsetSplit = $data[26];
                        $O2AuditSale->HandsetMake = $data[27];
                        $O2AuditSale->IMEI = $data[28];
                        $O2AuditSale->SubscriptionCost = $data[29];
                        $O2AuditSale->ClickCollectNowIndicator = $data[30];
                        $O2AuditSale->DespatchDate = $data[31];
                        $O2AuditSale->ReturnDate = $data[32];
                        $O2AuditSale->Postcode = $data[33];
                        if ($O2AuditSale->save()) {

                        } 
                    }   
                    $mail_data = array();
                    $mail_data['to'] = ['ngupta@usethegeeks.co.uk','akumar@usethegeeks.com','David.Locke@intelling.co.uk'];
                    $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
                    $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
                    $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.O2SaleFileProcessed';
                    $mail_data['filename'] = $File;
                    $mail_data['subject'] = 'O2Sales - File Alert';
    
                    $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                            $m->from($mail_data['from'], 'Intelling');
                            if (!empty($mail_data['cc'])) {
                                $m->cc($mail_data['cc']);
                            }
                            $m->to($mail_data['to'])->subject($mail_data['subject']);
                        });                 
                }
                shell_exec('/home/file_move_archive.sh '.$file_name);
            } else {
            $mail_data = array();
                $mail_data['to'] = ['ngupta@usethegeeks.co.uk','akumar@usethegeeks.com','David.Locke@intelling.co.uk'];
                $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
                $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
                $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.O2SaleFileNotFound';
                $mail_data['filename'] = $File;
                //$mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
                $mail_data['subject'] = 'O2Sales - No File Alert';
    
                $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                            $m->from($mail_data['from'], 'Intelling');
                            if (!empty($mail_data['cc'])) {
                                $m->cc($mail_data['cc']);
                            }
                            $m->to($mail_data['to'])->subject($mail_data['subject']);
                        });
    
                        die('BYE');
        }
    }
        

}
