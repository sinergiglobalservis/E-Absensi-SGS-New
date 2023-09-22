<?php

namespace App\Models\Master;

use App\Models\GlobalModel;

use Request;
use Crypt;

class MasterCustomer extends GlobalModel
{

    protected $table = 'mst_customer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'customer_code',
        'customer_name',
        'customer_address',
        'customer_pos_code',
        'customer_longitude',
        'customer_latitude',
        'schedule_list',
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

    public function isExists(Request $r)
    {
        $sql = $this->whereRaw("1=1")
            ->where('customer_code', $r->customer_code)
            ->orWhere('customer_name', $r->customer_name);

        return ($sql->count() > 0);
    }
}
