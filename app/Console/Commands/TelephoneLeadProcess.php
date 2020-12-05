<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\ScriptResponse;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;
use App\Model\Intelling\Campaign386;
use App\Model\MainDialer\MDList;
use App\Model\O2Combine\O2Sale;

class TelephoneLeadProcess extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:TelephoneLeadProcess';

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
//        $server = '10.68.120.59';
//        $serverPort = 22;
//        $connection = ssh2_connect($server, $serverPort);
//        $serverUser = 'root';
//        $serverPassword = '16IndiaGeeksUK';
//        if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
//           echo 'connected';
//        }
        $end = $start = Carbon::now()->subDays(1)->format('Y-m-d');
        $start = Carbon::parse($start)->toDateString() . ' 00:00:00';
        
        $end = Carbon::parse($end)->toDateString() . ' 23:59:59';
        $insert = [];
        $total = [];
        $total['Origional'] = 0;
        $total['Duplicate'] = 0;
        $total['New'] = 0;
        $resultArray = [];

        /* MainDialer List Data */
        $data = MDList::where('entry_date', '>=', $start)
                ->where('entry_date', '<=', $end)
                ->whereIn('list_id', [3655, 1218, 1422])
                ->whereIn('status', ['WAS', 'WASC', 'DECCC', 'DECAD', 'ADINFO', 'LODD', 'NI', 'NBD', 'LU'])
                ->whereIn('source_id',['SID1', 'SID72', 'SID76','SID46', 'SID84', 'SID464', 'SID460', 'SID34', 'SID74', 'SID130', 'SID414', 'SID312', 'SID384', 'SID98R', 'SID452', 'SID50','SID466','SID100', 'SID450'])
                ->get();
       
//       
        foreach ($data as $value) {
            $total['Origional'] += 1;
            $number = check_mobile_number($value->phone_number);
            $countData = Campaign386::where('field10', 'LEADGENINTELLING_TEL')->where('telephone', $number)->count();
            $totalInsert = [];
            $totalInsert['FILECODE'] = 'LEADGENINTELLING_TEL';
            $totalInsert['SIM'] = 1;
            $totalInsert['TITLE'] = $value->title;
            $totalInsert['FIRSTNAME'] = $value->first_name;
            $totalInsert['SURNAME'] = $value->last_name;
            $totalInsert['ADD1'] = $value->address1;
            $totalInsert['ADD2'] = $value->address2;
            $totalInsert['ADD3'] = $value->address3;
            $totalInsert['CITY'] = $value->city;
            $totalInsert['PCODE'] = $value->postal_code;
            $totalInsert['NETWORK'] = 'O2';
            $totalInsert['MEDIA'] = '';
            $totalInsert['SIMTYPE'] = 'TSIM';
            $totalInsert['TARIFF'] = 'Big Bundle 10 Data';
            $totalInsert['DATETIME'] = '';
            $totalInsert['EMAIL'] = $value->email;
            $totalInsert['OPTIN'] = 1;
            $totalInsert['MPN'] = check_mobile_number($value->phone_number);
            $totalInsert['ID'] = '1334-' . $value->phone_number;
            $totalInsert['KEYWORD'] = '';
            $totalInsert['SHORTCODE'] = '';
            $totalInsert['FLAG_COMPLETE'] = '';
            $totalInsert['COUNTRY'] = 'UK';
            $totalInsert['SKU'] = '24GDRIMN';
            $resultArray['Origional'][] = $totalInsert;
            if (!empty($countData)) {
                $resultArray['Duplicate'][] = $totalInsert;
                $total['Duplicate'] += 1;
            } else {
                $resultArray['New'][] = $totalInsert;
                $total['New'] += 1;
                $insertArray = [];
                $insertArray['id'] = md5(time() . '-' . $total['New']);
                $insertArray['inc'] = 0;
                $insertArray['result'] = 0;
                $insertArray['previous_result'] = 0;
                $insertArray['status'] = 0;
                $insertArray['telephone'] = check_mobile_number($value->phone_number);
                $insertArray['times_called'] = 0;
                $insertArray['times_callback'] = 0;
                $insertArray['field1'] = $value->title;
                $insertArray['field2'] = $value->first_name;
                $insertArray['field3'] = $value->middle_initial;
                $insertArray['field4'] = $value->last_name;
                $insertArray['field5'] = $value->address1;
                $insertArray['field6'] = $value->address2;
                $insertArray['field7'] = $value->address3;
                $insertArray['field8'] = $value->city;
                $insertArray['field9'] = $value->postal_code;
                $insertArray['field10'] = 'LEADGENINTELLING_TEL';
                $insertArray['use_which_telephone'] = 0;
                $insertArray['tel1_valid_to_dial'] = 1;
                $insertArray['tel2_valid_to_dial'] = 1;
                $insertArray['tel3_valid_to_dial'] = 1;
                $insertArray['tel1_valid_number'] = 1;
                $insertArray['tel2_valid_number'] = 1;
                $insertArray['tel3_valid_number'] = 1;
                $insertArray['email_address'] = $value->email;
                $insertArray['add_from'] = 'my-system-add';
                $insertArray['time_stamp'] = $value->entry_date;
                Campaign386::insert($insertArray);
            }
        }
        
        $TotalCount = [];
        /**/
        $data = Campaign386::where('field10', 'LEADGENINTELLING_TEL')
                ->where('time_stamp', '>=', $start)
                ->where('time_stamp', '<=', $end)
                ->get();
        $TotalCount['Campaign386'] = count($data);
        /* OPTINS */
        $dataEx = O2Sale::where('saletime', '>=', $start)
                ->where('saletime', '<=', $end)
                ->where('campaign_name', '3002')
                ->get();
        $TotalCount['dateEx'] = count($dataEx);
        
        
        if(!empty($TotalCount) && array_sum($TotalCount) <= 0 ){
            die('Bye');
        }
        $fileName = 'Intelling_Clickers_' . Carbon::now()->format('dmY') . '_2';
        Excel::create($fileName, function($excel) use($data, $dataEx) {
            $excel->setTitle('Lead TELEPHONE');
            $excel->sheet('Lead TELEPHONE', function($sheet) use($data, $dataEx) {
                $sheet->appendRow(['FILECODE',
                    'SIM',
                    'TITLE',
                    'FIRSTNAME',
                    'LASTNAME',
                    'ADD1',
                    'ADD2',
                    'ADD3',
                    'PCODE',
                    'NETWORK',
                    'MEDIA',
                    'SIMTYPE',
                    'TARIFF',
                    'DATE TIME',
                    'EMAIL',
                    'OPTIN',
                    'MPN',
                    'ID',
                    'KEYWORD',
                    'SHORTCODE',
                    'FLAG COMPLETE',
                    'COUNTRY',
                    'SKU'
                ]);

                $sheet->setOrientation('landscape');
                foreach ($data as $value) {
                    $sheet->appendRow([
                        'id1' => $value->field10,
                        'id2' => 1,
                        'id3' => $value->field1,
                        'id4' => $value->field2,
                        'id5' => $value->field4,
                        'id6' => $value->field5,
                        'id7' => $value->field6,
                        'id8' => $value->field8,
                        'id9' => $value->field9,
                        'id10' => 'O2',
                        'id11' => '',
                        'id12' => 'TSIM',
                        'id13' => 'Big Bundle 10 Data',
                        'id14' => date('d/m/Y H:i', strtotime($value->time_stamp)),
                        'id15' => $value->email_address,
                        'id16' => 1,
                        'id17' => '' . check_mobile_number_IC($value->telephone),
                        'id18' => '1334-' . $value->telephone,
                        'id19' => '',
                        'id20' => '',
                        'id21' => '',
                        'id22' => 'UK',
                        'id23' => '24GDRIMN',
                    ]);
                }

                if (!empty($dataEx)) {
                    foreach ($dataEx as $value) {
                        $sheet->appendRow([
                            'id1' => 'LEADGENINTELLING_TEL',
                            'id2' => 1,
                            'id3' => $value->title,
                            'id4' => $value->first_name,
                            'id5' => $value->last_name,
                            'id6' => $value->address1,
                            'id7' => $value->address2,
                            'id8' => $value->address3,
                            'id9' => $value->postal_code,
                            'id10' => 'O2',
                            'id11' => '',
                            'id12' => 'TSIM',
                            'id13' => 'Big Bundle 10 Data',
                            'id14' => date('d/m/Y H:i', strtotime($value->saletime)),
                            'id15' => $value->email,
                            'id16' => 1,
                            'id17' => '' . check_mobile_number_IC($value->phone_number),
                            'id18' => '1334-' . $value->phone_number,
                            'id19' => '',
                            'id20' => '',
                            'id21' => '',
                            'id22' => 'UK',
                            'id23' => '24GDRIMN',
                        ]);
                    }
                }
//                $sheet->setColumnFormat(array(
//    'Q' => \PHPExcel_Style_NumberFormat::FORMAT_TEXT
// ));
            });
        })->store('csv', storage_path('Automation/TelephoneLead'), true);

