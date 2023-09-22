<?php

namespace App\Http\Controllers\Transactions;

// get master of controller
use App\Http\Controllers\Controller;

// get models
use App\Models\Transaction\{Holiday};

// get library of process
use Request;
use Exception;

class HolidayController extends Controller
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
            // get data cuti by id user
            $data = Holiday::all();

            if (count($data) > 0) {
                $this->return = array_merge((array)$this->return, [
                    'status'    => true,
                    'data'      => $data,
                    'code'      => 200
                ]);
            } else {
                throw new Exception("Data Kosong");
            }
        } catch (\Throwable $err) {
            $this->return = array_merge((array)$this->return, [
                'status'    => true,
                'message'   => $err->getMessage(),
                'code'      => 200
            ]);
        }

        return response()->json($this->return, $this->return['code']);
    }

    public function add(Request $r)
    {
        if (!($id = (new Holiday)->add($r->all()))) {
            $this->return = array_merge((array)$this->return, [
                'status' => false,
                'message' => 'Gagal menambahkan data',
                'code' => 401
            ]);
        } else {
            $this->return = array_merge((array)$this->return, [
                'data'  => $id
            ]);
        }

        return response()->json($this->return, $this->return['code']);
    }
}
