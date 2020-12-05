<?php

namespace App\Model\Intelling;

use Illuminate\Database\Eloquent\Model;

class WebSetting extends Model {

    protected $connection = 'Intelling';
    protected $table = 'web_settings';
    public $timestamps = false;

}
