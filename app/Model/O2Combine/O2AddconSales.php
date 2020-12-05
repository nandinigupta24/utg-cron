<?php

namespace App\Model\O2Combine;

use Illuminate\Database\Eloquent\Model;

class O2AddconSales extends Model {

    protected $connection = 'O2Combine';
    protected $table = 'O2AddconSales';

    public function agent() {
        return $this->belongsTo('App\Model\O2Combine\Agent', 'agentid', 'user');
    }
}
    