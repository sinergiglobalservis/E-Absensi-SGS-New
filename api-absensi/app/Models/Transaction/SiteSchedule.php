<?php

namespace App\Models\Transaction;

use App\Models\GlobalModel;

use Request;
use Crypt;

class SiteSchedule extends GlobalModel
{
    protected $table = 'site_schedule';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'customer_code',
        'schedule_name',
        'schedule_code',
        'schedule_in',
        'schedule_out',
        'day',
        'weekday',
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
