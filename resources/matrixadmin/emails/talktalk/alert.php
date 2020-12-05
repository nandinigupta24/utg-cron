<!DOCTYPE html>
<html>
    <head>
        <title>

        </title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <link href="http://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" type="text/css" />
    </head>
    <body bgcolor="#fff" style="margin: 0px !important; padding: 0px !important;">
        Hi,
        
            <h2><?php echo @$data['moved_agents']['user']; ?></h2>
            <p>Login Time:- <?php echo @$data['moved_agents']['login_time']; ?></p>
            <p>Campaign:- <?php echo @$data['moved_agents']['campaign']; ?></p>
            <p>Sale Time:- <?php echo @$data['moved_agents']['sale_time']; ?></p>
            <br/><br/>
       </body>
</html>