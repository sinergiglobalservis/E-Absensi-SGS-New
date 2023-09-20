<?php

namespace App\Models\Master;

use App\Models\GlobalModel;

use Request;
use Crypt;

class MasterRoles extends GlobalModel
{

    protected $table = 'mst_roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'role_code',
        'role_name',
        'role_description',
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
            ->where('role_code', $r->role_code);

        return ($sql->count() > 0);
    }
}