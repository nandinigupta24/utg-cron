<html>
    <head>
        <title></title>
    </head>
    <body>
        <table class="table">
            <tr>
                <th>Campaign</th>
                <th>Success</th>
                <th>Duplicate</th>
                <th>Total</th>
            </tr>
            <?php foreach($data['data'] as $key=>$val){?>
            <tr>
                <th>{{@$key}}</th>
                <th>{{@$val['success']}}</th>
                <th>{{@$val['duplicate']}}</th>
                <th>{{@$val['total']}}</th>
            </tr>
            <?php }?>
        </table>
    </body>
</html>