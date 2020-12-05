<?php

namespace App\Model\O2Inbound;

use Illuminate\Database\Eloquent\Model;

class InboundSale extends Model {

    protected $connection = 'O2Inbound';
    protected $table = 'inboundSales';
    public $timestamps = false;
}
