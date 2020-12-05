<?php

namespace App\Console\Commands\Automation;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Model\Intelling\AgentTableCombined;
use Mail;
use App\Model\Intelling\SynergyTTAPI;
use Excel;
use App\Model\Intelling\FileImportLog;
use App\Model\Intelling\ImportDatasource;

class SynergyTalkTalk extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:SynergyTalkTalk'; 

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
        if(date('H') > 22){
            exit;
        }
        
        if(date('H') >= 0 && date('H') < 6){
            exit;
        }
        
       $fileImportArray = FileImportLog::where('type', 'TalkTalk')->whereNull('total')->count();
       
       if($fileImportArray > 0){
           die('Not Processed');
       }
       
       ini_set('memory_limit','2048M');
       $dateWorking = date('Y-m-d');
       $server = 'usethegeeks.ftp.uk';
        $serverPort = 22;
        $connection = ssh2_connect($server, $serverPort);
        $serverUser = 'akumar1';
        $serverPassword = '16IndiaGeeksUK';
        $listId = 10031;
//        $listId = 9898;
        $user = 'Intelling-OmniChannel';
        $pass = '2j4VHhYYHqkTnBjJ';
        $token = '0HeIOWyak5UIdeXKmD4twR7kty030UWXQ4fiU6e6kr7cuPgTm2';
        $localServerPath = storage_path('Automation/Synergy/TalkTalk/');
        $listIdSrch = DB::connection('OmniDialer')
                                    ->table('lists')
                                    ->whereIn('campaign_id',[1001,1003])
                                    ->pluck('list_id')
                                    ->toArray();
        $countData = SynergyTTAPI::count();
        $sourceIds = ImportDatasource::where('type','TalkTalk')->pluck('source_id')->toArray();
        $fileImportArray = FileImportLog::where('type','TalkTalk')
                                ->where('created_at','like',$dateWorking.'%')
                                ->pluck('filename')
                                ->toArray();
        
        if (ssh2_auth_password($connection, $serverUser, $serverPassword)) {
            echo "connected\n";

            $sftp = ssh2_sftp($connection);
            $sftp_fd = intval($sftp);

            $files = scandir("ssh2.sftp://$sftp_fd/Synergy/Synergy/TalkTalk/", SCANDIR_SORT_DESCENDING);
            $newFile = [];
            foreach ($files as $keyval=>$value) {
                
                if (in_array($value, ['.', '..', '...', '....'])) {
                    continue;
                }
                $date = date("Y-m-d", filemtime("ssh2.sftp://".$sftp_fd."/Synergy/Synergy/TalkTalk/" . $value));
                if ($date != $dateWorking) {
                    continue;
                }
                if(in_array($value,$fileImportArray)){
                    continue;
                }
                    $FileImportLog = new FileImportLog();
                    $FileImportLog->filename = $value;
                    $FileImportLog->type = 'TalkTalk';
                    $SaveFileName = date('Y-m-d').'-TALKTALK-'.$keyval.'-' . $value;
                    $content = file_get_contents("ssh2.sftp://$sftp_fd/Synergy/Synergy/TalkTalk/".$value);
                    $fp = fopen($localServerPath.$SaveFileName, "w");
                    fwrite($fp, $content);
                    fclose($fp);
                    $FileImportLog->save_filename = $SaveFileName;
                    if($FileImportLog->save()){
                        $newFile[$FileImportLog->id] = $SaveFileName;
                    }
            }
            if(count($newFile) == 0){
                die('BYE');
            }
            $dataUpdate = [];
            foreach ($newFile as $key=>$val) {
                
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
                        if($FileImportLog->save()){
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
                        if($FileImportLog->save()){
                            $dataUpdate[$key]['fail'] = 'yes';
                            $dataUpdate[$key]['error'] = 'phone_number field name is not correct';
                            continue;
                        }
                }
                $postData = [];
                foreach ($dataLoaded as $k => $v) {
                    if(empty($v['data_source'])){
                       continue; 
                    }
                    $VendorLeadCode = 'SynTT-'.date('Ymd');
                    if(empty($v['phone_number'])){
                       continue; 
                    }
                    $phone = get_phone_check($v['phone_number']);
                    $DataSource = $dataLoaded[0]['data_source'];
                    if(!in_array($DataSource,$sourceIds)){
                        $dataUpdate[$key]['datasource'][@$v['data_source']] = @$DataSource;
                        $ImportDatasource = new ImportDatasource();
                        $ImportDatasource->source_id = $DataSource;
                        $ImportDatasource->type = 'TalkTalk';
                        if($ImportDatasource->save()){
                            $sourceIds = ImportDatasource::where('type','TalkTalk')->pluck('source_id')->toArray();
                        }
                    }
                    $dataUpdate[$key]['total']++;
                    get_file_update($key,'total',$dataUpdate[$key]['total']);
                    $dataExistDialer = DB::connection('omni_dialer')
                                            ->table('list')
                                            ->whereIn('list_id',$listIdSrch)
                                            ->where('phone_number',$phone)
                                            ->count();

                    $dataExist = SynergyTTAPI::where('phone_number',$phone)->count();

                    $countData++;
                    $newVendorLeadCode = $VendorLeadCode.'-'.str_pad($countData, 5, '0', STR_PAD_LEFT);
                    if($dataExistDialer == 0 && $dataExist == 0){
                        $SynergyTTAPI = new SynergyTTAPI();
                        $SynergyTTAPI->datasource = @$v['data_source'];
                        $SynergyTTAPI->vendor_lead_code = $newVendorLeadCode;
                        $SynergyTTAPI->phone_number = @$phone;
                        $SynergyTTAPI->title = @$v['title'];
                        $SynergyTTAPI->first_name = @$v['first_name'];
                        $SynergyTTAPI->last_name = @$v['last_name'];
                        $SynergyTTAPI->address_1 = strip_tags(@$v['address_1']);
                        $SynergyTTAPI->address_2 = strip_tags(@$v['address_2']);
                        $SynergyTTAPI->address_3 = strip_tags(@$v['address_3']);
                        $SynergyTTAPI->city = strip_tags(@$v['city']);
                        $SynergyTTAPI->postal_code = strip_tags(@$v['post_code']);
                        $SynergyTTAPI->email = strip_tags(@$v['email']);
                        $SynergyTTAPI->secruity_phrase = strip_tags(@$v['security_phrase']);
                        $SynergyTTAPI->file_import_log_id = $key;
                        $SynergyTTAPI->duplicate = 'no';
                        
                        /*API POST DATA*/
                        
                        $postData[$k]['data_list'] = $listId;
                        $postData[$k]['main_phone'] = @$phone;
                        $postData[$k]['title'] = strip_tags(get_empty(@$v['title'],''));
                        $postData[$k]['first_name'] = strip_tags(get_empty(@$v['first_name'],''));
                        $postData[$k]['last_name'] = strip_tags(get_empty(@$v['last_name'],''));
                        $postData[$k]['postcode'] = strip_tags(get_empty(@$v['post_code'],''));
                        $postData[$k]['address1'] = strip_tags(get_empty(@$v['address_1'],''));
                        $postData[$k]['address2'] = strip_tags(get_empty(@$v['address_2'],''));
                        $postData[$k]['address3'] = strip_tags(get_empty(@$v['address_3'],''));
                        $postData[$k]['city'] = strip_tags(get_empty(@$v['city'],''));
                        $postData[$k]['email'] = (filter_var($v['email'], FILTER_VALIDATE_EMAIL)) ? $v['email'] : 'test@gmail.com';
                        $postData[$k]['source_code'] = $newVendorLeadCode;
                        $postData[$k]['source'] = $v['data_source'];
                        if ($SynergyTTAPI->save()) {
                            $postData[$k]['import_id'] = $SynergyTTAPI->id;
                            $dataUpdate[$key]['success']++;
                                get_file_update($key,'success',$dataUpdate[$key]['success']);
                        }
                    }else{
                        
                        $SynergyTTAPI = new SynergyTTAPI();
                        $SynergyTTAPI->datasource = @$v['data_source'];
                        $SynergyTTAPI->vendor_lead_code = $newVendorLeadCode;
                        $SynergyTTAPI->phone_number = @$phone;
                        $SynergyTTAPI->title = @$v['title'];
                        $SynergyTTAPI->first_name = @$v['first_name'];
                        $SynergyTTAPI->last_name = @$v['last_name'];
                        $SynergyTTAPI->address_1 = @$v['address_1'];
                        $SynergyTTAPI->address_2 = NULL;
                        $SynergyTTAPI->address_3 = NULL;
                        $SynergyTTAPI->city = @$v['city'];
                        $SynergyTTAPI->postal_code = @$v['post_code'];
                        $SynergyTTAPI->email = @$v['email'];
                        $SynergyTTAPI->secruity_phrase = @$v['security_phrase'];
                        $SynergyTTAPI->duplicate = 'yes';
                        $SynergyTTAPI->file_import_log_id = $key;
                        if(!empty($dataExistDialer) && $dataExistDialer > 0){
                        $dataListIds  = DB::connection('OmniDialer')
                                            ->table('list')
                                            ->whereIn('list_id',$listIdSrch)
                                            ->where('phone_number',$phone)
                                            ->pluck('list_id')->toArray();
                       
                        if(!empty($dataListIds)){
                            $SynergyTTAPI->duplicate_list_id = implode(',',$dataListIds);
                        }
                        }
                        if($SynergyTTAPI->save()){
                            get_file_update($key,'duplicate',$dataUpdate[$key]['duplicate']);
                            $dataUpdate[$key]['duplicate']++;
                        }
                    }
                }
                
                $postData1 = [];
                $postData1['token'] = $token;
                $postData1['customers'] = $postData;
                
                $dataPostOnAPIResponse = get_omni_api_curl_test($user, $pass, $token, $postData1);
                
                $FileImportLogUpdate->total =  $dataUpdate[$key]['total'];
                $FileImportLogUpdate->duplicate = $dataUpdate[$key]['duplicate'];
                $FileImportLogUpdate->success =  $dataUpdate[$key]['success'];
                $FileImportLogUpdate->datasource =  implode(',',$dataUpdate[$key]['datasource']);
                $FileImportLogUpdate->response_error = (!empty($errorFile) && count($errorFile) > 0) ? serialize($errorFile) : '';
                $FileImportLogUpdate->fail = 'no';
                $FileImportLogUpdate->api_response = (!empty($dataPostOnAPIResponse)) ? serialize($dataPostOnAPIResponse) : '';
                if($FileImportLogUpdate->save()){
                    get_response_update($dataPostOnAPIResponse);
                }
            }
        } else {
            echo "connection failed\n";
        }
        
        
        $arrayMailTo = ['diallersupport@intelling.co.uk'];
        $arrayMailCC = ['akumar@usethegeeks.com','joash@synergycontactcentre.com','annie.seisay@intelling.co.uk','dillan@synergycontactcentre.com','adrian@synergycontactcentre.com','joechem@synergycontactcentre.com','connor@synergycontactcentre.com','terence@synergycontactcentre.com','veekash@synergycontactcentre.com'];

        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.logs.synergy_o2';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : $arrayMailCC; 
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'SynergyTalkTalk data uploaded - ' . date('Y-m-d');
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
