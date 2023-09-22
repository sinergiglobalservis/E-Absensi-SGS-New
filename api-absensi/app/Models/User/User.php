<?php

namespace App\Models\User;

use App\Models\GlobalModel;

use Request;
use Crypt;

class User extends GlobalModel
{

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'email',
        'username',
        'pwd_hash',
        'status',
        'verified_token',
        'device_id',
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

    public function isExists(Request $r)
    {
        $sql = $this->where('email', $r->email)
            ->orWhere('username', $r->username);

        return ($sql->count() > 0);
    }

    public function checkUser(Request $r)
    {
        if ($r->has('email')) return $this->select('user_roles.mst_roles_id', 'users.*')->leftJoin('user_roles', 'user_roles.users_id', '=', 'users.id')->where('email', $r->email)->first();
        if ($r->has('username')) return $this->select('user_roles.mst_roles_id', 'users.*')->leftJoin('user_roles', 'user_roles.users_id', '=', 'users.id')->where('username', $r->username)->first();
    }

    public function validDeviceId(Request $r)
    {
        return $this->where(['device_id' => $r->device_id])->first();
    }

    public function validToken(Request $r)
    {
        return $this->where(['verified_token' => $r->token])->first();
    }

    public function login(Request $r)
    {
        try {
            // $sql = $this->whereRaw('1=1');
            $sql = $this->selectRaw('users.*, user_roles.mst_roles_id, placement.customer_code')
                ->leftJoin('user_roles', 'user_roles.users_id', '=', 'users.id')
                ->leftJoin('placement', 'placement.users_id', '=', 'users.id')
                ->leftJoin('mst_customer', 'mst_customer.customer_code', '=', 'placement.customer_code')
                ->where('mst_customer.status', 1);
            if ($r->has('email')) $sql->where('email', $r->email);
            if ($r->has('username')) $sql->where('username', $r->username);
            $sql = $sql->first();

            // all validation when login
            if (!$sql) throw new \Exception('Email/Nama Pengguna anda tidak ditemukan');
            if ($r->password != Crypt::decrypt($sql->pwd_hash)) throw new \Exception('Kata sandi tidak sesuai');
            if ($sql->verified_at == null) throw new \Exception('Anda belum memvalidasi pengguna anda, cek email sebelumnya dari kami.');
            if ($sql->status == 0) throw new \Exception('Akun anda tidak aktif.');

            // generate token
            $r->request->add([
                'username' => $sql->username,
                'email' => $sql->email,
            ]);
            $token = (new \App\Models\User\AccessToken)->generateToken($r);
            if (isset($token->error)) throw new Exception($token->error, 401);
            $sql->{'access_token'} = $token->access_token;

            return $sql->only(['id', 'email', 'username', 'mst_roles_id', 'customer_code', 'access_token']);
        } catch (\Exception $e) {
            \Log::error(json_encode($e));
            return ['error' => $e->getMessage()];
        }
    }

    public function logout(Request $r)
    {
        try {
            // remove token existing
            $token = (new \App\Models\User\AccessToken)->removeToken($r);
            if (isset($token->error)) throw new Exception($token->error, 401);
        } catch (\Exception $e) {
            \Log::error(json_encode($e));
            return ['error' => $e->getMessage()];
        }
    }
}
