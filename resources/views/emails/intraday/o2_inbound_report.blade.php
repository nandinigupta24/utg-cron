<html>
    <head>
    </head>
    <body style="color:#fff;">
        <?php 
        $date = $data['data']['date'];
        $AHTarray = $data['data']['AHT'];
        $SLAarray = $data['data']['SLA'];
        $abandonGraph = $data['data']['abandonGraph'];
        $campaignId = $data['data']['campaignId'];
//        $OutworkxCampaign = $data['data']['OutworkxCampaign'];
        ?>
       <table class="table table-bordered" style="color:#fff;">
<!--           <tr>
                <th colspan="18" class="text-center">
                    <h1 class="" style="color:#000 !important;">O2Inbound Intraday Report</h1>
                </th>
            </tr>-->
            <?php foreach($campaignId as $key=>$val){
                
                $totalCall = [];
                ?>
            <tr>
                <th colspan="25" class="text-center">
                    &nbsp;
                </th>
            </tr>
                            <tr style="background:#136975;">
                                <th colspan="25" class="text-center"> <?php echo $val;?></th>
                            </tr>
                            <tr style="background:#136975;">
                                <th colspan="2" class="text-center">10</th>
                                <th colspan="2" class="text-center">11</th>
                                <th colspan="2" class="text-center">12</th>
                                <th colspan="2" class="text-center">13</th>
                                <th colspan="2" class="text-center">14</th>
                                <th colspan="2" class="text-center">15</th>
                                <th colspan="2" class="text-center">16</th>
                                <th colspan="2" class="text-center">17</th>
                                <th colspan="2" class="text-center">18</th>
                                <th colspan="2" class="text-center">19</th>
                                <th colspan="2" class="text-center">20</th>
                                <th colspan="3" class="text-center"></th>
                            </tr>
                            <tr style="background:#136975;">
                                <?php
                                for($i=10;$i<=20;$i++){
                                    ?>
                                <td align="center">Offered</td>
                                <td align="center">Drop</td>
                                <?php }?>
                                <td align="center">Calls</td>
                                <td align="center">Sales</td>
                                <td align="center">Conversion</td>
                            </tr>
                             <tr style="background:#136975;">
                                <?php
                                for($i=10;$i<=20;$i++){
                                    $start = $i;
                                    $end = $i+1;
                                   $dataOutPut =  get_intraday_o2Inbound_offered_drop($date.' '.$start.':00:00',$date.' '.$end.':00:00',$val,15);
//                                   $totalOffered[] = @$dataOutPut['offered'];
//                                   $totalAbondaned[] = @$dataOutPut['drop'];
                                   
                                   $totalCall[] = @$dataOutPut['offered'];
                                   $totalCall[] = @$dataOutPut['drop'];
                                    ?>  
                                <td align="center">{{@$dataOutPut['offered']}}</td>
                                <td align="center" <?php if(!empty($dataOutPut['drop']) && $dataOutPut['drop'] > 0) {?> style="background:#FFC7CE;color:#9C0006;"<?php }?>>{{@$dataOutPut['drop']}}</td>
                                <?php }?>
                                <td align="center">{{$totalCalls = (!empty($totalCall) && count($totalCall) > 0) ? array_sum($totalCall) : 0}}</td>
                                <td align="center">{{$saleData = get_o2inbound_intraday_sale($date,$val)}}</td>
                                <td align="center">{{(!empty($totalCalls)) ? ceil((($saleData * 100)/$totalCalls)) : 0}}%</td>
                            </tr>
                             <?php }?>
             
                            
                            <tr>
                <th colspan="25" class="text-center">
                    &nbsp;
                </th>
            </tr>
                            <tr style="background:#136975;">
                                <th colspan="25" class="text-center"> Total</th>
                            </tr>
                            <tr style="background:#136975;">
                                <th colspan="2" class="text-center">10</th>
                                <th colspan="2" class="text-center">11</th>
                                <th colspan="2" class="text-center">12</th>
                                <th colspan="2" class="text-center">13</th>
                                <th colspan="2" class="text-center">14</th>
                                <th colspan="2" class="text-center">15</th>
                                <th colspan="2" class="text-center">16</th>
                                <th colspan="2" class="text-center">17</th>
                                <th colspan="2" class="text-center">18</th>
                                <th colspan="2" class="text-center">19</th>
                                <th colspan="2" class="text-center">20</th>
                                <th colspan="3" class="text-center"></th>
                            </tr>
                            <tr style="background:#136975;">
                                <?php
                                for($i=10;$i<=20;$i++){
                                    ?>
                                <td align="center">Offered</td>
                                <td align="center">Drop</td>
                                <?php }?>
                                <td align="center">Total Calls</td>
                                <td align="center">Sales</td>
                                <td align="center">Conversion</td>
                            </tr>
                             <tr style="background:#136975;">
                                <?php
                                $totalCall = [];
                                for($i=10;$i<=20;$i++){
                                    $start = $i;
                                    $end = $i+1;
                                   $dataOutPut =  get_intraday_o2Inbound_offered_drop_all($date.' '.$start.':00:00',$date.' '.$end.':00:00',$campaignId,15);
                                   $totalOffered[$i] = @$dataOutPut['offered'];
                                   $totalAbondaned[$i] = @$dataOutPut['drop'];
                                   
                                   $totalCall[] = @$dataOutPut['offered'];
                                   $totalCall[] = @$dataOutPut['drop']
                                    ?>  
                                <td align="center">{{@$dataOutPut['offered']}}</td>
                                <td align="center" <?php if(!empty($dataOutPut['drop']) && $dataOutPut['drop'] > 0) {?>style="background:#FFC7CE;color:#9C0006;" <?php }?>>{{@$dataOutPut['drop']}}</td>
                                <?php }?>
                                <td align="center">{{$totalCalls = (!empty($totalCall) && count($totalCall) > 0) ? array_sum($totalCall) : 0}}</td>
                                <td align="center">{{$totalSale = get_o2inbound_intraday_all_sale($date)}}</td>
                                <td align="center">{{(!empty($totalCalls)) ? ceil((($totalSale * 100)/$totalCalls)) : 0}}%</td>
                            </tr>
                            <tr style="color:#000;">
                                <th colspan="25" class="text-center">SLA</th>
                            </tr>
                            <tr>
                                <?php
                                for($i=10;$i<=20;$i++){
                                    if(!empty($totalOffered[$i])){
                                        $SLA = get_intraday_current_SLA(@$totalOffered[$i],@$totalAbondaned[$i],1);
                                        if($SLA >= 95){
                                            $color = 'style="background:#C6EFCE;color:#006100;"';
                                        }else{
                                            $color = 'style="background:#FFC7CE;color:#9C0006;"';
                                        }
                                    ?>
                                <td colspan="2" align="center" <?php echo $color;?>>{{$SLA}}%</td>
                                    <?php } else{?>
                                <td colspan="2" align="center" style="background:#FFC7CE;color:#9C0006;">0%</td>
                                <?php } }?>
                                <td colspan="3"></td>
                            </tr>
           
                            
                            <tr>
                                <td colspan="22"></td>
                            </tr>
                            <tr>
                                <td colspan="8">
                                     <?php
                    $url = 'http://export.highcharts.com/';
                    $dataSend = array(
                        'chart' => array('type' => 'line'),
                        'title' => array('text' => 'SLA % Answered within 15 Seconds'),
                        'xAxis' => array(
                            'categories' => array('10', '11', '12', '13','14', '15', '16', '17', '18','19','20'),
                            'title' => array('text' => 'Hour')),
                        'yAxis' => array(
                            'min' => 0,
                            'tickInterval' =>10,
                            'title' => array('text' => 'percentage')
                        ),
                        'series' => array(
                            0 => array(
                                'name' => '0 - Answered < 15 seconds',
                                'data' => (!empty($SLAarray)) ? $SLAarray : ''
                            ),
                            1 => array(
                                'name' => '0 - Target SLA(95%)',
                                'data' => array(95,95,95,95,95,95,95,95,95,95,95),
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
                    $imageSLA = get_image_path_chart('http://export.highcharts.com/'.$result,'SLA-'.date('Y-m-d-H-i-s'),'/IntradayReport/O2Inbound/');
                    
                    ?>
                    <img src="<?php echo $imageSLA; ?>" download='AHT'/>
                                </td>  
                                <td colspan="4"> <table class="table table-striped" style="color:#000;">
                                        <tr style="background-color:#136975;color:#fff !important;">
                                            <th class="text-center">Total Offered</th>
                                            <th class="text-center">Total Abandoned</th>
                                        </tr>
                                        <tr>
                                            <td class="text-center">{{$totalOfferedSum = array_sum($totalOffered)}}</td>
                                            <td class="text-center">{{$totalAbondanedSum = array_sum($totalAbondaned)}}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-center">Total Sale</th>
                                            <th class="text-center">Conversion</th>
                                        </tr>
                                        <tr>
                                            <td class="text-center">{{$totalSale = get_o2inbound_intraday_all_sale($date)}}</td>
                                            <td class="text-center">{{(!empty($totalOfferedSum)) ? number_format((($totalSale/$totalOfferedSum)*100),2) : 0}}%</td>
                                        </tr>
                                        <tr><th colspan="2" class="text-center">Current SLA</th></tr>
                                        <tr style="background:#C6EFCE;color:#006100;font-size:58px;" rowspan="4">
                                            <?php 
                                            $totalSLA = get_intraday_current_SLA($totalOfferedSum,$totalAbondanedSum,1);
                                            if($totalSLA >= 95){
                                            $color = 'style="background:#C6EFCE;color:#006100;"';
                                        }else{
                                            $color = 'style="background:#FFC7CE;color:#9C0006;"';
                                        }
                                            ?>
                                            <td colspan="2" class="text-center" {{$color}}>{{$totalSLA}}%</td>
                                        </tr>
                                    </table></td>
                                    <td colspan="8">
                                        <?php
                    $url = 'http://export.highcharts.com/';
                    $dataSend = array(
                        'chart' => array('type' => 'column'),
                        'title' => array('text' => 'Abandoned by Hour by Wait Time'),
                        'xAxis' => array(
                            'categories' => @$abandonGraph['Hour'],
                            'title' => array('text' => 'Hour')),
                        'yAxis' => array(
                            'min' => 0,
                            'tickInterval' =>10,
                            'title' => array('text' => 'percentage')
                        ),
                        'series' => array(
                            0 => array(
                                'name' => 'ABANDON - 0-15',
                                'data' => @$abandonGraph['0-15']
                            ),
                            1 => array(
                                'name' => 'ABANDON - >15',
                                'data' => @$abandonGraph['>15'],
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
                    $imageSLA = get_image_path_chart('http://export.highcharts.com/'.$result,'AWHT-'.date('Y-m-d-H-i-s'),'/IntradayReport/O2Inbound/');
                    
                    ?>
                    <img src="<?php echo $imageSLA; ?>" download='AHT'/>
                                        
                                    </td>
                            </tr>
                            <tr>
                                <td colspan="22">
                                <?php
                    $url = 'http://export.highcharts.com/';
                    $dataSend = array(
                        'chart' => array('type' => 'column'),
                        'title' => array('text' => '% of Calls dealt with By Inbound Team VS Overflow'),
                        'xAxis' => array(
                            'categories' => @$AHTarray['Hour'],
                            'title' => array('text' => 'Hour')),
                        'yAxis' => array(
                            'min' => 0,
                            'tickInterval' =>10,
                            'title' => array('text' => 'percentage')
                        ),
                        'series' => array(
                            0 => array(
                                'name' => 'Inbound Team',
                                'data' => @$AHTarray['InboundTeam']
                            ),
                            1 => array(
                                'type'=> 'spline',
                                'name' => 'Overflow',
                                'data' => @$AHTarray['Overflow'],
                            )
                        )
                    );
                
                    $dataPost = 'async=true&type=jpeg&width=1000&options=' . json_encode($dataSend);

                    $options = array(
                        'http' => array(
                            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                            'method' => 'POST',
                            'content' => $dataPost,
                    ));

                    $context = stream_context_create($options);
                    $result = file_get_contents($url, false, $context);
                    $imageSLA = get_image_path_chart('http://export.highcharts.com/'.$result,'ITO-'.date('Y-m-d-H-i-s'),'/IntradayReport/O2Inbound/');
                    
                    ?>
                    <img src="<?php echo $imageSLA; ?>" download='AHT'/>
                                </td>
                            </tr>
                        </table>
    </body>
</html>