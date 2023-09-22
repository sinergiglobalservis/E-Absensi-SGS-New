<?php

namespace App\Http\Controllers\Master;

// get master of controller
use App\Http\Controllers\Controller;

// get models
use App\Models\Master\{MasterDepartemen};

// get library of process
use Request;
use Exception;
use DB;


class MasterDepartemenController extends Controller
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
            // get data master customer join users
            $data = MasterDepartemen::orderBy('id', 'desc')->where('deleted_at', null)->get();
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
            // get data master customer by id
            $data = MasterDepartemen::find($id);

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

    public function getByCustomerCode($customer_code)
    {
        try {
            // get data master customer by id
            $data = MasterDepartemen::select('mst_departemen.*', 'mst_customer.customer_name')
                ->join('mst_customer', 'mst_customer.customer_code', '=', 'mst_departemen.customer_code')
                ->where('mst_departemen.customer_code', $customer_code)
                ->where('mst_departemen.deleted_at', null)
                ->get();

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
        // Validation code exists
        if ((new MasterDepartemen)->isExists($r)) {
            $this->return = array_merge((array)$this->return, [
                'status' => false,
                'message' => 'Kode atau nama telah terdaftar',
                'code' => 401
            ]);

            return response()->json($this->return, $this->return['code']);
        }

        try {
            if (!($id = (new MasterDepartemen)->add($r->all()))) {
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
            'departemen_code' => 'required',
            'departemen_name' => 'required',
            'updated_by' => 'required',
        ]);

        try {
            if (!($sql = (new MasterDepartemen)->edit($r->all(), $r->id))) {
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
        if (!($sql = (new MasterDepartemen)->del($r->id))) {
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

        $count = MasterDepartemen::count();

        if ($count > 0) {
            $kode = str_pad($kode + $count, 3, '0', STR_PAD_LEFT);
            return 'MD' . $kode;
        } else {
            return 'MD' . $kode;
        }
    }
}
