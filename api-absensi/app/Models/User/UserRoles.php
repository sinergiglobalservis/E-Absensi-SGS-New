<?php

namespace App\Models\User;

use App\Models\GlobalModel;

use Request;
use Crypt;

class UserRoles extends GlobalModel
{

    protected $table = 'user_roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'users_id',
        'mst_roles_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'verified_at',
        'updated_at',
        'deleted_at',
    ];

    public function __construct()
    {
        parent::__construct();
    }
}