<?php

namespace App\Models\Transaction;

use App\Models\GlobalModel;

use Request;
use Crypt;

class Profiles extends GlobalModel
{

    protected $table = 'profiles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'users_id',
        'nik',
        'profile_name',
        'profile_gender',
        'profile_address',
        'profile_phone',
        'profile_phone2',
        'remaining_leave',
        'identity_number',
        'birthplace',
        'birthdate',
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
}
