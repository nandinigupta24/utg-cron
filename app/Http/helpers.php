<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function customAsset($path, $secure = null) {
    return app('url')->asset('assets_new/' . $path, $secure);
}

function pr($array) {
    echo '<pre>';
    print_r($array);
}

function prd($array) {
    echo '<pre>';
    print_r($array);
    die();
}

function get_query_O2ReturnProcess($listId, $start, $end) {
    switch ($listId) {
        case 3001:
            $query = "select
L.lead_id,
L.vendor_lead_code,
L.status,
L.entry_date,
L.phone_number,
IF(entry_date IS NULL, '', '') AS Customer_ID,

IF(entry_date IS NULL, '', '') AS Account_Id,

P.custom_3 as Subscriber_ID,

P.custom_4 as Campaign_Code,

P.custom_5 as Cell_Code,

P.custom_7 as Treatment_Code,

date_format(L.last_local_call_time,'%Y%m%d%H%i%s') as Response_Date_Time,

(CASE

when L.status in ('A','AA','AB','ADC','ADCT','AFAX','AFTHRS','AL','AM','B','CHU','CPDATB','CPDB','CPDERR','CPDINV','CPDNA','CPDREJ','CPDSI',

'CPDSNC','CPDSR','CPDSUA','CPDSUK','CPDSV','CPDUK','DC','DECAD','DECB','DECCC','DL','DROP','ERI','FAX','INCALL','IVRXFR','LRERR',

'LSMERG','MAXCAL','MLINAT','N','NA','NANQUE','NEW','PDROP','PM','QCFAIL','QUEUE','QVMAIL','RLEDUP','TIMEOT',

'XDROP','DTEST','FAILT') then 25

when L.status in (' Busin','CALLBK','CBHOLD','DNC','DNCC','DNCL','GCALLB','LB','NOP','OPTOUT','PU','TPS','WD','WEBDOW',

'WRNNUM','DNOI','INCSC','Busin') then 24

when L.status in ('ACOnly','HS','SALE','TCAC','TCOnly','TSALE') then 28

when L.status in ('LODD','LU','MAE','NBD','NI','CBDNI','IC','InCon','NONLLU','NOUK','PPNG','WHS','REDPA') then 29

when L.status in ('REFER','UA','UTMUR') then 27

when L.status in ('O2C') then 26

ELSE ''

    END) AS ResponseStatus_Code,

IF(entry_date IS NULL, 'T', 'T') Response_Channel,

IF(entry_date IS NULL, '', '') AS Link_ID,

IF(entry_date IS NULL, '', '') AS Link_Name,

IF(entry_date IS NULL, '', '') AS Sub_Id,

IF(entry_date IS NULL, '', '') AS Sub_Id_Description,

IF(entry_date IS NULL, '', '') AS Response_Text,

(case

when status in ('CALLBK','CBHOLD','GCALLB','UA','INCSC') then 'A02'

when status in ('LB') then 'A03'

when status in ('WEBDOW','WD','WRNNUM') then 'A06'

when status in ('PU','CHU','AFTHRS') then 'A09'

when status in ('CPDB') then 'C03'

when status in (' Busin','DECAD','DL','LODD','LU','MAE','NBD','NOP','QUEUE','IC','InCon','NONLLU','NOUK','O2C','PPNG','UTMUR','WHS','Busin') then 'F07'

when status in ('DROP','ERI','MAXCAL','NANQUE','PM','QCFAIL','TIMEOT','XDROP','FAILT') then 'A10'

when status in ('AB','B') then 'B01'

when status in ('FAX','AFAX') then 'B02'

when status in ('A','AA','AL','AM') then 'B03'

when status in ('NEW','NA','N','QVMAIL','REFER') then 'B04'

when status in ('CPDATB','CPDB','CPDERR','CPDINV','CPDNA','CPDREJ','CPDSUA','CPDSI','CPDSNC','CPDSR','CPDSUK','CPDSV','CPDUK','RLEDUP','DECCC','DC','ADC','ADCT','IVRXFR','LRERR','PDROP','INCALL',

'LSMERG','MLINAT','DTEST') then 'B05'

when status in ('DECB') then 'F10'

when status in ('HS','SALE','ACOnly','TCAC','TCOnly','TSALE') then 'E01'

when status in ('NI','CBDNI') then 'F03'

when status in ('DNC','OPTOUT','DNCC','DNCL','DNOI') then 'C03'

when status in ('TPS') then 'D06'
when status in ('REDPA') then 'F11'

Else ''

END) as ResponseReason_Code,

(case

when L.status in ('ACOnly') then 'SALE_ACOnly'

when L.status in ('HS') then 'Handset_Sale'

when L.status in ('SALE') then 'sale'

when L.status in ('TCAC') then 'SALE-TC&AC'

when L.status in ('TCOnly') then 'SALE-TCOnly'

when L.status in ('TSALE') then 'Tablet_Sale'

Else ''

END) AS Product_Offer_Code,

IF(entry_date IS NULL, '', '') AS Forward_Count,

IF(entry_date IS NULL, '', '') AS Product_Offer_Desc,

IF(entry_date IS NULL, '', '') AS Responding_MPN,

IF(entry_date IS NULL, '', '') AS Product_Source_System,

#P.custom_1 as custom_1,

P.custom_2 as custom_1,

P.custom_6 as custom_2,

#P.custom_8 as custom_4,

P.custom_9 as custom_3,

#P.custom_10 as custom_6,

P.custom_11 as custom_4,

#P.custom_12 as custom_8,

P.custom_13 as custom_5

from list L

JOIN custom_fields_data P

on L.lead_id=P.lead_id

where L.list_id = '3001'

#and L.status = 'SALE'

and L.last_local_call_time !='NULL'

#and P.custom_4 = 5636

and P.custom_7 != ''

and L.last_local_call_time between '" . $start . "' and '" . $end . "'";
            break;
        case 3005:
            $query = "select
L.lead_id,
L.vendor_lead_code,
L.status,
L.entry_date,
L.phone_number,
P.custom_2 AS Customer_ID,

IF(entry_date IS NULL, '', '') AS Account_Id,

P.custom_3 as Subscriber_ID,

P.custom_4 as Campaign_Code,

P.custom_5 as Cell_Code,

P.custom_7 as Treatment_Code,

date_format(L.last_local_call_time,'%Y%m%d%H%i%s') as Response_Date_Time,

(CASE

when L.status in ('A','AA','AB','ADC','ADCT','AFAX','AFTHRS','AL','AM','B','CHU','CPDATB','CPDB','CPDERR','CPDINV','CPDNA','CPDREJ','CPDSI',

'CPDSNC','CPDSR','CPDSUA','CPDSUK','CPDSV','CPDUK','DC','DECAD','DECB','DECCC','DL','DROP','ERI','FAX','INCALL','IVRXFR','LRERR',

'LSMERG','MAXCAL','MLINAT','N','NA','NANQUE','NEW','PDROP','PM','QCFAIL','QUEUE','QVMAIL','RLEDUP','TIMEOT',

'XDROP','DTEST','FAILT') then 25

when L.status in (' Busin','CALLBK','CBHOLD','DNC','DNCC','DNCL','GCALLB','LB','NOP','OPTOUT','PU','TPS','WD','WEBDOW',

'WRNNUM','DNOI','INCSC','Busin') then 24

when L.status in ('ACOnly','HS','SALE','TCAC','TCOnly','TSALE') then 28

when L.status in ('LODD','LU','MAE','NBD','NI','CBDNI','IC','InCon','NONLLU','NOUK','PPNG','WHS','REDPA') then 29

when L.status in ('REFER','UA','UTMUR') then 27

when L.status in ('O2C') then 26

ELSE ''

    END) AS ResponseStatus_Code,

IF(entry_date IS NULL, 'T', 'T') Response_Channel,

IF(entry_date IS NULL, '', '') AS Link_ID,

IF(entry_date IS NULL, '', '') AS Link_Name,

IF(entry_date IS NULL, '', '') AS Sub_Id,

IF(entry_date IS NULL, '', '') AS Sub_Id_Description,

IF(entry_date IS NULL, '', '') AS Response_Text,

(case

when status in ('CALLBK','CBHOLD','GCALLB','UA','INCSC') then 'A02'

when status in ('LB') then 'A03'

when status in ('WEBDOW','WD','WRNNUM') then 'A06'

when status in ('PU','CHU','AFTHRS') then 'A09'

when status in ('CPDB') then 'C03'

when status in (' Busin','DECAD','DL','LODD','LU','MAE','NBD','NOP','QUEUE','IC','InCon','NONLLU','NOUK','O2C','PPNG','UTMUR','WHS','Busin') then 'F07'

when status in ('DROP','ERI','MAXCAL','NANQUE','PM','QCFAIL','TIMEOT','XDROP','FAILT') then 'A10'

when status in ('AB','B') then 'B01'

when status in ('FAX','AFAX') then 'B02'

when status in ('A','AA','AL','AM') then 'B03'

when status in ('NEW','NA','N','QVMAIL','REFER') then 'B04'

when status in ('CPDATB','CPDB','CPDERR','CPDINV','CPDNA','CPDREJ','CPDSUA','CPDSI','CPDSNC','CPDSR','CPDSUK','CPDSV','CPDUK','RLEDUP','DECCC','DC','ADC','ADCT','IVRXFR','LRERR','PDROP','INCALL',

'LSMERG','MLINAT','DTEST') then 'B05'

when status in ('DECB') then 'F10'

when status in ('HS','SALE','ACOnly','TCAC','TCOnly','TSALE') then 'E01'

when status in ('NI','CBDNI') then 'F03'

when status in ('DNC','OPTOUT','DNCC','DNCL','DNOI') then 'C03'

when status in ('TPS') then 'D06'
when status in ('REDPA') then 'F11'

Else ''

END) as ResponseReason_Code,

(case

when L.status in ('ACOnly') then 'SALE_ACOnly'

when L.status in ('HS') then 'Handset_Sale'

when L.status in ('SALE') then 'sale'

when L.status in ('TCAC') then 'SALE-TC&AC'

when L.status in ('TCOnly') then 'SALE-TCOnly'

when L.status in ('TSALE') then 'Tablet_Sale'

Else ''

END) AS Product_Offer_Code,

IF(entry_date IS NULL, '', '') AS Forward_Count,

IF(entry_date IS NULL, '', '') AS Product_Offer_Desc,

IF(entry_date IS NULL, '', '') AS Responding_MPN,

IF(entry_date IS NULL, '', '') AS Product_Source_System,

#P.custom_1 as custom_1,

P.custom_11 as custom_1,

P.custom_12 as custom_2,

#P.custom_8 as custom_4,

P.custom_13 as custom_3,

#P.custom_10 as custom_6,

P.custom_14 as custom_4,

#P.custom_12 as custom_8,

P.custom_16 as custom_5

from list L

JOIN custom_fields_data P

on L.lead_id=P.lead_id

where L.list_id = '3005'

#and L.status = 'SALE'

and L.last_local_call_time !='NULL'

#and P.custom_4 = 5636

and P.custom_7 != ''

and P.custom_5 !=''

and L.last_local_call_time between '" . $start . "' and '" . $end . "'";
            break;
        default:
    }
    return $query;
}

/* Mobile Numbers */

function check_mobile_number($number = null) {
    if (strlen($number) == 10) {
        return '0' . $number;
    } else {
        if (strlen($number) == 12) {
            return '0' . substr($number, 2);
        } else {
            if (strlen($number) == 11) {
                return $number;
            } else {
                return '0' . $number;
            }
        }
    }
}

function check_mobile_number_IC($number = null) {
    if (strlen($number) == 10) {
        $phoneNumber = '0' . $number;
    } else {
        if (strlen($number) == 12) {
            $phoneNumber = '0' . substr($number, 2);
        } else {
            if (strlen($number) == 11) {
                $phoneNumber = $number;
            } else {
                $phoneNumber = '0' . $number;
            }
        }
    }
    settype($phoneNumber, "string");
    return $phoneNumber;
}

function O2ReturnProcessValidation($value) {
    $response = [];
    foreach ($value as $key => $val) {
        if ($key == 'Campaign_Code') {
            if (strlen($val) != 4) {
                $response[] = 'Campaign Code will be 4 digit.';
            }
        } elseif ($key == 'Cell_Code') {
            if (substr($val, 0, 1) == 'A') {
                $number = substr($val, 1, 9);

                if ($number >= 000000001 && $number <= 999999999) {

                } else {
                    $response[] = 'Cell Code not matched in between A000000001 to A999999999.';
                }
            } else {
                $response[] = 'Cell Code not matched in between A000000001 to A999999999';
            }
        } elseif ($key == 'Treatment_Code') {
            if ($val >= 000000001 && $val <= 999999999) {

            } else {
                $response[] = 'Treatment Code will not matched in between 000000001 to 999999999.';
            }
        } elseif ($key == 'Response_Date_Time') {
            if (strlen($val) != 14) {
                $response[] = 'Response Date Time will not matched.';
            }
        } elseif ($key == 'ResponseStatus_Code') {
            if (!in_array($val, range(24, 29))) {
                $response[] = 'Response Status Code not matched in range of 24 to 29.';
            }
        } elseif ($key == 'Response_Channel') {
            if (!in_array($val, ['T', 'E', 'V', 'M', 'S'])) {
                $response[] = 'Response Channel Code not matched in T,E,V,M,S.';
            }
        } elseif ($key == 'Customer_ID') {
            if (!empty($val) && is_numeric($val)) {
                if ($val == NULL || $val == 'NULL') {
                    $response[] = 'Customer ID not matched as integer & not length max then 10.';
                } else {
                    settype($val, 'integer');
                    if ($val >= 0000000000 && $val <= 9999999999) {

                    } else {
                        $response[] = 'Customer ID not matched as integer & not length max then 10.';
                    }
                }
            } else {
                $response[] = 'Customer ID not matched as integer & not length max then 10.';
            }
        } elseif ($key == 'Subscriber_ID') {
            if (!empty($val)) {
                if ($val == NULL || $val == 'NULL') {
                    $response[] = 'Subscriber ID not matched as integer & not length max then 10.';
                } else {
                    settype($val, 'integer');
                    if ($val >= 0000000000 && $val <= 9999999999) {

                    } else {
                        $response[] = 'Subscriber ID not matched as integer & not length max then 10.';
                    }
                }
            } else {
                $response[] = 'Subscriber ID not matched as integer & not length max then 10.';
            }
        } elseif ($key == 'vendor_lead_code') {
            if (!empty($val) && is_numeric($val)) {
                if ($val == NULL || $val == 'NULL') {
                    $response[] = 'Account ID not matched as integer & not length max then 10.';
                } else {
                    settype($val, 'integer');
                    if ($val >= 0000000000 && $val <= 9999999999) {

                    } else {
                        $response[] = 'Account ID not matched as integer & not length max then 10.';
                    }
                }
            } else {
                $response[] = 'Account ID not matched as integer & not length max then 10.';
            }
        }
    }
    return $response;
}

function O2ReturnProcessValidation3001($value) {
    $response = [];
    foreach ($value as $key => $val) {
        if ($key == 'Campaign_Code') {
            if (strlen($val) != 4) {
                $response[] = 'Campaign Code will be 4 digit.';
            }
        } elseif ($key == 'Cell_Code') {
            if (substr($val, 0, 1) == 'A') {
                $number = substr($val, 1, 9);

                if ($number >= 000000001 && $number <= 999999999) {

                } else {
                    $response[] = 'Cell Code not matched in between A000000001 to A999999999.';
                }
            } else {
                $response[] = 'Cell Code not matched in between A000000001 to A999999999';
            }
        } elseif ($key == 'Treatment_Code') {
            if ($val >= 000000001 && $val <= 999999999) {

            } else {
                $response[] = 'Treatment Code will not matched in between 000000001 to 999999999.';
            }
        } elseif ($key == 'Response_Date_Time') {
            if (strlen($val) != 14) {
                $response[] = 'Response Date Time will not matched.';
            }
        } elseif ($key == 'ResponseStatus_Code') {
            if (!in_array($val, range(24, 29))) {
                $response[] = 'Response Status Code not matched in range of 24 to 29.';
            }
        } elseif ($key == 'Customer_ID') {
            if (!empty($val) && is_numeric($val)) {
                if ($val == NULL || $val == 'NULL') {
                    $response[] = 'Customer ID not matched as integer & not length max then 10.';
                } else {
                    settype($val, 'integer');
                    if ($val >= 0000000000 && $val <= 9999999999) {

                    } else {
                        $response[] = 'Customer ID not matched as integer & not length max then 10.';
                    }
                }
            } else {
                $response[] = 'Customer ID not matched as integer & not length max then 10.';
            }
        } elseif ($key == 'vendor_lead_code') {
            if (!empty($val) && is_numeric($val)) {
                if ($val == NULL || $val == 'NULL') {
                    $response[] = 'Account ID not matched as integer & not length max then 10.';
                } else {
                    settype($val, 'integer');
                    if ($val >= 0000000000 && $val <= 9999999999) {

                    } else {
                        $response[] = 'Account ID not matched as integer & not length max then 10.';
                    }
                }
            } else {
                $response[] = 'Account ID not matched as integer & not length max then 10.';
            }
        }
    }
    return $response;
}

