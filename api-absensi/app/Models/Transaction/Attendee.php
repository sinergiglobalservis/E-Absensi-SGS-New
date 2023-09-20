<?php

namespace App\Models\Transaction;

use App\Models\GlobalModel;

use Request;
use Crypt;

class Attendee extends GlobalModel
{

    // protected $table = 'attendee';
    protected $table = 'attendance';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'attendee_date',
        'attendee_time_in',
        'attendee_longitude_in',
        'attendee_latitude_in',
        'images_in',
        'attendee_time_out',
        'attendee_longitude_out',
        'attendee_latitude_out',
        'images_out',
        'workhour_code',
        'absence_ref',
        'absence_ref_desc',
        'day',
        'users_id',
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
