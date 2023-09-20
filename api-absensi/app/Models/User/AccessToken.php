<?php

namespace App\Models\User;

use App\Models\GlobalModel;

use Request;
use Crypt;

class AccessToken extends GlobalModel
{

    protected $table = 'access_token';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'email',
        'access_token',
        'ip_address',
        'log_from',
        'longitude',
        'latitude',
        'status',
        'until_at',
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function generateToken(Request $r)
    {
        if ($r->has('email')) {
            try {
                $sql = $this->where('status', '1')
                    ->where('email', $r->email)
                    ->first();

                if (!$sql) {
                    $sql = new \App\Models\User\AccessToken;
                    $sql->email = $r->email;
                    $sql->access_token = Crypt::encrypt($r->email . "||" . date("YmdHis"));
                    $sql->log_from = $r->appAccess->type;
                    $sql->ip_address = $r->appAccess->ip;
                    $sql->longitude = $r->appAccess->longitude ?? null;
                    $sql->latitude = $r->appAccess->latitude ?? null;
                    $sql->until_at = date("Y-m-d H:i:s", strtotime("+" . env("ACCESS_TOKEN_LIFETIME") . " days"));
                    if (!$sql->save()) {
                        throw new \Exception('Gagal menyimpan ke database', 401);
                    }
                }

                return $sql;
            } catch (\Exception $e) {
                \Log::error(json_encode($e));
                return ['error' => 'Terjadi kesalahan pada pengambilan token'];
            }
        }
        \Log::info("Token : Tidak ada email yang dicantumkan");
        return ['error' => 'Terjadi kesalahan pada pengambilan token'];
    }

    public function removeToken(Request $r)
    {
        if (isset($r->token)) {
            try {
                // delete token
                $sql = $this->where([
                    'access_token' => $r->token
                ])->delete();

                if (!$sql) {
                    throw new \Exception('Gagal menghapus token dari database', 401);
                }

                return $sql;
            } catch (\Exception $e) {
                \Log::error(json_encode($e));
                return ['error' => 'Terjadi kesalahan pada penghapusan token'];
            }
        }
        \Log::info("Token : Tidak ada token yang dicantumkan");
        return ['error' => 'Terjadi kesalahan pada penghapusan token'];
    }

    public function validAccess(Request $r)
    {
        return $this->where(['access_token' => $r->token])->first();
    }

    public function checkToken(Request $r)
    {
        if (is_null($this->curTimestamp)) $this->curTimestamp = date("Y-m-d H:i:s");

        if (isset($r->auth["token"])) {
            $sql = $this->where('access_token', $r->auth["token"])->first();
            if ($sql) {
                if ($sql->status == 0) {
                    $sql->delete();
                    throw new \Exception('Access Token sudah tidak aktif', 401);
                    return;
                }

                if (strtotime($this->curTimestamp) >= strtotime($sql->until_at)) {
                    $sql->delete();
                    throw new \Exception('Access Token Kadaluarsa', 401);
                    return;
                }

                return $sql->email;
            }

            throw new \Exception('Access Token tidak ditemukan', 401);
            return;
        }

        return;
    }
}
