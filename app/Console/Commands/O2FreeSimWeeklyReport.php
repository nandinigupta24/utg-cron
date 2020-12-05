<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\ScriptResponse;
use App\Model\SwitchExpertEnergy\SDSales;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;
use App\Model\UTGAPI\O2FreeSimLoadedRecord;
use App\Model\UTGAPI\O2FreeSimFileImport;

class O2FreeSimWeeklyReport extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:O2FreeSimWeeklyReport';

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
        
//        $now = Carbon::now();
//        $weekStartDate = $now->startOfWeek()->format('Y-m-d H:i:s');
//        $weekEndDate = $now->endOfWeek()->format('Y-m-d H:i:s');
//        
//        $WeekDateArray = [];
//        $WeekDateArray['Start'] = $now->startOfWeek()->format('Y-m-d');
//        $WeekDateArray['End'] = $now->endOfWeek()->format('Y-m-d');
            $date = date('Y-m-d');
            $weekStartDate = date('Y-m-d',strtotime('last Sunday', strtotime($date)));
            $weekEndDate = date('Y-m-d',strtotime('last Saturday', strtotime($date)));

            $WeekDateArray = [];
            $WeekDateArray['Start'] = $weekStartDate;
            $WeekDateArray['End'] = $weekEndDate;

//        $weekStartDate = '2019-04-14';
//        $weekEndDate = '2019-04-20';
//        
//        $WeekDateArray = [];
//        $WeekDateArray['Start'] = '2019-04-14';
//        $WeekDateArray['End'] = '2019-04-20';
        
        $data = \App\Model\UTGAPI\O2FreeSimFileImport::where('created_at','>=',$weekStartDate.' 00:00:00')
                ->where('created_at','<=',$weekEndDate.' 23:59:59')
                ->get()
                ->toArray();
       
        $arrayMailTo = ['Jason.Hearne@telefonica.com','Richard.Palmer@telefonica.com','dialerteam@usethegeeks.zendesk.com'];
        
        $result = Mail::send('emails.O2FREESIM_WEEKLY', ['data' => $data], function ($m) use($WeekDateArray,$arrayMailTo){
                    $m->from('intellingreports@intelling.co.uk','API Reports');
                    $m->cc(['akumar@usethegeeks.com','apanwar@usethegeeks.co.uk']);
                    $m->replyTo('intellingreports@intelling.co.uk', 'API Reports');
                    $m->to($arrayMailTo)
                            ->subject('O2FreeSim Leads ('.$WeekDateArray['Start'].' to '.$WeekDateArray['End'].')');
                });
    }

}
