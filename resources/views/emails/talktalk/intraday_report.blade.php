<html>
    <head>
        <style>
            #container {
                /*                min-width: 310px;
                                max-width: 800px;
                                height: 400px;
                                margin: 0 auto*/
            }
        </style>
    </head>
    <body style="color:#fff;">
        <?php $date = $data['data']['date']; ?>
        <table class="table table-bordered" style="color:#fff;">
            <tr style="background:#136975;">
                <th colspan="28" class="text-center">Hour</th>
            </tr>
            <tr style="background:#136975;">
                <th colspan="2">08</th>
                <th colspan="2">09</th>
                <th colspan="2">10</th>
                <th colspan="2">11</th>
                <th colspan="2">12</th>
                <th colspan="2">13</th>
                <th colspan="2">14</th>
                <th colspan="2">15</th>
                <th colspan="2">16</th>
                <th colspan="2">17</th>
                <th colspan="2">18</th>
                <th colspan="2">19</th>
                <th colspan="2">20</th>
                <th colspan="2">21</th>
            </tr>
            <tr>
                <th colspan="28" class="text-center" style="color:black">Email Channel Calls Received / Abandoned</th>
            </tr>
            <tr style="background:#136975;">
                <?php
                for ($i = 8; $i <= 21; $i++) {
                    ?>

                    <td>Offered</td>
                    <td>Drop</td>
                <?php } ?>
            </tr>
            <tr style="background:#136975;">
                <?php
                $campaign = ['offered'=>'TalkTalkEma','drop'=>'TTEmailOver'];
                for ($i = 8; $i <= 21; $i++) {
                    $start = $i;
                    $end = $i + 1;
                    $dataOutPut = get_intraday_offered_drop_TALKTALK($date . ' ' . $start . ':00:00', $date . ' ' . $end . ':00:00', $campaign, 15);
                    $totalOffered[] = @$dataOutPut['offered'];
                    $totalAbondaned[] = @$dataOutPut['drop'];
                    ?>
                    <td colspan="2">
                        <table class="table table-bordered" width="100%">
                            <tr style="background:#136975;">
                                <td style="color:#fff;" align="center">{{@$dataOutPut['offered']}}</td>
                                <!--<td style="background:#FFC7CE;color:#9C0006;" align="center">{{@$dataOutPut['drop']}}</td>-->
                                <td <?php if(!empty($dataOutPut['drop']) && $dataOutPut['drop'] > 0){?> style="background:#FFC7CE;color:#9C0006;" <?php }?> align="center">{{@$dataOutPut['drop']}}</td>
                            </tr>
                            <tr style="background:#C6EFCE;color:#006100;">
                                <td colspan="2" class="" align="center">{{@$dataOutPut['output']}}%</td>
                            </tr>
                        </table>
                    </td>
                <?php } ?>
            </tr>
            <tr>
                <th colspan="28" class="text-center" style="color:black">
                    Text Channel Calls Received / Abandoned
                </th>
            </tr>
            <tr style="background:#136975;">
                <?php for ($i = 8; $i <= 21; $i++) { ?>
                    <td>Offered</td>
                    <td>Drop</td>
                <?php } ?>
            </tr>
            <tr style="background:#136975;">
                <?php
                $campaign = ['drop'=>'TTSMSOver ','offered'=>'TalkTalkSMS'];
                for ($i = 8; $i <= 21; $i++) {
                    $start = $i;
                    $end = $i + 1;
                    $dataOutPut = get_intraday_offered_drop_TALKTALK($date . ' ' . $start . ':00:00', $date . ' ' . $end . ':00:00',$campaign, 15);
                    $totalOffered[] = @$dataOutPut['offered'];
                    $totalAbondaned[] = @$dataOutPut['drop'];
                    ?>
                    <td colspan="2">
                        <table class="table table-bordered" width="100%">
                            <tr style="background:#136975;">
                                <td style="color:#fff;" align="center">{{@$dataOutPut['offered']}}</td>
                                <td <?php if(!empty($dataOutPut['drop']) && $dataOutPut['drop'] > 0){?> style="background:#FFC7CE;color:#9C0006;" <?php }?> align="center">{{@$dataOutPut['drop']}}</td>
                            </tr>
                            <tr style="background:#C6EFCE;color:#006100;">
                                <td colspan="2" class="text-center" align="center">{{@$dataOutPut['output']}}%</td>
                            </tr>
                        </table>
                    </td>
                <?php } ?>
            </tr>
             <tr>
                <th colspan="28" class="text-center" style="color:black">
                    Bau Channel Calls Received / Abandoned
                </th>
            </tr>
            <tr style="background:#136975;">
                <?php 
                
                for ($i = 8; $i <= 21; $i++) { ?>
                    <td>Offered</td>
                    <td>Drop</td>
                <?php } ?>
            </tr>
            <tr style="background:#136975;">
                <?php
                $campaign = ['offered'=>'TalkTalkBau ','drop'=>'TTBAUOver'];
                for ($i = 8; $i <= 21; $i++) {
                    $start = $i;
                    $end = $i+1;
                    $dataOutPut = get_intraday_offered_drop_TALKTALK($date . ' ' . $start . ':00:00', $date . ' ' . $end . ':00:00',$campaign, 15);
                    $totalOffered[] = @$dataOutPut['offered'];
                    $totalAbondaned[] = @$dataOutPut['drop'];
                    ?>
                    <td colspan="2">
                        <table class="table table-bordered" width="100%">
                            <tr style="background:#136975;">
                                <td style="color:#fff;" align="center">{{@$dataOutPut['offered']}}</td>
                                <td <?php if(!empty($dataOutPut['drop']) && $dataOutPut['drop'] > 0){?> style="background:#FFC7CE;color:#9C0006;" <?php }?> align="center">{{@$dataOutPut['drop']}}</td>
                            </tr>
                            <tr style="background:#C6EFCE;color:#006100;">
                                <td colspan="2" class="text-center" align="center">{{@$dataOutPut['output']}}%</td>
                            </tr>
                        </table>
                    </td>
                <?php } ?>
            </tr>
             <tr>
                <th colspan="28" class="text-center" style="color:black">
                    TalkTalkSt Channel Calls Received / Abandoned
                </th>
            </tr>
            <tr style="background:#136975;">
                <?php 
                
                for ($i = 8; $i <= 21; $i++) { ?>
                    <td>Offered</td>
                    <td>Drop</td>
                <?php } ?>
            </tr>
            <tr style="background:#136975;">
                <?php
                $campaign = ['offered'=>'TalkTalkSto','drop'=>'TalkTalkSto'];
                for ($i = 8; $i <= 21; $i++) {
                    $start = $i;
                    $end = $i+1;
                    $dataOutPut = get_intraday_offered_drop_TALKTALK($date . ' ' . $start . ':00:00', $date . ' ' . $end . ':00:00',$campaign, 15);
                    $totalOffered[] = @$dataOutPut['offered'];
                    $totalAbondaned[] = @$dataOutPut['drop'];
                    ?>
                    <td colspan="2">
                        <table class="table table-bordered" width="100%">
                            <tr style="background:#136975;">
                                <td style="color:#fff;" align="center">{{@$dataOutPut['offered']}}</td>
                                <td <?php if(!empty($dataOutPut['drop']) && $dataOutPut['drop'] > 0){?> style="background:#FFC7CE;color:#9C0006;" <?php }?> align="center">{{@$dataOutPut['drop']}}</td>
                            </tr>
                            <tr style="background:#C6EFCE;color:#006100;">
                                <td colspan="2" class="text-center" align="center">{{@$dataOutPut['output']}}%</td>
                            </tr>
                        </table>
                    </td>
                <?php } ?>
            </tr>
            
            <!--TransferCS******CSTransfer-->
            <tr>
                <th colspan="28" class="text-center" style="color:black">
                    TransferCS Channel Calls Received / Abandoned
                </th>
            </tr>
            <tr style="background:#136975;">
                <?php 
                
                for ($i = 8; $i <= 21; $i++) { ?>
                    <td>Offered</td>
                    <td>Drop</td>
                <?php } ?>
            </tr>
            <tr style="background:#136975;">
                <?php
                $campaign = ['offered'=>'CSTransfer','drop'=>'CSTransfer'];
                for ($i = 8; $i <= 21; $i++) {
                    $start = $i;
                    $end = $i+1;
                    $dataOutPut = get_intraday_offered_drop_TALKTALK($date . ' ' . $start . ':00:00', $date . ' ' . $end . ':00:00',$campaign, 15);
                    $totalOffered[] = @$dataOutPut['offered'];
                    $totalAbondaned[] = @$dataOutPut['drop'];
                    ?>
                    <td colspan="2">
                        <table class="table table-bordered" width="100%">
                            <tr style="background:#136975;">
                                <td style="color:#fff;" align="center">{{@$dataOutPut['offered']}}</td>
                                <td <?php if(!empty($dataOutPut['drop']) && $dataOutPut['drop'] > 0){?> style="background:#FFC7CE;color:#9C0006;" <?php }?> align="center">{{@$dataOutPut['drop']}}</td>
                            </tr>
                            <tr style="background:#C6EFCE;color:#006100;">
                                <td colspan="2" class="text-center" align="center">{{@$dataOutPut['output']}}%</td>
                            </tr>
                        </table>
                    </td>
                <?php } ?>
            </tr>
            <!--TT_Non_Mobi*****TT_Non_Mobi-->
            <tr>
                <th colspan="28" class="text-center" style="color:black">
                    TT_Non_Mobi Channel Calls Received / Abandoned
                </th>
            </tr>
            <tr style="background:#136975;">
                <?php 
                
                for ($i = 8; $i <= 21; $i++) { ?>
                    <td>Offered</td>
                    <td>Drop</td>
                <?php } ?>
            </tr>
            <tr style="background:#136975;">
                <?php
                $campaign = ['offered'=>'TT_Non_Mobi','drop'=>'TT_Non_Mobi'];
                for ($i = 8; $i <= 21; $i++) {
                    $start = $i;
                    $end = $i+1;
                    $dataOutPut = get_intraday_offered_drop_TALKTALK($date . ' ' . $start . ':00:00', $date . ' ' . $end . ':00:00',$campaign, 15);
                    $totalOffered[] = @$dataOutPut['offered'];
                    $totalAbondaned[] = @$dataOutPut['drop'];
                    ?>
                    <td colspan="2">
                        <table class="table table-bordered" width="100%">
                            <tr style="background:#136975;">
                                <td style="color:#fff;" align="center">{{@$dataOutPut['offered']}}</td>
                                <td <?php if(!empty($dataOutPut['drop']) && $dataOutPut['drop'] > 0){?> style="background:#FFC7CE;color:#9C0006;" <?php }?> align="center">{{@$dataOutPut['drop']}}</td>
                            </tr>
                            <tr style="background:#C6EFCE;color:#006100;">
                                <td colspan="2" class="text-center" align="center">{{@$dataOutPut['output']}}%</td>
                            </tr>
                        </table>
                    </td>
                <?php } ?>
            </tr>
            <!--Loyality Transfer-->
            <tr>
                <th colspan="28" class="text-center" style="color:black">
                    Loyality Transfer Channel Calls Received / Abandoned
                </th>
            </tr>
             <tr style="background:#136975;">
                <?php 
                
                for ($i = 8; $i <= 21; $i++) { ?>
                    <td>Offered</td>
                    <td>Drop</td>
                <?php } ?>
            </tr>
            <tr style="background:#136975;">
                <?php
                $campaign = ['offered'=>'TalkTalk_lo','drop'=>'TalkTalk_lo'];
                for ($i = 8; $i <= 21; $i++) {
                    $start = $i;
                    $end = $i+1;
                    $dataOutPut = get_intraday_offered_drop_TALKTALK($date . ' ' . $start . ':00:00', $date . ' ' . $end . ':00:00',$campaign, 15);
                    $totalOffered[] = @$dataOutPut['offered'];
                    $totalAbondaned[] = @$dataOutPut['drop'];
                    ?>
                    <td colspan="2">
                        <table class="table table-bordered" width="100%">
                            <tr style="background:#136975;">
                                <td style="color:#fff;" align="center">{{@$dataOutPut['offered']}}</td>
                                <td <?php if(!empty($dataOutPut['drop']) && $dataOutPut['drop'] >0){?> style="background:#FFC7CE;color:#9C0006;" <?php }?> align="center">{{@$dataOutPut['drop']}}</td>
                            </tr>
                            <tr style="background:#C6EFCE;color:#006100;">
                                <td colspan="2" class="text-center" align="center">{{@$dataOutPut['output']}}%</td>
                            </tr>
                        </table>
                    </td>
                <?php } ?>
            </tr>
            <!--END LO-->
            
            <!--Test & Learn-->
            
            <tr>
                <th colspan="28" class="text-center" style="color:black">
                    Test & Learn Channel Calls Received / Abandoned
                </th>
            </tr>
             <tr style="background:#136975;">
                <?php 
                
                for ($i = 8; $i <= 21; $i++) { ?>
                    <td>Offered</td>
                    <td>Drop</td>
                <?php } ?>
            </tr>
            <tr style="background:#136975;">
                <?php
                $campaign = ['offered'=>'TalkTalk_le','drop'=>'TalkTalk_le'];
                for ($i = 8; $i <= 21; $i++) {
                    $start = $i;
                    $end = $i+1;
                    $dataOutPut = get_intraday_offered_drop_TALKTALK($date . ' ' . $start . ':00:00', $date . ' ' . $end . ':00:00',$campaign, 15);
                    $totalOffered[] = @$dataOutPut['offered'];
                    $totalAbondaned[] = @$dataOutPut['drop'];
                    ?>
                    <td colspan="2">
                        <table class="table table-bordered" width="100%">
                            <tr style="background:#136975;">
                                <td style="color:#fff;" align="center">{{@$dataOutPut['offered']}}</td>
                                <td <?php if(!empty($dataOutPut['drop']) && $dataOutPut['drop'] >0){?> style="background:#FFC7CE;color:#9C0006;" <?php }?> align="center">{{@$dataOutPut['drop']}}</td>
                            </tr>
                            <tr style="background:#C6EFCE;color:#006100;">
                                <td colspan="2" class="text-center" align="center">{{@$dataOutPut['output']}}%</td>
                            </tr>
                        </table>
                    </td>
                <?php } ?>
            </tr>
            <!--Test & Learn END-->
            
            <!--New ACQ Channel-->
            <tr>
                <th colspan="28" class="text-center" style="color:black">
                    New_Acq Channel Calls Received / Abandoned
                </th>
            </tr>
             <tr style="background:#136975;">
                <?php 
                
                for ($i = 8; $i <= 21; $i++) { ?>
                    <td>Offered</td>
                    <td>Drop</td>
                <?php } ?>
            </tr>
            <tr style="background:#136975;">
                <?php
                $campaign = ['offered'=>'New_Acq','drop'=>'New_Acq'];
                for ($i = 8; $i <= 21; $i++) {
                    $start = $i;
                    $end = $i+1;
                    $dataOutPut = get_intraday_offered_drop_TALKTALK($date . ' ' . $start . ':00:00', $date . ' ' . $end . ':00:00',$campaign, 15);
                    $totalOffered[] = @$dataOutPut['offered'];
                    $totalAbondaned[] = @$dataOutPut['drop'];
                    ?>
                    <td colspan="2">
                        <table class="table table-bordered" width="100%">
                            <tr style="background:#136975;">
                                <td style="color:#fff;" align="center">{{@$dataOutPut['offered']}}</td>
                                <td <?php if(!empty($dataOutPut['drop']) && $dataOutPut['drop'] >0){?> style="background:#FFC7CE;color:#9C0006;" <?php }?> align="center">{{@$dataOutPut['drop']}}</td>
                            </tr>
                            <tr style="background:#C6EFCE;color:#006100;">
                                <td colspan="2" class="text-center" align="center">{{@$dataOutPut['output']}}%</td>
                            </tr>
                        </table>
                    </td>
                <?php } ?>
            </tr>
            
            <!--CSBBNBA-->
            <tr>
                <th colspan="28" class="text-center" style="color:black">
                    CSBBNBA Channel Calls Received / Abandoned
                </th>
            </tr>
             <tr style="background:#136975;">
                <?php 
                
                for ($i = 8; $i <= 21; $i++) { ?>
                    <td>Offered</td>
                    <td>Drop</td>
                <?php } ?>
            </tr>
            <tr style="background:#136975;">
                <?php
                $campaign = ['offered'=>'CSBBNBA','drop'=>'CSBBNBA'];
                for ($i = 8; $i <= 21; $i++) {
                    $start = $i;
                    $end = $i+1;
                    $dataOutPut = get_intraday_offered_drop_TALKTALK($date . ' ' . $start . ':00:00', $date . ' ' . $end . ':00:00',$campaign, 15);
                    $totalOffered[] = @$dataOutPut['offered'];
                    $totalAbondaned[] = @$dataOutPut['drop'];
                    ?>
                    <td colspan="2">
                        <table class="table table-bordered" width="100%">
                            <tr style="background:#136975;">
                                <td style="color:#fff;" align="center">{{@$dataOutPut['offered']}}</td>
                                <td <?php if(!empty($dataOutPut['drop']) && $dataOutPut['drop'] >0){?> style="background:#FFC7CE;color:#9C0006;" <?php }?> align="center">{{@$dataOutPut['drop']}}</td>
                            </tr>
                            <tr style="background:#C6EFCE;color:#006100;">
                                <td colspan="2" class="text-center" align="center">{{@$dataOutPut['output']}}%</td>
                            </tr>
                        </table>
                    </td>
                <?php } ?>
            </tr>
            
            <!--END-->
            <tr>
                <td colspan="28">
                    <hr/>
                </td>
            </tr>
            <tr>
                <td colspan="10">
                    <?php