function O2ReturnProcessValidation1330($value) {
    $response = [];
    foreach ($value as $key => $val) {
        if ($key == 'Campaign_Code') {
            if (strlen($val) != 4) {
                $response[] = 'Campaign Code will be 4 digit.';
            }
        } elseif ($key == 'Cell_Code') {
            if (substr($val, 0, 1) == 'A') {
                $number = substr($val, 1, 9);

                if ($number >= 000000001 && $number <= 999999999) {

                } else {
                    $response[] = 'Cell Code not matched in between A000000001 to A999999999.';
                }
            } else {
                $response[] = 'Cell Code not matched in between A000000001 to A999999999';
            }
        } elseif ($key == 'Treatment_Code') {
            if ($val >= 000000001 && $val <= 999999999) {

            } else {
                $response[] = 'Treatment Code will not matched in between 000000001 to 999999999.';
            }
        } elseif ($key == 'Response_Date_Time') {
            if (strlen($val) != 14) {
                $response[] = 'Response Date Time will not matched.';
            }
        } elseif ($key == 'ResponseStatus_Code') {
            if (!in_array($val, range(24, 29))) {
                $response[] = 'Response Status Code not matched in range of 24 to 29.';
            }
        } elseif ($key == 'CustId') {
            if (!empty($val) && is_numeric($val)) {
                if ($val == NULL || $val == 'NULL') {
                    $response[] = 'Customer ID not matched as integer & not length max then 10.';
                } else {
                    settype($val, 'integer');
                    if ($val >= 0000000000 && $val <= 9999999999) {

                    } else {
                        $response[] = 'Customer ID not matched as integer & not length max then 10.';
                    }
                }
            } else {
                $response[] = 'Customer ID not matched as integer & not length max then 10.';
            }
        } elseif ($key == 'vendor_lead_code') {
            if (!empty($val) && is_numeric($val)) {
                if ($val == NULL || $val == 'NULL') {
                    $response[] = 'Account ID not matched as integer & not length max then 10.';
                } else {
                    settype($val, 'integer');
                    if ($val >= 0000000000 && $val <= 9999999999) {

                    } else {
                        $response[] = 'Account ID not matched as integer & not length max then 10.';
                    }
                }
            } else {
                $response[] = 'Account ID not matched as integer & not length max then 10.';
            }
        }
    }
    return $response;
}

function get_calculate_FTE_3005($start, $end) {
    $userGroup = ['Belfast' => 'Belfast', 'SYN' => 'Synergy', 'SLM' => 'SLM', 'TP' => 'Teleperformance', 'Out' => 'Outworx'];
    $newArray = [];
    foreach ($userGroup as $key => $group) {
        $groups = DB::connection('OmniDialer')
                        ->table('user_groups')
                        ->where('allowed_campaigns', 'LIKE', '%3005%')
                        ->where('group_name', 'like', '%' . $key . '%')
                        ->pluck('user_group')->toArray();
        $data = \DB::connection('OmniDialer')
                ->table('outbound_log')
                ->where('call_date', '>=', $start)
                ->where('call_date', '<=', $end)
                ->where('campaign_id', '3005')
                ->whereIn('user_group', $groups)
                ->select(DB::RAW('count(distinct(user)) as groupName'))
                ->get();
        $newArray[$group] = (!empty($data[0]->groupName)) ? $data[0]->groupName : 0;
    }
    return $newArray;
}

function get_name() {
    return 'Hi ANKUSH';
}

function get_omni_api_curl_test($user, $pass, $token, $postData1) {

    $Host = 'api3.cnx1.uk';
    $user = 'IntellingTwo';
    $pass = 'Eg926bD5GfbGEJwG';
    $token = 'brwh890FraGrLy1VpvwU9KwDhAwT0EdB4dlWc8IqpzBIjh894L';
    $postData1['token'] = $token;
    // Step One: Authenticate using credentials

    $postData = Array('username' => $user, 'password' => $pass);

    $ch = curl_init('https://api3.cnx1.uk/consumer/login');
    curl_setopt_array($ch, array(
        CURLOPT_POST => TRUE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'User-Agent: Intelling-API'
        ),
        CURLOPT_POSTFIELDS => json_encode($postData)
    ));

    $output = curl_exec($ch);

    $authtokens = json_decode($output, true);

    $authtoken = $authtokens['token'];
//    $authtoken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiI4YTlkMmQwZTNkNjM3OTRmM2E1Ny03YzdmNWQ0NTY4ZmUxZDZkZGJmM2Y5NDc5ZjkzODk5Y2VkZjcwMzBhNGJlMmVhYzgtMzNiMDMxNmQzYiIsImlhdCI6MTU0ODE0OTgyNCwiZXhwIjoxNTQ4MTUzNDI0LCJuYW1lIjoiSW50ZWxsaW5nLU9tbmlDaGFubmVsIn0.hRT51dHvS0nfvuG8QSd6UfmQofYyriUi0bZjow9jgLs';
//    echo $authtoken;
    $ch = curl_init('https://api3.cnx1.uk/customer/bulk_create');
    curl_setopt_array($ch, array(
        CURLOPT_POST => TRUE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $authtoken,
            'Content-Type: application/json',
            'User-Agent: Intelling-API'
        ),
        CURLOPT_POSTFIELDS => json_encode($postData1)
    ));
    $exec = curl_exec($ch);

    return json_decode($exec);
}

function get_empty($value, $replace) {
    if (!empty($value)) {
        return (!empty(trim($value)) && strlen(trim($value)) > 0) ? $value : '';
    } else {
        return $replace;
    }
}

function get_omni_api_curl($user, $pass, $postData1) {

    // Step One: Authenticate using credentials

    $postData = Array('username' => $user, 'password' => $pass);

    $ch = curl_init('https://api3.cnx1.uk/consumer/login');
    curl_setopt_array($ch, array(
        CURLOPT_POST => TRUE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'User-Agent: Intelling-API'
        ),
        CURLOPT_POSTFIELDS => json_encode($postData)
    ));

    $output = curl_exec($ch);

    $authtokens = json_decode($output, true);
    $authtoken = $authtokens['token'];

    $ch = curl_init('https://api3.cnx1.uk/customer/create');
    curl_setopt_array($ch, array(
        CURLOPT_POST => TRUE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $authtoken,
            'Content-Type: application/json',
            'User-Agent: Intelling-API'
        ),
        CURLOPT_POSTFIELDS => json_encode($postData1)
    ));
    $exec = curl_exec($ch);

    return json_decode($exec);
}

function get_data_sequence($currentPage, $perPage) {
    return ((($currentPage - 1) * $perPage ) + 1);
}

function get_duplicate_status($status) {
    switch ($status) {
        case 'yes':
            $statusReturn = 'Duplicate';
            $Class = 'warning';
            break;
        case 'no':
            $statusReturn = 'Loaded';
            $Class = 'success';
            break;
        default:
            $statusReturn = 'Not Defined';
            $Class = 'danger';
    }
    return '<label class="badge badge-' . $Class . '">' . $statusReturn . '</label>';
}

function get_phone_numbers($phone, $replace) {
    return preg_replace('/^(0*44|(?!\+0*44)0*)/', $replace, $phone);
}

function getUnserialize($data) {
    $output = array();
    $string = trim(preg_replace('/\s\s+/', ' ', $data));
    $string = preg_replace_callback('!s:(\d+):"(.*?)";!', function($m) {
        return 's:' . strlen($m[2]) . ':"' . $m[2] . '";';
    }, utf8_encode(trim(preg_replace('/\s\s+/', ' ', $string))));
    try {
        $output = unserialize($string);
    } catch (\Exception $e) {
        \Log::error("unserialize Data : " . print_r($string, true));
    }
    return $output;
}

function get_customerid_api_response($response) {
    if (!empty($response)) {
        $response = getUnserialize($response);
        if (!empty($response->customer_id)) {
            return $response->customer_id;
        } else {
            return '';
        }
    } else {
        return '';
    }
}

function clean($string) {
    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
    $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

    return preg_replace('/-+/', ' ', $string); // Replaces multiple hyphens with single one.
}

function convert_special_characters($str) {
    return iconv("Windows-1252", "UTF-8", $str);
}

/* Talking Customer Functions */

function get_talking_customer_ITVSO($date, $campaignId) {
    return \DB::connection('MainDialer')->table('inbound_log')
                    ->select(DB::raw('HOUR(call_date) as Hour'), DB::raw('count(*) as total'), DB::raw('SUM((CASE WHEN queue_seconds < 15 THEN 1 ELSE 0 END)) AS lessThan15'))
                    ->whereIn('campaign_id', $campaignId)
                    ->where('call_date', '>=', $date . ' 00:00:00')
                    ->where('call_date', '<', $date . ' 23:59:59')
                    ->whereIn('term_reason', ['AGENT', 'CALLER', 'NONE', 'QUEUETIMEOUT'])
                    ->groupBy(DB::raw('HOUR(call_date)'))
                    ->get();
}

function get_talking_customer_SLA($date, $campaignId) {
    return \DB::connection('MainDialer')->table('inbound_log')
                    ->select(DB::raw('HOUR(call_date) as Hour'), DB::raw('count(*) as total'), DB::raw('SUM((CASE WHEN queue_seconds < 15 THEN 1 ELSE 0 END)) AS lessThan15'))
                    ->whereIn('campaign_id', $campaignId)
                    ->where('call_date', '>=', $date . ' 00:00:00')
                    ->where('call_date', '<', $date . ' 23:59:59')
                    ->whereIn('term_reason', ['AGENT', 'CALLER', 'NONE', 'QUEUETIMEOUT'])
                    ->groupBy(DB::raw('HOUR(call_date)'))
                    ->get();
}

function get_talking_customer_ABANDON($date, $campaignId) {
    return \DB::connection('MainDialer')->table('inbound_log')
                    ->select(DB::raw('HOUR(call_date) as Hour'), DB::raw('count(*) as total'), DB::raw('SUM((CASE WHEN queue_seconds >= 15 THEN 1 ELSE 0 END)) AS greaterthan15'))
                    ->whereIn('campaign_id', $campaignId)
                    ->where('call_date', '>=', $date . ' 00:00:00')
                    ->where('call_date', '<', $date . ' 23:59:59')
                    ->where('term_reason', 'ABANDON')
                    ->groupBy(DB::raw('HOUR(call_date)'))
                    ->get();
}

function get_talking_customer_sales($date, $campaignID) {

    $saleMain = DB::connection('MainDialer')
            ->table('O2Script.customers')
            ->join('O2Script.sales', 'O2Script.customers.lead_id', 'O2Script.sales.lead_id')
            ->join('O2Script.sales_by_orig_agent', 'O2Script.sales.sale_id', 'O2Script.sales_by_orig_agent.sale_id')
            ->join('custom_view.sales_by_source_O2script', 'O2Script.sales_by_orig_agent.sale_id', 'custom_view.sales_by_source_O2script.sale_id')
            ->join('custom_view.inbound_log', 'O2Script.customers.lead_id', 'custom_view.inbound_log.lead_id')
            ->where('O2Script.sales_by_orig_agent.saledate', '>=', $date . ' 00:00:00')
            ->where('O2Script.sales_by_orig_agent.saledate', '<=', $date . ' 23:59:59')
            ->where('custom_view.inbound_log.campaign_id', $campaignID)
            ->where('O2Script.sales.order_num', 'like', 'MS-5%')
            ->count();
    return $saleMain;
}

function get_talking_customer_offered_drop($startDate, $endDate, $campaignId, $queueInSeconds) {
    $response = [];
    $response['offered'] = \DB::connection('MainDialer')->table('inbound_log')
            ->where('campaign_id', $campaignId)
            ->where('call_date', '>=', $startDate)
            ->where('call_date', '<', $endDate)
            ->count();

    $response['drop'] = \DB::connection('MainDialer')->table('inbound_log')
            ->where('campaign_id', $campaignId)
            ->where('call_date', '>=', $startDate)
            ->where('call_date', '<', $endDate)
            ->where('term_reason', 'ABANDON')
            ->where('queue_seconds', '>', $queueInSeconds)
            ->count();

    if (!empty($response['offered'])) {
        $response['output'] = number_format((((!empty($response['offered'])) ? (($response['offered'] - $response['drop']) / $response['offered']) : 0 ) * 100), 2);
    } else {
        $response['output'] = 0;
    }

    return $response;
}

function get_image_path_chart($url, $name, $directory) {
    $content = file_get_contents($url);
    $fp = fopen(storage_path('PieChart') . $directory . $name . ".jpg", "w");
    fwrite($fp, $content);
    fclose($fp);
    return 'https://api.usethegeeks.com/storage/PieChart/' . $directory . $name . '.jpg';
}

function get_intraday_current_SLA($a, $b, $numberFormat) {

    if (!empty($numberFormat)) {
        if (!empty($a)) {
            return number_format(((($a - $b) / $a) * 100), 2);
        } else {
            return 0;
        }
    }

    return (((!empty($b)) ? ($a / $b) : 0 ) * 100);
}

function get_status($status) {
    switch ($status) {
        case 'unpublished':
            $statusReturn = 'UnPublished';
            $Class = 'danger';
            break;
        case 'published':
            $statusReturn = 'Published';
            $Class = 'success';
            break;
        default:
            $statusReturn = 'Not Defined';
            $Class = 'warning';
    }
    return '<label class="badge badge-' . $Class . '">' . $statusReturn . '</label>';
}

