<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

// get models
use App\Models\User\{User, AccessToken};

// get library
use Request;
use Crypt;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function authIn(Request $r)
    {
        if (!($r->has('email') || $r->has('username')) || !$r->has('password')) {
            $this->return = array_merge($this->return, [
                'status' => false,
                'message' => 'Perhatikan input data anda',
                'code' => 401
            ]);
        }

        // verify device id
        // get row user
        // $checkUser = (new User)->checkUser($r);
        // // if roles android user then verify device id
        // if ($checkUser) {
        //     if ($checkUser->mst_roles_id == 1) {
        //         // checking device id
        //         if (!($r->has('device_id'))) {
        //             $this->return = array_merge($this->return, [
        //                 'status' => false,
        //                 'message' => 'Device ID tidak boleh kosong',
        //                 'code' => 401
        //             ]);
        //         }

        //         // update device id jika belum terisi
        //         if ($checkUser->device_id == '') {
        //             if (!($device_id = (new User)->edit(['device_id' => $r->device_id], $checkUser->id))) {
        //                 // if failed
        //                 $this->return = array_merge($this->return, [
        //                     'status' => false,
        //                     'message' => 'Gagal menyimpan device id',
        //                     'code' => 401
        //                 ]);
        //             } else {
        //                 $this->return = array_merge((array)$this->return, [
        //                     'data' => $device_id->id
        //                 ]);
        //             }
        //         }

        //         $verifyDeviceId = (new User)->validDeviceId($r);
        //         if (empty($verifyDeviceId)) {
        //             $this->return = array_merge($this->return, [
        //                 'status' => false,
        //                 'message' => 'Terdeteksi perubahan perangkat',
        //                 'code' => 401
        //             ]);
        //         }
        //     }
        // }

        if ($this->return['status']) {
            // get login data and validation inside there
            $sql = (new User)->login($r);

            if (isset($sql['error'])) {
                $this->return = array_merge($this->return, [
                    'status' => false,
                    'message' => $sql['error'],
                    'code' => 401
                ]);
            }

            $this->return['data'] = $sql;
        }

        return response()->json($this->return, $this->return['code']);
    }

    public function authOut(Request $r)
    {
        $sql = (new User)->logout($r);

        if (isset($sql['error'])) {
            $this->return = array_merge($this->return, [
                'status' => false,
                'message' => $sql['error'],
                'code' => 401
            ]);
        }

        return response()->json($this->return, $this->return['code']);
    }

    public function checkAuth(Request $r)
    {
        if (!($user = (new AccessToken)->validAccess($r))) {
            $this->return = array_merge((array)$this->return, [
                'status' => false,
                'message' => 'Token tidak ditemukan',
                'code' => 401
            ]);
        } else {
            $this->return = array_merge((array)$this->return, [
                'data' => $user->id
            ]);
        }

        return response()->json($this->return, $this->return['code']);
    }
}