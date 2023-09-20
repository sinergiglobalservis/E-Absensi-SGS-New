<?php

namespace App\Http\Controllers\Master;

// get master of controller
use App\Http\Controllers\Controller;

// get models
use App\Models\Master\{MasterWorkhour, MasterCustomer};

// get library of process
use Request;
use Exception;
use DB;


class MasterWorkhourController extends Controller
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
            // get data master workhour join users
            $data = MasterWorkhour::orderBy('template_name', 'asc')->where('deleted_at', null)->get();
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
            // get data master workhour by id
            $data = MasterWorkhour::find($id);

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

    // get all by customer code
    public function getByCustomerCode($customer_code)
    {
        try {
            // get data master workhour by id
            $mst_customer = MasterCustomer::orderBy('id', 'desc')->where('customer_code', $customer_code)->where('deleted_at', null)->get();
            foreach ($mst_customer as $key => $value) {
                $value->schedule_list = explode(',', $value->schedule_list);
                foreach ($value->schedule_list as $k => $v) {
                    $data[] = MasterWorkhour::where('workhour_code', $v)
                        ->where('deleted_at', null)
                        ->first();
                }
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
        // validation input
        // $this->validate($r, [
        //     'workhour_code' => 'required',
        //     'workhour_name' => 'required',
        //     'workhour_long_date' => 'required',
        //     'created_by' => 'required',
        // ]);

        // Validation code exists
        if ((new MasterWorkhour)->isExists($r)) {
            $this->return = array_merge((array)$this->return, [
                'status' => false,
                'message' => 'Kode telah terdaftar',
                'code' => 401
            ]);

            return response()->json($this->return, $this->return['code']);
        }

        try {
            if (!($id = (new MasterWorkhour)->add($r->all()))) {
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
        //     'workhour_code' => 'required',
        //     'workhour_name' => 'required',
        //     'workhour_long_date' => 'required',
        //     'updated_by' => 'required',
        // ]);

        try {
            if (!($sql = (new MasterWorkhour)->edit($r->all(), $r->id))) {
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
        if (!($sql = (new MasterWorkhour)->del($r->id))) {
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

        $count = MasterWorkhour::count();

        if ($count > 0) {
            $kode = str_pad($kode + $count, 3, '0', STR_PAD_LEFT);
            return 'MJK' . $kode;
        } else {
            return 'MJK' . $kode;
        }
    }
}