function get_campaign_hourly_report($dailerConnection, $start, $end) {
    $query = "select a.campaign_id, a.campaign_name,

      ifnull(a.Calls, 0) + ifnull(b.Calls,0) as CallsPlaced,

      ifnull(a.Agent_Calls, 0) + isnull(b.Calls) as CallsToAgent,

      a.Connects,

      a.DMCs + ifnull(b.DMCs, 0) as DMCs,

      a.Sales + ifnull(b.Sales, 0) as Sales,

    ROUND((a.Sales + ifnull(b.Sales, 0))/(a.DMCs + ifnull(b.DMCs, 0)) * 100, 2) as Conversion,

      a.DroppedCalls as Dropped,

      a.AnswerMachine as AnsweringMachines,

      ROUND(a.DroppedCalls / (a.Connects + a.AnswerMachine + a.DroppedCalls) * 100, 2) as DroppedRate,

      d.Talk, d.Pause, d.Wait, d.Dispo, SEC_TO_TIME(ROUND(d.TotalDMCTalksecs/d.DMCs)) AS AveDMCTalk,d.TotalDMCTalkSecs,

addtime(d.Talk,addtime(d.dispo,d.Wait)) as total_productive,(a.DMCs + ifnull(b.DMCs, 0))/(time_to_sec(addtime(d.Talk,addtime(d.dispo,d.Wait)))/3600) as DMC_Productive,(a.Connects/(ifnull(a.Agent_Calls, 0) + isnull(b.Calls)))*100 as Total_Connect_Rate,

((a.DMCs + ifnull(b.DMCs, 0))/a.Connects)*100 as Total_DMCrate,sec_to_time((time_to_sec(d.talk))/(ifnull(a.Agent_Calls, 0) + isnull(b.Calls))) as avg_talk,sec_to_time((time_to_sec(d.wait))/(ifnull(a.Agent_Calls, 0) + isnull(b.Calls))) as avg_wait,sec_to_time((time_to_sec(d.dispo))/(ifnull(a.Agent_Calls, 0) + isnull(b.Calls))) as avg_wrap



  from



  (select date(al.call_date) as Date, al.`campaign_id`, c.campaign_name,

  sum(case when status is not null and al.comments not in ('CHAT','EMAIL') then 1 else 0 end) as Calls,

  sum(case when status is not null and al.comments not in ('CHAT','EMAIL') and user != 'VDAD' then 1 else 0 end) as Agent_Calls,

  sum(case when al.status in (select status from campaign_status where human_answered = 'Y') or Status in (select status from system_status where human_answered = 'Y') then 1 else 0 end) as Connects,

  sum(case when al.status in (select status from campaign_status where customer_contact = 'Y') or Status in (select status from system_status where customer_contact = 'Y') then 1 else 0 end) as DMCs,

  sum(case when al.status in (select status from campaign_status where Sale = 'Y') or Status in (select status from system_status where Sale = 'Y') then 1 else 0 end) as Sales,

  sum(case when al.status = 'A' then 1 else 0 end) as AnswerMachine,

  sum(case when al.status in ('DROP') then 1 else 0 end) as DroppedCalls

  from outbound_log al

  JOIN campaigns c on al.campaign_id = c.campaign_id

  WHERE call_date between '" . $start . "' and '" . $end . "'

  group by date(al.call_date), al.`campaign_id`, c.campaign_name

  ) a

  LEFT JOIN



  (select date(cl.call_date) as Date, cl.`campaign_id`,

  sum(case when al.`list_id` is not null and cl.comments not in ('CHAT','EMAIL') then 1 else 0 end) as Calls,

  sum(case when al.`list_id` is not null and cl.comments = 'CHAT' then 1 else 0 end) as Chats,

  sum(case when cl.status in (select status from campaign_status where customer_contact = 'Y') or cl.status in (select status from system_status where customer_contact = 'Y') then 1 else 0 end) as DMCs,

  sum(case when cl.status in (select status from campaign_status where Sale = 'Y') or cl.status in (select status from system_status where Sale = 'Y') then 1 else 0 end) as Sales

  from inbound_log as cl, agent_log as al where al.uniqueid = cl.uniqueid and

  cl.call_date between '" . $start . "' and '" . $end . "'

  and al.event_time between '" . $start . "' and '" . $end . "'

  group by date(cl.call_date), al.`campaign_id`

  ) b on a.Date = b.Date and a.campaign_id = b.campaign_id



LEFT JOIN

(select date(al.event_time) as Date, al.`campaign_id` AS campaign_id,

        sum(case when status is not null then 1 else 0 end) as Calls,

        sum(case when al.status in (select status from campaign_status where human_answered = 'Y') or Status in (select status from system_status where human_answered = 'Y') then 1 else 0 end) as Connects,

        sum(case when al.status in (select status from campaign_status where customer_contact = 'Y') or Status in (select status from system_status where customer_contact = 'Y') then 1 else 0 end) as DMCs,

        sum(case when al.status in (select status from campaign_status where Sale = 'Y') or Status in (select status from system_status where Sale = 'Y') then 1 else 0 end) as Sales,

        SEC_TO_TIME(sum(case when al.dispo_epoch > al.talk_epoch then TIMESTAMPDIFF(SECOND,FROM_UNIXTIME(talk_epoch),FROM_UNIXTIME(dispo_epoch)) - cast(al.dead_sec as signed) ELSE cast(al.talk_sec as signed) - cast(al.dead_sec as signed) end)) as Talk,

        SEC_TO_TIME(sum(case when wait_epoch > pause_epoch then TIMESTAMPDIFF(SECOND,FROM_UNIXTIME(pause_epoch),FROM_UNIXTIME(wait_epoch)) else pause_sec end)) as Pause,

        SEC_TO_TIME(Sum(case when talk_epoch > wait_epoch then TIMESTAMPDIFF(SECOND,FROM_UNIXTIME(wait_epoch),FROM_UNIXTIME(talk_epoch)) else wait_sec end)) as Wait,

        SEC_TO_TIME(Sum(dispo_sec + cast(al.dead_sec as signed))) as Dispo,

        SEC_TO_TIME(Sum(case when Status in (select status from campaign_status where customer_contact = 'Y') or Status in (select status from system_status where customer_contact = 'Y') then (cast(Talk_sec as signed)) else 0 end)) as TotalDMCTalkSecs

        from agent_log al

        WHERE event_time between '" . $start . "' and '" . $end . "'

        group by date(al.event_time), al.`campaign_id`

        ) d ON a.campaign_id = d.campaign_id AND a.Date = d.Date



  order by 2";
    return \DB::connection($dailerConnection)->select($query);
}

function get_graph_values_full_dialer_value($connection, $start, $end, $campaignArray = NULL) {

    $query = "select a.Hour,a.campaign_id, a.campaign_name,

      ifnull(a.Calls, 0) + ifnull(b.Calls,0) as CallsPlaced,

      ifnull(a.Agent_Calls, 0) + isnull(b.Calls) as CallsToAgent,

      a.Connects,

      a.DMCs + ifnull(b.DMCs, 0) as DMCs,

      a.Sales + ifnull(b.Sales, 0) as Sales,

    ROUND((a.Sales + ifnull(b.Sales, 0))/(a.DMCs + ifnull(b.DMCs, 0)) * 100, 2) as Conversion,

      a.DroppedCalls as Dropped,

      a.AnswerMachine as AnsweringMachines,

      ROUND(a.DroppedCalls / (a.Connects + a.AnswerMachine + a.DroppedCalls) * 100, 2) as DroppedRate,

      d.Talk, d.Pause, d.Wait, d.Dispo, SEC_TO_TIME(ROUND(d.TotalDMCTalksecs/d.DMCs)) AS AveDMCTalk,d.TotalDMCTalkSecs,

addtime(d.Talk,addtime(d.dispo,d.Wait)) as total_productive,(a.DMCs + ifnull(b.DMCs, 0))/(time_to_sec(addtime(d.Talk,addtime(d.dispo,d.Wait)))/3600) as DMC_Productive,(a.Connects/(ifnull(a.Agent_Calls, 0) + isnull(b.Calls)))*100 as Total_Connect_Rate,

((a.DMCs + ifnull(b.DMCs, 0))/a.Connects)*100 as Total_DMCrate,sec_to_time((time_to_sec(d.talk))/(ifnull(a.Agent_Calls, 0) + isnull(b.Calls))) as avg_talk,sec_to_time((time_to_sec(d.wait))/(ifnull(a.Agent_Calls, 0) + isnull(b.Calls))) as avg_wait,sec_to_time((time_to_sec(d.dispo))/(ifnull(a.Agent_Calls, 0) + isnull(b.Calls))) as avg_wrap



  from



  (select HOUR(al.call_date) as Hour,date(al.call_date) as Date, al.`campaign_id`, c.campaign_name,

  sum(case when status is not null and al.comments not in ('CHAT','EMAIL') then 1 else 0 end) as Calls,

  sum(case when status is not null and al.comments not in ('CHAT','EMAIL') and user != 'VDAD' then 1 else 0 end) as Agent_Calls,

  sum(case when al.status in (select status from campaign_status where human_answered = 'Y') or Status in (select status from system_status where human_answered = 'Y') then 1 else 0 end) as Connects,

  sum(case when al.status in (select status from campaign_status where customer_contact = 'Y') or Status in (select status from system_status where customer_contact = 'Y') then 1 else 0 end) as DMCs,

  sum(case when al.status in (select status from campaign_status where Sale = 'Y') or Status in (select status from system_status where Sale = 'Y') then 1 else 0 end) as Sales,

  sum(case when al.status = 'A' then 1 else 0 end) as AnswerMachine,

  sum(case when al.status in ('DROP') then 1 else 0 end) as DroppedCalls

  from outbound_log al

  JOIN campaigns c on al.campaign_id = c.campaign_id

  WHERE call_date between '" . $start . "' and '" . $end . "'

  group by HOUR(al.call_date), al.`campaign_id`, c.campaign_name

  ) a

  LEFT JOIN



  (select HOUR(cl.call_date) as Hour,date(cl.call_date) as Date, cl.`campaign_id`,

  sum(case when al.`list_id` is not null and cl.comments not in ('CHAT','EMAIL') then 1 else 0 end) as Calls,

  sum(case when al.`list_id` is not null and cl.comments = 'CHAT' then 1 else 0 end) as Chats,

  sum(case when cl.status in (select status from campaign_status where customer_contact = 'Y') or cl.status in (select status from system_status where customer_contact = 'Y') then 1 else 0 end) as DMCs,

  sum(case when cl.status in (select status from campaign_status where Sale = 'Y') or cl.status in (select status from system_status where Sale = 'Y') then 1 else 0 end) as Sales

  from inbound_log as cl, agent_log as al where al.uniqueid = cl.uniqueid and

  cl.call_date between '" . $start . "' and '" . $end . "'

  and al.event_time between '" . $start . "' and '" . $end . "'

  group by HOUR(cl.call_date), al.`campaign_id`

  ) b on a.Date = b.Date and a.campaign_id = b.campaign_id



LEFT JOIN

(select HOUR(al.event_time) as Hour,date(al.event_time) as Date, al.`campaign_id` AS campaign_id,

        sum(case when status is not null then 1 else 0 end) as Calls,

        sum(case when al.status in (select status from campaign_status where human_answered = 'Y') or Status in (select status from system_status where human_answered = 'Y') then 1 else 0 end) as Connects,

        sum(case when al.status in (select status from campaign_status where customer_contact = 'Y') or Status in (select status from system_status where customer_contact = 'Y') then 1 else 0 end) as DMCs,

        sum(case when al.status in (select status from campaign_status where Sale = 'Y') or Status in (select status from system_status where Sale = 'Y') then 1 else 0 end) as Sales,

        SEC_TO_TIME(sum(case when al.dispo_epoch > al.talk_epoch then TIMESTAMPDIFF(SECOND,FROM_UNIXTIME(talk_epoch),FROM_UNIXTIME(dispo_epoch)) - cast(al.dead_sec as signed) ELSE cast(al.talk_sec as signed) - cast(al.dead_sec as signed) end)) as Talk,

        SEC_TO_TIME(sum(case when wait_epoch > pause_epoch then TIMESTAMPDIFF(SECOND,FROM_UNIXTIME(pause_epoch),FROM_UNIXTIME(wait_epoch)) else pause_sec end)) as Pause,

        SEC_TO_TIME(Sum(case when talk_epoch > wait_epoch then TIMESTAMPDIFF(SECOND,FROM_UNIXTIME(wait_epoch),FROM_UNIXTIME(talk_epoch)) else wait_sec end)) as Wait,

        SEC_TO_TIME(Sum(dispo_sec + cast(al.dead_sec as signed))) as Dispo,

        SEC_TO_TIME(Sum(case when Status in (select status from campaign_status where customer_contact = 'Y') or Status in (select status from system_status where customer_contact = 'Y') then (cast(Talk_sec as signed)) else 0 end)) as TotalDMCTalkSecs

        from agent_log al

        WHERE event_time between '" . $start . "' and '" . $end . "'

        group by HOUR(al.event_time), al.`campaign_id`

        ) d ON a.campaign_id = d.campaign_id AND a.Date = d.Date



  order by a.Hour ASC";
    $mainDialer = \DB::connection($connection)->select($query);
    $newArray = [];
    foreach ($mainDialer as $value) {
        if (!empty($campaignArray) && in_array($value->campaign_id, $campaignArray)) {
            continue;
        }

        if (!empty($newArray[$value->campaign_id]['Hour']) && in_array($value->Hour, $newArray[$value->campaign_id]['Hour'])) {
            continue;
        }
        $newArray[$value->campaign_id]['Hour'][] = $value->Hour;
        $newArray[$value->campaign_id]['ConnectRate'][] = (!empty($value->Total_Connect_Rate)) ? (int) $value->Total_Connect_Rate : 0;
        $newArray[$value->campaign_id]['DMCRate'][] = (!empty($value->Total_DMCrate)) ? (int) $value->Total_DMCrate : 0;
        $newArray[$value->campaign_id]['DMCs'][] = (!empty($value->DMCs)) ? (int) $value->DMCs : 0;
        $newArray[$value->campaign_id]['DMCsProductive'][] = (!empty($value->DMC_Productive)) ? (int) $value->DMC_Productive : 0;
        $newArray[$value->campaign_id]['CN'] = $value->campaign_name;
    }
    return $newArray;
}

function get_cat_dialer($dialer, $source) {
    $catArray = [];
    $catArray['OmniDialer'] = ['' => 'Intelling', 'CCI_WIFI' => 'Client', 'Switch_Experts_Energy' => 'Intelling', 'Switch_Experts_Market' => 'Intelling', 'TEST' => 'Not in use'];
    $catArray['AvatarDialer'] = ['LatchMediaIndia' => 'Base', 'O2WIFI' => 'Client'];
    $catArray['PrisionDialer'] = ['B2B_Data_Cleanse' => 'Intelling', 'LatchMedia_HMP' => 'Base', 'UKCS_Ford_B2B' => 'Intelling'];
    $catArray['MainDialer'] = ['' => 'Not In Use',
        '1|4567' => 'Not In Use',
        '118FreeNet' => 'Intelling',
        '1396' => 'LEAD',
        '1397' => 'LEAD',
        '1443' => 'LEAD',
        '1499' => 'LEAD',
        '1500' => 'LEAD',
        '1523' => 'LEAD',
        '1581' => 'LEAD',
        'AVATAR_HMP' => 'LEAD',
        'AVATAR_INDIA' => 'LEAD',
        'COREG_OILG' => 'LEAD',
        'EZ_PHONE' => 'LEAD',
        'O2_FREESIM_CLICKER' => 'LEAD',
        'O2_FREESIM_COHORT' => 'LEAD',
        'INTELLING_RECYCLES' => 'Intelling',
        'INTELLING_RENEWALS' => 'Intelling',
        'O2_FREESIM_RD' => 'Client Own',
        'O2_LEGACY_BB10' => 'Client Own',
        'O2_LEGACY_BB15' => 'Client Own',
        'O2_LEGACY_BB20' => 'Client Own',
        'O2_LEGACY_BB25' => 'Client Own',
        'O2_LEGACY_BB30' => 'Client Own',
        'O2_LEGACY_BBDATA' => 'Client Own',
        'O2_LEGACY_OTHER' => 'Client Own',
        'O2_P2P_NEW' => 'Client Own',
        'O2_P2P_RECYCLED' => 'Client Own',
        'O2_WELCOME_BB10' => 'Client Own',
        'O2_WELCOME_BB15' => 'Client Own',
        'O2_WELCOME_BB20' => 'Client Own',
        'O2_WELCOME_BB25' => 'Client Own',
        'O2_WELCOME_BB30' => 'Client Own',
        'O2_WELCOME_BBDATA' => 'Client Own',
        'O2_WELCOME_OTHER' => 'Client Own',
        'O2Avatar' => 'Lead',
        'O2AvatarLatch' => 'Base',
        'O2PreToPost' => 'Client Own',
        'PD_Telesurvey' => 'Lead',
        'SID1' => 'Lead',
        'SID100' => 'Lead',
        'SID12' => 'Lead',
        'SID128' => 'Lead',
        'SID130' => 'Lead',
        'SID14' => 'Lead',
        'SID15' => 'Lead',
        'SID184' => 'Lead',
        'SID19' => 'Lead',
        'SID196' => 'Lead',
        'SID2' => 'Lead',
        'SID20' => 'Lead',
        'SID32' => 'Lead',
        'SID34' => 'Lead',
        'SID34U' => 'Lead',
        'SID36' => 'Lead',
        'SID38' => 'Lead',
        'SID46' => 'Lead',
        'SID48' => 'Lead',
        'SID50' => 'Lead',
        'SID54' => 'Lead',
        'SID56' => 'Lead',
        'SID58' => 'Lead',
        'SID62' => 'Lead',
        'SID72' => 'Lead',
        'SID74' => 'Lead',
        'SID8' => 'Lead',
        'SID80' => 'Lead',
        'SID8R' => 'Lead',
        'SID98' => 'Lead',
        'SID98R' => 'Lead',
        'Source1' => 'Lead',
        'Source2' => 'Lead',
        'Source3' => 'Lead',
        'SUBID1' => 'Not In Use',
        'SWITCHEXPERTS_CLICK' => 'Lead',
    ];
    return @$catArray[$dialer][$source];
}