//                    pr($data['data']['AHT']);
                    $url = 'http://export.highcharts.com/';
                    $dataSend = array(
                        'chart' => array('type' => 'line'),
                        'title' => array('text' => 'SLA % Answered within 15 Seconds'),
                        'xAxis' => array(
                            'categories' => array('8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19','20','21'),
                            'title' => array('text' => 'Hour')),
                        'yAxis' => array(
                            'min' => 0,
                            'tickInterval' =>10,
                            'title' => array('text' => 'percentage')
                        ),
                        'series' => array(
                            0 => array(
                                'name' => '0 - Answered < 15 seconds',
                                'data' => array((int) @$data['data']['SLA'][8],(int) @$data['data']['SLA'][9],(int) @$data['data']['SLA'][10],(int) @$data['data']['SLA'][11],(int) @$data['data']['SLA'][12],(int) @$data['data']['SLA'][13],(int) @$data['data']['SLA'][14],(int) @$data['data']['SLA'][15],(int) @$data['data']['SLA'][16],(int) @$data['data']['SLA'][17],(int) @$data['data']['SLA'][18],(int) @$data['data']['SLA'][19],(int) @$data['data']['SLA'][20],(int) @$data['data']['SLA'][21])
                            ),
                            1 => array(
                                'name' => '0 - Target SLA(95%)',
                                'data' => array(95,95,95,95,95,95,95,95,95,95,95,95,95,95)
                            ),
                        )
                    );
