<?php

namespace App\Model\UTGAPI;

use Illuminate\Database\Eloquent\Model;

class FileImportData extends Model
{
    protected $connection = 'mysql';
    protected $table = 'file_import_data';
}