/* O2 Inbound Intraday Report */

function get_o2inbound_intraday_ITVSO($date, $campaignId) {
    return \DB::connection('NewConnex')->table('inbound_log')
                    ->select(DB::raw('HOUR(call_date) as Hour'), DB::raw('count(*) as total'), DB::raw('SUM((CASE WHEN queue_seconds < 15 THEN 1 ELSE 0 END)) AS lessThan15'))
                    ->whereIn('campaign_id', $campaignId)
                    ->where('call_date', '>=', $date . ' 00:00:00')
                    ->where('call_date', '<', $date . ' 23:59:59')
                    ->whereIn('term_reason', ['AGENT', 'CALLER', 'NONE', 'QUEUETIMEOUT'])
                    ->groupBy(DB::raw('HOUR(call_date)'))
                    ->get();
}

function get_o2inbound_intraday_SLA($date, $campaignId) {
    return \DB::connection('NewConnex')->table('inbound_log')
                    ->select(DB::raw('HOUR(call_date) as Hour'), DB::raw('count(*) as total'), DB::raw('SUM((CASE WHEN queue_seconds < 15 THEN 1 ELSE 0 END)) AS lessThan15'))
                    ->whereIn('campaign_id', $campaignId)
                    ->where('call_date', '>=', $date . ' 00:00:00')
                    ->where('call_date', '<', $date . ' 23:59:59')
                    ->whereIn('term_reason', ['AGENT', 'CALLER', 'NONE', 'QUEUETIMEOUT'])
                    ->groupBy(DB::raw('HOUR(call_date)'))
                    ->get();
}

function get_o2inbound_intraday_ABANDON($date, $campaignId) {
    return \DB::connection('NewConnex')->table('inbound_log')
                    ->select(DB::raw('HOUR(call_date) as Hour'), DB::raw('count(*) as total'), DB::raw('SUM((CASE WHEN queue_seconds >= 15 THEN 1 ELSE 0 END)) AS greaterthan15'))
                    ->whereIn('campaign_id', $campaignId)
                    ->where('call_date', '>=', $date . ' 00:00:00')
                    ->where('call_date', '<', $date . ' 23:59:59')
                    ->where('term_reason', 'ABANDON')
                    ->groupBy(DB::raw('HOUR(call_date)'))
                    ->get();
}

function get_intraday_o2Inbound_offered_drop($startDate, $endDate, $campaignId, $queueInSeconds) {
    $response = [];
    $response['offered'] = \DB::connection('NewConnex')->table('inbound_log')
            ->where('campaign_id', $campaignId)
            ->where('call_date', '>=', $startDate)
            ->where('call_date', '<', $endDate)
            ->count();

    $response['drop'] = \DB::connection('NewConnex')->table('inbound_log')
            ->where('campaign_id', $campaignId)
            ->where('call_date', '>=', $startDate)
            ->where('call_date', '<', $endDate)
            ->where('term_reason', 'ABANDON')
            ->where('queue_seconds', '>', $queueInSeconds)
            ->count();

    if (!empty($response['offered'])) {

        $response['output'] = number_format((((!empty($response['offered'])) ? (($response['offered'] - $response['drop']) / $response['offered']) : 0 ) * 100), 2);
    } else {
        $response['output'] = 0;
    }

    return $response;
}

function get_intraday_o2Inbound_offered_drop_all($startDate, $endDate, $campaignId, $queueInSeconds) {
    $response = [];
    $response['offered'] = \DB::connection('NewConnex')->table('inbound_log')
            ->whereIn('campaign_id', $campaignId)
            ->where('call_date', '>=', $startDate)
            ->where('call_date', '<', $endDate)
            ->count();

    $response['drop'] = \DB::connection('NewConnex')->table('inbound_log')
            ->whereIn('campaign_id', $campaignId)
            ->where('call_date', '>=', $startDate)
            ->where('call_date', '<', $endDate)
            ->where('term_reason', 'ABANDON')
            ->where('queue_seconds', '>', $queueInSeconds)
            ->count();

    if (!empty($response['offered'])) {

        $response['output'] = number_format((((!empty($response['offered'])) ? (($response['offered'] - $response['drop']) / $response['offered']) : 0 ) * 100), 2);
    } else {
        $response['output'] = 0;
    }

    return $response;
}

function get_o2inbound_intraday_sale($date, $campaign) {
    $saleMain = DB::connection('MainDialer')
            ->table('O2Script.customers')
            ->join('O2Script.sales', 'O2Script.customers.lead_id', 'O2Script.sales.lead_id')
            ->join('O2Script.sales_by_orig_agent', 'O2Script.sales.sale_id', 'O2Script.sales_by_orig_agent.sale_id')
            ->join('custom_view.sales_by_source_O2script', 'O2Script.sales_by_orig_agent.sale_id', 'custom_view.sales_by_source_O2script.sale_id')
            ->join('custom_view.inbound_log', 'O2Script.customers.lead_id', 'custom_view.inbound_log.lead_id')
            ->where('O2Script.sales_by_orig_agent.saledate', '>=', $date . ' 00:00:00')
            ->where('O2Script.sales_by_orig_agent.saledate', '<=', $date . ' 23:59:59')
            ->where('custom_view.inbound_log.campaign_id', $campaign)
            ->where('O2Script.sales.order_num', 'like', 'MS-5%')
            ->count();

    return $saleMain;
}

function get_o2inbound_intraday_all_sale($date) {
    $CampaignID = ['EurotradeM', 'MTA_Leadgen', 'Synergy', 'OilGenco', 'Topic', 'OutworxIn', 'Ignition', 'Synthesis', 'IPTel', 'Grosvenor'];

    $saleMain = DB::connection('MainDialer')
            ->table('O2Script.customers')
            ->join('O2Script.sales', 'O2Script.customers.lead_id', 'O2Script.sales.lead_id')
            ->join('O2Script.sales_by_orig_agent', 'O2Script.sales.sale_id', 'O2Script.sales_by_orig_agent.sale_id')
            ->join('custom_view.sales_by_source_O2script', 'O2Script.sales_by_orig_agent.sale_id', 'custom_view.sales_by_source_O2script.sale_id')
            ->join('custom_view.inbound_log', 'O2Script.customers.lead_id', 'custom_view.inbound_log.lead_id')
            ->where('O2Script.sales_by_orig_agent.saledate', '>=', $date . ' 00:00:00')
            ->where('O2Script.sales_by_orig_agent.saledate', '<=', $date . ' 23:59:59')
            ->whereIn('custom_view.inbound_log.campaign_id', $CampaignID)
            ->where('O2Script.sales.order_num', 'like', 'MS-5%')
            ->count();
    return @$saleMain;
}

/* Switch Expert Intraday Report */

function get_intraday_average_LIS_SEIR($date, $CampaignID) {
    return \DB::connection('OmniDialer')->table('inbound_log')
                    ->select(DB::raw('HOUR(call_date) as Hour'), DB::raw('AVG(length_in_sec) as average'))
                    ->whereIn('campaign_id', $CampaignID)
                    ->where('call_date', '>=', $date . ' 00:00:00')
                    ->where('call_date', '<=', $date . ' 23:59:59')
                    ->groupBy(DB::raw('HOUR(call_date)'))
                    ->get();
}

function get_intraday_average_QIS_SEIR($date, $CampaignID) {
    return \DB::connection('OmniDialer')->table('inbound_log')
                    ->select(DB::raw('HOUR(call_date) as Hour'), DB::raw('AVG(queue_seconds) as average'))
                    ->whereIn('campaign_id', $CampaignID)
                    ->where('call_date', '>=', $date . ' 00:00:00')
                    ->where('call_date', '<=', $date . ' 23:59:59')
                    ->groupBy(DB::raw('HOUR(call_date)'))
                    ->get();
}

function get_intraday_IWS_SEIR($date, $CampaignID) {
    return \DB::connection('OmniDialer')->table('inbound_log')
                    ->select(DB::raw('HOUR(call_date) as Hour'), DB::raw('count(*) as sale'))
                    ->whereIn('campaign_id', $CampaignID)
                    ->where('call_date', '>=', $date . ' 00:00:00')
                    ->where('call_date', '<=', $date . ' 23:59:59')
                    ->where('status', 'SALE')
                    ->groupBy(DB::raw('HOUR(call_date)'))
                    ->get();
}

function get_intraday_SLA_SEIR($date, $CampaignID) {
    return \DB::connection('OmniDialer')->table('inbound_log')
                    ->select(DB::raw('HOUR(call_date) as Hour'), DB::raw('count(*) as total'), DB::raw('count(queue_seconds < 15) AS lessThan15'))
                    ->whereIn('campaign_id', $CampaignID)
                    ->where('call_date', '>=', $date . ' 00:00:00')
                    ->where('call_date', '<=', $date . ' 23:59:59')
                    ->whereIn('term_reason', ['AGENT', 'CALLER', 'NONE', 'QUEUETIMEOUT'])
                    ->groupBy(DB::raw('HOUR(call_date)'))
                    ->get();
}

function SwitchExper_offered_drop_campaign($date, $campaignId, $queueInSeconds) {
    $response = [];
    $response['offered'] = \DB::connection('OmniDialer')->table('inbound_log')
            ->where('campaign_id', $campaignId)
            ->where('call_date', '>=', $date . ' 00:00:00')
            ->where('call_date', '<=', $date . ' 23:59:59')
            ->count();

    $response['drop'] = \DB::connection('OmniDialer')->table('inbound_log')
            ->where('campaign_id', $campaignId)
            ->where('call_date', '>=', $date . ' 00:00:00')
            ->where('call_date', '<=', $date . ' 23:59:59')
            ->where('term_reason', 'ABANDON')
            ->where('queue_seconds', '>', $queueInSeconds)
            ->count();

    if (!empty($response['offered'])) {

        $response['output'] = number_format((((!empty($response['offered'])) ? (($response['offered'] - $response['drop']) / $response['offered']) : 0 ) * 100), 2);
    } else {
        $response['output'] = 0;
    }

    return $response;
}

function SwitchExper_offered_drop($startDate, $endDate, $campaignId, $queueInSeconds) {
    $response = [];
    $response['offered'] = \DB::connection('OmniDialer')->table('inbound_log')
            ->where('campaign_id', $campaignId)
            ->where('call_date', '>=', $startDate)
            ->where('call_date', '<', $endDate)
            ->count();

    $response['drop'] = \DB::connection('OmniDialer')->table('inbound_log')
            ->where('campaign_id', $campaignId)
            ->where('call_date', '>=', $startDate)
            ->where('call_date', '<', $endDate)
            ->where('term_reason', 'ABANDON')
            ->where('queue_seconds', '>', $queueInSeconds)
            ->count();

    if (!empty($response['offered'])) {

        $response['output'] = number_format((((!empty($response['offered'])) ? (($response['offered'] - $response['drop']) / $response['offered']) : 0 ) * 100), 2);
    } else {
        $response['output'] = 0;
    }

    return $response;
}

function SwitchExper_sale($date, $Campaign) {
    return \DB::connection('OmniDialer')->table('inbound_log')
                    ->whereIn('campaign_id', $Campaign)
                    ->where('call_date', '>=', $date . ' 00:00:00')
                    ->where('call_date', '<', $date . ' 23:59:59')
                    ->where('status', 'SALE')
                    ->count();
}

function SwitchExper_sale_campaign($date, $Campaign) {

    if ($Campaign == 'RightDeal') {
        return \DB::connection('IntellingScriptDB')->table('SDSales')
                        ->where('security_phrase', 'RightDeal')
                        ->where('createddate', '>=', $date . ' 00:00:00')
                        ->where('createddate', '<=', $date . ' 23:59:59')
                        ->whereNotNull('salemsorder')
                        ->where('saleoutcome', 'Sale')
                        ->whereNotIn('salemsorder', ['MS-0', 'MS-00', 'MS-000', 'MS-0000', 'MS-00000', 'MS-000000', 'MS-0000000', 'MS-00000000', 'MS-000000000', 'MS-0000000000', 'MS-00000000000', 'MS-000000000000'])
                        ->count();
    } else {
        return \DB::connection('IntellingScriptDB')->table('SDSales')
                        ->where('security_phrase', $Campaign)
                        ->where('createddate', '>=', $date . ' 00:00:00')
                        ->where('createddate', '<=', $date . ' 23:59:59')
                        ->whereNotNull('salemsorder')
                        ->where('saleoutcome', 'Sale')
                        ->whereNotIn('salemsorder', ['MS-0', 'MS-00', 'MS-000', 'MS-0000', 'MS-00000', 'MS-000000', 'MS-0000000', 'MS-00000000', 'MS-000000000', 'MS-0000000000', 'MS-00000000000', 'MS-000000000000'])
                        ->count();
    }
}

function get_calculate_FTE_3003($start, $end) {
    $userGroup = ['Belfast', 'Burnley', 'Southmoor', 'Synergy', 'SLM'];
    $newArray = [];
    foreach ($userGroup as $group) {
        $groups = DB::connection('NewConnex')
                        ->table('user_groups')
                        ->where('allowed_campaigns', 'LIKE', '%3003%')
                        ->where('group_name', 'like', '%' . $group . '%')
                        ->pluck('user_group')->toArray();
        $data = \DB::connection('NewConnex')
                ->table('outbound_log')
                ->where('call_date', '>=', $start)
                ->where('call_date', '<=', $end)
                ->where('campaign_id', '3003')
                ->whereIn('user_group', $groups)
                ->select(DB::RAW('count(distinct(user)) as groupName'))
                ->get();
        $newArray[$group] = (!empty($data[0]->groupName)) ? $data[0]->groupName : 0;
    }

    return $newArray;
}

