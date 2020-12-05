<?php

namespace App\Console\Commands\Hourly;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;

class CampaignReportMainGraph extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CampaignReportMainGraph';

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

        $lastHour = Carbon::now()->subHour(1)->format('H');

        $start = date('Y-m-d') . ' 00:00:00';
        $end = date('Y-m-d') . ' ' . $lastHour . ':59:59';

        if (date('H') > 20) {
            die('BYE');
        }

        if (date('H') >= 0 && date('H') < 10) {
            die('BYE');
        }
        $dynamicNumber = date('Y-m-d').'-'.$lastHour . '-00-00';
        
        $HideCampaign = [1404, 3027, 1308, 3008];
//        $MainDialer = get_graph_values_full_dialer_value('MainDialer', $start, $end, $HideCampaign);
        $MainDialer = get_hourly_report_campaign_graph($start, $end,'MainDialer',$HideCampaign);

        foreach ($MainDialer as $key => $dialer) {
            $url = 'http://export.highcharts.com/';
            $dataSend = array(
                'chart' => array('zoomType' => 'xy'),
                'title' => array('text' => @$MainDialer[$key]['CN']),
                'xAxis' => array(
                    'categories' => @$MainDialer[$key]['Hour'],
                    'title' => array('text' => 'Hour'),
                    'crosshair' => true,
                ),
                'yAxis' => array(
                    array(
                        'min' => 0,
                        'tickInterval' => 10,
                        'title' => array('text' => 'CONNECT / DMC RATE %')
                    ),
                    array(
                        'title' => array('text' => "DMC'S PER HEAD")
                    )
                ),
                'series' => array(
                    0 => array(
                        'name' => "DMC's Delivered (Productive Time)",
                        'data' => @$MainDialer[$key]['DMCsProductive'],
                        'type' => 'column',
                        'yAxis' => 1,
                    ),
                    1 => array(
                        'name' => 'Connect Rate %',
                        'data' => @$MainDialer[$key]['ConnectRate'],
                        'type' => 'spline'
                    ),
                    2 => array(
                        'name' => 'DMC Rate %',
                        'data' => @$MainDialer[$key]['DMCRate'],
                        'type' => 'spline'
                    )
                )
            );
            $dataPost = 'async=true&type=jpeg&width=400&options=' . json_encode($dataSend);

            $options = array(
                'http' => array(
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => $dataPost,
            ));

            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            $imageSLA = get_image_path_chart('http://export.highcharts.com/' . $result, $key . '-' . $dynamicNumber, '/Hourly/CampaignReport/Main/');
        }
    }

}
