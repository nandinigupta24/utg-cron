
<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <link href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <body>

        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="example">
                            <thead>
                                <tr>
                                    <th>Customer_ID</th>
                                    <th>Account_Id</th>
                                    <th>Subscriber_ID</th>
                                    <th>Campaign_Code</th>
                                    <th>Cell_Code</th>
                                    <th>Treatment_Code</th>
                                    <th>Response_Date_Time</th>
                                    <th>ResponseStatus_Code</th>
                                    <th>Response_Channel</th>
                                    <th>Link_ID</th>
                                    <th>Link_Name</th>
                                    <th>Sub_Id</th>
                                    <th>Sub_Id_Description</th>
                                    <th>Response_Text</th>
                                    <th>ResponseReason_Code</th>
                                    <th>Product_Offer_Code</th>
                                    <th>Forward_Count</th>
                                    <th>Product_Offer_Desc</th>
                                    <th>Responding_MPN</th>
                                    <th>Product_Source_System</th>
                                    <th>custom_1</th>
                                    <th>custom_2</th>
                                    <th>custom_3</th>
                                    <th>custom_4</th>
                                    <th>custom_5</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($PostArray as $array)

                                <tr>
                                    <td>{{@$array['Customer_ID']}}</td>
                                    <td>{{@$array['Account_Id']}}</td>
                                    <td>{{@$array['Subscriber_ID']}}</td>
                                    <td>{{@$array['Campaign_Code']}}</td>
                                    <td>{{@$array['Cell_Code']}}</td>
                                    <td>{{str_pad(@$array['Treatment_Code'], 9, '0', STR_PAD_LEFT)}}</td>
                                    <td>{{@$array['Response_Date_Time']}}</td>
                                    <td>{{@$array['ResponseStatus_Code']}}</td>
                                    <td>{{@$array['Response_Channel']}}</td>
                                    <td>{{@$array['Link_ID']}}</td>
                                    <td>{{@$array['Link_Name']}}</td>
                                    <td>{{@$array['Sub_Id']}}</td>
                                    <td>{{@$array['Sub_Id_Description']}}</td>
                                    <td>{{@$array['Response_Text']}}</td>
                                    <td>{{@$array['ResponseReason_Code']}}</td>
                                    <td>{{@$array['Product_Offer_Code']}}</td>
                                    <td>{{@$array['Forward_Count']}}</td>
                                    <td>{{@$array['Product_Offer_Desc']}}</td>
                                    <td>{{@$array['Responding_MPN']}}</td>
                                    <td>{{@$array['Product_Source_System']}}</td>
                                    <td>{{@$array['custom_1']}}</td>
                                    <td>{{@$array['custom_2']}}</td>
                                    <td>{{@$array['custom_3']}}</td>
                                    <td>{{@$array['custom_4']}}</td>
                                    <td>{{@$array['custom_5']}}</td>
                                    <td>{{@$array['Status']}}</td>
                                </tr>

                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <script>
    $(document).ready(function () {
        $('#example').DataTable();
    });
        </script>
    </body>
</html>
