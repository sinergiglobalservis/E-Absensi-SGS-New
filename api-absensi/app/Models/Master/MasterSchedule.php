<?php

namespace App\Models\Master;

use App\Models\GlobalModel;

use Request;
use Crypt;

class MasterSchedule extends GlobalModel
{

    protected $table = 'mst_schedule';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'mst_schedule_code',
        'schedule_name',
        'description',
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
        $sql = $this->whereRaw("1=1")
            ->where('mst_schedule_code', $r->mst_schedule_code);

        return ($sql->count() > 0);
    }
}
