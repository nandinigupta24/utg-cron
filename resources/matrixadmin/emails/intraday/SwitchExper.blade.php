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
        <?php 
        $date = $data['data']['date'];
        $campaign = $data['data']['Campaign']; 
         ?>
        <table class="table table-bordered" style="color:#fff;">
            <?php foreach($campaign as $val){?>
            <tr style="background:#136975;">
                <th colspan="28" class="text-center"><?php echo $val;?></th>
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
                for ($i = 8; $i <= 21; $i++) {
                    $start = $i;
                    $end = $i + 1;
                    $dataOutPut = SwitchExper_offered_drop($date . ' ' . $start . ':00:00', $date . ' ' . $end . ':00:00',$val, 5);
                    $totalOffered[] = @$dataOutPut['offered'];
                    $totalAbondaned[] = @$dataOutPut['drop'];
                    ?>
                    <td colspan="2">
                        <table class="table table-bordered" width="100%">
                            <tr style="background:#136975;">
                                <td style="color:#fff;" align='center'>{{@$dataOutPut['offered']}}</td>
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
                <td colspan="28">
                    <hr/>
                </td>
            </tr>
            <?php }?>
             <tr >
            <?php foreach($campaign as $key=>$val){
                        $calls = SwitchExper_offered_drop_campaign($date,$val,5);
                        $totalCalls = @$calls['offered'] + @$calls['drop'] + 0;
                        $sale = SwitchExper_sale_campaign($date,$key);
                ?>
                 <td colspan="5">
                     <table class="table table-bordered" width="100%" style="color:#fff;background:#136975;">
                         <tr style="background:#C6EFCE;color:#006100;">
                             <td colspan="2" align="center"><?php echo $val;?></td>
                         </tr>
                         <tr>
                             <td align="center">Total Sales</td>
                             <td align="center"><?php echo @$sale;?></td>
                         </tr>
                         <tr>
                             <td align="center">Total Conversion</td>
                             <td align="center"><?php echo (!empty($totalCalls)) ? number_format((($sale*100)/$totalCalls),2) : 0;?>%</td>
                         </tr>
                         <tr>
                             <td align="center">Total Lost Calls</td>
                             <td align="center"><?php echo (!empty($calls['drop'])) ? @$calls['drop'] : 0;?></td>
                         </tr>
                     </table>
                 </td>   
            <?php }?>
                 <td colspan="3"></td>
            </tr>
            
            
        </table>


    </body>
</html>