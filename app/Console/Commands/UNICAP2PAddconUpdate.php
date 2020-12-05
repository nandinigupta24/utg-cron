<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\UTGAPI\UNICAP2PAddcon;
use DB;

class UNICAP2PAddconUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:UNICAP2PAddconUpdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update list id and datasource in table UNICAP2PAddconUpdate';

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
      $start = time();
      echo 'Start - '.$start.'\n';
      $dialer = 'OmniDialer';
      $CampaignID = 3005;
      $CampaignListID = DB::connection($dialer)->table('lists')->where('campaign_id', $CampaignID)->pluck('list_id');
      $results = UNICAP2PAddcon::select('id','PLUS44', 'datasource', 'duplicate_status', 'list_id')->where('list_id',0)->take(100)->get();
      if($results){
        foreach($results as $row){
          $DuplicateStatus  = 'yes';
          $DataSource       = 'O2_ADDCONS_RECYCLED';
          $ListdID          = 30051;
          $PhoneNumber = $row->PLUS44;
          $DataExist = DB::connection($dialer)
                  ->table('list')
                  ->whereIn('list_id', $CampaignListID)
                  ->where('phone_number', $PhoneNumber)
                  ->count();
          if ($DataExist == 0) {
              $DataExistListArchive = DB::connection($dialer)->table('list_archive')->whereIn('list_id', $CampaignListID)->where('phone_number',$PhoneNumber)->count();
              if($DataExistListArchive == 0){
                     $DuplicateStatus = 'no';
                     $DataSource = 'O2_ADDCONS';
                     $ListdID = 3005;
              }
          }
          $row->list_id                              = $ListdID;
          $row->datasource                           = $DataSource;
          $row->duplicate_status                     = $DuplicateStatus;
          $row->save();
        }
      }
      echo 'end - '.time().'\n';
      echo "Total Seconds ".(time()-$start);
    }
}
