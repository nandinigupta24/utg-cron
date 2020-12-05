<?php //

namespace App\Console\Commands;

use App\Exceptions\CustomException;
use Illuminate\Console\Command;
use App\Model\O2Audit\O2AuditSaleSLM;
use DB;
use Illuminate\Support\Str;
use Mail;

class O2AuditSalesSLMImport extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:O2AuditSalesSLMImport {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'O2AuditSalesSLM Imported from server';

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

        $LocalDirectory = '/mnt/SLM';
        $dateFileGet = $this->argument('date') ? : date('Y-m-d');
        $date =  date('Ymd', strtotime($dateFileGet));
        $file_name = 'SLMDataExtract_'.$date.'.csv';

        $File = $LocalDirectory . '/'.$file_name;

        if(file_exists($File)) {
            $handle = fopen($File, "r");
            $row = 0;
                if (empty($handle) === false) {
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) { 
                        $row++;
                        if ($row == 1) continue;
                        $O2AuditSaleslm = new O2AuditSaleSLM();
                        $O2AuditSaleslm->SaleDateTime = $data[0];
                        $O2AuditSaleslm->AgentName = $data[1];
                        $O2AuditSaleslm->PackageType = $data[2];
                        $O2AuditSaleslm->Result = $data[3];
                        $O2AuditSaleslm->OrderNumber = $data[4];
                        $O2AuditSaleslm->Forename = $data[5];
                        $O2AuditSaleslm->Surename = $data[6];
                        $O2AuditSaleslm->CreateDate = date('Y-m-d');                       
                        if ($O2AuditSaleslm->save()) {

                        } 
                    }   
                    $mail_data = array();
                    $mail_data['to'] = ['ngupta@usethegeeks.co.uk','akumar@usethegeeks.com','David.Locke@intelling.co.uk'];
                    $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
                    $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
                    $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.O2SaleSLMFileProcessed';
                    $mail_data['filename'] = $File;
                    $mail_data['subject'] = 'O2SalesSLM - File Alert';
    
                    $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                            $m->from($mail_data['from'], 'Intelling');
                            if (!empty($mail_data['cc'])) {
                                $m->cc($mail_data['cc']);
                            }
                            $m->to($mail_data['to'])->subject($mail_data['subject']);
                        });                 
                } 
                shell_exec('/home/file_move_slm_archive.sh '.$file_name);
            } else {
            $mail_data = array();
                $mail_data['to'] = ['ngupta@usethegeeks.co.uk','akumar@usethegeeks.com','David.Locke@intelling.co.uk'];
                $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
                $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
                $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.O2SaleSLMFileNotFound';
                $mail_data['filename'] = $File;
                //$mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
                $mail_data['subject'] = 'O2SalesSLM - No File Alert';
    
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
