<?php

namespace App\Models\Transaction;

use App\Models\GlobalModel;

use Request;
use Crypt;

class AbsenceDetail extends GlobalModel
{

    protected $table = 'absence_detail';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'absence_id',
        'absence_date',
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
