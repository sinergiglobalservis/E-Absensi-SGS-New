<?php

namespace App\Models\Master;

use App\Models\GlobalModel;

use Request;
use Crypt;

class MasterScheduleDetail extends GlobalModel
{

    protected $table = 'mst_schedule_detail';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'mst_schedule_code',
        'schedule_code',
        'schedule_name',
        'day',
        'schedule_in',
        'schedule_out',
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

    // public function isExists(Request $r)
    // {
    //     $sql = $this->whereRaw("1=1")
    //         ->where('schedule_code', $r->schedule_code);

    //     return ($sql->count() > 0);
    // }
}
