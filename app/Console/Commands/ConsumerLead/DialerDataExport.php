<?php

namespace App\Console\Commands\ConsumerLead;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;
use App\Model\ConsumerMaster\ConsumerLead;

class DialerDataExport extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:DialerDataExport';

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
        ini_set('max_execution_time ', '30000');
        ini_set('memory_limit', '2048M');
        
        $start = Carbon::now()->subDays(1)->startOfDay();
        $end = Carbon::now()->subDays(1)->endOfDay();
        
        $dialer = ['OmniDialer' => 'omni_dialer', 'PrisionDialer' => 'avtar_dialer','NewDialer'=>'new_dialer'];
        $Count = 1;
        foreach ($dialer as $key => $val) {
            $data = DB::connection($key)
                    ->table('list')
                    ->where('entry_date', '>=', $start)
                    ->where('entry_date', '<=', $end)
                    ->orderBy('entry_date', 'ASC')
                    ->get();

            foreach ($data as $value) {
                $array = [];
                $array['lead_id'] = (!empty($value->lead_id)) ? @$value->lead_id : NULL;
                $array['entry_date'] = @$value->entry_date;
                $array['modify_date'] = @$value->modify_date;
                $array['status'] = (!empty($value->status)) ? @$value->status : NULL;
                $array['user'] = (!empty($value->user)) ? @$value->user : NULL;
                $array['vendor_lead_code'] = (!empty($value->vendor_lead_code)) ? @$value->vendor_lead_code : NULL;
                $array['source_id'] = (!empty($value->source_id)) ? @$value->source_id : NULL;
                $array['list_id'] = (!empty($value->list_id)) ? @$value->list_id : NULL;
                $array['gmt_offset_w'] = (!empty($value->gmt_offset_w)) ? @$value->gmt_offset_w : NULL;
                $array['called_since_last_reset'] = (!empty($value->called_since_last_reset)) ? @$value->called_since_last_reset : NULL;
                $array['phone_code'] = (!empty($value->phone_code)) ? @$value->phone_code : NULL;
                $array['phone_number'] = (!empty($value->phone_number)) ? @$value->phone_number : NULL;
                $array['title'] = (!empty($value->title)) ? @$value->title : NULL;
                $array['first_name'] = (!empty($value->first_name)) ? @$value->first_name : NULL;
                $array['middle_initial'] = (!empty($value->middle_initial)) ? @$value->middle_initial : NULL;
                $array['last_name'] = (!empty($value->last_name)) ? @$value->last_name : NULL;
                $array['address1'] = (!empty($value->address1)) ? @$value->address1 : NULL;
                $array['address2'] = (!empty($value->address2)) ? @$value->address2 : NULL;
                $array['address3'] = (!empty($value->address3)) ? @$value->address3 : NULL;
                $array['city'] = (!empty($value->city)) ? @$value->city : NULL;
                $array['state'] = (!empty($value->state)) ? @$value->state : NULL;
                $array['province'] = (!empty($value->province)) ? @$value->province : NULL;
                $array['postal_code'] = (!empty($value->postal_code)) ? @$value->postal_code : NULL;
                $array['country_code'] = (!empty($value->country_code)) ? @$value->country_code : NULL;
                $array['gender'] = (!empty($value->gender)) ? @$value->gender : NULL;
                $array['date_of_birth'] = (!empty($value->date_of_birth) && $value->date_of_birth != '0000-00-00') ? @$value->date_of_birth : NULL;
                $array['alt_phone'] = (!empty($value->alt_phone)) ? @$value->alt_phone : NULL;
                $array['email'] = (!empty($value->email)) ? @$value->email : NULL;
                $array['security_phrase'] = (!empty($value->security_phrase)) ? @$value->security_phrase : NULL;
                $array['comments'] = (!empty($value->comments)) ? @$value->comments : NULL;
                $array['called_count'] = (!empty($value->called_count)) ? @$value->called_count : NULL;
                $array['last_local_call_time'] = (!empty($value->last_local_call_time)) ? @$value->last_local_call_time : NULL;
                $array['rank'] = (!empty($value->rank)) ? @$value->rank : NULL;
                $array['owner'] = (!empty($value->owner)) ? @$value->owner : NULL;
                $array['entry_list_id'] = (!empty($value->entry_list_id)) ? @$value->entry_list_id : NULL;
                $array['best_time_to_call'] = (!empty($value->best_time_to_call)) ? @$value->best_time_to_call : NULL;
                $array['sms_count'] = (!empty($value->sms_count)) ? @$value->sms_count : NULL;
                $array['assigned_data_cli'] = (!empty($value->assigned_data_cli)) ? @$value->assigned_data_cli : NULL;
                $array['date_inserted'] = (!empty($value->date_inserted)) ? @$value->date_inserted : NULL;
                $array['email_count'] = (!empty($value->email_count)) ? @$value->email_count : NULL;
                $array['days_old'] = (!empty($value->days_old)) ? @$value->days_old : NULL;
                $array['dialer_name'] = $val;
                ConsumerLead::insert($array);
//                 echo PHP_EOL.'Finished -- '.$Count++;
            }
        }
    }

}