//        $server = '217.33.190.84';
//        $serverPort = 22;
//        $connection = ssh2_connect($server, $serverPort);
//        $serverUser = 'outboundcalling';
//        $serverPassword = '2nc8ySlT';
//        if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
//           echo 'connected';
//           ssh2_scp_send($connection, "/var/www/html/cron/storage/Automation/TelephoneLead/" . $fileName . '.xlsx', "/Data In/" . $fileName . '.xlsx');
//        } 


        $src = "/var/www/html/cron/storage/Automation/TelephoneLead/" . $fileName . '.csv';

// remote absolute path (make sure you have a write permission)
        $dest = "/Data In/" . $fileName . '.csv';

        $remoteAddress = '217.33.190.84';

        $connection = ssh2_connect($remoteAddress, 22);
        $serverUser = 'outboundcalling';
        $serverPassword = '2nc8ySlT';
        ssh2_auth_password($connection, $serverUser, $serverPassword);

        $sftp = ssh2_sftp($connection);

        $sftpStream = fopen('ssh2.sftp://' . $sftp . $dest, 'w');

        if (!$sftpStream) {
            throw new \Exception('Could not open remote file: ' . $dest);
        }

        try {
            $dataToSend = file_get_contents($src);

            fwrite($sftpStream, $dataToSend);

            fclose($sftpStream);
        } catch (\Exception $e) {
            // log error

            fclose($sftpStream);
        }
        $data = [];
        $data['Filename'] = $fileName . '.csv';
        $data['Count'] = array_sum($TotalCount);

//        $arrayMailTo = ['diallersupport@intelling.co.uk'];
        $arrayMailTo = [env('DIALER_TEAM_EMAIL')];
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.telephone_leads';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com', 'apanwar@usethegeeks.co.uk'];
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'O2 Prepay Process - Telephone (' . date('Y-m-d') . ')';
        $mail_data['data'] = @$data;

        $result = Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
                    $m->from($mail_data['from'], 'API Reports');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'API Reports');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                });
    }

}