function get_group_sales_O2Consumer($start, $end) {
    $array = ['Southmoor' => 'O2OutboundSTH', 'Synergy' => 'SynergyPremium', 'Belfast' => 'O2OutboundBLF', 'Burnley' => 'O2OutboundBUR'];
    $userGroup = ['BLF' => 'Belfast', 'BUR' => 'Burnley', 'STH' => 'Southmoor', 'SYN' => 'Synergy', 'TP' => 'TP'];
    $newArray = [];
    $groupsArray = [];
    $campaign = 1307;
    foreach ($userGroup as $key => $group) {
        $groups = DB::connection('MainDialer')
                        ->table('user_groups')
                        ->where('allowed_campaigns', 'LIKE', '%' . $campaign . '%')
                        ->where('user_group', 'LIKE', '%' . $key . '%')
                        ->pluck('user_group')->toArray();

        $AcceptQuery = "select
s.sale_id,
s.lead_id,
s.sale_date,
c.phone_number,
s.sold_by,
coalesce(al.user_group, ol.user_group, il.user_group) as user_group,
s.product_type,
s.make,
s.model,
s.order_num,
s.campaign_sold_on,
s.tariff_type,
s.upfront_cost,
c.vendor_id,
c.list_id,
ss.source_id,
s.campaign_sold_on,
coalesce(al.campaign_id, ol.campaign_id) as campaign_id,
il.campaign_id as inbound_group,
c.first_name as FirstName,
c.last_name as LastName,
c.title as Title,
c.postal_code as PostalCode
from O2Script.sales s
join O2Script.customers c on s.lead_id = c.lead_id
JOIN custom_view.sales_by_source_O2script ss ON ss.sale_id = s.sale_id
left join
(
select lead_id , from_unixtime(talk_epoch) as log_start, from_unixtime(dispo_epoch + dispo_sec)  as log_end, u.full_name as agent_name, campaign_id,
ifnull(al.user_group, u.user_group) as user_group
from custom_view.agent_log al
join custom_view.users u on al.user = u.user
) al on s.lead_id = al.lead_id and s.sale_date between al.log_start and al.log_end and s.sold_by = al.agent_name
left join (select lead_id, ol.call_date as call_start, from_unixtime(ol.end_epoch) as call_end, u.full_name as agent_name, ifnull(ol.user_group, u.user_group) as user_group,
campaign_id
from custom_view.inbound_log ol
join custom_view.users u on ol.user = u.user
) il on s.lead_id = il.lead_id and s.sale_date between il.call_start and il.call_end and s.sold_by = il.agent_name
left join  (select lead_id, ol.call_date as call_start, from_unixtime(ol.end_epoch) as call_end, u.full_name as agent_name, ifnull(ol.user_group, u.user_group) as user_group,
campaign_id
from custom_view.outbound_log ol
join custom_view.users u on ol.user = u.user
) ol
on s.lead_id = ol.lead_id and s.sale_date between ol.call_start and ol.call_end and s.sold_by = ol.agent_name
where s.sale_date between  '".$start."' and '".$end."' AND s.campaign_sold_on = 1307 AND al.user_group IN ('" . implode("','", $groups) . "') AND s.order_num LIKE 'MS-5%'";
        $Accept = \DB::connection('MainDialer')->select($AcceptQuery);

        $DeclineQuery = "select
s.sale_id,
s.lead_id,
s.sale_date,
c.phone_number,
s.sold_by,
coalesce(al.user_group, ol.user_group, il.user_group) as user_group,
s.product_type,
s.make,
s.model,
s.order_num,
s.campaign_sold_on,
s.tariff_type,
s.upfront_cost,
c.vendor_id,
c.list_id,
ss.source_id,
s.campaign_sold_on,
coalesce(al.campaign_id, ol.campaign_id) as campaign_id,
il.campaign_id as inbound_group,
c.first_name as FirstName,
c.last_name as LastName,
c.title as Title,
c.postal_code as PostalCode
from O2Script.sales s
join O2Script.customers c on s.lead_id = c.lead_id
JOIN custom_view.sales_by_source_O2script ss ON ss.sale_id = s.sale_id
left join
(
select lead_id , from_unixtime(talk_epoch) as log_start, from_unixtime(dispo_epoch + dispo_sec)  as log_end, u.full_name as agent_name, campaign_id,
ifnull(al.user_group, u.user_group) as user_group
from custom_view.agent_log al
join custom_view.users u on al.user = u.user
) al on s.lead_id = al.lead_id and s.sale_date between al.log_start and al.log_end and s.sold_by = al.agent_name
left join (select lead_id, ol.call_date as call_start, from_unixtime(ol.end_epoch) as call_end, u.full_name as agent_name, ifnull(ol.user_group, u.user_group) as user_group,
campaign_id
from custom_view.inbound_log ol
join custom_view.users u on ol.user = u.user
) il on s.lead_id = il.lead_id and s.sale_date between il.call_start and il.call_end and s.sold_by = il.agent_name
left join  (select lead_id, ol.call_date as call_start, from_unixtime(ol.end_epoch) as call_end, u.full_name as agent_name, ifnull(ol.user_group, u.user_group) as user_group,
campaign_id
from custom_view.outbound_log ol
join custom_view.users u on ol.user = u.user
) ol
on s.lead_id = ol.lead_id and s.sale_date between ol.call_start and ol.call_end and s.sold_by = ol.agent_name
where s.sale_date between  '".$start."' and '".$end."' AND s.campaign_sold_on = 1307 AND al.user_group IN ('" . implode("','", $groups) . "') AND s.order_num LIKE 'MS-0%'";
        $Decline = \DB::connection('MainDialer')->select($DeclineQuery);


        $newArray[$group]['Accept'] = count($Accept);
        $newArray[$group]['Decline'] = count($Decline);
    }
    return $newArray;
}

function get_calculate_FTE($start, $end) {
    $userGroup = ['BLF' => 'Belfast', 'BUR' => 'Burnley', 'STH' => 'Southmoor', 'SYN' => 'Synergy', 'SLM' => 'SLM', 'TP' => 'TP'];
    $newArray = [];
    $groupsArray = [];
    foreach ($userGroup as $key => $group) {
        $groups = DB::connection('NewDialer')
                        ->table('user_groups')
                        ->where('allowed_campaigns', 'LIKE', '%1307%')
                        ->where('user_group', 'like', '%' . $key . '%')
                        ->pluck('user_group')->toArray();
        foreach ($groups as $val) {
            $groupsArray[] = $val;
        }
        $data = \DB::connection('NewDialer')
                ->table('outbound_log')
                ->where('call_date', '>=', $start)
                ->where('call_date', '<=', $end)
                ->where('campaign_id', '1307')
                ->whereIn('user_group', $groups)
                ->select(DB::RAW('count(distinct(user)) as groupName'))
                ->get();
        $newArray[$group] = (!empty($data[0]->groupName)) ? $data[0]->groupName : 0;
    }
    return $newArray;
}

function get_publisher_report($start, $end, $listids) {

    $listid = implode("','", $listids);

    $query = "Select sid,LeadsLoaded,Agent_Calls,Connects,ROUND(Connects/Agent_Calls*100,2) as ConnectRate,Average_Calls,
DMCs,ROUND(DMCs/Connects*100,2) as DMCRate,AnsweringMachine,`Drop`,Completed,CampaignId
from
(select ol.list_id,l.source_id as sid,
date(ol.call_date) as Date, ol.`campaign_id` as CampaignId,
sum(case when ol.status is not null then 1 else 0 end) as Calls,
sum(case when ol.status is not null and ol.user != 'VDAD' then 1 else 0 end) as Agent_Calls,
sum(case when ol.status in (select status from campaign_status where human_answered = 'Y')
or ol.status in (select status from system_status where human_answered = 'Y') then 1 else 0 end) as Connects,
SUM(l.called_count)/count(l.lead_id) as Average_Calls,
sum(case when ol.status in (select status from campaign_status where customer_contact = 'Y')
or ol.status in (select status from system_status where customer_contact = 'Y') then 1 else 0 end) as DMCs,
sum(case when ol.status = 'A' then 1 else 0 end) as AnsweringMachine,
sum(case when ol.status in ('DROP') then 1 else 0 end) as `Drop`,
sum(case when ol.status in (select status from campaign_status where completed = 'Y')
or ol.status in (select status from system_status where completed = 'Y') then 1 else 0 end) as Completed
from outbound_log ol
JOIN list l ON l.lead_id=ol.lead_id
WHERE ol.call_date between '" . $start . " 00:00:00' AND '" . $end . " 23:59:59'
and l.entry_date between '" . $start . " 00:00:00' AND '" . $end . " 23:59:59'
and ol.campaign_id in (select campaign_id from campaigns)
AND l.source_id != ''
AND l.list_id in ('" . $listid . "')
group by l.source_id
) a LEFT JOIN
(select l.source_id as source_id,count(*) as LeadsLoaded
from custom_view.`list` l
where l.list_id in ('" . $listid . "')
and l.entry_date between '" . $start . " 00:00:00' AND '" . $end . " 23:59:59' group by l.source_id) b
on a.sid = b.source_id";
    return $query;
}

/* TalkTalk Reports */

function get_intraday_average_LIS($date) {
    return \DB::connection('NewDialer')->table('inbound_log')
                    ->select(DB::raw('HOUR(call_date) as Hour'), DB::raw('AVG(length_in_sec) as average'))
                    ->whereIn('campaign_id', ['TalkTalkEma', 'TalkTalkSMS', 'TalkTalkBau', 'TalkTalkSto', 'CSTransfer', 'TT_Non_Mobi', 'TalkTalk_lo', 'TalkTalk_le', 'New_Acq', 'CSBBNBA'])
                    ->where('call_date', '>=', $date . ' 00:00:00')
                    ->where('call_date', '<', $date . ' 23:59:59')
                    ->groupBy(DB::raw('HOUR(call_date)'))
                    ->get();
}

function get_intraday_average_QIS($date) {
    return \DB::connection('NewDialer')->table('inbound_log')
                    ->select(DB::raw('HOUR(call_date) as Hour'), DB::raw('AVG(queue_seconds) as average'))
                    ->whereIn('campaign_id', ['TalkTalkEma', 'TalkTalkSMS', 'TalkTalkBau', 'TalkTalkSto', 'CSTransfer', 'TT_Non_Mobi', 'TalkTalk_lo', 'TalkTalk_le', 'New_Acq', 'CSBBNBA'])
                    ->where('call_date', '>=', $date . ' 00:00:00')
                    ->where('call_date', '<', $date . ' 23:59:59')
                    ->groupBy(DB::raw('HOUR(call_date)'))
                    ->get();
}

function get_intraday_IWS($date) {
    return \DB::connection('NewDialer')->table('inbound_log')
                    ->select(DB::raw('HOUR(call_date) as Hour'), DB::raw('count(*) as sale'))
                    ->whereIn('campaign_id', ['TalkTalkEma', 'TalkTalkSMS', 'TalkTalkBau', 'TalkTalkSto', 'CSTransfer', 'TT_Non_Mobi', 'TalkTalk_lo', 'TalkTalk_le', 'New_Acq', 'CSBBNBA'])
                    ->where('call_date', '>=', $date . ' 00:00:00')
                    ->where('call_date', '<', $date . ' 23:59:59')
                    ->where('status', 'SALE')
                    ->groupBy(DB::raw('HOUR(call_date)'))
                    ->get();
}

function get_intraday_SLA($date) {
    return \DB::connection('NewDialer')->table('inbound_log')
                    ->select(DB::raw('HOUR(call_date) as Hour'), DB::raw('count(*) as total'), DB::raw('count(queue_seconds < 15) AS lessThan15'))
                    ->whereIn('campaign_id', ['TalkTalkEma', 'TalkTalkSMS', 'TalkTalkBau', 'TalkTalkSto', 'CSTransfer', 'TT_Non_Mobi', 'TalkTalk_lo', 'TalkTalk_le', 'New_Acq', 'CSBBNBA'])
                    ->where('call_date', '>=', $date . ' 00:00:00')
                    ->where('call_date', '<', $date . ' 23:59:59')
                    ->whereIn('term_reason', ['AGENT', 'CALLER', 'NONE', 'QUEUETIMEOUT'])
                    ->groupBy(DB::raw('HOUR(call_date)'))
                    ->get();
}

function get_intraday_offered_drop_TALKTALK($startDate, $endDate, $campaignId, $queueInSeconds) {
    $response = [];
    $response['offered'] = \DB::connection('NewDialer')->table('inbound_log')
            ->where('campaign_id', $campaignId['offered'])
            ->where('call_date', '>=', $startDate)
            ->where('call_date', '<', $endDate)
            ->count();
    $response['drop'] = \DB::connection('NewDialer')->table('inbound_log')
            ->where('campaign_id', $campaignId['drop'])
            ->where('call_date', '>=', $startDate)
            ->where('call_date', '<', $endDate)
            ->whereIn('term_reason', ['ABANDON', 'QUEUETIMEOUT'])
            ->where('queue_seconds', '>', $queueInSeconds)
            ->count();
    if (!empty($response['offered'])) {

        $response['output'] = number_format((((!empty($response['offered'])) ? (($response['offered'] - $response['drop']) / $response['offered']) : 0 ) * 100), 2);
    } else {
        $response['output'] = 0;
    }

    return $response;
}

function get_intraday_SLA_TALKTALK() {
    $start = date("Y-m-d H:i:s", strtotime('-2 hours'));
    $end = date('Y-m-d H:i:s');
    $campaign = [];
    $campaign['offered'] = ['CSBBNBA', 'New_Acq', 'TalkTalkEma', 'TalkTalkBau', 'TalkTalkSMS', 'TalkTalkSto', 'CSTransfer', 'TT_Non_Mobi'];
    $campaign['drop'] = ['TTEmailOver', 'TTBAUOver', 'TTSMSOver', 'TalkTalkSto', 'CSTransfer', 'TT_Non_Mobi'];
    $response = [];
    $response['offered'] = \DB::connection('NewDialer')->table('inbound_log')
            ->whereIn('campaign_id', $campaign['offered'])
            ->where('call_date', '>=', $start)
            ->where('call_date', '<', $end)
            ->count();

    $response['answered'] = \DB::connection('NewDialer')->table('inbound_log')
            ->whereIn('campaign_id', $campaign['offered'])
            ->where('call_date', '>=', $start)
            ->where('call_date', '<', $end)
            ->whereNotIn('status', ['A', 'NEW'])
            ->where('queue_seconds', '<=', 15)
            ->count();
    if (!empty($response['offered'])) {
        $response['SLA'] = (($response['answered'] / $response['offered']) * 100);
    } else {
        $response['SLA'] = 0;
    }
    return $response;
}

function get_divide($a, $b, $numberFormat = NULL) {
    if (!empty($numberFormat)) {

        return number_format((((!empty($b)) ? ($a / $b) : 0 ) * 100), 2);
    }
    return (((!empty($b)) ? ($a / $b) : 0 ) * 100);
}

function get_intraday_sale($date) {
    $saleMain = DB::connection('TalkTalkO2Inbound')
            ->table('Sales')
            ->where('orderid', '<>', '', 'and')
            ->where('callstart', '>=', $date . ' 00:00:00')
            ->where('callstart', '<=', $date . ' 23:59:59')
            ->whereNotIn('orderid', [000000000])
            ->whereIn('security_phrase', ['TalkTalkEma', 'TalkTalkSMS', 'TalkTalkBau', 'TalkTalkSto', 'CSTransfer', 'TT_Non_Mobi', 'TalkTalk_lo', 'TalkTalk_le', 'New_Acq', 'CSBBNBA'])
            ->whereIn('status', ['SALE', 'HS'])
            ->count();
    $saleAdditional = DB::connection('TalkTalkO2Inbound')
            ->table('additional_sales')
            ->join('Sales', 'Sales.saleid', '=', 'additional_sales.saleid')
            ->where('Sales.created_at', '>=', $date . ' 00:00:00')
            ->where('Sales.created_at', '<=', $date . ' 23:59:59')
            ->whereIn('Sales.security_phrase', ['TalkTalkEma', 'TalkTalkSMS', 'TalkTalkBau', 'TalkTalkSto', 'CSTransfer', 'TT_Non_Mobi', 'TalkTalk_lo', 'TalkTalk_le', 'New_Acq', 'CSBBNBA'])
            ->whereIn('Sales.status', ['SALE', 'HS'])
            ->whereNotNull('Sales.orderid')
            ->whereNotIn('Sales.orderid', ['000000000'])
            ->count();
    return ($saleMain + $saleAdditional);
}

function get_file_update($id, $fieldname, $fieldvalue) {
    return \App\Model\Intelling\FileImportLog::where('id', $id)->update([$fieldname => $fieldvalue]);
}

function get_phone_check($phone) {
    $lenPhone = strlen($phone);
    if ($lenPhone == 10) {
        return '0' . $phone;
    } else {
        return $phone;
    }
}

function get_break_O2FreeSim($filename) {
    $fielExplode = explode('_', $filename);
    if (!empty($fielExplode[1])) {
        $fielExplode2 = explode('.', $fielExplode[1]);
        if (!empty($fielExplode2[0])) {
            return date('Y-m-d', strtotime($fielExplode2[0]));
        } else {
            return '';
        }
    } else {
        return '';
    }
}

