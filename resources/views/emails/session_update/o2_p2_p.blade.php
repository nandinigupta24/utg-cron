<html>
    <head>
        <title></title>
        <style>
            table.list tr td {
                /*padding: 5px 13px;*/
                border-bottom:1px solid #007c9c;
                border-right:1px solid #007c9c;

            }
            table.list tr{
                padding: 0px;
                border-bottom: 1px solid #000;

            }
            table.list {
                clear: both;
                background: #f2f2f2;
                margin: 2px 0;
                /*margin-bottom:50px*/
            }
            table {
                border-collapse: collapse;
            }
            table, th, td {
                border: 2px solid #1290AC;
            }
            .green{color:#00CC66; font-weight:bold}
            .orange{color:orange; font-weight:bold}
            .red{color:red; font-weight:bold}
        </style>
    </head>
    <body>
        <div class="wrapper">
            <div class="row">
                <div class="col-lg-12">

                    <?php
                    $newArray = $data['data']['newArray'];
                    $SPATarget = $data['data']['SPATarget'];
                    $SessionUpdateTime = $data['data']['SessionUpdateTime'];
                    $SessionUpdateName = $data['data']['SessionUpdateName'];
                    $FTE1 = $data['data']['FTE1'];
                    ?>
                    <table class="list"  cellpadding="2" width="100%">
                        <tr align="center">
                            <td><strong>Site</strong></td>
                            <td><strong>FTE</strong></td>
                            <td><strong>Sale Target</strong></td>
                            <td><strong>Currently</strong></td>
                            <td><strong>%Session<br />Target</strong></td>
                            <td><strong>%To Day<br />Target</strong></td>
                            <td><strong>Sales<br />Remain</strong></td>
                            <td><strong>Session SPA<br />Target</strong></td>
                            <td><strong>SPA</strong></td>
                            <td><strong>SPH Req for<br />Site</strong></td>
                            <td><strong>Declines</strong></td>
                            <td><strong>Total Sales</strong></td>
                            <td><strong>POS</strong></td>
                    <!--        <td><strong>Session Target Sales</strong></td>
                            <td><strong>Session SPA Target</strong></td>
                            <td><strong>100% SPA</strong></td>
                            <td><strong>85% SPA</strong></td>
                            <td><strong>Start Time</strong></td>
                            <td><strong>End Time</strong></td>
                            <td><strong>Session Update Time</strong></td>
                            <td><strong>Hours Worked</strong></td>
                            <td><strong>Hours Remaining</strong></td>-->

                        </tr>

                        <tbody>
                            <?php
                            foreach ($newArray as $key => $value) {
                                $sale = @$value['Accept'];
                                $decline = @$value['Decline'];
                                $totalSale = ($sale + $decline);
                                switch ($key) {
                                    case 'SLM':
                                        $FTE = (!empty($FTE1['SLM'])) ? $FTE1['SLM'] : 0;
                                        $StartTime = 11;
                                        $EndTime = 19;
                                        $HoursWorked = ($SessionUpdateTime - $StartTime);
                                        $HoursRemaining = ($EndTime - $SessionUpdateTime);
                                        $SaleTarget = $FTE * $SPATarget;
                                        $SessionTargetSale = (($SaleTarget / 8) * $HoursWorked);
                                        $PercentageSessionTarget = (!empty($SessionTargetSale)) ? ceil(($sale / $SessionTargetSale) * 100) : 0;
                                        $PercentageDayToTarget = (!empty($SaleTarget)) ? ($sale / $SaleTarget) * 100 : 0;
                                        $SalesRemain = ($SaleTarget - $sale);
                                        $SPA = (!empty($FTE)) ? ($sale / $FTE) : 0;
                                        $SPHReqForSite = ($SalesRemain / 6);
                                        $POS = (!empty($totalSale)) ? (($sale / $totalSale) * 100) : 0;
                                        $SessionSPATarget = (($SPATarget / 8) * $HoursWorked);
                                        break;
                                    case 'Outworx':
                                        $FTE = (!empty($FTE1['Outworx'])) ? $FTE1['Outworx'] : 0;
                                        $StartTime = 11;
                                        $EndTime = 19;
                                        $HoursWorked = ($SessionUpdateTime - $StartTime);
                                        $HoursRemaining = ($EndTime - $SessionUpdateTime);
                                        $SaleTarget = $FTE * $SPATarget;
                                        $SessionTargetSale = (($SaleTarget / 8) * $HoursWorked);
                                        $PercentageSessionTarget = (!empty($SessionTargetSale)) ? ceil(($sale / $SessionTargetSale) * 100) : 0;
                                        $PercentageDayToTarget = (!empty($SaleTarget)) ? ($sale / $SaleTarget) * 100 : 0;
                                        $SalesRemain = ($SaleTarget - $sale);
                                        $SPA = (!empty($FTE)) ? ($sale / $FTE) : 0;
                                        $SPHReqForSite = ($SalesRemain / 6);
                                        $POS = (!empty($totalSale)) ? (($sale / $totalSale) * 100) : 0;
                                        $SessionSPATarget = (($SPATarget / 8) * $HoursWorked);
                                        break;
                                    case 'Belfast':
                                        $FTE = (!empty($FTE1['Belfast'])) ? $FTE1['Belfast'] : 0;
                                        $StartTime = 11;
                                        $EndTime = 19;
                                        $HoursWorked = ($SessionUpdateTime - $StartTime);
                                        $HoursRemaining = ($EndTime - $SessionUpdateTime);
                                        $SaleTarget = $FTE * $SPATarget;
                                        $SessionTargetSale = (($SaleTarget / 8) * $HoursWorked);
                                        $PercentageSessionTarget = (!empty($SessionTargetSale)) ? ceil(($sale / $SessionTargetSale) * 100) : 0;
                                        $PercentageDayToTarget = (!empty($SaleTarget)) ? ($sale / $SaleTarget) * 100 : 0;
                                        $SalesRemain = ($SaleTarget - $sale);
                                        $SPA = (!empty($FTE)) ? ($sale / $FTE) : 0;
                                        $SPHReqForSite = ($SalesRemain / 6);
                                        $POS = (!empty($totalSale)) ? (($sale / $totalSale) * 100) : 0;
                                        $SessionSPATarget = (($SPATarget / 8) * $HoursWorked);
                                        break;
                                    default:
                                }
                                $totalArray['FTE'][] = $FTE;
                                $totalArray['SaleTarget'][] = $SaleTarget;
                                $totalArray['Sale'][] = $sale;
                                $totalArray['decline'][] = $decline;
                                $totalArray['SaleTotal'][] = $totalSale;
                                if ($POS >= 70 && $POS <= 100) {
                                    $color = 'green';
                                } elseif ($POS >= 60 && $POS < 70) {
                                    $color = 'orange';
                                } elseif ($POS < 60) {
                                    $color = 'red';
                                } else {
                                    $color = 'red';
                                }

                                if ($PercentageSessionTarget >= 1) {
                                    $imsSrc = 'green.png';
                                } elseif ($PercentageSessionTarget >= .85 && $PercentageSessionTarget < 1) {
                                    $imsSrc = 'orange.png';
                                } else {
                                    $imsSrc = 'red.png';
                                }
                                ?>

    <!--                    <tr>
        <th>{{$key}}</th>
        <td>{{$FTE}}</td>
        <td>{{$SaleTarget}}</td>
        <td>{{$sale}}</td>
        <td><img src='https://reports.usethegeeks.com/assets/img/{{$imsSrc}}' style="width:10px;"/>{{@$PercentageSessionTarget}}%</td>
        <td>{{number_format($PercentageDayToTarget,2)}}%</td>
        <td>{{$SalesRemain}}</td>
        <td>{{number_format($SessionSPATarget,2)}}</td>
        <td>{{number_format($SPA,2)}}</td>
        <td>{{number_format($SPHReqForSite,2)}}</td>
        <td>{{$decline}}</td>
        <td>{{$totalSale}}</td>
        <td><span style="color:<?php echo $color; ?> !important;">{{number_format($POS,2)}}%</span></td>
        <td>{{number_format(@$SessionTargetSale,2)}}</td>
        <td>{{number_format($SessionSPATarget,2)}}</td>
        <td>{{number_format($SessionSPATarget,2)}}</td>
        <td>{{number_format((($SessionSPATarget*85)/100),2)}}</td>
        <td>{{$StartTime}}.00</td>
        <td>{{$EndTime}}.00</td>
        <td>{{$SessionUpdateTime}}.00</td>
        <td>{{$HoursWorked}}.00</td>
        <td>{{$HoursRemaining}}.00</td>
    </tr>-->
                                <tr align="center">
                                    <td>{{$key}}</td>
                                    <td>{{$FTE}}</td>
                                    <td>{{$SaleTarget}}</td>
                                    <td>{{$sale}}</td>
                                    <td><img src='http://reports.usethegeeks.com/assets/images/report/{{$imsSrc}}'/>{{@$PercentageSessionTarget}}%</td>
                                    <td>{{ceil($PercentageDayToTarget)}}%</td>
                                    <td>{{$SalesRemain}}</td>
                                    <td>{{$SessionSPATarget}}</td>
                                    <td>{{ceil($SPA)}}</td>
                                    <td>{{ceil($SPHReqForSite)}}</td>
                                    <td>{{$decline}}</td>
                                    <td>{{$totalSale}}</td>
                                    <td class="<?php echo $color; ?>">{{ceil($POS)}}%</td>
            <!--                        <td>{{@$SessionTargetSale}}</td>
                                    <td>{{ceil($SessionSPATarget)}}</td>
                                    <td>{{ceil($SessionSPATarget)}}</td>
                                    <td>{{ceil(($SessionSPATarget*85)/100)}}</td>
                                    <td>{{$StartTime}}.00</td>
                                    <td>{{$EndTime}}.00</td>
                                    <td>{{$SessionUpdateTime}}.00</td>
                                    <td>{{$HoursWorked}}.00</td>
                                    <td>{{$HoursRemaining}}.00</td>-->
                                </tr>
                            <?php } ?>
                            <?php
                            $FTE = array_sum($totalArray['FTE']);
                            $SaleTarget = array_sum($totalArray['SaleTarget']);
                            $sale = array_sum($totalArray['Sale']);
                            $decline = array_sum($totalArray['decline']);
                            $totalSale = array_sum($totalArray['SaleTotal']);
                            $StartTime = 11;
                            $EndTime = 19;
                            $HoursWorked = ($SessionUpdateTime - $StartTime);
                            $HoursRemaining = ($EndTime - $SessionUpdateTime);
                            $SessionTargetSale = (($SaleTarget / 8) * $HoursWorked);
                            $PercentageSessionTarget = (!empty($SessionTargetSale)) ? (($sale / $SessionTargetSale) * 100) : 0;
                            $PercentageDayToTarget = (!empty($SaleTarget)) ? (($sale / $SaleTarget) * 100) : 0;
                            $SalesRemain = ($SaleTarget - $sale);
                            $SPA = (!empty($FTE)) ? ($sale / $FTE) : 0;
                            $SPHReqForSite = (!empty($SalesRemain)) ? ($SalesRemain / 6) : 0;
                            $POS = (!empty($totalSale)) ? (($sale / $totalSale) * 100) : 0;
                            $SessionSPATarget = (!empty($SPATarget)) ? (($SPATarget / 8) * $HoursWorked) : 0;
                            if ($POS >= 70 && $POS <= 100) {
                                $color = 'green';
                            } elseif ($POS >= 60 && $POS < 70) {
                                $color = 'orange';
                            } elseif ($POS < 60) {
                                $color = 'red';
                            } else {
                                $color = 'red';
                            }
                            ?>
        <!--                <tr class="total">
                            <th style="border-top-style:double !important;border-bottom-style:double !important;">Totals</th>
                            <td style="border-top-style:double !important;border-bottom-style:double !important;">{{$FTE}}</td>
                            <td style="border-top-style:double !important;border-bottom-style:double !important;">{{$SaleTarget}}</td>
                            <td style="border-top-style:double !important;border-bottom-style:double !important;">{{$sale}}</td>
                            <td style="border-top-style:double !important;border-bottom-style:double !important;">{{@$PercentageSessionTarget}}%</td>
                            <td style="border-top-style:double !important;border-bottom-style:double !important;">{{$PercentageDayToTarget}}%</td>
                            <td style="border-top-style:double !important;border-bottom-style:double !important;">{{$SalesRemain}}</td>
                            <td style="border-top-style:double !important;border-bottom-style:double !important;">{{$SessionSPATarget}}</td>
                            <td style="border-top-style:double !important;border-bottom-style:double !important;">{{$SPA}}</td>
                            <td style="border-top-style:double !important;border-bottom-style:double !important;">{{$SPHReqForSite}}</td>
                            <td style="border-top-style:double !important;border-bottom-style:double !important;">{{$decline}}</td>
                            <td style="border-top-style:double !important;border-bottom-style:double !important;">{{$totalSale}}</td>
                            <td style="border-top-style:double !important;border-bottom-style:double !important;"><span style="color:<?php echo $color; ?> !important;">{{$POS}}%</span></td>
                            <td style="border-top-style:double !important;border-bottom-style:double !important;">{{@$SessionTargetSale}}</td>
                            <td style="border-top-style:double !important;border-bottom-style:double !important;">{{$SessionSPATarget}}</td>
                            <td style="border-top-style:double !important;border-bottom-style:double !important;">{{$SessionSPATarget}}</td>
                            <td style="border-top-style:double !important;border-bottom-style:double !important;">{{($SessionSPATarget*85)/100}}</td>
                            <td style="border-top-style:double !important;border-bottom-style:double !important;">{{$StartTime}}.00</td>
                            <td style="border-top-style:double !important;border-bottom-style:double !important;">{{$EndTime}}.00</td>
                            <td style="border-top-style:double !important;border-bottom-style:double !important;">{{$SessionUpdateTime}}.00</td>
                            <td style="border-top-style:double !important;border-bottom-style:double !important;">{{$HoursWorked}}.00</td>
                            <td style="border-top-style:double !important;border-bottom-style:double !important;">{{$HoursRemaining}}.00</td>
                        </tr>-->
                            <tr align="center">
                                <td><strong>Totals</strong></td>
                                <td><strong>{{$FTE}}</strong></td>
                                <td><strong>{{$SaleTarget}}</strong></td>
                                <td><strong>{{$sale}}</strong></td>
                                <td><strong>{{ceil(@$PercentageSessionTarget)}}%</strong></td>
                                <td><strong>{{ceil($PercentageDayToTarget)}}%</strong></td>
                                <td><strong>{{$SalesRemain}}</strong></td>
                                <td><strong>{{ceil($SessionSPATarget)}}</strong></td>
                                <td><strong>{{ceil($SPA)}}</strong></td>
                                <td><strong>{{ceil($SPHReqForSite)}}</strong></td>
                                <td><strong>{{$decline}}</strong></td>
                                <td><strong>{{$totalSale}}</strong></td>
                                <td class="<?php echo $color; ?>"><strong>{{ceil($POS)}}%</strong></td>
            <!--                    <td><strong>{{@$SessionTargetSale}}</strong></td>
                                <td><strong>{{ceil($SessionSPATarget)}}</strong></td>
                                <td><strong>{{ceil($SessionSPATarget)}}</strong></td>
                                <td><strong>{{ceil(($SessionSPATarget*85)/100)}}</strong></td>
                                <td><strong>{{$StartTime}}.00</strong></td>
                                <td><strong>{{$EndTime}}.00</strong></td>
                                <td><strong>{{$SessionUpdateTime}}.00</strong></td>
                                <td><strong>{{$HoursWorked}}.00</strong></td>
                                <td><strong>{{$HoursRemaining}}.00</strong></td>-->
                            </tr>
                        </tbody>    
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>