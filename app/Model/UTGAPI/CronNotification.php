<?php

namespace App\Model\UTGAPI;

use Illuminate\Database\Eloquent\Model;

class CronNotification extends Model {

    protected $connection = 'mysql';
    protected $table = 'cron_notifications';
    protected $fillable = ['cron_detail_id'];

}