function get_hourly_report_campaign_graph($start, $end, $dialer, $campaignArray) {
    $query = "select a. Date, a.campaign_id, a.campaign_name,       ifnull(a.Calls, 0) + ifnull(b.Calls,0) as CallsPlaced,       ifnull(a.Agent_Calls, 0) + isnull(b.Calls) as CallsToAgent,       a.Connects,       a.DMCs + ifnull(b.DMCs, 0) as DMCs,       a.Sales + ifnull(b.Sales, 0) as Sales,     ROUND((a.Sales + ifnull(b.Sales, 0))/(a.DMCs + ifnull(b.DMCs, 0)) * 100, 2) as Conversion,       a.DroppedCalls as Dropped,        a.AnswerMachine as AnsweringMachines,       ROUND(a.DroppedCalls / (a.Connects + a.AnswerMachine + a.DroppedCalls) * 100, 2) as DroppedRate,       d.Talk, d.Pause, d.Wait, d.Dispo, SEC_TO_TIME(ROUND(d.TotalDMCTalksecs/d.DMCs)) AS AveDMCTalk,d.TotalDMCTalkSecs, addtime(d.Talk,addtime(d.dispo,d.Wait)) as total_productive,(a.DMCs + ifnull(b.DMCs, 0))/(time_to_sec(addtime(d.Talk,addtime(d.dispo,d.Wait)))/3600) as DMC_Productive,(a.Connects/(ifnull(a.Agent_Calls, 0) + isnull(b.Calls)))*100 as Total_Connect_Rate, ((a.DMCs + ifnull(b.DMCs, 0))/a.Connects)*100 as Total_DMCrate,sec_to_time((time_to_sec(d.talk))/(ifnull(a.Agent_Calls, 0) + isnull(b.Calls))) as avg_talk,sec_to_time((time_to_sec(d.wait))/(ifnull(a.Agent_Calls, 0) + isnull(b.Calls))) as avg_wait,sec_to_time((time_to_sec(d.dispo))/(ifnull(a.Agent_Calls, 0) + isnull(b.Calls))) as avg_wrap   from      (select hour(al.call_date) as Date, al.`campaign_id`, c.campaign_name,   sum(case when status is not null and al.comments not in ('CHAT','EMAIL') then 1 else 0 end) as Calls,   sum(case when status is not null and al.comments not in ('CHAT','EMAIL') and user != 'VDAD' then 1 else 0 end) as Agent_Calls,   sum(case when al.status in (select status from campaign_status where human_answered = 'Y') or Status in (select status from system_status where human_answered = 'Y') then 1 else 0 end) as Connects,   sum(case when al.status in (select status from campaign_status where customer_contact = 'Y') or Status in (select status from system_status where customer_contact = 'Y') then 1 else 0 end) as DMCs,   sum(case when al.status in (select status from campaign_status where Sale = 'Y') or Status in (select status from system_status where Sale = 'Y') then 1 else 0 end) as Sales,   sum(case when al.status = 'A' then 1 else 0 end) as AnswerMachine,   sum(case when al.status in ('DROP') then 1 else 0 end) as DroppedCalls   from outbound_log al   JOIN campaigns c on al.campaign_id = c.campaign_id   WHERE call_date between '" . $start . "' and '" . $end . "'   group by hour(al.call_date), al.`campaign_id`, c.campaign_name   ) a   LEFT JOIN      (select hour(cl.call_date) as Date, cl.`campaign_id`,   sum(case when al.`list_id` is not null and cl.comments not in ('CHAT','EMAIL') then 1 else 0 end) as Calls,   sum(case when al.`list_id` is not null and cl.comments = 'CHAT' then 1 else 0 end) as Chats,   sum(case when cl.status in (select status from campaign_status where customer_contact = 'Y') or cl.status in (select status from system_status where customer_contact = 'Y') then 1 else 0 end) as DMCs,   sum(case when cl.status in (select status from campaign_status where Sale = 'Y') or cl.status in (select status from system_status where Sale = 'Y') then 1 else 0 end) as Sales   from inbound_log as cl, agent_log as al where al.uniqueid = cl.uniqueid and   cl.call_date between '" . $start . "' and '" . $end . "'   and al.event_time between '" . $start . "' and '" . $end . "'   group by hour(cl.call_date), al.`campaign_id`   ) b on a.Date = b.Date and a.campaign_id = b.campaign_id LEFT JOIN (select hour(al.event_time) as Date, al.`campaign_id` AS campaign_id,         sum(case when status is not null then 1 else 0 end) as Calls,         sum(case when al.status in (select status from campaign_status where human_answered = 'Y') or Status in (select status from system_status where human_answered = 'Y') then 1 else 0 end) as Connects,         sum(case when al.status in (select status from campaign_status where customer_contact = 'Y') or Status in (select status from system_status where customer_contact = 'Y') then 1 else 0 end) as DMCs,         sum(case when al.status in (select status from campaign_status where Sale = 'Y') or Status in (select status from system_status where Sale = 'Y') then 1 else 0 end) as Sales,         SEC_TO_TIME(sum(case when al.dispo_epoch > al.talk_epoch then TIMESTAMPDIFF(SECOND,FROM_UNIXTIME(talk_epoch),FROM_UNIXTIME(dispo_epoch)) - cast(al.dead_sec as signed) ELSE cast(al.talk_sec as signed) - cast(al.dead_sec as signed) end)) as Talk,         SEC_TO_TIME(sum(case when wait_epoch > pause_epoch then TIMESTAMPDIFF(SECOND,FROM_UNIXTIME(pause_epoch),FROM_UNIXTIME(wait_epoch)) else pause_sec end)) as Pause,         SEC_TO_TIME(Sum(case when talk_epoch > wait_epoch then TIMESTAMPDIFF(SECOND,FROM_UNIXTIME(wait_epoch),FROM_UNIXTIME(talk_epoch)) else wait_sec end)) as Wait,         SEC_TO_TIME(Sum(dispo_sec + cast(al.dead_sec as signed))) as Dispo,         SEC_TO_TIME(Sum(case when Status in (select status from campaign_status where customer_contact = 'Y') or Status in (select status from system_status where customer_contact = 'Y') then (cast(Talk_sec as signed)) else 0 end)) as TotalDMCTalkSecs         from agent_log al         WHERE event_time between '" . $start . "' and '" . $end . "'         group by hour(al.event_time), al.`campaign_id`         ) d ON a.campaign_id = d.campaign_id AND a.Date = d.Date   order by 2,1";
    $data = DB::connection($dialer)->select($query);

    $newArray = [];
    foreach ($data as $value) {
        if (!empty($campaignArray) && in_array($value->campaign_id, $campaignArray)) {
            continue;
        }

        if (!empty($newArray[$value->campaign_id]['Hour']) && in_array($value->Date, $newArray[$value->campaign_id]['Hour'])) {
            continue;
        }
        $newArray[$value->campaign_id]['Hour'][] = $value->Date;
        $newArray[$value->campaign_id]['ConnectRate'][] = (!empty($value->Total_Connect_Rate)) ? (int) $value->Total_Connect_Rate : 0;
        $newArray[$value->campaign_id]['DMCRate'][] = (!empty($value->Total_DMCrate)) ? (int) $value->Total_DMCrate : 0;
        $newArray[$value->campaign_id]['DMCs'][] = (!empty($value->DMCs)) ? (int) $value->DMCs : 0;
        $newArray[$value->campaign_id]['DMCsProductive'][] = (!empty($value->DMC_Productive)) ? (int) $value->DMC_Productive : 0;
        $newArray[$value->campaign_id]['CN'] = $value->campaign_name;
    }
    return $newArray;
}

function get_hourly_report_campaign_graph_NewConnex($start, $end, $dialer, $campaignArray) {
    $query = "select a. Date, a.campaign_id, a.campaign_name,       ifnull(a.Calls, 0) + ifnull(b.Calls,0) as CallsPlaced,       ifnull(a.Agent_Calls, 0) + isnull(b.Calls) as CallsToAgent,       a.Connects,       a.DMCs + ifnull(b.DMCs, 0) as DMCs,       a.Sales + ifnull(b.Sales, 0) as Sales,     ROUND((a.Sales + ifnull(b.Sales, 0))/(a.DMCs + ifnull(b.DMCs, 0)) * 100, 2) as Conversion,       a.DroppedCalls as Dropped,        a.AnswerMachine as AnsweringMachines,       ROUND(a.DroppedCalls / (a.Connects + a.AnswerMachine + a.DroppedCalls) * 100, 2) as DroppedRate,       d.Talk, d.Pause, d.Wait, d.Dispo, SEC_TO_TIME(ROUND(d.TotalDMCTalksecs/d.DMCs)) AS AveDMCTalk,d.TotalDMCTalkSecs, addtime(d.Talk,addtime(d.dispo,d.Wait)) as total_productive,(a.DMCs + ifnull(b.DMCs, 0))/(time_to_sec(addtime(d.Talk,addtime(d.dispo,d.Wait)))/3600) as DMC_Productive,(a.Connects/(ifnull(a.Agent_Calls, 0) + isnull(b.Calls)))*100 as Total_Connect_Rate, ((a.DMCs + ifnull(b.DMCs, 0))/a.Connects)*100 as Total_DMCrate,sec_to_time((time_to_sec(d.talk))/(ifnull(a.Agent_Calls, 0) + isnull(b.Calls))) as avg_talk,sec_to_time((time_to_sec(d.wait))/(ifnull(a.Agent_Calls, 0) + isnull(b.Calls))) as avg_wait,sec_to_time((time_to_sec(d.dispo))/(ifnull(a.Agent_Calls, 0) + isnull(b.Calls))) as avg_wrap   from      (select hour(al.call_date) as Date, al.`campaign_id`, c.campaign_name,   sum(case when status is not null and al.comments not in ('CHAT','EMAIL') then 1 else 0 end) as Calls,   sum(case when status is not null and al.comments not in ('CHAT','EMAIL') and user != 'VDAD' then 1 else 0 end) as Agent_Calls,   sum(case when al.status in (select status from campaign_status where human_answered = 'Y') or Status in (select status from system_status where human_answered = 'Y') then 1 else 0 end) as Connects,   sum(case when al.status in (select status from campaign_status where customer_contact = 'Y') or Status in (select status from system_status where customer_contact = 'Y') then 1 else 0 end) as DMCs,   sum(case when al.status in (select status from campaign_status where Sale = 'Y') or Status in (select status from system_status where Sale = 'Y') then 1 else 0 end) as Sales,   sum(case when al.status = 'A' then 1 else 0 end) as AnswerMachine,   sum(case when al.status in ('DROP') then 1 else 0 end) as DroppedCalls   from outbound_log al   JOIN campaigns c on al.campaign_id = c.campaign_id   WHERE call_date between '" . $start . "' and '" . $end . "'   group by hour(al.call_date), al.`campaign_id`, c.campaign_name   ) a   LEFT JOIN      (select hour(cl.call_date) as Date, cl.`campaign_id`,   sum(case when al.`list_id` is not null and cl.comments not in ('CHAT','EMAIL') then 1 else 0 end) as Calls,   sum(case when al.`list_id` is not null and cl.comments = 'CHAT' then 1 else 0 end) as Chats,   sum(case when cl.status in (select status from campaign_status where customer_contact = 'Y') or cl.status in (select status from system_status where customer_contact = 'Y') then 1 else 0 end) as DMCs,   sum(case when cl.status in (select status from campaign_status where Sale = 'Y') or cl.status in (select status from system_status where Sale = 'Y') then 1 else 0 end) as Sales   from inbound_log as cl, agent_log as al where al.uniqueid = cl.uniqueid and   cl.call_date between '" . $start . "' and '" . $end . "'   and al.event_time between '" . $start . "' and '" . $end . "'   group by hour(cl.call_date), al.`campaign_id`   ) b on a.Date = b.Date and a.campaign_id = b.campaign_id LEFT JOIN (select hour(al.event_time) as Date, al.`campaign_id` AS campaign_id,         sum(case when status is not null then 1 else 0 end) as Calls,         sum(case when al.status in (select status from campaign_status where human_answered = 'Y') or Status in (select status from system_status where human_answered = 'Y') then 1 else 0 end) as Connects,         sum(case when al.status in (select status from campaign_status where customer_contact = 'Y') or Status in (select status from system_status where customer_contact = 'Y') then 1 else 0 end) as DMCs,         sum(case when al.status in (select status from campaign_status where Sale = 'Y') or Status in (select status from system_status where Sale = 'Y') then 1 else 0 end) as Sales,         SEC_TO_TIME(sum(case when al.dispo_epoch > al.talk_epoch then TIMESTAMPDIFF(SECOND,FROM_UNIXTIME(talk_epoch),FROM_UNIXTIME(dispo_epoch)) - cast(al.dead_sec as signed) ELSE cast(al.talk_sec as signed) - cast(al.dead_sec as signed) end)) as Talk,         SEC_TO_TIME(sum(case when wait_epoch > pause_epoch then TIMESTAMPDIFF(SECOND,FROM_UNIXTIME(pause_epoch),FROM_UNIXTIME(wait_epoch)) else pause_sec end)) as Pause,         SEC_TO_TIME(Sum(case when talk_epoch > wait_epoch then TIMESTAMPDIFF(SECOND,FROM_UNIXTIME(wait_epoch),FROM_UNIXTIME(talk_epoch)) else wait_sec end)) as Wait,         SEC_TO_TIME(Sum(dispo_sec + cast(al.dead_sec as signed))) as Dispo,         SEC_TO_TIME(Sum(case when Status in (select status from campaign_status where customer_contact = 'Y') or Status in (select status from system_status where customer_contact = 'Y') then (cast(Talk_sec as signed)) else 0 end)) as TotalDMCTalkSecs         from agent_log al         WHERE event_time between '" . $start . "' and '" . $end . "'         group by hour(al.event_time), al.`campaign_id`         ) d ON a.campaign_id = d.campaign_id AND a.Date = d.Date   order by 2,1";
    $data = DB::connection($dialer)->select($query);

    $newArray = [];
    foreach ($data as $value) {
        if (!empty($campaignArray) && in_array($value->campaign_id, $campaignArray)) {



            if (!empty($newArray[$value->campaign_id]['Hour']) && in_array($value->Date, $newArray[$value->campaign_id]['Hour'])) {
                continue;
            }
            $newArray[$value->campaign_id]['Hour'][] = $value->Date;
            $newArray[$value->campaign_id]['ConnectRate'][] = (!empty($value->Total_Connect_Rate)) ? (int) $value->Total_Connect_Rate : 0;
            $newArray[$value->campaign_id]['DMCRate'][] = (!empty($value->Total_DMCrate)) ? (int) $value->Total_DMCrate : 0;
            $newArray[$value->campaign_id]['DMCs'][] = (!empty($value->DMCs)) ? (int) $value->DMCs : 0;
            $newArray[$value->campaign_id]['DMCsProductive'][] = (!empty($value->DMC_Productive)) ? (int) $value->DMC_Productive : 0;
            $newArray[$value->campaign_id]['CN'] = $value->campaign_name;
        }
    }
    return $newArray;
}

function cron_notification_update($action, $syntax, $cron_notification_id = NULL) {

    $command = str_replace('command:', '', $syntax);

    $dataCommand = \App\Model\UTGAPI\CronDetail::where('syntax', $command)->first();
    if (!empty($dataCommand->id)) {
        $array = [];
        $array['cron_detail_id'] = $dataCommand->id;
        switch ($action) {
            case 'insert':
                $id = \App\Model\UTGAPI\CronNotification::create($array)->id;
                break;
            case 'update':
                \App\Model\UTGAPI\CronNotification::where('id', $cron_notification_id)->orderBy('id', 'desc')->update($array);
                break;
            default:
        }
        if (!empty($id)) {
            return $id;
        }
    } else {
        \Log::error($command . ' does not exist! Pleas check its last run on ' . Carbon::now());
    }
}

function get_email_listing_import($array) {
    if (!empty($array) && count($array) > 0) {
        return implode(',', $array);
    } else {
        return '';
    }
}

function get_email_listing_export($list) {
    if (!empty($list)) {
        return explode(',', $list);
    } else {
        return [];
    }
}

function get_command_syntax($syntax) {
    return str_replace('command:', '', $syntax);
}

function get_command_detail($syntax) {
    $signature = str_replace('command:', '', $syntax);
    $data = \App\Model\UTGAPI\CronDetail::where('syntax', $signature)->first();
    if (!empty($data)) {
        $postData = [];
        $postData['Status'] = $data->status;
        $postData['EmailNotification'] = $data->email_notification;
        if (!empty($data->email_to) && $data->email_to) {
            $ExportList = get_email_listing_export($data->email_to);
            $EmailListingTO = App\Model\UTGAPI\EmailListing::whereIn('id', $ExportList)->pluck('email', 'id')->toArray();
            $postData['EmailTO'] = $EmailListingTO;
        }
        if (!empty($data->email_cc) && $data->email_cc) {
            $ExportList = get_email_listing_export($data->email_cc);
            $EmailListingCC = App\Model\UTGAPI\EmailListing::whereIn('id', $ExportList)->pluck('email', 'id')->toArray();
            $postData['EmailCC'] = $EmailListingCC;
        }

        return $postData;
    } else {
        return '';
    }
}

function get_response_update($data) {
    if (empty($data->customers)) {
        return '';
    }
    $Customers = $data->customers;
    $array = [];
    foreach ($Customers as $key => $value) {
        if (!empty($value->customer_id)) {
            $SynergyTTAPI = \App\Model\UTGAPI\O2FreeSimLoadedRecord::find($key);
            $SynergyTTAPI->lead_id = $value->customer_id;
            $SynergyTTAPI->api_response = serialize($value);
            if ($SynergyTTAPI->save()) {

            }
        } else {
            $array[] = $value;
        }
    }
    if (count($array) > 0) {
        get_MainDialerAPI_response($array);
    }
    return 'succcess';
}