//pr($dataSend);
                    
                    $dataPost = 'async=true&type=jpeg&width=400&options=' . json_encode($dataSend);

                    $options = array(
                        'http' => array(
                            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                            'method' => 'POST',
                            'content' => $dataPost,
                    ));

                    $context = stream_context_create($options);
                    $result = file_get_contents($url, false, $context);
                    $imageSLA = get_image_path_chart('http://export.highcharts.com/'.$result,'SLA-'.date('Y-m-d-H-i-s'),'/IntradayReport/TalkTalk/');
                    
                    ?>
                    <img src="<?php echo $imageSLA; ?>" download='AHT'/>
                </td>
                <td colspan="10">
                    <?php
                    $url = 'http://export.highcharts.com/';
                    $dataSend = array(
                        'chart' => array('type' => 'column'),
                        'title' => array('text' => 'Interval-wise Sales'),
                        'xAxis' => array(
                            'categories' => array('8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19','20','21'),
                            'title' => array('text' => 'Hour')),
                        'yAxis' => array(
                            'min' => 0,
                            'tickInterval' => 5,
                            'title' => array('text' => 'Sales Count')
                        ),
                        'series' => array(
                            0 => array(
                                'name' => 'Sales',
                                'data' => array((int) @$data['data']['IWS'][8],(int) @$data['data']['IWS'][9],(int) @$data['data']['IWS'][10],(int) @$data['data']['IWS'][11],(int) @$data['data']['IWS'][12],(int) @$data['data']['IWS'][13],(int) @$data['data']['IWS'][14],(int) @$data['data']['IWS'][15],(int) @$data['data']['IWS'][16],(int) @$data['data']['IWS'][17],(int) @$data['data']['IWS'][18],(int) @$data['data']['IWS'][19],(int) @$data['data']['IWS'][20],(int) @$data['data']['IWS'][21])
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
                    $imageIWS = get_image_path_chart('http://export.highcharts.com/'.$result,'IWS-'.date('Y-m-d-H-i-s'),'/IntradayReport/TalkTalk/');
                    
                    ?>
                    <img src="<?php echo $imageIWS; ?>" download='AHT'/>

                </td>
                <td colspan="8" >
                    <table class="table table-bordered" style="color:#000;" width="100%" height="270px">
                        <tr>
                            <th class="text-center">Total Offered</th>
                            <th class="text-center">Total Abandoned</th>
                        </tr>
                        <tr>
                            <td class="text-center" align="center">{{$totalOfferedSum = array_sum($totalOffered)}}</td>
                            <td class="text-center" align="center">{{$totalAbondanedSum = array_sum($totalAbondaned)}}</td>
                        </tr>
                        <tr>
                            <th class="text-center">Total Sale</th>
                            <th class="text-center">Conversion</th>
                        </tr>
                        <tr>
                            <td class="text-center" align="center">{{$totalSale = get_intraday_sale($date)}}</td>
                            <td class="text-center" align="center">{{get_divide($totalSale,$totalOfferedSum,1)}}%</td>
                        </tr>
                        <tr><th colspan="2" class="text-center">Current SLA</th></tr>
                        <?php 
                        $CurrentSLA = get_intraday_current_SLA($totalOfferedSum,$totalAbondanedSum,1);
                        
                        $CurrentSLA = get_intraday_SLA_TALKTALK();
                        if(!empty($CurrentSLA['SLA'])){
                            if($CurrentSLA['SLA'] >= 95){
                                $color = 'style="background:#C6EFCE;color:#006100;"';
                            }else{
                                $color = 'style="background:#FFC7CE;color:#9C0006;"';
                            }
                        }
                        ?>
                        <tr style="background:#C6EFCE;color:#006100;">
                            <td colspan="2" class="text-center" align="center">{{@$CurrentSLA['SLA']}}%</td>
                        </tr>
                    </table>

                </td>
            </tr>
            <tr><td colspan="28">
                    <hr/>
                </td></tr>
            <tr>
                <td colspan="14">
                   <?php
//                    pr($data['data']['AHT']);
                    $url = 'http://export.highcharts.com/';
                    $dataSend = array(
                        'chart' => array('type' => 'line'),
                        'title' => array('text' => 'Average Queue Seconds'),
                        'xAxis' => array(
                            'categories' => array('8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19','20','21'),
                            'title' => array('text' => 'Hour')),
                        'yAxis' => array(
                            'min' => 0.00,
                            'tickInterval' => 0.100,
                            'title' => array('text' => 'Queue In Seconds')
                        ),
                        'series' => array(
                            0 => array(
                                'name' => 'Average of length_in_sec',
                                'data' => array((int) @$data['data']['QIS'][8],(int) @$data['data']['QIS'][9],(int) @$data['data']['QIS'][10],(int) @$data['data']['QIS'][11],(int) @$data['data']['QIS'][12],(int) @$data['data']['QIS'][13],(int) @$data['data']['QIS'][14],(int) @$data['data']['QIS'][15],(int) @$data['data']['QIS'][16],(int) @$data['data']['QIS'][17],(int) @$data['data']['QIS'][18],(int) @$data['data']['QIS'][19],(int) @$data['data']['QIS'][20],(int) @$data['data']['QIS'][21])
                            ),
                        )
                    );
//pr($dataSend);
                    
                    $dataPost = 'async=true&type=jpeg&width=400&options=' . json_encode($dataSend);

                    $options = array(
                        'http' => array(
                            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                            'method' => 'POST',
                            'content' => $dataPost,
                    ));

                    $context = stream_context_create($options);
                    $result = file_get_contents($url, false, $context);
                    $imageQIS = get_image_path_chart('http://export.highcharts.com/'.$result,'QIS-'.date('Y-m-d-H-i-s'),'/IntradayReport/TalkTalk/');
                    
                    ?>
                    <img src="<?php echo $imageQIS; ?>" download='QIS'/>  
                </td>
                <td colspan="14">
                    <?php
//                    pr($data['data']['AHT']);
                    $url = 'http://export.highcharts.com/';
                    $dataSend = array(
                        'chart' => array('type' => 'column'),
                        'title' => array('text' => 'AHT by Hour Vs Target of 1080 Seconds'),
                        'xAxis' => array(
                            'categories' => array('8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19','20','21'),
                            'title' => array('text' => 'Hour')),
                        'yAxis' => array(
                            'min' => 0,
                            'tickInterval' => 200,
                            'title' => array('text' => 'AHT (sec)')
                        ),
                        'series' => array(
                            0 => array(
                                'name' => 'Average of length_in_sec',
                                'data' => array((int) @$data['data']['AHT'][8],(int) @$data['data']['AHT'][9],(int) @$data['data']['AHT'][10],(int) @$data['data']['AHT'][11],(int) @$data['data']['AHT'][12],(int) @$data['data']['AHT'][13],(int) @$data['data']['AHT'][14],(int) @$data['data']['AHT'][15],(int) @$data['data']['AHT'][16],(int) @$data['data']['AHT'][17],(int) @$data['data']['AHT'][18],(int) @$data['data']['AHT'][19],(int) @$data['data']['AHT'][20],(int) @$data['data']['AHT'][21])
//                                'data' => $$data['data']['AHT']
                            ),
                            1 => array(
                                'type' => 'spline',
                                'name' => 'Target',
                                'data' => array(1080, 1080, 1080, 1080, 1080, 1080, 1080, 1080, 1080, 1080, 1080, 1080,1080,1080)
                            ),
                        )
                    );
//pr($dataSend);
                    
                    $dataPost = 'async=true&type=jpeg&width=400&options=' . json_encode($dataSend);

                    $options = array(
                        'http' => array(
                            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                            'method' => 'POST',
                            'content' => $dataPost,
                    ));

                    $context = stream_context_create($options);
                    $result = file_get_contents($url, false, $context);
                    $imageAHT = get_image_path_chart('http://export.highcharts.com/'.$result,'AHT-'.date('Y-m-d-H-i-s'),'/IntradayReport/TalkTalk/');
                    ?>
                    <img src="<?php echo $imageAHT; ?>" download='AHT'/>
                </td>
            </tr>
        </table>


    </body>
</html>