<!doctype html>
<html>
    <head>
        <meta name="viewport" content="width=device-width">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Weekly Report</title>
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
    <body class="" >
        <table class="list" cellpadding="2" width="100%">
            <thead>
                <tr align="center" style="background-color:#ED7D31;color:#fff;">
                    <th style="width:12%">Date</th>
                    <th>Number of Records</th>
                    <!--<th>Loaded Records</th>-->
                    <th>Big Bundle Records Loaded</th>
                    <th>Duplicate On Loaded</th>
                    <th>Count Of PACKDESC</th>
                    <th>Count Of FILECODE</th>
                    <!--<th>Count Of Classic</th>-->
                    <th>Loaded</th>
                    <th>Duplicate Of Classic</th>
                </tr>    
            </thead>
            <tbody>
                <?php foreach ($data as $value) { ?>
                    <tr style="text-align:center;">
                        <td>{{date('Y-m-d',strtotime($value['created_at']))}}</td>
                        <td>{{(!empty($value['number_of_records'])) ? $value['number_of_records'] : 0}}</td>
                        <td>{{(!empty($value['loaded_records'])) ? $value['loaded_records'] : 0}}</td>
                        <td>{{(!empty($value['duplicate_on_loaded'])) ? $value['duplicate_on_loaded'] : 0}}</td>
                        <td>{{(!empty($value['count_of_PACKDESC'])) ? $value['count_of_PACKDESC'] : 0}}</td>
                        <td>{{(!empty($value['count_of_FILECODE'])) ? $value['count_of_FILECODE'] : 0}}</td>
                        <td>{{(!empty($value['CPAYG_loaded_records'])) ? $value['CPAYG_loaded_records'] : 0}}</td>
                        <td>{{(!empty($value['CPAYG_duplicate_on_loaded'])) ? $value['CPAYG_duplicate_on_loaded'] : 0}}</td>
                    </tr>
                <?php } ?>

            </tbody>
        </table>
    </body>
</html>
