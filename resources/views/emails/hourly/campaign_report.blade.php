<html>
    <head>
        <title>Hourly Report</title>
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
       <!--        <style>
                   table th td {border: 1px solid #000 !important;text-align:center!important;}
                   </style>-->

                    <table class="list"  cellpadding="2" width="100%">
                        <?php
                        foreach ($data['Table'] as $key => $value) {
                            if ($key == 'Values') {
                                ?>
                                <?php if ($key == 'ConnectRate') { ?>
                                    <tr align="center">
                                        <td><strong>{{$key}}</strong></td>
                                        <?php foreach ($value as $val) { ?>
                                            <td><strong>{{$val}}%</strong></td>
                                        <?php } ?>
                                    </tr>

                                <?php } elseif ($key == 'DMCRate') { ?>
                                    <tr align="center">
                                        <td><strong>{{$key}}</strong></td>
                                        <?php foreach ($value as $val) { ?>
                                            <td><strong>{{$val}}%</strong></td>
                                        <?php } ?>
                                    </tr>

                                <?php } elseif ($key == 'Drop') { ?>
                                    <tr align="center">
                                        <td><strong>{{$key}}</strong></td>
                                        <?php foreach ($value as $val) { ?>
                                            <td><strong>{{$val}}%</strong></td>
                                        <?php } ?>
                                    </tr>

                                <?php } else { ?>
                                    <tr align="center">
                                        <td><strong>{{$key}}</strong></td>
                                        <?php foreach ($value as $val) { ?>
                                            <td><strong>{{$val}}</strong></td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                                <!--</thead>-->
                                <tbody >
                                <?php } else { ?>
                                    <?php if ($key == 'ConnectRate') { ?>
                                        <tr align="center">
                                            <td>{{str_replace('_',' ',$key)}}</td>
                                            <?php foreach ($value as $val) { ?>
                                                <td>{{$val}}%</td>
                                            <?php } ?>
                                        </tr>
                                    <?php } elseif ($key == 'DMCRate') { ?>
                                        <tr align="center">
                                            <td>{{str_replace('_',' ',$key)}}</td>
                                            <?php foreach ($value as $val) { ?>
                                                <td>{{$val}}%</td>
                                            <?php } ?>
                                        </tr>
                                    <?php } elseif ($key == 'Drop') { ?>
                                        <tr align="center">
                                            <td>{{str_replace('_',' ',$key)}}</td>
                                            <?php foreach ($value as $val) { ?>
                                                <td>{{$val}}%</td>
                                            <?php } ?>
                                        </tr>
                                    <?php } else { ?>
                                        <tr align="center">
                                            <td>{{str_replace('_',' ',$key)}}</td>
                                            <?php foreach ($value as $val) { ?>
                                                <td>{{$val}}</td>
                                            <?php } ?>
                                        </tr>
                                    <?php } ?>
                                <?php } ?>
                                <?php }
                            ?>
                        </tbody>
                    </table>
                    <hr/>
                    <?php
                    foreach ($data['Main']['CampaignID'] as $value) {
                        $fileName = $value . "-" . $data['DynamicNumber'] . ".jpg";
                        $url = storage_path('PieChart/Hourly/CampaignReport/Main/') . $fileName;
                        if (file_exists($url)) {
                            echo '<img src="https://api.usethegeeks.com/storage/PieChart/Hourly/CampaignReport/Main/' . $fileName . '"/>';
                        }
                    }
                    ?>
                    <?php
                    foreach ($data['Omni']['CampaignID'] as $value) {
                        $fileName = $value . "-" . $data['DynamicNumber'] . ".jpg";
                        $url = storage_path('PieChart/Hourly/CampaignReport/Omni/') . $fileName;
                        if (file_exists($url)) {
                            echo '<img src="https://api.usethegeeks.com/storage/PieChart/Hourly/CampaignReport/Omni/' . $fileName . '"/>';
                        }
                    }
                    ?>

                </div>
            </div>
        </div>
    </body>
</html>