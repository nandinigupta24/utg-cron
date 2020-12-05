<?php

namespace App\Model\IntellingScriptDB;

use Illuminate\Database\Eloquent\Model;

class SDSales extends Model {

    protected $connection = 'IntellingScriptDB';
    protected $table = 'SDSales';

    public function sdsales_additional() {
        return $this->hasMany('App\Model\IntellingScriptDB\SDSalesAdditional', 'saleid', 'saleid')
                ->whereNotNull('salemsorder')
                ->where('salemsorder','!=','');
    }
}
