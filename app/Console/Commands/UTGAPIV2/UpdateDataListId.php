<?php

namespace App\Console\Commands\UTGAPIV2;

use Illuminate\Console\Command;
use App\Model\UTGAPI\FileImportData;
use App\Model\UTGAPI\FileImportLog;
use DB;

class UpdateDataListId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'utgapiv2:updatedatalistid {--id=} {--filetype=} {--date=} {--code=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update list id of file_import_data table with daily limit.
                              Optional params --id=1 for particular file --filetype=P2P-SMARTPHONE-UNICA , --date=2020-01-01 for file date --code=A******
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
      ini_set('max_execution_time', 6000);
      ini_set('memory_limit', '2048M');
      $id = $this->option('id');
      $filetype = $this->option('filetype');
      $code = $this->option('code');
      $date = $this->option('date');
      $files = FileImportLog::orderBy('id', 'desc');
      if($id && is_numeric($id)) {
        $files->where('id', $id);
      }else {
        $files->where('is_transfer', 1);
      }
      if($filetype) {
        $files->where('type', $filetype);
      }
      if($date) {
        $dateFileGet =  date('Ymd', strtotime($date));
        $files->where('filename', 'like', "%$dateFileGet%");
      } else {
        $log_addcon_file = FileImportLog::selectRaw('*, SUBSTR(filename, 9, 8) AS DateOrder');
        if($filetype) {
          $log_addcon_file->where('type', $filetype);
        }
        $log_addcon_file = $log_addcon_file->orderBy('DateOrder','desc')->first();
        $files->where('filename', 'like', "%$log_addcon_file->DateOrder%");
      }
      if($code) {
        $files->where('filename', 'like', "%$code%");
      }
      $dataFiles = $files->get();

      if($dataFiles){
        $loopFilesType =  [
            'P2P-SMARTPHONE-UNICA' => [
                'list_ids' => ['4010','40101'],
                'data_source' => ['O2_P2P_MOBILE', 'O2_P2P_MOBILE_RECYCLED']
            ],
            'P2P-CHURN-UNICA' => [
              'list_ids' => ['3045','30451'],
              'data_source' => ['O2_P2P_CHURN', 'O2_P2P_CHURN_RECYCLED']
            ],
            'P2P-CORE-UNICA' => [
              'list_ids' => ['3001','30011'],
              'data_source' => ['O2_PRETOPOST', 'O2_PRETOPOST_RECYCLED']
            ],
            'P2P-ADDCON-UNICA' => [
              'list_ids' => ['3005','30051'],
              'data_source' => ['O2_ADDCONS', 'O2_ADDCONS_RECYCLED']
            ],
            'O2UNICA' => [
              'list_ids' => [],
            ]
        ];
        $ArrayCountUpdate = [];
        $FileCodeArray = [];
        $FileCodeArray['A001114912'] = ['Tarrif'=>['CLASSICPAYG'],'Datasource'=>'O2_P2PADDITIONAL_CLASSIC','List'=>1330,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_CLASSIC_REC','Duplicate_List'=>13302];
        $FileCodeArray['A001114914'] = ['Tarrif'=>['INTERNATIONALSIM'],'Datasource'=>'O2_P2PADDITIONAL_INTERNATIONAL','List'=>1330,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_INTERNATIONAL_REC','Duplicate_List'=>13302];
        $FileCodeArray['A001114916'] = ['Tarrif'=>['CLASSICPAYG','INTERNATIONALSIM'],'Datasource'=>'O2_P2PADDITIONAL','List'=>1330,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_REC','Duplicate_List'=>13302];
        $FileCodeArray['A001114910'] = ['Tarrif'=>['CLASSICPAYG'],'Datasource'=>'O2_P2PADDITIONAL_SUB10_CLASSIC','List'=>13303,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_SUB10_CLASSIC_REC','Duplicate_List'=>13305];
        $FileCodeArray['A001732657'] = ['Tarrif'=>['CLASSICPAYG'],'Datasource'=>'O2_P2PADDITIONAL_SUB10_CLASSIC','List'=>1330,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_SUB10_CLASSIC_REC','Duplicate_List'=>13302];
        $FileCodeArray['A001113235'] = ['Tarrif'=>['INTERNATIONALSIM'],'Datasource'=>'O2_P2PADDITIONAL_SUB10_INTERNATIONAL','List'=>13303,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_SUB10_INTERNATIONAL_REC','Duplicate_List'=>13305];
        $FileCodeArray['A001114899'] = ['Tarrif'=>['CLASSICPAYG','INTERNATIONALSIM'],'Datasource'=>'O2_P2PADDITIONAL_SUB10','List'=>13303,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_SUB10_REC','Duplicate_List'=>13305];
        $FileCodeArray['A001376530'] = ['Tarrif'=>['CLASSICPAYG','INTERNATIONALSIM'],'Datasource'=>'O2_P2PADDITIONAL_SUB10','List'=>13303,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_SUB10_REC','Duplicate_List'=>13305];
        $FileCodeArray['A001732655'] = ['Tarrif'=>['CLASSICPAYG','INTERNATIONALSIM'],'Datasource'=>'O2_P2PADDITIONAL_SUB10','List'=>13303,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_SUB10_REC','Duplicate_List'=>13305];

        $FileCodeNewArray = [];
        $FileCodeNewArray[1330]['CLASSICPAYG'] = ['Datasource'=>'O2_P2PADDITIONAL_CLASSIC','List'=>1330,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_CLASSIC_REC','Duplicate_List'=>13302];
        $FileCodeNewArray[1330]['INTERNATIONALSIM'] = ['Datasource'=>'O2_P2PADDITIONAL_INTERNATIONAL','List'=>1330,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_INTERNATIONAL_REC','Duplicate_List'=>13302];
        $FileCodeNewArray[1330]['CLASSICPAYG-INTERNATIONALSIM'] = ['Datasource'=>'O2_P2PADDITIONAL','List'=>1330,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_REC','Duplicate_List'=>13302];

        $FileCodeNewArray[13303]['CLASSICPAYG'] = ['Datasource'=>'O2_P2PADDITIONAL_SUB10_CLASSIC','List'=>13303,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_SUB10_REC','Duplicate_List'=>13305];
        $FileCodeNewArray[13303]['INTERNATIONALSIM'] = ['Datasource'=>'O2_P2PADDITIONAL_SUB10_INTERNATIONAL','List'=>13303,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_SUB10_INTERNATIONAL_REC','Duplicate_List'=>13305];
        $FileCodeNewArray[13303]['CLASSICPAYG-INTERNATIONALSIM'] = ['Datasource'=>'O2_P2PADDITIONAL_SUB10','List'=>13303,'Duplicate_Datasource'=>'O2_P2PADDITIONAL_SUB10_REC','Duplicate_List'=>13305];

        $Count_International_Classic = [];
        $Count_International_Classic['Classic'] = 0;
        $Count_International_Classic['International'] = 0;

        $yearDates = [date('Y-m-d H:i:s', strtotime('-1 year')), date('Y-m-d H:i:s')];
        foreach($dataFiles as $logFile){
          if($logFile->type == 'P2P-CHURN-UNICA') {
            $dialer = 'MainDialer';
          } else {
            $dialer = 'OmniDialer';
          }
          if($logFile->type == 'O2UNICA') {
            $FileName = explode('_',$logFile->filename);
            if(empty($FileName[8])){
               continue;
            }
            $FileCode = $FileName[8];
            $ArrayCountUpdate[$FileCode]['Useable'] = 0 ;
            $ArrayCountUpdate[$FileCode]['NonUseable'] = 0 ;
            $ArrayCountUpdate[$FileCode]['Loaded'] = 0 ;
            $ArrayCountUpdate[$FileCode]['Recycled'] = 0 ;

            if(empty($FileCodeArray[$FileCode])){
              continue;
            }
            $CodeArray = $FileCodeArray[$FileCode];
            $FileCodeNewArrayResult = $FileCodeNewArray[$CodeArray['List']];
            $datas = FileImportData::where('list_id', 0)->where('file_type', $logFile->type)->where('import_file_id', $logFile->id)->take($logFile->daily_limit)->get();
            $logType = $loopFilesType[$logFile->type];
            utgapilog($logFile->type."Data Update Start - ".$logFile->id.' with limit -'.$logFile->daily_limit);
            if($datas){
              foreach($datas as $row){

                $DuplicateStatus  = 'yes';
                $data = json_decode($row->data, true);

                if(empty($data[35])){
                  $datasource = $FileCodeNewArrayResult['CLASSICPAYG-INTERNATIONALSIM']['Datasource'];
                  $ListID = $CodeArray['List'];

                  $DuplicateListID = $FileCodeNewArrayResult['CLASSICPAYG-INTERNATIONALSIM']['Duplicate_List'];
                  $DuplicateDatasource = $FileCodeNewArrayResult['CLASSICPAYG-INTERNATIONALSIM']['Duplicate_Datasource'];
                }else{
                  if($data[35] == 'CLASSICPAYG'){
                      $datasource = $FileCodeNewArrayResult['CLASSICPAYG']['Datasource'];
                      $ListID = $CodeArray['List'];
                      $DuplicateListID = $FileCodeNewArrayResult['CLASSICPAYG']['Duplicate_List'];
                      $DuplicateDatasource = $FileCodeNewArrayResult['CLASSICPAYG']['Duplicate_Datasource'];
                  }elseif($data[35] == 'INTERNATIONALSIM'){
                      $datasource = $FileCodeNewArrayResult['INTERNATIONALSIM']['Datasource'];
                      $ListID = $CodeArray['List'];

                      $DuplicateListID = $FileCodeNewArrayResult['INTERNATIONALSIM']['Duplicate_List'];
                      $DuplicateDatasource = $FileCodeNewArrayResult['INTERNATIONALSIM']['Duplicate_Datasource'];
                  }else{
                      $datasource = $FileCodeNewArrayResult['CLASSICPAYG-INTERNATIONALSIM']['Datasource'];
                      $ListID = $CodeArray['List'];

                      $DuplicateListID = $FileCodeNewArrayResult['CLASSICPAYG-INTERNATIONALSIM']['Duplicate_List'];
                      $DuplicateDatasource = $FileCodeNewArrayResult['CLASSICPAYG-INTERNATIONALSIM']['Duplicate_Datasource'];
                  }
                }

                $PhoneNumber = get_phone_numbers($data[0], '0');

                $DialerExist = DB::connection($dialer)->table('list')->where('phone_number',$PhoneNumber)->whereBetween('entry_date', $yearDates)->where('list_id',$ListID)->count();
                $duplicateStatus = 'no';
                if($DialerExist > 0){
                    $duplicateStatus = 'yes';
                    $datasource = $DuplicateDatasource;
                    $listID = $DuplicateListID;
                    $ArrayCountUpdate[$FileCode]['Recycled']++;
                    $DataSourceArray[$CodeArray['Duplicate_Datasource']] = $CodeArray['Duplicate_Datasource'];
                }else{
                  $DataExistListArchive = DB::connection($dialer)
                    ->table('list_archive')
                    ->where('list_id', $ListID)
                    ->whereBetween('entry_date', $yearDates)
                    ->where('phone_number',$PhoneNumber)
                    ->count();

                  if($DataExistListArchive > 0){
                    $duplicateStatus = 'yes';
                    $datasource = $DuplicateDatasource;
                    $listID = $DuplicateListID;
                    $ArrayCountUpdate[$FileCode]['Recycled']++;
                    $DataSourceArray[$CodeArray['Duplicate_Datasource']] = $CodeArray['Duplicate_Datasource'];
                  }else{
                    $DataSourceArray[$CodeArray['Datasource']] = $CodeArray['Datasource'];
                    $ArrayCountUpdate[$FileCode]['Loaded']++;
                  }
                }
                if($data[35] == 'CLASSICPAYG'){
                  $Count_International_Classic['Classic']++;
                }elseif($data[35] == 'INTERNATIONALSIM'){
                  $Count_International_Classic['International']++;
                }
                $row->list_id = $ListID;
                $row->datasource  = $datasource;
                $row->duplicate = $duplicateStatus;
                $row->save();
              }
              utgapilog("Data Updated");
            }
          } else {
            $datas = FileImportData::where('list_id', 0)->where('file_type', $logFile->type)->where('import_file_id', $logFile->id)->take($logFile->daily_limit)->get();
            $logType = $loopFilesType[$logFile->type];
            utgapilog($logFile->type."Data Update Start - ".$logFile->id.' with limit -'.$logFile->daily_limit);
            if($datas){
                foreach($datas as $row){

                  $DuplicateStatus  = 'yes';
                  $DataSource       = $logType['data_source'][1];
                  $ListdID          = $logType['list_ids'][1];
                  $data = json_decode($row->data, true);

                  $PhoneNumber = get_phone_numbers($data[0], '0');

                  $DataExist = DB::connection($dialer)
                          ->table('list')
                          ->whereIn('list_id', $logType['list_ids'])
                          ->whereBetween('entry_date', $yearDates)
                          ->where('phone_number', $PhoneNumber)
                          ->count();
                    if ($DataExist == 0) {
                        $DataExistListArchive = DB::connection($dialer)
                        ->table('list_archive')
                        ->whereIn('list_id', $logType['list_ids'])
                        ->whereBetween('entry_date', $yearDates)
                        ->where('phone_number', $PhoneNumber)
                        ->count();
                        if($DataExistListArchive == 0){
                               $DuplicateStatus = 'no';
                               $DataSource       = $logType['data_source'][0];
                               $ListdID          = $logType['list_ids'][0];
                        }
                    }
                    $row->list_id = $ListdID;
                    $row->datasource  = $DataSource;
                    $row->duplicate = $DuplicateStatus;
                    $row->save();
                }
                utgapilog("Data Updated");
            }
          }
        }
      }else{
          utgapilog("No Data file");
      }
    }
}
