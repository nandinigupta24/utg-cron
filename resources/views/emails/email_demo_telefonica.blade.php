
<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <style type="text/css">
            /* FONTS */
            @import url('https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i');

            /* CLIENT-SPECIFIC STYLES */
            body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
            table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
            img { -ms-interpolation-mode: bicubic; }

            /* RESET STYLES */
            img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
            table { border-collapse: collapse !important; }
            body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; }

            /* iOS BLUE LINKS */
            a[x-apple-data-detectors] {
                color: inherit !important;
                text-decoration: none !important;
                font-size: inherit !important;
                font-family: inherit !important;
                font-weight: inherit !important;
                line-height: inherit !important;
            }

            /* MOBILE STYLES */
            @media screen and (max-width:600px){
                h1 {
                    font-size: 32px !important;
                    line-height: 32px !important;
                }
            }

            /* ANDROID CENTER FIX */
            div[style*="margin: 16px 0;"] { margin: 0 !important; }
        </style>
    </head>
    <body style="background-color: #f3f5f7; margin: 0 !important; padding: 0 !important;">

        <!-- HIDDEN PREHEADER TEXT -->
        <!--<div style="display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: 'Poppins', sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;">
            We're thrilled to have you here! Get ready to dive into your new account.
        </div>-->

        <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <!-- LOGO -->

            <!-- HERO -->
<!--            <tr>
                <td align="center" style="padding: 0px 10px 0px 10px;">
                    [if (gte mso 9)|(IE)]>
                    <table align="center" border="0" cellspacing="0" cellpadding="0" width="600">
                    <tr>
                    <td align="center" valign="top" width="600">
                    <![endif]
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td bgcolor="#ffffff" align="center" valign="top" style="padding: 40px 20px 20px 20px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: 'Poppins', sans-serif; font-size: 48px; font-weight: 400; letter-spacing: 2px; line-height: 48px;">
                                <h1 style="font-size: 36px; font-weight: 600; margin: 0;">O2 Intraday Report!!</h1>
                            </td>
                        </tr>
                    </table>
                    [if (gte mso 9)|(IE)]>
                    </td>
                    </tr>
                    </table>
                    <![endif]
                </td>
            </tr>-->
            <!-- COPY BLOCK -->
            <tr>
                <td align="center" style="padding: 0px 10px 0px 10px;">
                    <!--[if (gte mso 9)|(IE)]>
                    <table align="center" border="0" cellspacing="0" cellpadding="0" width="600">
                    <tr>
                    <td align="center" valign="top" width="600">
                    <![endif]-->
                    <h2>O2 Sales</h2>
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" >
                        <!-- COPY -->
                        <tr>
                            <td bgcolor="#ffffff" align="left" style="padding: 20px 30px 40px 30px; color: #666666; font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 400; line-height: 25px;">
                                <table border="1" cellpadding="1" cellspacing="1" width="100%" class="table table-bordered">
                                    <thead>
                                        <tr style="background-color: teal;color:#fff;text-align: center;">
                                            <th>TIME</th>
                                            <th>Calls Offered</th>
                                            <th>Calls Answered</th>
                                            <th>Calls Abandoned</th>
                                            <th>Abandoned %</th>
                                            <th>PCA %</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach($data['data1'] as $value)
                                            <tr style="text-align: center;">
                                                <td style="background-color: teal;color:#fff;">{{$value->CallDate}}.00</td>
                                                <td>{{$value->Calls}}</td>
                                                <td>{{$value->Connects}}</td>
                                                <td>{{$value->DROP_CALL}}</td>
                                                <td>{{$value->Abandon}}%</td>
                                                <td>{{(!empty($value->Calls)) ? number_format((($value->DROP_CALL/$value->Calls)*100),2) : 0}}%</td>
                                            </tr>
                                            @endforeach	
                                            
                                            <tr>
                                                <th style="background-color: teal;color:#fff;"></th>
                                                <th>{{$data['data3'][0]->Calls}}</th>
                                                <th>{{$data['data3'][0]->Connects}}</th>
                                                <th>{{$data['data3'][0]->DROP_CALL}}</th>
                                                <th>{{$data['data3'][0]->Abandon}}%</th>
                                                <th>{{(!empty($data['data4'][0]->Calls)) ? number_format((($data['data4'][0]->DROP_CALL/$data['data4'][0]->Calls)*100),2) : 0}}%</th>
                                            </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <!-- BULLETPROOF BUTTON -->
                    </table>
                    <h2>O2 Retentions</h2>
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" >
                        <!-- COPY -->
                        <tr>
                            <td bgcolor="#ffffff" align="left" style="padding: 20px 30px 40px 30px; color: #666666; font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 400; line-height: 25px;">
                                <table border="1" cellpadding="1" cellspacing="1" width="100%" class="table table-bordered">
                                    <thead>
                                        <tr style="background-color: teal;color:#fff;text-align: center;">
                                            <th>TIME</th>
                                            <th>Calls Offered</th>
                                            <th>Calls Answered</th>
                                            <th>Calls Abandoned</th>
                                            <th>Abandoned %</th>
                                            <th>PCA %</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @foreach($data['data2'] as $value)
                                            <tr style="text-align: center;">
                                                <td style="background-color: teal;color:#fff;">{{$value->CallDate}}.00</td>
                                                <td>{{$value->Calls}}</td>
                                                <td>{{$value->Connects}}</td>
                                                <td>{{$value->DROP_CALL}}</td>
                                                <td>{{$value->Abandon}}%</td>
                                                <td>{{(!empty($value->Calls)) ? number_format((($value->DROP_CALL/$value->Calls)*100),2) : 0}}%</td>
                                            </tr>
                                            @endforeach	
                                            
                                            <tr>
                                                <th style="background-color: teal;color:#fff;"></th>
                                                <th>{{$data['data4'][0]->Calls}}</th>
                                                <th>{{$data['data4'][0]->Connects}}</th>
                                                <th>{{$data['data4'][0]->DROP_CALL}}</th>
                                                <th>{{$data['data4'][0]->Abandon}}%</th>
                                                <th>{{(!empty($data['data4'][0]->Calls)) ? number_format((($data['data4'][0]->DROP_CALL/$data['data4'][0]->Calls)*100),2) : 0}}%</th>
                                            </tr>
                                            
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <!-- BULLETPROOF BUTTON -->
                    </table>
                   
                    <!--[if (gte mso 9)|(IE)]>
                    </td>
                    </tr>
                    </table>
                    <![endif]-->
                </td>
            </tr>

        </table>

    </body>
</html>


