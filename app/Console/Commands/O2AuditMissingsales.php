<?php //

namespace App\Console\Commands;

use App\Exceptions\CustomException;
use Illuminate\Console\Command;
use App\Model\O2Audit\O2AuditMissing;
use DB;
use Illuminate\Support\Str;
use Mail;

class O2AuditMissingSales extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:O2AuditMissingSales {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'O2AuditMissingSales Imported from server';

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

        $LocalDirectory = '/mnt/MissingSales';
        $dateFileGet = $this->argument('date') ? : date('Y-m-d');
        $date =  date('Ymd', strtotime($dateFileGet));
        $file_name = 'MissingSales_'.$date.'.csv';

        $File = $LocalDirectory . '/'.$file_name;

        if(file_exists($File)) {
            $handle = fopen($File, "r");
            $row = 0;
                if (empty($handle) === false) {
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $row++;
                        if ($row == 1) continue;
                        $O2AuditMissing = new O2AuditMissing();
                        $O2AuditMissing->SaleDate = $data[0];
                        $O2AuditMissing->SaleTime = $data[1];
                        $O2AuditMissing->OrderNum = $data[2];
                        $O2AuditMissing->CLI_Number = $data[3];
                        $O2AuditMissing->AssociateName = $data[4];
                        $O2AuditMissing->CSA_ID = $data[5];
                        $O2AuditMissing->CampaignName = $data[6];
                        $O2AuditMissing->TT_LineName = $data[7];
                        $O2AuditMissing->O2I_DDI = $data[8];
                        $O2AuditMissing->O2I_HKSource = $data[9];
                        $O2AuditMissing->MatchedCampaign = $data[10];
                        if ($O2AuditMissing->save()) {

                        }
                    }
                    $mail_data = array();
                    $mail_data['to'] = ['ngupta@usethegeeks.co.uk','akumar@usethegeeks.com','David.Locke@intelling.co.uk'];
                    $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
                    $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
                    $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.O2SaleMissingFileProcessed';
                    $mail_data['filename'] = $File;
                    $mail_data['subject'] = 'O2Audit Missing Sales - File Alert';

                    $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                            $m->from($mail_data['from'], 'Intelling');
                            if (!empty($mail_data['cc'])) {
                                $m->cc($mail_data['cc']);
                            }
                            $m->to($mail_data['to'])->subject($mail_data['subject']);
                        });
                }
                shell_exec('/home/file_move_missing_archive.sh '.$file_name);
            } else {
            $mail_data = array();
                $mail_data['to'] = ['ngupta@usethegeeks.co.uk','akumar@usethegeeks.com','David.Locke@intelling.co.uk'];
                $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
                $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
                $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.O2SaleMissingFileNotFound';
                $mail_data['filename'] = $File;
                //$mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
                $mail_data['subject'] = 'O2Audit Missing Sales - No File Alert';

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
