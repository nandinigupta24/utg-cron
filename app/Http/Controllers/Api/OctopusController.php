<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use App\Model\UTGAPI\OctopusAPIO2LGSE;

class OctopusController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {

//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        $data = OctopusAPIO2LGSE::orderBy('id', 'desc')->paginate(10);
        
        return view('Api.Octopus.file', compact(['data']));
    }
    
    public function manage(Request $request) {
        $date = date('Y-m-d');
        $arrayCount = [];
        $arrayCount['Total'] = OctopusAPIO2LGSE::where('created_at','>=',$date.' 00:00:00')->count();
        $arrayCount['Loaded'] = OctopusAPIO2LGSE::where('created_at','>=',$date.' 00:00:00')->where('duplicate_status','no')->count();
        $arrayCount['Duplicate'] = OctopusAPIO2LGSE::where('created_at','>=',$date.' 00:00:00')->where('duplicate_status','yes')->count();
        
        $data = OctopusAPIO2LGSE::where('created_at', '>=', $date . ' 00:00:00')
                ->select('inbound_group', 'duplicate_status','campaign_name',DB::RAW('count(*) as total'))
                ->groupBy('inbound_group', 'duplicate_status','campaign_name')
                ->get();
//        pr($data->toArray());
//        exit;
        $arrayCountGroup = [];
        foreach ($data as $value) {
            if (!empty($arrayCountGroup[$value->inbound_group][$value->campaign_name])) {
                $arrayCountGroup[$value->inbound_group][$value->campaign_name]['Total'] = $arrayCountGroup[$value->inbound_group][$value->campaign_name]['Total'] + $value->total;
            } else {
                $arrayCountGroup[$value->inbound_group][$value->campaign_name]['Total'] = $value->total;
            }
            if ($value->duplicate_status == 'no') {
                $arrayCountGroup[$value->inbound_group][$value->campaign_name]['Loaded'] = $value->total;
            } else {
                $arrayCountGroup[$value->inbound_group][$value->campaign_name]['Duplicate'] = $value->total;
            }
        }
       
        return view('Api.Octopus.manage', compact(['arrayCount','arrayCountGroup']));
    }

}
