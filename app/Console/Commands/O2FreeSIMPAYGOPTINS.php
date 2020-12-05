<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Mail;
use Excel;

class O2FreeSIMPAYGOPTINS extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:O2FreeSIMPAYGOPTINS';

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

        $startDate = Carbon::now()->subDays(1)->toDateString() . " 00:00:00";
        $endDate = Carbon::now()->subDays(1)->toDateString() . " 23:59:59";

        $arrayChron = array();
        $arrayChron['title'] = 'O2FreeSIMPAYGOPTINS';
        $arrayChron['running_time'] = date('Y-m-d H:i:s');
        $arrayChron['start'] = $startDate;
        $arrayChron['end'] = $endDate;
        DB::connection('Intelling')->table('chron_log')->insert($arrayChron);
        $myModal = DB::connection('O2Combine')->table('O2Sales')
                ->where('saletime', '>=', $startDate);
        $myModal->where('saletime', '<=', $endDate);
        $myModal->where('campaign_name', '3002');
        $myModal->where(function($query) {
            $query->where('submit_payg_preorder', '<>', '')
                    ->orWhere('new_payg_bundle_request', '<>', '')
                    ->orWhere('ishappy_payg_bundle_order', '<>', '')
                    ->orWhere('payg_bundle_order', '<>', '');
        });

        $dataEx = $myModal->get();
//        if ($request->query('export') && in_array($request->query('export'), ['xlsx', 'xls', 'csv', 'pdf'])) {
        $file = Excel::create('O2FreeSIMPAYGOPTINS', function($excel) use($dataEx) {
                    $excel->setTitle('O2FreeSIMPAYGOPTINS');
                    $excel->sheet('O2FreeSIMPAYGOPTINS', function($sheet) use($dataEx) {
                        $datasheet[] = ['Date of Sale', 'Time of Sale', 'Agent Name', 'Payg Bundle Order', 'Ishappy Payg Bundle Order', 'New Payg Bundle Request', 'Submit Payg Preorder', 'Customer Name', 'Customer Adress', 'Telephone', 'Email'];

                        $sheet->setOrientation('landscape');

                        foreach ($dataEx as $key1 => $value1) {

                            $datasheet[] = [
                                'id1' => date("Y-m-d", strtotime($value1->saletime)),
                                'id2' => date("h:i:sa", strtotime($value1->saletime)),
                                'id3' => (@$value1->agent->full_name) ? @$value1->agent->full_name : '',
                                'id4' => @$value1->payg_bundle_order,
                                'id5' => @$value1->ishappy_payg_bundle_order,
                                'id6' => @$value1->new_payg_bundle_request,
                                'id7' => @$value1->submit_payg_preorder,
                                'id8' => @$value1->first_name . ' ' . @$value1->last_name,
                                'id9' => @$value1->address1 . ',' . @$value1->address2 . ',' . @$value1->address3,
                                'id10' => @$value1->phone_number,
                                'id11' => @$value1->email
                            ];
                        }
                        $sheet->fromArray($datasheet);
                    });
                });
//                'diallerteam@intelling.co.uk'
        $arrayMailTo = [env('DIALER_TEAM_EMAIL'), 'datasupport@intelling.co.uk'];
        $mail_data = array();
        $mail_data['to'] = $arrayMailTo;
        $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
        $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
        $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.email';
        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : 'akumar@usethegeeks.com';
        $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'O2 PAYG Free Sims';
        $mail_data['pdf_type'] = !empty($data['pdf_type']) ? $data['pdf_type'] : "Pdf Attachment";
        $mail_data['pdf_attachment'] = !empty($data['pdf_attachment']) ? $data['pdf_attachment'] : null;


        $result = Mail::send($mail_data['view'], $mail_data, function ($m) use ($mail_data, $file) {
                    $m->from($mail_data['from'], 'Intelling');
                    if (!empty($mail_data['cc'])) {
                        $m->cc($mail_data['cc']);
                    }
                    $m->replyTo('intellingreports@intelling.co.uk', 'Intelling');
                    $m->to($mail_data['to'])->subject($mail_data['subject']);
                    $m->attach($file->store("xls", false, true)['full']);
                    if (!empty($mail_data['pdf_attachment']) && isset($mail_data['pdf_attachment'])) {
                        if (is_array($mail_data['pdf_attachment'])) {
                            foreach ($mail_data['pdf_attachment'] as $key => $pdf_attachment) {
                                $ext = '';
                                $ext = strrchr($pdf_attachment, '.');
                                $ext = trim($ext, '.');
                                $m->attach($pdf_attachment, ['as' => array_get($mail_data['pdf_type'], $key, ''), 'mime' => array_get(unserialize(MIME_FORMAT), $ext, 'application/pdf')]);
                            }
                        } else {
                            $ext = '';
                            $ext = strrchr($mail_data['pdf_attachment'], '.');
                            $ext = trim($ext, '.');
                            $m->attach($file->store("xls", false, true)['full']);
                        }
                    }
                });
        unset($_GET['mail']);
        /* End Mail */
    }

}
