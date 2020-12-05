<?php

namespace App\Console\Commands\UTGAPIV2;

use Illuminate\Console\Command;
use App\Model\UTGAPI\FileImportLog;
use App\Model\UTGAPI\FileImportData;
use Mail;
use DB;

class ImportFileData extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'utgapiv2:importfiledata {--id=} {--filetype=} {--date=} {--code=}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Import data from files to data tables.
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
          $files->where('is_transfer', 0);
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
        $loopFilesType =  [
          'P2P-SMARTPHONE-UNICA' => [
              'days' => '30'
          ],
          'P2P-CHURN-UNICA' => [
            'days' => '10'
          ],
          'P2P-CORE-UNICA' => [
            'days' => '10'
          ],
          'P2P-ADDCON-UNICA' => [
            'days' => '10'
          ],
          'O2UNICA' => [
            'days' => '10'
          ]
      ];
        if($dataFiles){

            $LocalImportDirectory = '/var/www/html/cron/storage/Automation/O2UNICA/';
            foreach($dataFiles as $logFile) {
                utgapilog("File Import started ID - ".$logFile->id);
                $File = $LocalImportDirectory . 'OUT/' . $logFile->filename . '.csv';
                if(!file_exists($File)) continue;

                if(!$logFile->total) {
                    $totalRows = count(file($File));
                    $logFile->total = $totalRows;
                    $logFile->save();
                    $fileDate = substr($logFile->filename, 0, 16);
                    $otherFilesOnSameDay = FileImportLog::where('filename', 'like', "$fileDate%")->where('type', $logFile->type)->get();
                    $totalRecordsOnDay = 0;
                    foreach ($otherFilesOnSameDay as $fileOnDay) {
                        $outFile = $LocalImportDirectory . 'OUT/' . $fileOnDay->filename . '.csv';
                        if(file_exists($outFile)) {
                          $totalRows = count(file($outFile));
                          $totalRecordsOnDay += $totalRows;
                          $fileOnDay->total = $totalRows;
                          $fileOnDay->save();
                        }
                    }
                    if($totalRecordsOnDay>0){
                      $perDayTransferLimit = $totalRecordsOnDay/$loopFilesType[$logFile->type]['days'];
                      foreach ($otherFilesOnSameDay as $fileOnDay) {
                          $outFile = $LocalImportDirectory . 'OUT/' . $fileOnDay->filename . '.csv';
                          if(file_exists($outFile)) {
                            $fileOnDay->daily_limit = ceil((($fileOnDay->total/$totalRecordsOnDay)*$perDayTransferLimit));
                            $fileOnDay->save();
                          }
                      }
                    }
                }

                $handle = fopen($File, "r");
                if (empty($handle) === false) {
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                      if($data && is_array($data) && count($data)>0){
                        $FileImportData = new FileImportData();
                        $FileImportData->file_type = $logFile->type;
                        $FileImportData->import_file_id = $logFile->id;
                        $FileImportData->data = json_encode($data);
                        $FileImportData->save();
                      }
                    }
                }
                $logFile->is_transfer = 1;
                $logFile->save();
                utgapilog("File Import Ended ID - ".$logFile->id);
            }
        }else {
          utgapilog("No File found");
        }
    }
}
