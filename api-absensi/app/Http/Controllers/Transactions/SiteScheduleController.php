<?php

namespace App\Http\Controllers\Transactions;

// get master of controller
use App\Http\Controllers\Controller;

// get models
use App\Models\Transaction\{SiteSchedule};
use App\Models\Master\{MasterCustomer, MasterSchedule, MasterScheduleDetail};

// get library of process
use Request;
use Exception;

use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SiteScheduleController extends Controller
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
            // show all data schedule / schedule
            $data = SiteSchedule::leftJoin('mst_customer', 'mst_customer.customer_code', '=', 'site_schedule.customer_code')
                ->select('mst_customer.customer_name', 'site_schedule.schedule_name', 'site_schedule.schedule_in', 'site_schedule.schedule_out')
                ->where('site_schedule.deleted_at', null)
                ->orderBy('site_schedule.id', 'desc')
                ->get();

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

    // listing all user with schedule
    public function getScheduleByCustomer($customer_code)
    {
        $data = SiteSchedule::leftJoin('mst_customer', 'mst_customer.customer_code', '=', 'site_schedule.customer_code')
            ->select('site_schedule.id', 'mst_customer.customer_name', 'site_schedule.schedule_name', 'site_schedule.schedule_code', 'site_schedule.schedule_in', 'site_schedule.schedule_out', 'site_schedule.day', 'site_schedule.weekday')
            ->where('site_schedule.deleted_at', null)
            ->where('site_schedule.customer_code', $customer_code)
            ->orderBy('site_schedule.id', 'desc')
            // ->groupBy('site_schedule.schedule_name', 'mst_customer.id')
            ->get();
        if (count($data) > 0) {
            $this->return = array_merge((array)$this->return, [
                'status'    => true,
                'data'      => $data,
                'code'      => 200
            ]);
        } else {
            $this->return = array_merge((array)$this->return, [
                'status'    => true,
                'message'   => 'empty',
                'data'      => [],
                'code'      => 200
            ]);
        }

        return response()->json($this->return, $this->return['code']);
    }

    public function getByScheduleCode($schedule_code)
    {
        try {
            $data = SiteSchedule::where('schedule_code', $schedule_code)->orderBy('id')->get();

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

    // get schedule group by schedule code
    public function groupScheduleByCustomerCode($customer_code)
    {
        try {
            $data = SiteSchedule::selectRaw('MIN(id) AS id, MIN(schedule_code) AS schedule_code, MIN(schedule_name) AS schedule_name')
                ->where('customer_code', $customer_code)
                ->groupBy('schedule_code')
                ->orderBy('schedule_code')
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
        // try {
        foreach ($r->schedule as $key => $value) {
            foreach ($value['scheduleDaily'] as $k => $v) {
                $data = [
                    'customer_code' => $r->customer_code,
                    'schedule_code' => $value['schedule_code'],
                    'schedule_name' => $value['schedule_name'],
                    'schedule_in' => $v['schedule_in'],
                    'schedule_out' => $v['schedule_out'],
                    'day'   => $v['day'],
                    'weekday'   => $v['weekday'],
                    'created_by' => $r->created_by
                ];
                $save = (new SiteSchedule)->add($data);
            }
        }
        // success return
        $this->return = array_merge((array)$this->return, [
            'data' => $save
        ]);
        // } catch (\Throwable $err) {
        //     $this->return = array_merge((array)$this->return, [
        //         'status' => false,
        //         'message' => $err->getMessage(),
        //         'code' => 401
        //     ]);
        // }

        return response()->json($this->return, $this->return['code']);
    }

    // edit or assign schedule
    public function edit(Request $r)
    {
        if (!($update = (new SiteSchedule)->edit([
            'schedule_code' => $r->schedule_code,
            'schedule_name' => $r->schedule_name,
            'schedule_in' => $r->schedule_in,
            'schedule_out' => $r->schedule_out,
            'day' => $r->day,
            'weekday' => $r->weekday,
            'updated_by' => $r->updated_by
        ], $r->id))) {
            $this->return = array_merge((array)$this->return, [
                'status' => false,
                'message' => 'Gagal merubah jadwal',
                'code' => 401
            ]);
        } else {
            $this->return = array_merge((array)$this->return, [
                'data' => $update->id
            ]);
        }
        return response()->json($this->return, $this->return['code']);
    }

    // upload schedule with insert multiple from excel
    public function uploadSchedule(Request $r)
    {
        unset($r['appAccess']);
        foreach ($r->post() as $key => $val) {
            $data = [
                'schedule_code' => $r->schedule_code,
                'schedule_name' => $r->schedule_name,
                'schedule_in' => $r->schedule_in,
                'schedule_out' => $r->schedule_out,
                'day' => $r->day,
                'weekday' => $r->weekday,
                'updated_by' => $r->updated_by
            ];
            // $add = (new SiteSchedule)->add($data);
            $add = (new SiteSchedule)->updateOrCreate(
                ['users_id' => $data['users_id']],
                ['users_id' => $data['users_id'], 'schedule_code' => $data['schedule_code'], 'updated_by' => $data['created_by']]
            );
        }
        if ($add) {
            return response()->json([
                'status' => true,
                'message' => 'Berhasil',
                'data'  => [],
                'code' => 200
            ]);
        } else {
            $this->return = array_merge((array)$this->return, [
                'status' => false,
                'message' => 'Gagal menambahkan jadwal',
                'code' => 401
            ]);
        }
    }

    // download format excel for upload schedule via file excel
    public function ExportExcel($data)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');
        try {
            $spreadSheet = new Spreadsheet();
            // workhsheet 1
            $spreadSheet->getActiveSheet()->setTitle("Jadwal");
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(14);
            $spreadSheet->getActiveSheet()->setCellValue('A1', 'schedule_code');
            $spreadSheet->getActiveSheet()->setCellValue('B1', 'schedule_name');
            $spreadSheet->getActiveSheet()->setCellValue('C1', 'schedule_in');
            $spreadSheet->getActiveSheet()->setCellValue('D1', 'schedule_out');
            $spreadSheet->getActiveSheet()->setCellValue('E1', 'day');
            $count = 2;
            foreach ($data as $d) {
                $spreadSheet->getActiveSheet()->setCellValue('A' . $count, $d->schedule_code);
                $spreadSheet->getActiveSheet()->setCellValue('B' . $count, $d->schedule_name);
                $spreadSheet->getActiveSheet()->setCellValue('C' . $count, $d->schedule_in);
                $spreadSheet->getActiveSheet()->setCellValue('D' . $count, $d->schedule_out);
                $spreadSheet->getActiveSheet()->setCellValue('E' . $count, $d->day);

                $count = $count + 1;
            }

            // check folder is exists
            if (!File::isDirectory(base_path('public/excel'))) {
                // if not exists then create folder
                File::makeDirectory(base_path('public/excel'));
            }

            if (!File::isDirectory(base_path('public/excel/format'))) {
                File::makeDirectory(base_path('public/excel/format'));
            }

            $Excel_writer = new Xls($spreadSheet);
            $path = base_path('public/excel/format/format_upload_jadwal_customer.xls');
            $Excel_writer->save($path);
            // exit();
        } catch (Exception $e) {
            return;
        }
    }

    public function exportData(Request $r)
    {
        $data = SiteSchedule::where('customer_code', $r->customer_code)->get();
        // export excel
        $this->ExportExcel($data);
        // setup filename
        $this->return = array_merge((array)$this->return, [
            'data' => url('excel/format/format_upload_jadwal_customer.xls')
        ]);

        return response()->json($this->return, $this->return['code']);
    }
}
