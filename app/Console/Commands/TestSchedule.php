<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;
use Log;
use App\Model\Intelling\O2DataFileLogs;
use App\Model\Intelling\O2Data;

class TestSchedule extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:TestSchedule';

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
        ini_set('memory_limit', '2048M');
        
        $mail_data = array();
        $mail_data['to'] = ['apanwar@usethegeeks.co.uk'];
        $mail_data['msg'] = 'Hi';
        $mail_data['view'] = 'emails.email';
        $mail_data['subject'] = 'Test Schedule';

        Mail::send($mail_data['view'], ['data' => $mail_data], function ($m) use ($mail_data) {
            $m->replyTo('intellingreports@intelling.co.uk', 'API Reports');
            $m->to($mail_data['to'])->subject($mail_data['subject']);
        });
    }

}
