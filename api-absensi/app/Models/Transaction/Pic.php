<?php

namespace App\Models\Transaction;

use App\Models\GlobalModel;

use Request;
use Crypt;

class Pic extends GlobalModel
{

    protected $table = 'pic_list';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'users_id',
        'customer_code',
        'status',
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
