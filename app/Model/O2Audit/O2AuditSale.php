<?php

namespace App\Model\O2Audit;

use Illuminate\Database\Eloquent\Model;

class O2AuditSale extends Model {

    public $timestamps = false;
    protected $connection = 'Intelling_DW';
    protected $table = 'O2Audit_O2SalesImportStaging';

}
