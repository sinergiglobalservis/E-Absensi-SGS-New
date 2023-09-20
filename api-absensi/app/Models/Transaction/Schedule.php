<?php

namespace App\Models\Transaction;

use App\Models\GlobalModel;

use Request;
use Crypt;

class Schedule extends GlobalModel
{
    protected $table = 'schedule';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'users_id',
        'schedule_code',
        'schedule_start_date',
        'schedule_end_date',
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

    public function isExists(Request $r)
    {
        $sql = $this->where('users_id', $r->users_id);

        return ($sql->count() > 0);
    }
}
