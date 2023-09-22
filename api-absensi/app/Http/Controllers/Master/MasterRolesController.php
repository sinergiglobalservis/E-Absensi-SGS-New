<?php

namespace App\Http\Controllers\Master;

// get master of controller
use App\Http\Controllers\Controller;

// get models
use App\Models\Master\{MasterRoles};

// get library of process
use Request;
use Exception;
use DB;


class MasterRolesController extends Controller
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
            // get data master role join users
            $data = MasterRoles::where('deleted_at', null)->orderBy('role_code', 'asc')->get();
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

    public function getById($id)
    {
        try {
            // get data master role by id
            $data = MasterRoles::find($id);

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
        // validation input
        $this->validate($r, [
            'role_code' => 'required',
            'role_name' => 'required',
            'role_description' => 'required',
            'created_by' => 'required',
        ]);

        try {
            if (!($id = (new MasterRoles)->add($r->all()))) {
                // if not inserted
                throw new Exception("Gagal Menyimpan Data");
            } else {
                // if success
                $this->return = array_merge((array)$this->return, [
                    'data' => $id
                ]);
            }
        } catch (\Throwable $err) {
            $this->return = array_merge((array)$this->return, [
                'status' => false,
                'message' => $err->getMessage(),
                'code' => 401
            ]);
        }

        return response()->json($this->return, $this->return['code']);
    }

    public function edit(Request $r)
    {
        // validation input
        // $this->validate($r, [
        //     'id' => 'required',
        //     'role_code' => 'required',
        //     'role_name' => 'required',
        //     'role_description' => 'required',
        //     'updated_by' => 'required',
        // ]);

        // Validation code exists
        // if ((new MasterRoles)->isExists($r)) {
        //     $this->return = array_merge((array)$this->return, [
        //         'status' => false,
        //         'message' => 'Kode Absen telah terdaftar',
        //         'code' => 401
        //     ]);

        //     return response()->json($this->return, $this->return['code']);
        // }

        try {
            if (!($sql = (new MasterRoles)->edit($r->all(), $r->id))) {
                // if failed
                throw new Exception("Gagal Menyimpan Data");
            } else {
                // if success
                $this->return = array_merge((array)$this->return, [
                    'data' => $sql->id
                ]);
            }
        } catch (\Throwable $err) {
            $this->return = array_merge((array)$this->return, [
                'status' => false,
                'message' => $err->getMessage(),
                'code' => 401
            ]);
        }


        return response()->json($this->return, $this->return['code']);
    }

    public function delete(Request $r)
    {
        // proses soft delete
        if (!($sql = (new MasterRoles)->del($r->id))) {
            // if failed
            $this->return = array_merge((array)$this->return, [
                'status' => false,
                'message' => 'Gagal menghapus data',
                'code' => 401
            ]);
        } else {
            // if success
            return response()->json([
                'status' => true,
                'message' => 'Berhasil!',
                'data'  => [],
                'code'  => 200
            ], 200);
        }
    }

    public function getCode()
    {
        $kode =  '001';

        $count = MasterRoles::count();

        if ($count > 0) {
            $kode = str_pad($kode + $count, 3, '0', STR_PAD_LEFT);
            return 'MRA' . $kode;
        } else {
            return 'MRA' . $kode;
        }
    }
}
