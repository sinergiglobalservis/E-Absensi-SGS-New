<?php

namespace App\Http\Controllers\Settings;

// get master of controller
use App\Http\Controllers\Controller;

// get models
use App\Models\User\{User, UserRoles, AccessToken};
use App\Models\Transaction\{Placement, Profiles, PlacementDepartemen};

// get library of process
use Request;
use Crypt;
use Mail;
use Exception;

use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class UserController extends Controller
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

    public function show()
    {
        try {
            // get data users
            $data = User::leftjoin('user_roles', 'user_roles.users_id', '=', 'users.id')
                ->leftjoin('mst_roles', 'mst_roles.id', '=', 'user_roles.mst_roles_id')
                ->leftjoin('placement', 'placement.users_id', '=', 'users.id')
                ->leftjoin('mst_customer', 'mst_customer.customer_code', '=', 'placement.customer_code')
                ->leftjoin('profiles', 'profiles.users_id', '=', 'users.id')
                ->leftjoin('placement_departemen AS pd', 'pd.users_id', '=', 'users.id')
                ->leftjoin('mst_departemen', 'mst_departemen.departemen_code', '=', 'pd.departemen_code')
                ->select('users.id', 'mst_roles.role_name', 'users.email', 'users.status', 'users.username', 'mst_customer.customer_name', 'mst_customer.customer_code', 'profiles.profile_name', 'profiles.profile_gender', 'profiles.nik', 'profiles.profile_address', 'profiles.profile_phone', 'mst_departemen.departemen_name', 'mst_departemen.departemen_code', 'user_roles.mst_roles_id', 'profiles.birthplace', 'profiles.birthdate', 'profiles.identity_number')
                ->where('users.deleted_at', null)
                ->orderBy('users.id', 'desc')
                ->get();
            //     ->toSql();
            // return $data;

            foreach ($data as $key => $value) {
                $value['absence_attachment'] = url('documents/' . $value['absence_attachment']);
            }

            if (count($data) > 0) {
                $this->return = array_merge((array)$this->return, [
                    'data' => $data
                ]);
            } else {
                throw new Exception("Data Kosong");
            }
        } catch (\Throwable $err) {
            $this->return = array_merge((array)$this->return, [
                'status'    => false,
                'message'   => $err->getMessage(),
                'code'      => 200
            ]);
        }

        return response()->json($this->return, $this->return['code']);
    }

    public function getByUserId($users_id)
    {
        try {
            // get data users
            $data = User::leftjoin('user_roles', 'user_roles.users_id', '=', 'users.id')
                ->leftjoin('mst_roles', 'mst_roles.id', '=', 'user_roles.mst_roles_id')
                ->leftjoin('placement', 'placement.users_id', '=', 'users.id')
                ->leftjoin('mst_customer', 'mst_customer.customer_code', '=', 'placement.customer_code')
                ->leftjoin('profiles', 'profiles.users_id', '=', 'users.id')
                ->select('users.id', 'mst_roles.role_name', 'users.email', 'users.status', 'users.username', 'mst_customer.customer_name', 'profiles.profile_name', 'profiles.profile_gender', 'profiles.nik', 'profiles.profile_address', 'profiles.profile_phone', 'profiles.profile_phone2', 'profiles.remaining_leave', 'profiles.cuti_besar')
                ->where('users.id', $users_id)
                ->where('users.deleted_at', null)
                ->orderBy('users.id', 'desc')
                ->first();

            if (count($data) > 0) {
                $this->return = array_merge((array)$this->return, [
                    'data' => $data
                ]);
            } else {
                throw new Exception("Data Kosong");
            }
        } catch (\Throwable $err) {
            $this->return = array_merge((array)$this->return, [
                'status'    => false,
                'message'   => $err->getMessage(),
                'code'      => 200
            ]);
        }

        return response()->json($this->return, $this->return['code']);
    }

    public function getBy($customer_code)
    {
        try {
            // get data users
            $data = User::leftjoin('user_roles', 'user_roles.users_id', '=', 'users.id')
                ->leftjoin('mst_roles', 'mst_roles.id', '=', 'user_roles.mst_roles_id')
                ->leftjoin('placement', 'placement.users_id', '=', 'users.id')
                ->leftjoin('mst_customer', 'mst_customer.customer_code', '=', 'placement.customer_code')
                ->leftjoin('profiles', 'profiles.users_id', '=', 'users.id')
                ->leftjoin('placement_departemen AS pd', 'pd.users_id', '=', 'users.id')
                ->leftjoin('mst_departemen', 'mst_departemen.departemen_code', '=', 'pd.departemen_code')
                ->select('users.id', 'mst_roles.role_name', 'users.email', 'users.status', 'users.username', 'mst_customer.customer_name', 'mst_customer.customer_code', 'profiles.profile_name', 'profiles.profile_gender', 'profiles.nik', 'profiles.profile_address', 'profiles.profile_phone', 'mst_departemen.departemen_name', 'mst_departemen.departemen_code', 'user_roles.mst_roles_id', 'profiles.birthplace', 'profiles.birthdate', 'profiles.identity_number')
                ->where('mst_customer.customer_code', $customer_code)
                ->where('users.deleted_at', null)
                ->orderBy('users.id', 'desc')
                ->get();
            //     ->toSql();
            // return $data;

            foreach ($data as $key => $value) {
                $value['absence_attachment'] = url('documents/' . $value['absence_attachment']);
            }

            if (count($data) > 0) {
                $this->return = array_merge((array)$this->return, [
                    'data' => $data
                ]);
            } else {
                throw new Exception("Data Kosong");
            }
        } catch (\Throwable $err) {
            $this->return = array_merge((array)$this->return, [
                'status'    => false,
                'message'   => $err->getMessage(),
                'code'      => 200
            ]);
        }

        return response()->json($this->return, $this->return['code']);
    }

    public function add(Request $r)
    {
        // Validation email exists
        if ((new User)->isExists($r)) {
            $this->return = array_merge((array)$this->return, [
                'status' => false,
                'message' => 'Email atau username telah terdaftar',
                'code' => 401
            ]);

            return response()->json($this->return, $this->return['code']);
        }

        // Append new request parameter
        $token = md5($this->curTimestamp);
        $r->request->add([
            'pwd_hash' => Crypt::encrypt(substr($token, 0, 8)),
            'verified_token' => md5($token)
        ]);

        $id = (new User)->add([
            'email' => $r->email,
            'username' => $r->username,
            'pwd_hash' => $r->pwd_hash,
            'verified_token' => $r->verified_token,
            'created_by' => $r->created_by,
            'device_id' => '',
            'status'    => 0,
        ]);

        if (!$id) {
            $this->return = array_merge((array)$this->return, [
                'status' => false,
                'message' => 'Gagal Menambahkan Pengguna',
                'code' => 401
            ]);
        } else {
            // check profile post
            if ($r->has('nik')) {
                $profile = array(
                    'users_id' => $id,
                    'nik' => $r->nik,
                    'profile_name' => $r->profile_name,
                    'profile_gender' => $r->profile_gender,
                    'profile_address' => ($r->profile_address != '' ? $r->profile_address : '-'),
                    'profile_phone' => ($r->profile_phone != '' ? $r->profile_phone : '-'),
                    'profile_phone2' => '',
                    'remaining_leave' => 0,
                    'identity_number' => $r->identity_number,
                    'birthplace' => $r->birthplace,
                    'birthdate' => $r->birthdate,
                    'created_by' => $r->created_by
                );
                $save = (new Profiles)->add($profile);
            }

            // check customer post
            if ($r->has('customer_code')) {
                $customer = array(
                    'users_id' => $id,
                    'customer_code' => $r->customer_code,
                    'created_by' => $r->created_by
                );
                $save = (new Placement)->add($customer);
            }
            // check placement departemen code
            if ($r->has('departemen_code')) {
                $placement_departemen = array(
                    'users_id' => $id,
                    'departemen_code' => $r->departemen_code,
                    'created_by' => $r->created_by
                );
                $save = (new PlacementDepartemen)->add($placement_departemen);
            }

            // check role access post
            if ($r->has('mst_roles_id')) {
                // if not exists
                $userRole = array(
                    'users_id' => $id,
                    'mst_roles_id' => $r->mst_roles_id,
                    'created_by' => $r->created_by
                );
                $save = (new UserRoles)->add($userRole);
            }

            // $mail = (new Mail)->to($r->email)
            //     ->from(env('MAIL_USERNAME'), 'INFO Admin (no-reply)')
            //     ->subject('[KBP] User Validation')
            //     ->body(
            //         '<h1>Selamat Bergabung</h1>
            //         <h4>Aplikasi Absensi</h4>
            //         <br \/><br \/>
            //         <a target="_blank" href="' . env("WEB_URL") . 'verify?token=' . $r->verified_token . '">[ Klik disini untuk mendapatkan kredensial anda ]</a>'
            //     )
            //     ->send();


            $this->return = array_merge((array)$this->return, [
                'data' => $id
            ]);
        }

        return response()->json($this->return, $this->return['code']);
    }

    public function edit(Request $r)
    {
        // Validation email exists
        if (!(new User)->isExists($r)) {
            $this->return = array_merge((array)$this->return, [
                'status' => false,
                'message' => 'Email Pengguna tidak terdaftar',
                'code' => 401
            ]);

            return response()->json($this->return, $this->return['code']);
        }

        // check apakah password diubah atau tidak
        if ($r->has('password') && $r->password != null) {
            $r->request->add([
                'pwd_hash' => Crypt::encrypt(substr($r->password, 0, 8))
            ]);
        }

        // check profile post
        $profilesCheck = null;
        if ($r->has('nik')) {
            // check profile exists
            $profilesCheck = Profiles::where('users_id', $r->id)->first();
            if (count($profilesCheck) > 0) {
                // if exists
                $profile = array(
                    'nik' => $r->nik,
                    'profile_name' => $r->profile_name,
                    'profile_gender' => $r->profile_gender,
                    'profile_address' => $r->profile_address,
                    'profile_phone' => $r->profile_phone,
                    // 'profile_phone2' => $r->profile_phone2,
                    'identity_number' => ($r->has('identity_number') ? $r->identity_number : $profilesCheck->identity_number),
                    'birthplace' => ($r->has('birthplace') ? $r->birthplace : $profilesCheck->birthplace),
                    'birthdate' => ($r->has('birthdate') ? $r->birthdate : $profilesCheck->birthdate),
                    'updated_by' => $r->updated_by,
                    'updated_at' => date("Y-m-d H:i:s"),
                );
                if (!($update = (new Profiles)->edit($profile, $profilesCheck->id))) {
                    $this->return = array_merge((array)$this->return, [
                        'status' => false,
                        'message' => 'Gagal merubah profile',
                        'code' => 400
                    ]);
                }
            } else {
                // if not exists
                $profile = array(
                    'users_id' => $r->id,
                    'nik' => $r->nik,
                    'identity_number' => ($r->has('identity_number') ? $r->identity_number : null),
                    'profile_name' => $r->profile_name,
                    'profile_gender' => $r->profile_gender,
                    'profile_address' => $r->profile_address,
                    'profile_phone' => $r->profile_phone,
                    'profile_phone2' => '',
                    'birthplace' => ($r->has('birthplace') ? $r->birthplace : null),
                    'birthdate' => ($r->has('birthdate') ? $r->birthdate : null),
                    'created_by' => $r->created_by,
                    'remaining_leave' => 0
                );
                if (!($insert = (new Profiles)->add($profile))) {
                    $this->return = array_merge((array)$this->return, [
                        'status' => false,
                        'message' => 'Gagal menambah data profile',
                        'code' => 400
                    ]);
                }
            }
        }

        // check placement post
        $placementsCheck = null;
        if ($r->has('customer_code')) {
            // check placement exists
            $placementsCheck = Placement::where('users_id', $r->id)->first();
            if (count($placementsCheck) > 0) {
                // if exists
                $placement = array(
                    'customer_code' => $r->customer_code,
                    'updated_by' => $r->updated_by,
                    'updated_at' => date("Y-m-d H:i:s"),
                );
                if (!($update = (new Placement)->edit($placement, $placementsCheck->id))) {
                    $this->return = array_merge((array)$this->return, [
                        'status' => false,
                        'message' => 'Gagal merubah penempatan perusahaan',
                        'code' => 400
                    ]);
                }
            } else {
                // if not exists
                $placement = array(
                    'users_id' => $r->id,
                    'customer_code' => $r->customer_code,
                    'created_by' => $r->created_by,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                );
                if (!($insert = (new Placement)->add($placement))) {
                    $this->return = array_merge((array)$this->return, [
                        'status' => false,
                        'message' => 'Gagal menambah penempatan perusahaan',
                        'code' => 400
                    ]);
                }
            }
        }

        // check placement departemen post
        $placementDepartemensCheck = null;
        if ($r->has('departemen_code')) {
            // check placement departemen exists
            $placementDepartemensCheck = PlacementDepartemen::where('users_id', $r->id)->first();
            if (count($placementDepartemensCheck) > 0) {
                // if exists
                $placementDepartemen = array(
                    'departemen_code' => $r->departemen_code,
                    'updated_by' => $r->updated_by,
                    'updated_at' => date("Y-m-d H:i:s")
                );
                if (!($update = (new PlacementDepartemen)->edit($placementDepartemen, $placementDepartemensCheck->id))) {
                    $this->return = array_merge((array)$this->return, [
                        'status' => false,
                        'message' => 'Gagal merubah penempatan departemen',
                        'code' => 400
                    ]);
                }
            } else {
                // if not exists
                $placementDepartemen = array(
                    'users_id' => $r->id,
                    'departemen_code' => $r->departemen_code,
                    'created_by' => $r->created_by,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                );
                if (!($insert = (new PlacementDepartemen)->add($placementDepartemen))) {
                    $this->return = array_merge((array)$this->return, [
                        'status' => false,
                        'message' => 'Gagal menambah penempatan departemen',
                        'code' => 400
                    ]);
                }
            }
        }

        // check role access post
        $rolesCheck = null;
        if ($r->has('mst_roles_id')) {
            // check user role exists
            $rolesCheck = UserRoles::where('users_id', $r->id)->first();
            if (count($rolesCheck) > 0) {
                // if exists
                $userRole = array(
                    'mst_roles_id' => $r->mst_roles_id,
                    'updated_by' => $r->updated_by,
                    'updated_at' => date("Y-m-d H:i:s"),
                );
                if (!($update = (new UserRoles)->edit($userRole, $rolesCheck->id))) {
                    $this->return = array_merge((array)$this->return, [
                        'status' => false,
                        'message' => 'Gagal merubah role akses',
                        'code' => 400
                    ]);
                }
            } else {
                // if not exists
                $userRole = array(
                    'users_id' => $r->id,
                    'mst_roles_id' => $r->mst_roles_id,
                    'created_by' => $r->updated_by,
                    'updated_by' => $r->updated_by,
                    'updated_at' => date("Y-m-d H:i:s"),
                );

                if (!($insert = (new UserRoles)->add($userRole))) {
                    $this->return = array_merge((array)$this->return, [
                        'status' => false,
                        'message' => 'Gagal merubah role akses',
                        'code' => 400
                    ]);
                }
            }
        }

        if (!($sql = (new User)->edit($r->all(), $r->id))) {
            $this->return = array_merge((array)$this->return, [
                'status' => false,
                'message' => 'Gagal merubah pengguna',
                'code' => 400
            ]);
        } else {
            // generate email confirmation to get password
            // $mail = (new Mail)->to($sql->email)
            //     ->from(env('MAIL_USERNAME'), 'INFO Admin (no-reply)')
            //     ->subject('[KBP] Edit User')
            //     ->body('<h1>Ada yang mengubah user anda</h1><br />Data diubah pada tanggal berikut : ' . date("d-m-Y H:i:s", strtotime($sql->updated_at)))
            //     ->send();

            $this->return = array_merge((array)$this->return, [
                'data' => $sql->id
            ]);
        }

        return response()->json($this->return, $this->return['code']);
    }

    public function valid(Request $r)
    {
        // Validation user token
        if (!($user = (new User)->validToken($r))) {
            $this->return = array_merge((array)$this->return, [
                'status' => false,
                'message' => 'Token tidak sesuai, mohon periksa kembali token yang anda kirimkan',
                'code' => 401
            ]);

            return response()->json($this->return, $this->return['code']);
        }


        // Append new request parameter
        $r->request->add([
            'status' => '1',
            'verified_at' => $this->curTimestamp
        ]);

        if (!($sql = (new User)->edit($r->all(), $user->id))) {
            $this->return = array_merge((array)$this->return, [
                'status' => false,
                'message' => 'Gagal merubah pengguna',
                'code' => 401
            ]);
        } else {
            // generate email password
            $mail = (new Mail)->to($sql->email)
                ->from(env('MAIL_USERNAME'), 'INFO Admin (no-reply)')
                ->subject('[KBP] Email Validated')
                ->body('<h1>Email anda telah divalidasi</h1><br />Nama Pengguna : ' . $sql->username . '<br />Kata sandi anda saat ini adalah : ' . Crypt::decrypt($sql->pwd_hash))
                ->send();

            $this->return = array_merge((array)$this->return, [
                'data' => $sql->id
            ]);
        }

        return response()->json($this->return, $this->return['code']);
    }

    public function resetPassword(Request $r)
    {
        $isActive = User::where(['id' => $r->id, 'status' => 1])->first();
        if ($isActive) {
            // Append new request parameter
            $token = md5($this->curTimestamp);
            $r->request->add([
                'pwd_hash' => Crypt::encrypt(substr($token, 0, 8))
            ]);

            if (!($sql = (new User)->edit(['pwd_hash' => $r->pwd_hash], $r->id))) {
                $this->return = array_merge((array)$this->return, [
                    'status' => false,
                    'message' => 'Gagal merubah pengguna',
                    'code' => 401
                ]);
            } else {
                // generate email password
                $mail = (new Mail)->to($sql->email)
                    ->from(env('MAIL_USERNAME'), 'INFO Admin (no-reply)')
                    ->subject('[KBP] Reset Password')
                    ->body('<h1>Reset Password Berhasil</h1><br />Kata sandi anda saat ini adalah : ' . Crypt::decrypt($sql->pwd_hash))
                    ->send();

                $this->return = array_merge((array)$this->return, [
                    'data' => $sql->id
                ]);
            }
        } else {
            $this->return = array_merge((array)$this->return, [
                'status' => false,
                'message' => 'User tidak aktif',
                'code' => 401
            ]);
        }
        return response()->json($this->return, $this->return['code']);
    }

    public function sendVerification(Request $r)
    {
        foreach ($r->users as $key => $value) {
            $data = User::select('email', 'verified_token')
                ->where(['id' => $value['id'], 'verified_at' => null])
                ->first();
            if ($data) {
                $mail = (new Mail)->to($data->email)
                    ->from(env('MAIL_USERNAME'), 'INFO Admin (no-reply)')
                    ->subject('[KBP] User Validation')
                    ->body(
                        '<h1>Selamat Bergabung</h1>
                    <h4>Aplikasi Absensi</h4>
                    <br \/><br \/>
                    <a target="_blank" href="' . env("WEB_URL") . 'verify?token=' . $data->verified_token . '">[ Klik disini untuk mendapatkan kredensial anda ]</a>'
                    )
                    ->send();
            }
        }

        $this->return = array_merge((array)$this->return, [
            'status'    => true,
            'message'   => 'Email berhasil dikirim',
            'code'      => 200
        ]);

        return response()->json($this->return, $this->return['code']);
    }

    public function ExportExcel()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');
        try {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->setCellValue('A1', 'No Identitas');
            $spreadSheet->getActiveSheet()->setCellValue('B1', 'No Pegawai');
            $spreadSheet->getActiveSheet()->setCellValue('C1', 'Nama Lengkap');
            $spreadSheet->getActiveSheet()->setCellValue('D1', 'Username');
            $spreadSheet->getActiveSheet()->setCellValue('E1', 'Email');
            $spreadSheet->getActiveSheet()->setCellValue('F1', 'Alamat');
            $spreadSheet->getActiveSheet()->setCellValue('G1', 'Tempat Lahir');
            $spreadSheet->getActiveSheet()->setCellValue('H1', 'Tanggal Lahir (yyyy-mm-dd');
            $spreadSheet->getActiveSheet()->setCellValue('I1', 'Telepon');
            $spreadSheet->getActiveSheet()->setCellValue('J1', 'Role Akses');
            $spreadSheet->getActiveSheet()->setCellValue('K1', 'Perusahaan');
            $spreadSheet->getActiveSheet()->setCellValue('L1', 'Departemen');
            $spreadSheet->getActiveSheet()->setCellValue('M1', 'Jenis Kelamin (L/P)');

            // check folder is exists
            if (!File::isDirectory(base_path('public/excel'))) {
                // if not exists then create folder
                File::makeDirectory(base_path('public/excel'));
            }

            if (!File::isDirectory(base_path('public/excel/users'))) {
                File::makeDirectory(base_path('public/excel/users'));
            }

            $Excel_writer = new Xls($spreadSheet);
            $path = base_path('public/excel/users/') . 'template_karyawan.xls';
            $Excel_writer->save($path);
            // exit();
        } catch (Exception $e) {
            return;
        }
    }

    public function exportData(Request $r)
    {
        // Validation user token
        if (!($user = (new AccessToken)->validAccess($r))) {
            $this->return = array_merge((array)$this->return, [
                'status' => false,
                'message' => 'Token tidak sesuai, mohon periksa kembali token yang anda kirimkan',
                'code' => 401
            ]);

            return response()->json($this->return, $this->return['code']);
        }

        // export excel
        $this->ExportExcel();
        // setup filename
        $this->return = array_merge((array)$this->return, [
            'data' => url('excel/users/template_karyawan.xls')
        ]);

        return response()->json($this->return, $this->return['code']);
    }
}
