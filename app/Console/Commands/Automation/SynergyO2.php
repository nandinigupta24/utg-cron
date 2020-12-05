<?php

namespace App\Console\Commands\Automation;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Model\Intelling\AgentTableCombined;
use Mail;
use App\Model\Intelling\SynergyO2API;
use Excel;
use App\Model\Intelling\FileImportLog;
use App\Model\Intelling\ImportDatasource;

class SynergyO2 extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:SynergyO2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to create leads';

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

        if (date('H') > 22) {
            exit;
        }

        if (date('H') >= 0 && date('H') < 6) {
            exit;
        }
        $dateWorking = date('Y-m-d');
        $ValidationDate = date('Y-m-d', strtotime("-90 days"));
        ini_set('memory_limit', '2048M');
        $server = 'usethegeeks.ftp.uk';
        $serverPort = 22;
        $connection = ssh2_connect($server, $serverPort);
        $serverUser = 'akumar1';
        $serverPassword = '16IndiaGeeksUK';
        $listId = 30032;
        $campaignId = [1306, 1307];
        $localServerPath = storage_path('Automation/Synergy/O2/');

        $listIdSrch = DB::connection('MainDialer')
                ->table('lists')
                ->whereIn('campaign_id', $campaignId)
                ->pluck('list_id')
                ->toArray();

        $countData = SynergyO2API::count();
        $sourceIds = ImportDatasource::where('type', 'O2')->pluck('source_id')->toArray();
        $fileImportArray = FileImportLog::where('type', 'O2')->where('created_at', 'like', $dateWorking . '%')->pluck('filename')->toArray();

        if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
            echo "connected\n";
            $sftp = ssh2_sftp($connection);
            $sftp_fd = intval($sftp);

            $files = scandir("ssh2.sftp://" . $sftp_fd . "/Synergy/Synergy/O2/", SCANDIR_SORT_DESCENDING);
            $newFile = [];
            foreach ($files as $keyval => $value) {

                if (in_array($value, ['.', '..', '...', '....'])) {
                    continue;
                }

                $date = date("Y-m-d", filemtime("ssh2.sftp://" . $sftp_fd . "/Synergy/Synergy/O2/" . $value));
                if ($date != $dateWorking) {
                    continue;
                }
                if (in_array($value, $fileImportArray)) {
                    continue;
                }
                $FileImportLog = new FileImportLog();
                
                $FileImportLog->filename = $value;
                $FileImportLog->type = 'O2';
                $SaveFileName = 'O2-' . date('Y-m-d') . '-' . $keyval . '-' . $value;
                $content = file_get_contents("ssh2.sftp://$sftp_fd/Synergy/Synergy/O2/" . $value);
                $fp = fopen($localServerPath.$SaveFileName, "w");
                fwrite($fp, $content);
                fclose($fp);

                $FileImportLog->save_filename = $SaveFileName;
                if ($FileImportLog->save()) {
                    $newFile[$FileImportLog->id] = $SaveFileName;
                }

            }

            $dataUpdate = [];
            if (count($newFile) == 0) {
                die('BYE');
            }

            foreach ($newFile as $key => $val) {
                $FileImportLogUpdate = FileImportLog::find($key);

                $dataUpdate[$key]['total'] = 0;
                $dataUpdate[$key]['success'] = 0;
                $dataUpdate[$key]['duplicate'] = 0;
                $dataUpdate[$key]['datasource'] = [];
                $dataUpdate[$key]['filename'] = $FileImportLogUpdate->filename;
                $dataUpdate[$key]['fail'] = '';
                $dataUpdate[$key]['error'] = '';

                $reader = Excel::load($localServerPath . $val)->get();
                $dataLoaded = $reader->toArray();
                $headerRow = $reader->first()->keys()->toArray();
                $errorFile = [];
                if (!in_array('data_source', $headerRow)) {
                    $errorFile['data_source'] = 'Datasource column not exact matching';
                    $FileImportLog = FileImportLog::find($key);
                    $FileImportLog->fail = 'yes';
                    $FileImportLog->response_error = (!empty($errorFile) && count($errorFile) > 0) ? serialize($errorFile) : '';
                    if ($FileImportLog->save()) {
                        $dataUpdate[$key]['fail'] = 'yes';
                        $dataUpdate[$key]['error'] = 'data_source field name is not correct';
                        continue;
                    }
                }
                if (!in_array('phone_number', $headerRow)) {
                    $errorFile['phone_number'] = 'Datasource column not exact matching';
                    $FileImportLog = FileImportLog::find($key);
                    $FileImportLog->fail = 'yes';
                    $FileImportLog->response_error = (!empty($errorFile) && count($errorFile) > 0) ? serialize($errorFile) : '';
                    if ($FileImportLog->save()) {
                        $dataUpdate[$key]['fail'] = 'yes';
                        $dataUpdate[$key]['error'] = 'phone_number field name is not correct';
                        continue;
                    }
                }

                $VendorLeadCode = 'O2ConsumerSynergy-' . date('YmdHis');

                foreach ($dataLoaded as $k => $v) {
                    if (empty($v['data_source'])) {
                        continue;
                    }
                    if (!in_array($v['data_source'], $sourceIds)) {
                        $dataUpdate[$key]['datasource'][@$v['data_source']] = @$v['data_source'];
                        $ImportDatasource = new ImportDatasource();
                        $ImportDatasource->source_id = $v['data_source'];
                        $ImportDatasource->type = 'O2';
                        if ($ImportDatasource->save()) {
                            $sourceIds = ImportDatasource::where('type', 'O2')->pluck('source_id')->toArray();
                        }
                    }

                    $dataUpdate[$key]['total'] ++;
                    get_file_update($key, 'total', $dataUpdate[$key]['total']);
                    $phone = get_phone_check($v['phone_number']);
                    $dataExistDialer = DB::connection('MainDialer')
                            ->table('list')
                            ->where('entry_date', '>=', $ValidationDate.' 23:59:59')
                            ->whereIn('list_id', $listIdSrch)
                            ->where('phone_number', $phone)
                            ->count();

                    $dataExist = SynergyO2API::where('created_at', '>=', $ValidationDate)->where('phone', $phone)->count();
                    $countData++;

                    $newVendorLeadCode = $VendorLeadCode . '-' . str_pad($countData, 8, '0', STR_PAD_LEFT);
                    if ($dataExistDialer == 0 && $dataExist == 0) {
                        $SynergyTTAPI = new SynergyO2API();
                        $SynergyTTAPI->datasource = @$v['data_source'];
                        $SynergyTTAPI->vendor_lead_code = $newVendorLeadCode;
                        $SynergyTTAPI->phone = @$phone;
                        $SynergyTTAPI->title = @$v['title'];
                        $SynergyTTAPI->first_name = @$v['first_name'];
                        $SynergyTTAPI->last_name = @$v['last_name'];
                        $SynergyTTAPI->add1 = @$v['address_1'];
                        $SynergyTTAPI->add2 = @$v['address_2'];
                        $SynergyTTAPI->add3 = @$v['address_3'];
                        $SynergyTTAPI->city = @$v['city'];
                        $SynergyTTAPI->country = @$v['country'];
                        $SynergyTTAPI->postal_code = @$v['post_code'];
                        $SynergyTTAPI->email = @$v['email'];
                        $SynergyTTAPI->secruity_phrase = @$v['security_phrase'];
                        $SynergyTTAPI->duplicate = 'no';
                        $SynergyTTAPI->file_import_log_id = $key;


                        /* API POST DATA */
                        $dataArray = [];
                        $dataArray['list_id'] = $listId;
                        $dataArray['phone_number'] = @$phone;
                        $dataArray['title'] = @$v['title'];
                        $dataArray['first_name'] = @$v['first_name'];
                        $dataArray['last_name'] = @$v['last_name'];
                        $dataArray['postal_code'] = @$v['post_code'];
                        $dataArray['email'] = @$v['email'];
                        $dataArray['address1'] = @$v['address_1'];
                        $dataArray['address2'] = @$v['address_2'];
                        $dataArray['address3'] = @$v['address_3'];
                        $dataArray['city'] = @$v['city'];
                        $dataArray['country'] = @$v['country'];
                        $dataArray['vendor_lead_code'] = $newVendorLeadCode;
                        $dataArray['source_id'] = @$v['data_source'];
                        $dataArray['custom_fields'] = 'Y';
                        $queryString = http_build_query($dataArray);
                        if ($SynergyTTAPI->save()) {
                            $lastID = $SynergyTTAPI->id;
                            $data = @file_get_contents('http://10.29.104.7/Admin/.functions/non_agent_api.php?source=Test&user=APIUSER&pass=APIUSER&function=add_lead&' . $queryString);
                            $NeatleyAPIConnexUpdate = SynergyO2API::find($lastID);
                            $NeatleyAPIConnexUpdate->api_response = $data;
                            if ($NeatleyAPIConnexUpdate->save()) {
                                $dataUpdate[$key]['success'] ++;
                                get_file_update($key, 'success', $dataUpdate[$key]['success']);
                            }
                        }
                    } else {

                        $SynergyTTAPI = new SynergyO2API();
                        $SynergyTTAPI->datasource = @$v['data_source'];
                        $SynergyTTAPI->vendor_lead_code = $newVendorLeadCode;
                        $SynergyTTAPI->phone = @$phone;
                        $SynergyTTAPI->title = @$v['title'];
                        $SynergyTTAPI->first_name = @$v['first_name'];
                        $SynergyTTAPI->last_name = @$v['last_name'];
                        $SynergyTTAPI->add1 = @$v['address_1'];
                        $SynergyTTAPI->add2 = @$v['address_2'];
                        $SynergyTTAPI->add3 = @$v['address_3'];
                        $SynergyTTAPI->city = @$v['city'];
                        $SynergyTTAPI->country = @$v['country'];
                        $SynergyTTAPI->postal_code = @$v['post_code'];
                        $SynergyTTAPI->email = @$v['email'];
                        $SynergyTTAPI->secruity_phrase = @$v['security_phrase'];
                        $SynergyTTAPI->duplicate = 'yes';
                        $SynergyTTAPI->file_import_log_id = $key;
                        if (!empty($dataExistDialer) && $dataExistDialer > 0) {
                            $dataListIds = DB::connection('main_dialer')
                                            ->table('list')
                                            ->whereIn('list_id', $listIdSrch)
                                            ->where('phone_number', $phone)
                                            ->pluck('list_id')->toArray();

                            if (!empty($dataListIds)) {
                                $SynergyTTAPI->duplicate_list_id = implode(',', $dataListIds);
                            }
                        }
                        if ($SynergyTTAPI->save()) {
                            $dataUpdate[$key]['duplicate'] ++;
                            get_file_update($key, 'duplicate', $dataUpdate[$key]['duplicate']);
                        }
                    }
                }


                $FileImportLogUpdate->total = $dataUpdate[$key]['total'];
                $FileImportLogUpdate->duplicate = $dataUpdate[$key]['duplicate'];
                $FileImportLogUpdate->success = $dataUpdate[$key]['success'];
                $FileImportLogUpdate->datasource = implode(',', $dataUpdate[$key]['datasource']);
                $FileImportLogUpdate->response_error = (!empty($errorFile) && count($errorFile) > 0) ? serialize($errorFile) : '';
                $FileImportLogUpdate->fail = 'no';
                if ($FileImportLogUpdate->save()) {
                    
                }
            }
        } else {
            echo "connection failed\n";
        }


        $arrayMailTo = ['diallersupport@intelling.co.uk'];
        $arrayMailCC = ['akumar@usethegeeks.com', 'joash@synergycontactcentre.com', 'annie.seisay@intelling.co.uk', 'dillan@synergycontactcentre.com', 'adrian@synergycontactcentre.com', 'joechem@synergycontactcentre.com', 'connor@synergycontactcentre.com', 'terence@synergycontactcentre.com', 'veekash@synergycontactcentre.com'];

        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.logs.synergy_o2';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : $arrayMailCC;
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'Synergy O2 Leads - ' . date('Y-m-d');
        $mail_data['data'] = @$dataUpdate;

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
