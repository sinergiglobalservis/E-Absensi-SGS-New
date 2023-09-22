<?php

namespace App\Models\Master;

use App\Models\GlobalModel;

use Request;
use Crypt;

class MasterDepartemen extends GlobalModel
{

    protected $table = 'mst_departemen';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'departemen_code',
        'departemen_name',
        'customer_code',
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
            ->where('departemen_code', $r->departemen_code);
        // ->orWhere('departemen_name', $r->departemen_name);

        return ($sql->count() > 0);
    }
}
