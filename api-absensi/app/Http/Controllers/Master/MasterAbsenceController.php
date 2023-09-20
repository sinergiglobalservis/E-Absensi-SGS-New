<?php

namespace App\Http\Controllers\Master;

// get master of controller
use App\Http\Controllers\Controller;

// get models
use App\Models\Master\{MasterAbsence};

// get library of process
use Request;
use Exception;
use DB;


class MasterAbsenceController extends Controller
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
            // get data master absence join users
            $data = MasterAbsence::orderBy('id', 'desc')->where('deleted_at', null)->get();
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
            // get data master absence by id
            $data = MasterAbsence::find($id);

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
        // $this->validate($r, [
        //     'absence_code' => 'required',
        //     'absence_name' => 'required',
        //     'absence_long_date' => 'required',
        //     'created_by' => 'required',
        // ]);

        // Validation code exists
        if ((new MasterAbsence)->isExists($r)) {
            $this->return = array_merge((array)$this->return, [
                'status' => false,
                'message' => 'Kode Absen telah terdaftar',
                'code' => 401
            ]);

            return response()->json($this->return, $this->return['code']);
        }

        try {
            if (!($id = (new MasterAbsence)->add($r->all()))) {
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
        $this->validate($r, [
            'id' => 'required',
            'absence_code' => 'required',
            'absence_name' => 'required',
            'absence_long_date' => 'required',
            'updated_by' => 'required',
        ]);

        try {
            if (!($sql = (new MasterAbsence)->edit($r->all(), $r->id))) {
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
        if (!($sql = (new MasterAbsence)->del($r->id))) {
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

        $count = MasterAbsence::count();

        if ($count > 0) {
            $kode = str_pad($kode + $count, 3, '0', STR_PAD_LEFT);
            return 'MTA' . $kode;
        } else {
            return 'MTA' . $kode;
        }
    }

    // get master cuti
    public function getCuti()
    {
        try {
            // get data master absence join users
            $data = MasterAbsence::orderBy('id', 'desc')->where('absence_type', 2)->where('deleted_at', null)->get();
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

    // get master cuti
    public function getSakit()
    {
        try {
            // get data master absence join users
            $data = MasterAbsence::orderBy('id', 'desc')->where('absence_type', 1)->where('deleted_at', null)->get();
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

    // get master izin
    public function getIzin()
    {
        try {
            // get data master absence join users
            $data = MasterAbsence::orderBy('id', 'desc')->where('absence_type', 3)->where('deleted_at', null)->get();
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
}
