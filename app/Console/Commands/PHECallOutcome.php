<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendDiallerOperationMail;
use DB;
use Excel;
use ZipArchive;
use File;

class PHECallOutcome extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:PHECallOutcome';

    /**
     * The console command description.
     * 
     * @var string
     */
    protected $description = 'Email Cron to Dialler Team and Operations';

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

        $start = $end = date('Y-m-d');
//        $start = $end = '2020-03-13';

        $data = DB::connection('IntellingScriptDB')->table('PHE_data')
                ->where('created_at', '>=', $start . ' 00:00:00')
                ->where('created_at', '<=', $end . ' 23:59:59')
                ->get();

        $filename = 'IntellingLTD_'.date('Ymd');
        Excel::create($filename, function($excel) use($data) {
            $excel->setTitle('PHE Reports');

            $excel->sheet('Call Outcome', function($sheet) use($data) {

                $sheet->appendRow(['date', 'providerName', 'country', 'assessmentOutcome', 'selfIsolationNoticeCompletion', 'primaryQuestion', 'secondaryQuestion', 'otherQuestion']);

                $sheet->setOrientation('landscape');

                foreach ($data as $value) {
                    $sheet->appendRow([
                        'id1' => $value->date,
                        'id2' => $value->provider_name,
                        'id3' => $value->country,
                        'id4' => $value->assessment_outcome,
                        'id5' => $value->self_isolation_notice_completion,
                        'id6' => $value->primary_question,
                        'id7' => $value->secondary_question,
                        'id8' => $value->other_question
                    ]);
                }
            });
        })->store('csv', storage_path('Automation/PHE'), true);

//        $filename = date('Y-m-d') . '-NHS';
//        Excel::create($filename, function($excel) use($data) {
//            $excel->setTitle('NHS Reports');
//
//            $excel->sheet('Outcome 2', function($sheet) use($data) {
//
//                $sheet->appendRow(['Date', 'Time', 'UniqueID', 'Caller Title', 'Caller First Name', 'Caller Surname', 'Caller Return Telephone Number', 'Caller Date Of Birth', 'Gender', 'Add1', 'Add2', 'Add3', 'Add4', 'Town', 'County', 'Postcode']);
//
//                $sheet->setOrientation('landscape');
//
//                foreach ($data as $value) {
//                    $sheet->appendRow([
//                        'id1' => $value->date,
//                        'id2' => $value->time,
//                        'id3' => $value->UniqueID,
//                        'id4' => $value->CallerTitle,
//                        'id5' => $value->CallerFirstName,
//                        'id6' => $value->CallerSurname,
//                        'id7' => $value->CallerReturnTelephoneNumber,
//                        'id8' => $value->CallerDateOfBirth,
//                        'id9' => $value->Gender,
//                        'id10' => $value->CallerAdd1,
//                        'id11' => $value->CallerAdd2,
//                        'id12' => $value->CallerAdd3,
//                        'id13' => $value->CallerAdd4,
//                        'id14' => $value->CallerTown,
//                        'id15' => $value->CallerCounty,
//                        'id16' => $value->CallerPostcode
//                    ]);
//                }
//            });
//        })->store('xlsx', storage_path('Automation/NHS/CSV'), true);
//
//
//        $zip = new ZipArchive;
//
//
//        $fileName1 = date('Y-m-d') . '-NHS.zip';
//
//
//        if ($zip->open(storage_path('Automation/NHS/ZIP/' . $fileName1), ZipArchive::CREATE) === TRUE) {
//            $files = File::files(storage_path('Automation/NHS/input/'));
//
//            foreach ($files as $key => $value) {
//                $relativeNameInZipFile = basename($value);
//                $zip->addFile($value, $relativeNameInZipFile);
//            }
//
//            $zip->close();
//        }
//
//        response()->download(storage_path('Automation/NHS/ZIP/' . $fileName1));
//        unlink(storage_path('Automation/NHS/input/'.$filename.'.xlsx'));
        
        $arrayMailTo = ['apickett@usethegeeks.co.uk', 'apanwar@usethegeeks.co.uk','kerry.anderson@intelling.co.uk','Jenny.Blake1@intelling.co.uk','nhs111cell.facmi-covid19@nhs.net'];
//        $arrayMailTo = ['apanwar@usethegeeks.co.uk'];
//        $arrayMailTo = ['apickett@usethegeeks.co.uk'];
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.email';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'Daily PHE Records';

        $result = Mail::send($mail_data['view'], $mail_data, function ($m) use ($mail_data, $filename) {
                    $m->from($mail_data['from'], 'Intelling');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                    $m->attach(storage_path('Automation/PHE/') . $filename . '.csv');
                });


//        $server = '3.8.11.11';
//        $serverPort = 22;
//        $connection = ssh2_connect($server, $serverPort);
//        $serverUser = 'root';
//        $serverPassword = 'Utgesx0012!!';
//        $ServerDirectory = '/home/Serco/';
//
//        if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
//            ssh2_scp_send($connection, "/var/www/html/cron/storage/Automation/NHS/ZIP/" . $filename . ".zip", $ServerDirectory . $filename . ".zip");
//        }
//
//        $server = '35.179.1.12';
//        $serverPort = 22;
//        $connection = ssh2_connect($server, $serverPort);
//        $serverUser = 'root';
//        $serverPassword = 'Utgesx0012!!';
//        $ServerDirectory = '/var/www/html/intelling/int/campaigns/storage/';
//
//        if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
//            ssh2_scp_send($connection, "/var/www/html/cron/storage/Automation/NHS/ZIP/" . $filename . ".zip", $ServerDirectory . 'ZIP/' . $filename . ".zip");
//            ssh2_scp_send($connection, "/var/www/html/cron/storage/Automation/NHS/CSV/" . $filename . ".xlsx", $ServerDirectory . 'CSV/' . $filename . ".xlsx");
//        }
    }

}
