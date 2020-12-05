<?php

namespace App\Model\UTGAPI;

use Illuminate\Database\Eloquent\Model;

class CommandLog extends Model {

    protected $connection = 'mysql';
    protected $table = 'command_logs';

}
