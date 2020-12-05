<?php

namespace App\Http\Controllers\Admin\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Artisan;
use Hash;
use Auth;
use Mail;
use App\Model\Intelling\LeadSupplier;

class LeadController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {

        $this->middleware('auth');
    }

    public function manage(Request $request) {
        $data = LeadSupplier::orderBy('id','desc')->paginate(10);
//        $data = LeadSupplier::find(19);
////         $data = DB::connection('MainDialer')->table('list')->where('list_id',30036)->get();
////         $data = DB::connection('MainDialer')->table('custom_field_data')->where('list_id',30036)->get();
////         $data = DB::connection('MainDialer')->select('SHOW TABLES');
//         pr(unserialize($data->api_response));
//         exit;
        return view('admin.supplier.manage', compact(['data', 'total']));
    }

}
