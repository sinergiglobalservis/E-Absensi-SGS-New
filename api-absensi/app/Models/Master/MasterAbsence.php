<?php

namespace App\Models\Master;

use App\Models\GlobalModel;

use Request;
use Crypt;

class MasterAbsence extends GlobalModel
{

    protected $table = 'mst_absence_type';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'absence_code',
        'absence_name',
        'absence_long_date',
        'absence_type',
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
            ->where('absence_code', $r->absence_code);

        return ($sql->count() > 0);
    }
}
