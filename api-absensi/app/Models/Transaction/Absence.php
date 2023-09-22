<?php

namespace App\Models\Transaction;

use App\Models\GlobalModel;

use Request;
use Crypt;

class Absence extends GlobalModel
{

    protected $table = 'absence';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'absence_code',
        'absence_description',
        'absence_attachment',
        'absence_address',
        'absence_date',
        // 'absence_start_date',
        // 'absence_end_date',
        // 'absence_duration',
        'approval_1',
        'approval_2',
        'absence_latitude',
        'absence_longitude',
        'total_leave',
        'users_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function __construct()
    {
        parent::__construct();
    }
}