function get_MainDialerAPI_response($array) {

    $arrayMailTo = ['apanwar@usethegeeks.co.uk'];
    $mail_data = array();
    $mail_data['to'] = $arrayMailTo;
    $mail_data['from'] = !empty($data['from']) ? $data['from'] : 'intellingreports@intelling.co.uk';
    $mail_data['msg'] = !empty($data['message']) ? $data['message'] : '';
    $mail_data['view'] = !empty($data['view']) ? $data['view'] : 'emails.O2FreeSim_ERROR';
//        $mail_data['cc'] = !empty($data['cc']) ? $data['cc'] : ['akumar@usethegeeks.com'];
    $mail_data['subject'] = !empty($data['subject']) ? $data['subject'] : 'O2FreeSim Leads ';
    $mail_data['data'] = $array;

    Mail::send($mail_data['view'], ['data' => $array], function ($m) use ($mail_data) {
        $m->from($mail_data['from'], 'API Reports');
        if (!empty($mail_data['cc'])) {
            $m->cc($mail_data['cc']);
        }
        $m->replyTo('intellingreports@intelling.co.uk', 'API Reports');
        $m->to($mail_data['to'])->subject($mail_data['subject']);
    });
}

function get_omni_response_update($data) {
    if (empty($data->customers)) {
        return '';
    }
    $Customers = $data->customers;
    $array = [];
    foreach ($Customers as $key => $value) {
        if (!empty($value->customer_id)) {
            $SynergyTTAPI = \App\Model\Intelling\SwitchExpertOPTins::where('saleid', $key)->update(['leadId' => $value->customer_id, 'api_response' => serialize($value)]);
        }
    }
    return 'succcess';
}

function SwitchExper_offered_drop_campaign_TC2($date, $campaignId, $queueInSeconds) {
    $response = [];
    $response['offered'] = \DB::connection('OmniDialer')->table('inbound_log')
            ->where('campaign_id', $campaignId)
            ->where('call_date', '>=', $date . ' 00:00:00')
            ->where('call_date', '<=', $date . ' 23:59:59')
            ->where('length_in_sec', '>', $queueInSeconds)
            ->count();

    $response['drop'] = 0;

    if (!empty($response['offered'])) {

        $response['output'] = number_format((((!empty($response['offered'])) ? (($response['offered'] - $response['drop']) / $response['offered']) : 0 ) * 100), 2);
    } else {
        $response['output'] = 0;
    }

    return $response;
}

function get_omni_response_update_NEATLEY($data) {
    if (empty($data->customers)) {
        return '';
    }
    $Customers = $data->customers;
    $array = [];
    foreach ($Customers as $key => $value) {
        if (!empty($value->customer_id)) {
            $SynergyTTAPI = App\Model\UTGAPI\NeatleyOPTins::where('id', $key)->update(['api_lead_id' => $value->customer_id, 'api_response' => serialize($value)]);
        }
    }
    return 'succcess';
}

function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
    $sort_col = array();
    foreach ($arr as $key => $row) {
        $sort_col[$key] = $row[$col];
    }

    array_multisort($sort_col, $dir, $arr);
}

function get_calculate_FTE_3001($start, $end, $Campaign, $searchName) {

    $userGroup = ['SLM' => 'SLM', 'Out' => 'Outworx'];
    $newArray = [];
    if ($searchName == 'blf') {
        $groups1 = DB::connection('OmniDialer')
                        ->table('user_groups')
                        ->where('allowed_campaigns', 'LIKE', '%3001%')
                        ->where('user_group', 'like', '%blf%')
                        ->pluck('user_group')->toArray();
        $groups2 = DB::connection('OmniDialer')
                        ->table('user_groups')
                        ->where('allowed_campaigns', 'LIKE', '%1330%')
                        ->where('user_group', 'like', '%blf%')
                        ->pluck('user_group')->toArray();
        $groups = array_merge($groups1, $groups2);
    } else {
        $groups = DB::connection('OmniDialer')
                        ->table('user_groups')
                        ->where('allowed_campaigns', 'LIKE', '%' . implode(',', $Campaign) . '%')
                        ->where('user_group', 'like', '%' . $searchName . '%')
                        ->pluck('user_group')->toArray();
    }
    $data = DB::connection('OmniDialer')
            ->table('outbound_log')
            ->where('call_date', '>=', $start)
            ->where('call_date', '<=', $end)
            ->whereIn('campaign_id', $Campaign)
            ->whereIn('user_group', $groups)
            ->select(DB::RAW('count(distinct(user)) as groupName'))
            ->get();
    $newArray = (!empty($data[0]->groupName)) ? $data[0]->groupName : 0;


    return $newArray;
}

function get_new_dialer_api_LeadPOST($postData) {
    $user = 'IntellingOne';
    $pass = 'QyWpdMMZeA6XW6Ht';
    $token = 'nOiTKop6vLmCIyvSiYKlh4tbPnrDtvGT299IVOR08adUBjOuni';
//    $user = 'IntellingNONTPS';
//    $pass = 'zm2c8DDJbbSwEEDr';
//    $token = 'x4rZzIrucnithycLp0TZxxs6L4HsM9ao0efSTjntncQmPPwuhp';

    $postData1 = [];
    $postData1['token'] = $token;
    $postData1['customers'] = $postData;
    // Step One: Authenticate using credentials

    $postData = Array('username' => $user, 'password' => $pass);

    $ch = curl_init('https://api3.cnx1.uk/consumer/login');
    curl_setopt_array($ch, array(
        CURLOPT_POST => TRUE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
//            'User-Agent: Intelling-API'
        ),
        CURLOPT_POSTFIELDS => json_encode($postData)
    ));

    $output = curl_exec($ch);

    $authtokens = json_decode($output, true);
    $authtoken = $authtokens['token'];

    $ch = curl_init('https://api3.cnx1.uk/customer/bulk_create');
    curl_setopt_array($ch, array(
        CURLOPT_POST => TRUE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $authtoken,
            'Content-Type: application/json',
            'User-Agent: Intelling-API'
        ),
        CURLOPT_POSTFIELDS => json_encode($postData1)
    ));
    $exec = curl_exec($ch);

    return json_decode($exec);
}

function get_main_response_update($data) {
    if (empty($data->customers)) {
        return '';
    }
    $Customers = $data->customers;
    $array = [];
    foreach ($Customers as $key => $value) {
        if (!empty($value->customer_id)) {
            $SynergyTTAPI = \App\Model\Intelling\SEMobileOPTins::where('id', $key)->update(['api_lead_id' => $value->customer_id, 'api_response' => serialize($value)]);
        }
    }
    return 'succcess';
}

/* Date Range For O2Inbound Intraday Report */
/* O2 Inbound Intraday Report */

function get_o2inbound_intraday_ITVSO_RANGE($start, $end, $campaignId) {
    return \DB::connection('NewConnex')->table('inbound_log')
                    ->select(DB::raw('HOUR(call_date) as Hour'), DB::raw('count(*) as total'), DB::raw('SUM((CASE WHEN queue_seconds < 15 THEN 1 ELSE 0 END)) AS lessThan15'))
                    ->whereIn('campaign_id', $campaignId)
                    ->where('call_date', '>=', $start . ' 00:00:00')
                    ->where('call_date', '<', $end . ' 23:59:59')
                    ->whereIn('term_reason', ['AGENT', 'CALLER', 'NONE', 'QUEUETIMEOUT'])
                    ->groupBy(DB::raw('HOUR(call_date)'))
                    ->get();
}

function get_o2inbound_intraday_SLA_RANGE($start, $end, $campaignId) {
    return \DB::connection('NewConnex')->table('inbound_log')
                    ->select(DB::raw('HOUR(call_date) as Hour'), DB::raw('count(*) as total'), DB::raw('SUM((CASE WHEN queue_seconds < 15 THEN 1 ELSE 0 END)) AS lessThan15'))
                    ->whereIn('campaign_id', $campaignId)
                    ->where('call_date', '>=', $start . ' 00:00:00')
                    ->where('call_date', '<', $end . ' 23:59:59')
                    ->whereIn('term_reason', ['AGENT', 'CALLER', 'NONE', 'QUEUETIMEOUT'])
                    ->groupBy(DB::raw('HOUR(call_date)'))
                    ->get();
}

function get_o2inbound_intraday_ABANDON_RANGE($start, $end, $campaignId) {
    return \DB::connection('NewConnex')->table('inbound_log')
                    ->select(DB::raw('HOUR(call_date) as Hour'), DB::raw('count(*) as total'), DB::raw('SUM((CASE WHEN queue_seconds >= 15 THEN 1 ELSE 0 END)) AS greaterthan15'))
                    ->whereIn('campaign_id', $campaignId)
                    ->where('call_date', '>=', $start . ' 00:00:00')
                    ->where('call_date', '<', $end . ' 23:59:59')
                    ->where('term_reason', 'ABANDON')
                    ->groupBy(DB::raw('HOUR(call_date)'))
                    ->get();
}

function get_intraday_o2Inbound_offered_drop_RANGE($startDate, $endDate, $campaignId, $queueInSeconds) {
    $response = [];
    $response['offered'] = \DB::connection('NewConnex')->table('inbound_log')
            ->where('campaign_id', $campaignId)
            ->where('call_date', '>=', $startDate)
            ->where('call_date', '<', $endDate)
            ->count();

    $response['drop'] = \DB::connection('NewConnex')->table('inbound_log')
            ->where('campaign_id', $campaignId)
            ->where('call_date', '>=', $startDate)
            ->where('call_date', '<', $endDate)
            ->where('term_reason', 'ABANDON')
            ->where('queue_seconds', '>', $queueInSeconds)
            ->count();

    if (!empty($response['offered'])) {

        $response['output'] = number_format((((!empty($response['offered'])) ? (($response['offered'] - $response['drop']) / $response['offered']) : 0 ) * 100), 2);
    } else {
        $response['output'] = 0;
    }

    return $response;
}

function get_intraday_o2Inbound_offered_drop_all_RANGE($startDate, $endDate, $campaignId, $queueInSeconds) {
    $response = [];
    $response['offered'] = \DB::connection('NewConnex')->table('inbound_log')
            ->whereIn('campaign_id', $campaignId)
            ->where('call_date', '>=', $startDate)
            ->where('call_date', '<', $endDate)
            ->count();

    $response['drop'] = \DB::connection('NewConnex')->table('inbound_log')
            ->whereIn('campaign_id', $campaignId)
            ->where('call_date', '>=', $startDate)
            ->where('call_date', '<', $endDate)
            ->where('term_reason', 'ABANDON')
            ->where('queue_seconds', '>', $queueInSeconds)
            ->count();

    if (!empty($response['offered'])) {

        $response['output'] = number_format((((!empty($response['offered'])) ? (($response['offered'] - $response['drop']) / $response['offered']) : 0 ) * 100), 2);
    } else {
        $response['output'] = 0;
    }

    return $response;
}

function get_o2inbound_intraday_sale_RANGE($start, $end, $campaign) {

    $saleMain = DB::connection('MainDialer')
            ->table('O2Script.customers')
            ->join('O2Script.sales', 'O2Script.customers.lead_id', 'O2Script.sales.lead_id')
            ->join('O2Script.sales_by_orig_agent', 'O2Script.sales.sale_id', 'O2Script.sales_by_orig_agent.sale_id')
            ->join('custom_view.sales_by_source_O2script', 'O2Script.sales_by_orig_agent.sale_id', 'custom_view.sales_by_source_O2script.sale_id')
            ->join('custom_view.inbound_log', 'O2Script.customers.lead_id', 'custom_view.inbound_log.lead_id')
            ->where('O2Script.sales_by_orig_agent.saledate', '>=', $date . ' 00:00:00')
            ->where('O2Script.sales_by_orig_agent.saledate', '<=', $date . ' 23:59:59')
            ->where('custom_view.inbound_log.campaign_id', $campaign)
            ->where('O2Script.sales.order_num', 'like', 'MS-5%')
            ->count();
    return $saleMain;
}

function get_o2inbound_intraday_all_sale_RANGE($start, $end) {
    $saleMain = \App\Model\O2Inbound\InboundSale::where('createddate', '>=', $start . ' 00:00:00')
            ->where('createddate', '<', $end . ' 23:59:59')
//            ->whereNotIn('orderid', ['MS-0', 'MS-00', 'MS-000', 'MS-0000', 'MS-00000', 'MS-000000', 'MS-0000000', 'MS-00000000', 'MS-000000000', 'MS-0000000000'])
            ->where('orderid', 'LIKE', 'MS-5%')
            ->whereIn('report_query', ['MTA_Leadgen', 'Synergy', 'OilGenco', 'MTALGN', 'TOPIC', 'TouchstonIn', 'OutworxIn', 'ignition', 'Synthesis', 'IPTel', 'Grosvenor', 'MTASales'])
            ->count();
//    $saleMain1 = \App\Model\O2Inbound\InboundSale::where('createddate', '>=', $date . ' 00:00:00')
//            ->where('createddate', '<', $date . ' 23:59:59')
////            ->whereNotIn('orderid', ['MS-0', 'MS-00', 'MS-000', 'MS-0000', 'MS-00000', 'MS-000000', 'MS-0000000', 'MS-00000000', 'MS-000000000', 'MS-0000000000'])
//            ->where('orderid','LIKE','MS-5%')
//            ->whereNull('report_query')
//            ->count();
//    return (@$saleMain + @$saleMain1);
    return @$saleMain;
}

function get_O2UNICA_response_update($data) {
    if (empty($data->customers)) {
        return '';
    }
    $Customers = $data->customers;
    $array = [];
    foreach ($Customers as $key => $value) {
        if (!empty($value->customer_id)) {
            $DataListing = App\Model\O2UNICA\DataListing::find($key);
            $DataListing->lead_id = $value->customer_id;
            $DataListing->api_response = serialize($value);
            if ($DataListing->save()) {

            }
        } else {
            $array[] = $value;
        }
    }
}

function get_OMNI_api_LeadPOST($postData) {
    $user = 'IntellingTwo';
    $pass = 'Eg926bD5GfbGEJwG';
    $token = 'brwh890FraGrLy1VpvwU9KwDhAwT0EdB4dlWc8IqpzBIjh894L';
//    $user = 'IntellingNONTPS';
//    $pass = 'zm2c8DDJbbSwEEDr';
//    $token = 'x4rZzIrucnithycLp0TZxxs6L4HsM9ao0efSTjntncQmPPwuhp';

    $postData1 = [];
    $postData1['token'] = $token;
    $postData1['customers'] = $postData;
    // Step One: Authenticate using credentials

    $postData = Array('username' => $user, 'password' => $pass);

    $ch = curl_init('https://api3.cnx1.uk/consumer/login');
    curl_setopt_array($ch, array(
        CURLOPT_POST => TRUE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
//            'User-Agent: Intelling-API'
        ),
        CURLOPT_POSTFIELDS => json_encode($postData)
    ));

    $output = curl_exec($ch);

    $authtokens = json_decode($output, true);
    $authtoken = $authtokens['token'];

    $ch = curl_init('https://api3.cnx1.uk/customer/bulk_create');
    curl_setopt_array($ch, array(
        CURLOPT_POST => TRUE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $authtoken,
            'Content-Type: application/json',
            'User-Agent: Intelling-API'
        ),
        CURLOPT_POSTFIELDS => json_encode($postData1)
    ));
    $exec = curl_exec($ch);

    return json_decode($exec);
}

function get_unicaP2PCore_response($data) {
    if (empty($data->customers)) {
        return '';
    }
    $Customers = $data->customers;
    $array = [];
    foreach ($Customers as $key => $value) {
        if (!empty($value->customer_id)) {
            $DataListing = App\Model\UTGAPI\UNICAP2PCore::find($key);
            $DataListing->lead_id = $value->customer_id;
            $DataListing->api_response = serialize($value);
            if ($DataListing->save()) {

            }
        } else {
            $array[] = $value;
        }
    }
}

function countDays($day, $start, $end) {
    //get the day of the week for start and end dates (0-6)
    $w = array(date('w', $start), date('w', $end));

    //get partial week day count
    if ($w[0] < $w[1]) {
        $partialWeekCount = ($day >= $w[0] && $day <= $w[1]);
    } else if ($w[0] == $w[1]) {
        $partialWeekCount = $w[0] == $day;
    } else {
        $partialWeekCount = ($day >= $w[0] || $day <= $w[1]);
    }

    //first count the number of complete weeks, then add 1 if $day falls in a partial week.
    return floor(( $end - $start ) / 60 / 60 / 24 / 7) + $partialWeekCount;
}

