<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\ScriptResponse;
use App\Model\SwitchExpertEnergy\SDSales;
use Session;
use Mail;
use Excel;
use Illuminate\Support\Facades\Artisan;
use App\Model\O2Inbound\InboundSale;
use App\Model\UTGAPI\O2ReturnProcessData;
use App\Model\UTGAPI\O2UNICA;

class ProdCRM extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ProdCRM';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        
        
//        $query = "SELECT CompanyRegNo,CompanyName,address_line_1,address_line_2,address_line_3,address_line_4,Town,County,Telephone_Number,Employees,Website,Company_Turnover,SIC2_Description,
//Contact_Title,Contact_First_Name,Contact_Last_Name,Contact_DOB,SO_Contact_Job_Title,Contact_Mobile,Contact_Email
//FROM intelling.B2B_intelling limit 1,1";
//        $data = DB::connection('Intelling')->select($query);
////        pr($data[0]);
////        exit;
//        $queryData = [];
//        $queryData['name'] = (!empty($data[0]->Contact_First_Name)) ? @$data[0]->Contact_Title.' '.@$data[0]->Contact_First_Name.' '.@$data[0]->Contact_Last_Name : $data[0]->CompanyName;
//        $queryData['email'] = $data[0]->Contact_Email;
//        $queryData['phonenumber'] = $data[0]->Telephone_Number;
//        $queryData['company'] = $data[0]->CompanyName;
//        $queryData['city'] = $data[0]->Town;
//        $queryData['state'] = $data[0]->County;
//        $queryData['Country'] = '';
//        $queryData['description'] = $data[0]->SIC2_Description;
//        $queryData['address'] = $data[0]->address_line_1.','.$data[0]->address_line_2.','.$data[0]->address_line_3.','.$data[0]->address_line_4;
//        $queryData['tags'] = '';
//        $queryData['source'] = 3;
//        $queryData['status'] = 2;
//        $queryData['website'] = $data[0]->Website;
//        
//        
////        'status' => string '2' (length=1) 
////                'source' => string '6' (length=1) 
////                'assigned' => string '1' (length=1) 
////                'client_id' => string '5' (length=1) 
////                'tags' => string '' (length=0) 
////                'name' => string 
////                'Lead Name' (length=9) 
////                'contact' => string 
////                'Contact A' (length=9) 
////                'title' => string 
////                'Position A' (length=10) 
////                'email' => string 'AAA@gmail.com' (length=13) 
////                'website' => string '' (length=0) 
////                'phonenumber' => string '123456789' (length=9) 
////                'company' => string 'TheCompany' (length=51) 
////                'address' => string '1 Silk Point' (length=53) 
////                'city' => string 'John Test' (length=9) 
////                'state' => string '' (length=0) 
////                'default_language' => string 'english' (length=10) 
////                'description' => string 'Description' (length=11) 
////                'custom_contact_date' => string '' (length=0) 
////                'is_public' => string 'on' (length=2) 
////                'contacted_today' => string 'on' (length=2)
//        
//        $curl = curl_init();
//        curl_setopt_array($curl, array(
//                CURLOPT_URL => "https://prod-crm.usethegeeks.co.uk/api/leads",
//                CURLOPT_RETURNTRANSFER => true,
//                CURLOPT_ENCODING => "",
//                CURLOPT_MAXREDIRS => 10,
//                CURLOPT_TIMEOUT => 30,
//                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//                CURLOPT_CUSTOMREQUEST => "POST",
//                CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"name\"\r\n\r\n" . $queryData['name'] . "\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"email\"\r\n\r\n" . $queryData['email'] . "\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"phonenumber\"\r\n\r\n" . $queryData['phonenumber'] . "\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"company\"\r\n\r\n" . $queryData['company'] . "\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"country\"\r\n\r\n" . $queryData['Country'] . "\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"description\"\r\n\r\n" . $queryData['description'] . "\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"tags\"\r\n\r\n" . $queryData['tags'] . "\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"source\"\r\n\r\n" . $queryData['source'] . "\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"status\"\r\n\r\n" . $queryData['status'] . "\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--\r\nContent-Disposition: form-data; name=\"address\"\r\n\r\n" . $queryData['address'] . "\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"website\"\r\n\r\n" . $queryData['website'] . "\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"city\"\r\n\r\n" . $queryData['city'] . "\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"state\"\r\n\r\n" . $queryData['state'] . "\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
//                CURLOPT_HTTPHEADER => array(
//                    "authtoken: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoiVVRHVUsiLCJuYW1lIjoiVVRHVUsiLCJwYXNzd29yZCI6bnVsbCwiQVBJX1RJTUUiOjE1ODA0NzM0Mjh9.HtVgknsPF7U_jqrlqnTeMAcv7R8pzmTDCdyWwGgxSPM",
//                    "cache-control: no-cache",
//                    "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW",
//                    "postman-token: 52c23060-edc0-8e94-a7ed-f8a39199ab9a"
//                ),
//            ));
//
//            $response = curl_exec($curl);
//            $err = curl_error($curl);
//
//            curl_close($curl);
//
//            if ($err) {
//                echo "cURL Error #:" . $err;
//            } else {
//                echo $response;
//            }
    }

}
