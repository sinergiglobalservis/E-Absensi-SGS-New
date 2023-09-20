<?php

namespace App\Http\Controllers\Master;

// get master of controller
use App\Http\Controllers\Controller;

// get models
use App\Models\Transaction\{SiteSchedule};
use App\Models\Master\{MasterSchedule, MasterScheduleDetail, MasterCustomer};

// get library of process
use Request;
use Exception;
use DB;


class MasterScheduleController extends Controller
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
            $data = MasterScheduleDetail::where('deleted_at', null)->orderBy('id', 'desc')->get();
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

    public function getAll()
    {
        try {
            // get data master workhour join users
            $data = MasterScheduleDetail::selectRaw('MIN(id) AS id,MIN(schedule_code) AS schedule_code, MIN(schedule_name) AS schedule_name, MIN(schedule_in) AS schedule_in, MIN(schedule_out) AS schedule_out')
                ->where('deleted_at', null)
                ->groupBy('schedule_code')
                ->orderBy('schedule_name', 'asc')->get();
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

    public function getByScheduleCode($schedule_code)
    {
        try {
            $data = MasterScheduleDetail::where('schedule_code', $schedule_code)->orderBy('id')->get();

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
            $data = SiteSchedule::where('customer_code', $customer_code)->orderBy('id')->get();

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
            $data = SiteSchedule::find($id);

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
        if ((new MasterSchedule)->isExists($r)) {
            $this->return = array_merge((array)$this->return, [
                'status' => false,
                'message' => 'Kode telah terdaftar',
                'code' => 401
            ]);

            return response()->json($this->return, $this->return['code']);
        }

        try {
            $mst_schedule = [
                'mst_schedule_code' => $r->mst_schedule_code,
                'schedule_name' => $r->mst_schedule_name,
                'description' => $r->description,
                'created_by' => $r->created_by,
            ];
            // $mst_schedule = true;
            // if (!$mst_schedule == true) {
            if (!($id = (new MasterSchedule)->add($mst_schedule))) {
                // if not inserted
                throw new Exception("Gagal Menyimpan Data");
            } else {
                // insert detail schedule
                if ($r->isNonShift == true) {
                    if ($r->monday_in != '' && $r->monday_out != '') {
                        $detail = [
                            'mst_schedule_code' => $r->mst_schedule_code,
                            'schedule_code' => $r->mst_schedule_code,
                            'schedule_name' => $r->mst_schedule_name,
                            'day'   => 'Monday',
                            'schedule_in' => $r->monday_in,
                            'schedule_out' => $r->monday_out,
                            'created_by' => $r->created_by
                        ];
                        $save = (new MasterScheduleDetail)->add($detail);
                    }

                    if ($r->tuesday_in != '' && $r->tuesday_out != '') {
                        $detail = [
                            'mst_schedule_code' => $r->mst_schedule_code,
                            'schedule_code' => $r->mst_schedule_code,
                            'schedule_name' => $r->schedule_name,
                            'day'   => 'Tuesday',
                            'schedule_in' => $r->tuesday_in,
                            'schedule_out' => $r->tuesday_out,
                            'created_by' => $r->created_by
                        ];
                        $save = (new MasterScheduleDetail)->add($detail);
                    }

                    if ($r->wednesday_in != '' && $r->wednesday_out != '') {
                        $detail = [
                            'mst_schedule_code' => $r->mst_schedule_code,
                            'schedule_code' => $r->mst_schedule_code,
                            'schedule_name' => $r->schedule_name,
                            'day'   => 'Wednesday',
                            'schedule_in' => $r->wednesday_in,
                            'schedule_out' => $r->wednesday_out,
                            'created_by' => $r->created_by
                        ];
                        $save = (new MasterScheduleDetail)->add($detail);
                    }

                    if ($r->thursday_in != '' && $r->thursday_out != '') {
                        $detail = [
                            'mst_schedule_code' => $r->mst_schedule_code,
                            'schedule_code' => $r->mst_schedule_code,
                            'schedule_name' => $r->schedule_name,
                            'day'   => 'Thursday',
                            'schedule_in' => $r->thursday_in,
                            'schedule_out' => $r->thursday_out,
                            'created_by' => $r->created_by
                        ];
                        $save = (new MasterScheduleDetail)->add($detail);
                    }

                    if ($r->friday_in != '' && $r->friday_out != '') {
                        $detail = [
                            'mst_schedule_code' => $r->mst_schedule_code,
                            'schedule_code' => $r->mst_schedule_code,
                            'schedule_name' => $r->schedule_name,
                            'day'   => 'Friday',
                            'schedule_in' => $r->friday_in,
                            'schedule_out' => $r->friday_out,
                            'created_by' => $r->created_by
                        ];
                        $save = (new MasterScheduleDetail)->add($detail);
                    }

                    if ($r->saturday_in != '' && $r->saturday_out != '') {
                        $detail = [
                            'mst_schedule_code' => $r->mst_schedule_code,
                            'schedule_code' => $r->mst_schedule_code,
                            'schedule_name' => $r->schedule_name,
                            'day'   => 'Saturday',
                            'schedule_in' => $r->saturday_in,
                            'schedule_out' => $r->saturday_out,
                            'created_by' => $r->created_by
                        ];
                        $save = (new MasterScheduleDetail)->add($detail);
                    } else {
                        $detail = [
                            'mst_schedule_code' => $r->mst_schedule_code,
                            'schedule_code' => $r->mst_schedule_code,
                            'schedule_name' => $r->schedule_name,
                            'day'   => 'Saturday',
                            'schedule_in' => '00:00:00',
                            'schedule_out' => '00:00:00',
                            'created_by' => $r->created_by
                        ];
                        $save = (new MasterScheduleDetail)->add($detail);
                    }

                    if ($r->sunday_in != '' && $r->sunday_out != '') {
                        $detail = [
                            'mst_schedule_code' => $r->mst_schedule_code,
                            'schedule_code' => $r->mst_schedule_code,
                            'schedule_name' => $r->schedule_name,
                            'day'   => 'Saturday',
                            'schedule_in' => $r->sunday_in,
                            'schedule_out' => $r->sunday_out,
                            'created_by' => $r->created_by
                        ];
                        $save = (new MasterScheduleDetail)->add($detail);
                    } else {
                        $detail = [
                            'mst_schedule_code' => $r->mst_schedule_code,
                            'schedule_code' => $r->mst_schedule_code,
                            'schedule_name' => $r->schedule_name,
                            'day'   => 'Sunday',
                            'schedule_in' => '00:00:00',
                            'schedule_out' => '00:00:00',
                            'created_by' => $r->created_by
                        ];
                        $save = (new MasterScheduleDetail)->add($detail);
                    }
                } else {
                    foreach ($r->shift as $key => $value) {
                        $day = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
                        foreach ($day as $k => $v) {
                            $detail = [
                                'mst_schedule_code' => $r->mst_schedule_code,
                                'schedule_code' => $value['schedule_code'],
                                'schedule_name' => $value['schedule_name'],
                                'day'   => $v,
                                'schedule_in' => $value['schedule_in'],
                                'schedule_out' => $value['schedule_out'],
                                'created_by' => $r->created_by
                            ];
                            $save = (new MasterScheduleDetail)->add($detail);
                        }
                    }
                }
                // success return
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
            if (!($sql = (new MasterScheduleDetail)->edit($r->all(), $r->id))) {
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
        if (!($sql = (new MasterSchedule)->del($r->id))) {
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

        $count = MasterSchedule::count();

        if ($count > 0) {
            $kode = str_pad($kode + $count, 3, '0', STR_PAD_LEFT);
            return 'MSC' . $kode;
        } else {
            return 'MSC' . $kode;
        }
    }
}