function get_count_WorkingDays($start, $end) {
    $start = strtotime($start);
    $end = strtotime($end);
    return countDays(1, $start, $end) +
            countDays(2, $start, $end) +
            countDays(3, $start, $end) +
            countDays(4, $start, $end) +
            countDays(5, $start, $end);
}

/**
 * @param $interval
 * @param $datefrom
 * @param $dateto
 * @param bool $using_timestamps
 * @return false|float|int|string
 */
function datediff($interval, $datefrom, $dateto, $using_timestamps = false) {
    /*
      $interval can be:
      yyyy - Number of full years
      q    - Number of full quarters
      m    - Number of full months
      y    - Difference between day numbers
      (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
      d    - Number of full days
      w    - Number of full weekdays
      ww   - Number of full weeks
      h    - Number of full hours
      n    - Number of full minutes
      s    - Number of full seconds (default)
     */

    if (!$using_timestamps) {
        $datefrom = strtotime($datefrom, 0);
        $dateto = strtotime($dateto, 0);
    }

    $difference = $dateto - $datefrom; // Difference in seconds
    $months_difference = 0;

    switch ($interval) {
        case 'yyyy': // Number of full years
            $years_difference = floor($difference / 31536000);
            if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom) + $years_difference) > $dateto) {
                $years_difference--;
            }

            if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto) - ($years_difference + 1)) > $datefrom) {
                $years_difference++;
            }

            $datediff = $years_difference;
            break;

        case "q": // Number of full quarters
            $quarters_difference = floor($difference / 8035200);

            while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($quarters_difference * 3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                $months_difference++;
            }

            $quarters_difference--;
            $datediff = $quarters_difference;
            break;

        case "m": // Number of full months
            $months_difference = floor($difference / 2678400);

            while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                $months_difference++;
            }

            $months_difference--;

            $datediff = $months_difference;
            break;

        case 'y': // Difference between day numbers
            $datediff = date("z", $dateto) - date("z", $datefrom);
            break;

        case "d": // Number of full days
            $datediff = floor($difference / 86400);
            break;

        case "w": // Number of full weekdays
            $days_difference = floor($difference / 86400);
            $weeks_difference = floor($days_difference / 7); // Complete weeks
            $first_day = date("w", $datefrom);
            $days_remainder = floor($days_difference % 7);
            $odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?

            if ($odd_days > 7) { // Sunday
                $days_remainder--;
            }

            if ($odd_days > 6) { // Saturday
                $days_remainder--;
            }

            $datediff = ($weeks_difference * 5) + $days_remainder;
            break;

        case "ww": // Number of full weeks
            $datediff = floor($difference / 604800);
            break;

        case "h": // Number of full hours
            $datediff = floor($difference / 3600);
            break;

        case "n": // Number of full minutes
            $datediff = floor($difference / 60);
            break;

        default: // Number of full seconds (default)
            $datediff = $difference;
            break;
    }

    return $datediff;
}

function get_file_break($fileName) {
    $FileNameArray = explode('_', $fileName);
    $reverse = array_reverse($FileNameArray);
    $response = $reverse[1];
    return $response;
}

function get_unicaP2PChurn_response($data) {
    if (empty($data->customers)) {
        return '';
    }
    $Customers = $data->customers;
    $array = [];
    foreach ($Customers as $key => $value) {
        if (!empty($value->customer_id)) {
            $DataListing = App\Model\UTGAPI\UNICAP2PChurn::find($key);
            $DataListing->lead_id = $value->customer_id;
            $DataListing->api_response = serialize($value);
            if ($DataListing->save()) {

            }
        } else {
            $array[] = $value;
        }
    }
}

function get_unicaP2PAddcon_response($data) {
    if (empty($data->customers)) {
        return '';
    }
    $Customers = $data->customers;
    $array = [];
    foreach ($Customers as $key => $value) {
        if (!empty($value->customer_id)) {
            $DataListing = App\Model\UTGAPI\UNICAP2PAddcon::find($key);
            $DataListing->lead_id = $value->customer_id;
            $DataListing->api_response = serialize($value);
            if ($DataListing->save()) {

            }
        } else {
            $array[] = $value;
        }
    }
}

function get_connex_response($data) {
    if (empty($data->customers)) {
        return '';
    }
    $Customers = $data->customers;
    $array = [];
    $updatdRecords = [];
    foreach ($Customers as $key => $value) {
        if (!empty($value->customer_id)) {
            $DataListing = App\Model\UTGAPI\FileImportData::find($key);
            $DataListing->lead_id = $value->customer_id;
            $DataListing->api_response = json_encode($value);
            if ($DataListing->save()) {
                if(isset($updatdRecords[$DataListing->list_id])) {
                    $updatdRecords[$DataListing->list_id]++;
                }else {
                    $updatdRecords[$DataListing->list_id] = 1;
                }
            }
        } else {
            $array[] = $value;
        }
    }
    return $updatdRecords;
}

function mailTemplateHelperConnex() {
    $loopFilesMail =  [
        'P2P-SMARTPHONE-UNICA' => [
          'list_ids' => [
              '4010' => ['O2_P2P_MOBILE', 'Loaded'],
              '40101' => ['O2_P2P_MOBILE_RECYCLED', 'Recycled'],
            ],
          'subject' => 'Automation - P2P Smartphone Unica - 4010',
          'header' => 'O2 P2P Smartphone Unica - 4010'
      ],
      'P2P-CHURN-UNICA' => [
        'list_ids' => [
            '3045' => ['O2_P2P_CHURN', 'Loaded'],
            '30451' => ['O2_P2P_CHURN_RECYCLED', 'Recycled'],
        ],
        'subject' => 'Automation - P2P Churn - 3045',
        'header' => 'O2 P2P Churn - 3045'
      ],
      'P2P-CORE-UNICA' => [
        'list_ids' => [
            '3001' => ['O2_PRETOPOST','Loaded'],
            '30011' => ['O2_PRETOPOST_RECYCLED','Recycled'],
        ],
        'subject' => 'P2P CORE - 3001 - Automation',
        'header' => 'O2 P2P Core - 3001'
      ],
      'P2P-ADDCON-UNICA' => [
        'list_ids' => [
            '3005' => ['O2_ADDCONS','Loaded'],
            '30051' => ['O2_ADDCONS_RECYCLED','Recycled'],
        ],
        'subject' => 'Automation - P2P Addcon - 3005',
        'header' => 'O2 P2P Addcon - 3005'
      ],
      'O2UNICA' => [
        'list_ids' => [
            '1330' => ['O2_P2PADDITIONAL_CLASSIC', 'Loaded'],
            '13302' => ['O2_P2PADDITIONAL_REC', 'Recycled'],
            '13303' => ['O2_P2PADDITIONAL_SUB10', 'Loaded'],
            '13305' => ['O2_P2PADDITIONAL_SUB10_REC', 'Recycled'],
        ],
        'subject' => 'P2P Additional',
        'header' => 'P2P Additional'
      ]
    ];
    return $loopFilesMail;
}

function O2ReturnProcessValidationNew($value) {
    $response = [];
    foreach ($value as $key => $val) {
        if ($key == 'Campaign_Code') {
            if (!empty($val)) {
                if (strlen(ltrim($val, "0")) != 4) {
                    $response[] = 'Campaign Code will be 4 digit.';
                }
            }
        } elseif ($key == 'Cell_Code') {
            if (substr($val, 0, 1) == 'A') {
                $number = substr($val, 1, 9);

                if ($number >= 000000001 && $number <= 999999999) {

                } else {
                    $response[] = 'Cell Code not matched in between A000000001 to A999999999.';
                }
            } else {
                $response[] = 'Cell Code not matched in between A000000001 to A999999999';
            }
        } elseif ($key == 'Treatment_Code') {
            if ($val >= 000000001 && $val <= 999999999) {

            } else {
                $response[] = 'Treatment Code will not matched in between 000000001 to 999999999.';
            }
        } elseif ($key == 'Response_Date_Time') {
            if (strlen($val) != 14) {
                $response[] = 'Response Date Time will not matched.';
            }
        } elseif ($key == 'ResponseStatus_Code') {
            if (!in_array($val, range(24, 29))) {
                $response[] = 'Response Status Code not matched in range of 24 to 29.';
            }
        } elseif ($key == 'Response_Channel') {
            if (!in_array($val, ['T', 'E', 'V', 'M', 'S'])) {
                $response[] = 'Response Channel Code not matched in T,E,V,M,S.';
            }
        } elseif ($key == 'Customer_ID') {
            if (!empty($val) && is_numeric($val)) {
                if ($val == NULL || $val == 'NULL') {
                    $response[] = 'Customer ID not matched as integer & not length max then 10.';
                } else {
                    settype($val, 'integer');
                    if ($val >= 0000000000 && $val <= 9999999999) {

                    } else {
                        $response[] = 'Customer ID not matched as integer & not length max then 10.';
                    }
                }
            } else {
//                $response[] = 'Customer ID not matched as integer & not length max then 10.';
            }
        } elseif ($key == 'Subscriber_ID') {
            if (!empty($val)) {
                if ($val == NULL || $val == 'NULL') {
                    $response[] = 'Subscriber ID not matched as integer & not length max then 10.';
                } else {
                    settype($val, 'integer');
                    if ($val >= 0000000000 && $val <= 9999999999) {

                    } else {
                        $response[] = 'Subscriber ID not matched as integer & not length max then 10.';
                    }
                }
            } else {
//                $response[] = 'Subscriber ID not matched as integer & not length max then 10.';
            }
        } elseif ($key == 'Account_Id') {
            if (!empty($val)) {
                if ($val == NULL || $val == 'NULL') {
                    $response[] = 'Account ID not matched as integer & not length max then 10.';
                } else {
                    settype($val, 'integer');
                    if ($val >= 0000000000 && $val <= 9999999999) {

                    } else {
                        $response[] = 'Account ID not matched as integer & not length max then 10.';
                    }
                }
            } else {
//                $response[] = 'Subscriber ID not matched as integer & not length max then 10.';
            }
        }
    }
    return $response;
}

function check_SITE($group) {
    $site = ['blf' => 'belfast', 'sth' => 'southmoor', 'bur' => 'burnley', 'syn' => 'synergy'];
   $group = strtolower($group);
    $result = '';
    foreach ($site as $k => $v) {
        if (strpos($group, $k) !== false) {
            $result = $k;
        }else{
         if (strpos($group, $v) !== false) {
                $result = $k;
            }
        }
    }

    return strtoupper($result);
}


function getDatesFromRange($start, $end, $format = 'Y-m-d') {

    // Declare an empty array
    $array = array();

    // Variable that store the date interval
    // of period 1 day
    $interval = new DateInterval('P1D');

    $realEnd = new DateTime($end);
    $realEnd->add($interval);

    $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

    // Use loop to store date into array
    foreach($period as $date) {
        $array[] = $date->format($format);
    }

    // Return the array elements
    return $array;
}

function Zip($source, $destination)
{
    if (!extension_loaded('zip') || !file_exists($source)) {
        return false;
    }

    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
        return false;
    }

    $source = str_replace('\\', '/', realpath($source));

    if (is_dir($source) === true)
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($files as $file)
        {
            $file = str_replace('\\', '/', $file);

            // Ignore "." and ".." folders
            if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                continue;

            $file = realpath($file);

            if (is_dir($file) === true)
            {
                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
            }
            else if (is_file($file) === true)
            {
                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
            }
        }
    }
    else if (is_file($source) === true)
    {
        $zip->addFromString(basename($source), file_get_contents($source));
    }

    return $zip->close();
}

function get_intraday_o2sales_offered_drop($startDate, $endDate, $campaignId, $queueInSeconds) {
    $response = [];
    $response['offered'] = \DB::connection('MainDialer')->table('inbound_log')
            ->where('campaign_id', $campaignId)
            ->where('call_date', '>=', $startDate)
            ->where('call_date', '<', $endDate)
            ->count();

    $response['drop'] = \DB::connection('MainDialer')->table('inbound_log')
            ->where('campaign_id', $campaignId)
            ->where('call_date', '>=', $startDate)
            ->where('call_date', '<', $endDate)
            ->where('term_reason', 'ABANDON')
            ->where('queue_seconds', '>', $queueInSeconds)
            ->count();

    if (!empty($response['offered'])) {

        $response['output'] = number_format((((!empty($response['offered'])) ? (($response['offered'] - $response['drop']) / $response['offered']) : 0 ) * 100), 2);
    } else {
        $response['output'] = 0;
    }

    return $response;
}

function get_o2sales_intraday_sale($date, $campaign) {
    $saleMain = DB::connection('MainDialer')
            ->table('O2Script.customers')
            ->join('O2Script.sales', 'O2Script.customers.lead_id', 'O2Script.sales.lead_id')
            ->join('O2Script.sales_by_orig_agent', 'O2Script.sales.sale_id', 'O2Script.sales_by_orig_agent.sale_id')
            ->join('custom_view.sales_by_source_O2script', 'O2Script.sales_by_orig_agent.sale_id', 'custom_view.sales_by_source_O2script.sale_id')
            ->join('custom_view.inbound_log', 'O2Script.customers.lead_id', 'custom_view.inbound_log.lead_id')
            ->where('O2Script.sales_by_orig_agent.saledate', '>=', $date . ' 00:00:00')
            ->where('O2Script.sales_by_orig_agent.saledate', '<=', $date . ' 23:59:59')
            ->where('custom_view.inbound_log.campaign_id', $campaign)
            ->where('O2Script.sales.order_num', 'like', 'MS-5%')
            ->count();

    return $saleMain;
}

function get_intraday_o2sales_offered_drop_all($startDate, $endDate, $campaignId, $queueInSeconds) {
    $response = [];
    $response['offered'] = \DB::connection('NewConnex')->table('inbound_log')
            ->whereIn('campaign_id', $campaignId)
            ->where('call_date', '>=', $startDate)
            ->where('call_date', '<', $endDate)
            ->count();

    $response['drop'] = \DB::connection('NewConnex')->table('inbound_log')
            ->whereIn('campaign_id', $campaignId)
            ->where('call_date', '>=', $startDate)
            ->where('call_date', '<', $endDate)
            ->where('term_reason', 'ABANDON')
            ->where('queue_seconds', '>', $queueInSeconds)
            ->count();

    if (!empty($response['offered'])) {

        $response['output'] = number_format((((!empty($response['offered'])) ? (($response['offered'] - $response['drop']) / $response['offered']) : 0 ) * 100), 2);
    } else {
        $response['output'] = 0;
    }

    return $response;
}

function get_o2sales_intraday_all_sale($date,$CampaignID) {
//    $CampaignID = ['EurotradeM', 'MTA_Leadgen', 'Synergy', 'OilGenco', 'Topic', 'OutworxIn', 'Ignition', 'Synthesis', 'IPTel', 'Grosvenor'];

    $saleMain = DB::connection('MainDialer')
            ->table('O2Script.customers')
            ->join('O2Script.sales', 'O2Script.customers.lead_id', 'O2Script.sales.lead_id')
            ->join('O2Script.sales_by_orig_agent', 'O2Script.sales.sale_id', 'O2Script.sales_by_orig_agent.sale_id')
            ->join('custom_view.sales_by_source_O2script', 'O2Script.sales_by_orig_agent.sale_id', 'custom_view.sales_by_source_O2script.sale_id')
            ->join('custom_view.inbound_log', 'O2Script.customers.lead_id', 'custom_view.inbound_log.lead_id')
            ->where('O2Script.sales_by_orig_agent.saledate', '>=', $date . ' 00:00:00')
            ->where('O2Script.sales_by_orig_agent.saledate', '<=', $date . ' 23:59:59')
            ->whereIn('custom_view.inbound_log.campaign_id', $CampaignID)
            ->where('O2Script.sales.order_num', 'like', 'MS-5%')
            ->count();
    return @$saleMain;
}

/*Command Log Table*/
function insert_command_log($title,$description,$command_name){
    $CommandLog = new App\Model\UTGAPI\CommandLog();
    $CommandLog->title = $title;
    $CommandLog->description = $description;
    $CommandLog->command_name = $command_name;
    $CommandLog->save();
}

function utgapilog($message = '') {
  if($message) \Log::channel('utgapilog')->info($message);
}
