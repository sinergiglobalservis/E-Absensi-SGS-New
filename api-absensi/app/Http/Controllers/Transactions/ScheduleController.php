<?php

namespace App\Http\Controllers\Transactions;

// get master of controller
use App\Http\Controllers\Controller;

// get models
use App\Models\Transaction\{Schedule, SiteSchedule};
use App\Models\User\{User};
use App\Models\Master\{MasterCustomer, MasterSchedule, MasterScheduleDetail};

// get library of process
use Request;
use Exception;

use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ScheduleController extends Controller
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
            $data = Schedule::leftJoin('mst_schedule AS msc', 'msc.schedule_code', '=', 'schedule.schedule_code')
                ->leftJoin('users', 'users.id', '=', 'schedule.users_id')
                ->leftJoin('profiles', 'users.id', '=', 'profiles.users_id')
                ->select('profiles.profile_name', 'msc.schedule_name', 'msc.schedule_in', 'msc.schedule_out', 'msc.total_hour', 'msc.total_day', 'schedule.*')
                ->where('schedule.deleted_at', null)
                ->orderBy('schedule.id', 'desc')
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

    // get schedule by user id
    public function getScheduleByUserId(Request $r)
    {
        try {
            $data = SiteSchedule::selectRaw('site_schedule.schedule_name, site_schedule.schedule_code, site_schedule.schedule_in, site_schedule.schedule_out, site_schedule.day')
                ->from('site_schedule AS site_schedule')
                // ->leftJoin('mst_schedule AS msc', 'site_schedule.mst_schedule_code', '=', 'msc.mst_schedule_code')
                ->leftJoin('schedule AS sc', 'site_schedule.schedule_code', '=', 'sc.schedule_code')
                ->where('sc.users_id', $r->users_id)
                ->whereRaw("'" . $r->now . "' between schedule_start_date AND schedule_end_date")
                ->where('site_schedule.day', $r->day)
                ->first();

            if (count($data) == 0) {
                // shift
                $data = SiteSchedule::selectRaw('site_schedule.schedule_name, site_schedule.schedule_code, site_schedule.schedule_code, site_schedule.schedule_in, site_schedule.schedule_out, site_schedule.day')
                    ->from('site_schedule AS site_schedule')
                    // ->leftJoin('mst_schedule AS msc', 'site_schedule.mst_schedule_code', '=', 'msc.mst_schedule_code')
                    ->leftJoin('schedule AS sc', 'site_schedule.schedule_code', '=', 'sc.schedule_code')
                    ->where('sc.users_id', $r->users_id)
                    ->whereRaw("'" . $r->now . "' between schedule_start_date AND schedule_end_date")
                    ->first();
            }

            if (count($data) > 0) {
                $this->return = array_merge((array)$this->return, [
                    'status'    => true,
                    'data'      => $data,
                    'code'      => 200
                ]);
            } else {
                throw new Exception("Jadwal tidak ditemukan, silakan hubungi admin");
            }
        } catch (\Throwable $err) {
            $this->return = array_merge((array)$this->return, [
                'status'    => true,
                'message'   => $err->getMessage(),
                'code'      => 404
            ]);
        }

        return response()->json($this->return, $this->return['code']);
    }

    // listing all user with schedule
    public function getScheduleByCustomer($customer_code)
    {
        $data = Schedule::leftJoin('site_schedule AS site_schedule', 'site_schedule.schedule_code', '=', 'schedule.schedule_code')
            ->leftJoin('users', 'users.id', '=', 'schedule.users_id')
            ->leftJoin('profiles', 'users.id', '=', 'profiles.users_id')
            ->leftJoin('placement', 'placement.users_id', '=', 'users.id')
            ->leftJoin('mst_customer', 'mst_customer.customer_code', '=', 'placement.customer_code')
            ->select('users.id as users_id', 'schedule.id', 'profiles.profile_name', 'schedule.schedule_code', 'schedule.schedule_start_date', 'schedule.schedule_end_date')
            ->selectRaw('MIN(site_schedule.schedule_name) AS schedule_name, MIN(site_schedule.schedule_in) AS schedule_in, MIN(site_schedule.schedule_out) AS schedule_out')
            ->where('schedule.deleted_at', null)
            ->where('placement.customer_code', $customer_code)
            ->orderBy('schedule.schedule_start_date', 'desc')
            ->groupBy('schedule.id', 'users.id', 'profiles.id')
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
                'data'      => [],
                'message'   => 'Data empty',
                'code'      => 200
            ]);
        }

        return response()->json($this->return, $this->return['code']);
    }

    public function checkShift($schedule_code)
    {
        $data = MasterScheduleDetail::selectRaw('MIN(site_schedule.schedule_name) AS schedule_name, MIN(site_schedule.schedule_code) AS schedule_code')
            ->from('site_schedule AS site_schedule')
            ->where('site_schedule.mst_schedule_code', $schedule_code)
            ->groupBy('schedule_code')
            ->get();
        if (count($data) > 0) {
            $this->return = array_merge((array)$this->return, [
                'status'    => true,
                'data'      => $data,
                'code'      => 200
            ]);
        }

        return response()->json($this->return, $this->return['code']);
    }

    // assign schedule
    public function add(Request $r)
    {
        if ($r->shift != null) {
            $r->merge(['schedule_code' => $r->shift]);
        }
        if (!($id = (new Schedule)->add($r->all()))) {
            $this->return = array_merge((array)$this->return, [
                'status' => false,
                'message' => 'Gagal menambahkan jadwal',
                'code' => 401
            ]);
        } else {
            $this->return = array_merge((array)$this->return, [
                'data'  => $id,
            ]);
        }

        return response()->json($this->return, $this->return['code']);
    }

    // edit or assign schedule
    public function edit(Request $r)
    {
        if (!($update = (new Schedule)->edit([
            'schedule_code' => $r->schedule_code,
            'schedule_start_date' => $r->schedule_start_date,
            'schedule_end_date' => $r->schedule_end_date,
            'users_id' => $r->users_id,
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
            $user = User::selectRaw('id')->where('username', $val['users_id'])->first();
            // return $user;
            $data = [
                'users_id' => $user['id'],
                'schedule_code' => $val['schedule_code'],
                'schedule_start_date' => $val['schedule_start_date'],
                'schedule_end_date' => $val['schedule_end_date'],
                'created_by' => $val['created_by'],
            ];
            $add = (new Schedule)->add($data);
            // $add = (new Schedule)->updateOrCreate(
            //     ['users_id' => $data['users_id']],
            //     ['users_id' => $data['users_id'], 'schedule_code' => $data['schedule_code'], 'updated_by' => $data['created_by']]
            // );
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
    public function ExportExcel($data, $schedule, $mst_customer)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');
        try {
            $spreadSheet = new Spreadsheet();
            // workhsheet 1
            $spreadSheet->getActiveSheet()->setTitle("Jadwal");
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(14);
            $spreadSheet->getActiveSheet()->setCellValue('A1', 'users_id');
            $spreadSheet->getActiveSheet()->setCellValue('B1', 'name');
            $spreadSheet->getActiveSheet()->setCellValue('C1', 'schedule_code');
            $spreadSheet->getActiveSheet()->setCellValue('D1', 'nama_shift');
            $spreadSheet->getActiveSheet()->setCellValue('E1', 'schedule_start_date');
            $spreadSheet->getActiveSheet()->setCellValue('F1', 'schedule_end_date');

            $spreadSheet->getActiveSheet()->setCellValue('A2', 'wajib diisi dengan username user');
            $spreadSheet->getActiveSheet()->setCellValue('B2', 'tidak wajib diisi');
            $spreadSheet->getActiveSheet()->setCellValue('C2', 'Check Worksheet 2 (kolom schedule_code)');
            $spreadSheet->getActiveSheet()->setCellValue('D2', 'tidak wajib diisi');
            $spreadSheet->getActiveSheet()->setCellValue('E2', 'Format Tanggal : 2023-08-01');
            $spreadSheet->getActiveSheet()->setCellValue('F2', 'Format Tanggal : 2023-08-10');

            // $count = 2;
            // foreach ($data as $d) {
            //     $spreadSheet->getActiveSheet()->setCellValue('A' . $count, $d->users_id);
            //     $spreadSheet->getActiveSheet()->setCellValue('B' . $count, $d->profile_name);

            //     $count = $count + 1;
            // }


            // worksheet 2
            $sheet_2 = $spreadSheet->createSheet();
            $sheet_2->setTitle("Master Jadwal");

            // master jadwal pada customer tersebut
            $sheet_2->setCellValue('A1', 'Jadwal Kerja Perusahaan : ' . $mst_customer->customer_name);
            $sheet_2->setCellValue('A2', 'schedule_code');
            $sheet_2->setCellValue('B2', 'nama_shift');
            $sheet_2->setCellValue('C2', 'jam_masuk');
            $sheet_2->setCellValue('D2', 'jam_pulang');

            $scount = 3;
            foreach ($schedule as $sc) {
                $sheet_2->setCellValue('A' . $scount, $sc->schedule_code);
                $sheet_2->setCellValue('B' . $scount, $sc->schedule_name);
                $sheet_2->setCellValue('C' . $scount, $sc->schedule_in);
                $sheet_2->setCellValue('D' . $scount, $sc->schedule_out);

                $scount = $scount + 1;
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
            $path = base_path('public/excel/format/format_upload_schedule.xls');
            $Excel_writer->save($path);
            // exit();
        } catch (Exception $e) {
            return;
        }
    }

    public function exportData(Request $r)
    {
        $mst_customer = MasterCustomer::where('customer_code', $r->customer_code)->first();
        // get data master jam kerja by id
        $siteSchedule = SiteSchedule::where('customer_code', $r->customer_code)->get();

        $data = Schedule::rightJoin('users', 'users.id', '=', 'schedule.users_id')
            ->leftJoin('site_schedule AS site_schedule', 'site_schedule.schedule_code', '=', 'schedule.schedule_code')
            ->leftJoin('profiles', 'users.id', '=', 'profiles.users_id')
            ->leftJoin('placement', 'placement.users_id', '=', 'users.id')
            ->leftJoin('mst_customer', 'mst_customer.customer_code', '=', 'placement.customer_code')
            ->select('users.id as users_id', 'profiles.profile_name')
            ->selectRaw('MIN(site_schedule.schedule_name) AS schedule_name,MIN(site_schedule.schedule_in) AS schedule_in,MIN(site_schedule.schedule_out) AS schedule_out, MIN(schedule.schedule_code) AS schedule_code')
            ->where('schedule.deleted_at', null)
            ->where('placement.customer_code', $r->customer_code)
            ->orderBy('schedule.users_id', 'desc')
            ->groupBy('users.id', 'profiles.id', 'schedule.users_id')
            ->get();
        // export excel
        $this->ExportExcel($data, $siteSchedule, $mst_customer);
        // setup filename
        $this->return = array_merge((array)$this->return, [
            'data' => url('excel/format/format_upload_schedule.xls')
        ]);

        return response()->json($this->return, $this->return['code']);
    }
}
