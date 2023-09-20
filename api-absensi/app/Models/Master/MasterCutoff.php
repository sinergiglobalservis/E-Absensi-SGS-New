<?php

namespace App\Models\Master;

use App\Models\GlobalModel;

use Request;
use Crypt;

class MasterCutoff extends GlobalModel
{

    protected $table = 'mst_cutoff';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'customer_code',
        'cutoff_start',
        'cutoff_end',
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
    //         ->where('customer_code', $r->customer_code);

    //     return ($sql->count() > 0);
    // }
}
