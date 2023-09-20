<?php

namespace App\Models\Master;

use App\Models\GlobalModel;

use Request;
use Crypt;

class MasterWorkhour extends GlobalModel
{

    protected $table = 'mst_workhour';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'template_name',
        'workhour_code',
        'workhour_name',
        'workhour_in',
        'workhour_out',
        'total_hour',
        'total_day',
        'keterangan',
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
            ->where('workhour_code', $r->workhour_code);

        return ($sql->count() > 0);
    }
}
