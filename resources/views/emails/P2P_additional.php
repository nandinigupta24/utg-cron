<html>
    <head>
        <title>Error on Dialer API</title>
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
        Hi,<table class="">
            <tr>
        <?php 
        $FileCodeArray = [];
        $FileCodeArray[] = 'A001114912';
        $FileCodeArray[] = 'A001114914';
        $FileCodeArray[] = 'A001114916';
        $FileCodeArray[] = 'A001114910';
        $FileCodeArray[] = 'A001113235';
        $FileCodeArray[] = 'A001114899';
        
        
        foreach($FileCodeArray as $FileCode){
            $useable = $FileCode.'_useable';
            $non_useable = $FileCode.'_non_useable';
            $loaded = $FileCode.'_loaded';
            $recycled = $FileCode.'_recycled';
        ?>
            <td>
        <table class="list" style="width:100%;">
            <tr><th colspan="2"><?php echo $FileCode;?></th></tr>
            <tr><th>Useable</th><td><?php echo @$data['data']->$useable;?></td></tr>
            <tr><th>Non Useable</th><td><?php echo @$data['data']->$non_useable;?></td></tr>
            <tr><th>Loaded</th><td><?php echo @$data['data']->$loaded;?></td></tr>
            <tr><th>Recycled</th><td><?php echo @$data['data']->$recycled;?></td></tr>
        </table>
                </td>
        <?php }?>
                   </tr>
        </table>
    </body>
</html>