<?php

namespace App\Model\O2Audit;

use Illuminate\Database\Eloquent\Model;

class O2AuditMissing extends Model {

    public $timestamps = false;
    protected $connection = 'Intelling_DW';
    protected $table = 'O2Audit_MissingSales';

}
