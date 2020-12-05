<?php

namespace App\Console\Commands\UTGAPIV2;

use Illuminate\Console\Command;
use App\Model\UTGAPI\FileImportLog as FileImportTable;
use Mail;
use DB;

class FileImportLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'utgapiv2:fileimport {--filetype=} {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get All Types of files from file server.
                                              Optional params --filetype=P2P-SMARTPHONE-UNICA , --date=2020-01-01
                                              option for file types: P2P-SMARTPHONE-UNICA, P2P-CHURN-UNICA, P2P-CORE-UNICA, P2P-ADDCON-UNICA, O2UNICA';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $filetype = $this->option('filetype');
        $date = $this->option('date') ? : date('Y-m-d');
        $dateFileGet =  date('Ymd', strtotime($date));

        $loopFilesType = $filesTypes = [
            'P2P-SMARTPHONE-UNICA' => [
                'match_codes' => ['A001554730','A000996975'],
                'match_string' => 'UNI_105_'.$dateFileGet.'*_SMP_OBC_*.dat.gz.gpg',
                'date_cycle' => 30
            ],
            'P2P-CHURN-UNICA' => [
                'match_codes' => ['A001594207', 'A001644919', 'A001645526', 'A001645530', 'A001645534'],
                'match_string' => 'UNI_105_'.$dateFileGet.'*_05519_InLifeChurn_Monthly_LBM_*.dat.gz.gpg',
                'date_cycle' => 0
            ],
            'P2P-CORE-UNICA' => [
                'match_codes' => ['A000980064'],
                'match_string' => 'UNI_105_'.$dateFileGet.'*_05597_Intellitest_05597_*.dat.gz.gpg',
                'date_cycle' => 0
            ],
            'P2P-ADDCON-UNICA' => [
                'match_codes' => ['A001855533','A001855527','A001855521','A001855515','A001647417','A001647427','A001647437','A001647451','A001647453','A001647455','A001647461','A001647433','A001647457','A001706754','A001706738','A001706549','A001706539'],
                'match_string' => 'UNI_105_'.$dateFileGet.'*_Intelling_OBC_AddConns_Mobile_Tablet_*.dat.gz.gpg',
                'date_cycle' => 14
            ],
            'O2UNICA' => [
                'match_codes' => ['A001114912','A001114914', 'A001114916', 'A001114910','A001732657', 'A001113235','A001114899', 'A001376530', 'A001732655'],
                'match_string' => 'UNI_105_'.$dateFileGet.'*_05545_P2P_Weekly_Intelling_05545_*.dat.gz.gpg',
                'date_cycle' => 5
            ]
        ];

        if(!$this->option('date')) {
          $log_addcon_file = FileImportTable::selectRaw('*, SUBSTR(filename, 9, 8) AS DateOrder');
          if($filetype) {
            $log_addcon_file->where('type', $filetype);
          }
          $log_addcon_file = $log_addcon_file->orderBy('DateOrder','desc')->first();
          if($loopFilesType[$log_addcon_file->type]['date_cycle']) {
            $create_date = date_create(date('Y-m-d', strtotime($log_addcon_file->DateOrder)));
            $current_date = date_create(date('Y-m-d'));
            $diff=date_diff($current_date,$create_date);
            $day = $diff->format("%a");
            if($day < $loopFilesType[$log_addcon_file->type]['date_cycle']) {
              utgapilog('Date is not matching');
              die('Bye');
            }
          }

          }

        if($filetype && isset($filesTypes[$filetype])) {
          $loopFilesType = [];
          $loopFilesType[$filetype] = $filesTypes[$filetype];
        }
        $server             = '109.234.196.231';
        $serverPort         = 22;
        $connection         = ssh2_connect($server, $serverPort);
        $serverUser         = 'O2UNICA';
        $serverPassword     = '569WbxXq';
        $ServerDirectory    = '/O2Data/';
        $LocalDirectory     = '/var/www/html/cron/storage/Automation/O2UNICA/IN/';
        $LocalImportDirectory = '/var/www/html/cron/storage/Automation/O2UNICA/';
        $dialer             = 'OmniDialer';
        $user               = 'Intelling-OmniChannel';
        $pass               = '2j4VHhYYHqkTnBjJ';
        $token              = '0HeIOWyak5UIdeXKmD4twR7kty030UWXQ4fiU6e6kr7cuPgTm2';

        if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
            utgapilog("SSH Connection Start");
            $sftp = ssh2_sftp($connection);
            $sftp_fd = intval($sftp);
            foreach($loopFilesType as $TypeFile => $file_type_ar) {
                  $newFile            = [];
                  $files = scandir("ssh2.sftp://" . $sftp_fd . $ServerDirectory.$file_type_ar['match_string'], SCANDIR_SORT_DESCENDING);
                  // dd($files);
                  $Count = 0;
                  $ArrayCodeProcess = $file_type_ar['match_codes'];
                  foreach ($files as $value) {
                      $FileSourceCode = get_file_break($value);
                      if (in_array($FileSourceCode, $ArrayCodeProcess)) {
                          if (strpos($value, $dateFileGet) !== false) {
                              $content = file_get_contents("ssh2.sftp://" . $sftp_fd . $ServerDirectory . $value);
                              $fp = fopen($LocalDirectory . $value, "w");
                              fwrite($fp, $content);
                              fclose($fp);
                              $filename = pathinfo($value, PATHINFO_FILENAME);
                              $filenameEXT = pathinfo($value, PATHINFO_EXTENSION);
                              if ($filenameEXT != 'mfst') {
                                  $filename = str_replace('.dat.gz.gpg', '', $value);
                                  $filenameEXT = 'dat.gz.gpg';
                              }
                              $FileImportLog = FileImportTable::where('type', $TypeFile)->where('original_filename',$value)->first() ? : new FileImportTable();
                              $FileImportLog->original_filename = $value;
                              $FileImportLog->filename = $filename;
                              $FileImportLog->type = $TypeFile;
                              $FileImportLog->file_extension = $filenameEXT;
                              $FileImportLog->total = 0;
                              $FileImportLog->success = 0;
                              $FileImportLog->failed = 0;
                              if ($FileImportLog->save()) {
                                  $newFile[$FileImportLog->id]['name'] = $filename . '.csv';
                                  $newFile[$FileImportLog->id]['type'] = $filenameEXT;
                              }
                          }
                      }
                      utgapilog($TypeFile." File_id ".$FileImportLog->id." Name - ".$value);
                  }
                  if(count($newFile) == 0){
                      $mail_data = array();
                      $mail_data['to'] = ['automation@usethegeeks.zendesk.com', 'Nicola.Sharrock@intelling.co.uk'];
                     // $mail_data['to'] = ['ngupta@usethegeeks.co.uk'];
                      $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
                      $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
                      $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.automation.P2PAddconAlert';
                      $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
                      $mail_data['subject'] = $TypeFile.' Date '.$date.' No File Alert';

                      $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                                $m->from($mail_data['from'], 'Intelling');
                                if (!empty($mail_data['cc'])) {
                                    $m->cc($mail_data['cc']);
                                }
                                $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                                $m->to($mail_data['to'])->subject($mail_data['subject']);
                          });

                  }
              }
              utgapilog("SSH Connection End");
              shell_exec('/home/file_conv_O2UNICA.sh');
        } else {
          utgapilog("SSH Connection Failed to connect");
        }
    }
}
