<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;
use App\Model\Intelling\O2Data;
use App\Model\Intelling\O2DataFileLogs;
use App\Model\Intelling\O2OldData;
use App\Model\Intelling\WebSetting;

class O2FreeSimSFTPTest extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:O2FreeSimSFTPTest';

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
        ini_set('max_execution_time', 60000);
        ini_set('memory_limit', '2048M');
        $FileLogId = 1670;
        $WebSettingData = WebSetting::find(3);
        $datWebSettings = $WebSettingData->value;
        $data = O2OldData::skip($datWebSettings)->take(5000)->get()->toArray();
     
        $CountNewArray = 1;
        foreach ($data as $value) {
            echo PHP_EOL .'-- '.$CountNewArray++.' Record of 5000';
            O2DataFileLogs::where('id', $FileLogId)->update(['total' => DB::raw('total+1')]);

            if (empty($value['telephone'])) {
                O2DataFileLogs::where('id', $FileLogId)->update(['fail' => DB::raw('fail+1')]);
                continue;
            }
            $CountExist = O2Data::where('Telephone', $value['telephone'])->count();
            if ($CountExist > 0) {
                O2DataFileLogs::where('id', $FileLogId)->update(['fail' => DB::raw('fail+1')]);
                continue;
            }
            
            $O2Data = new O2Data();
            $O2Data->file_log_id = $FileLogId;
            $O2Data->Reference = $value['field2'];
            $O2Data->Title = $value['field1'];
            $O2Data->Firstname = $value['field3'];
            $O2Data->Surname = $value['field4'];
            $O2Data->Address1 = $value['field5'];
            $O2Data->Address2 = $value['field6'];
            $O2Data->Postcode = $value['field9'];
            $O2Data->email = $value['field11'];
            $O2Data->Postcode = $value['field9'];
            $O2Data->call_start = date('Y-m-d H:i:s',strtotime($value['call_start']));
            $O2Data->Telephone = $value['telephone'];
            $O2Data->save_full_data = serialize($value);
            if ($O2Data->save()) {
                O2DataFileLogs::where('id', $FileLogId)->update(['success' => DB::raw('success+1')]);
            }
        }
        
        WebSetting::where('id',3)->update(['value' => DB::raw('value+5000')]);
    }

}
