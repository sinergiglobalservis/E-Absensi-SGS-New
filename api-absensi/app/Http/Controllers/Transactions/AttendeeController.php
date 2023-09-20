<?php

namespace App\Http\Controllers\Transactions;

// get master of controller
use App\Http\Controllers\Controller;

// get models
use App\Models\Master\{MasterCustomer};
use App\Models\Transaction\{Attendee, Absence, Holiday, Schedule, Placement};
use App\Models\User\{User};

// get library of process
use Request;
use Exception;

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Illuminate\Support\Facades\File;

class AttendeeController extends Controller
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
            // get data attendee join users
            $data = Attendee::selectRaw('attendance.id, attendee_date, attendance.users_id, attendee_time_in, attendee_latitude_in, attendee_longitude_in, images_in AS img_in, attendee_time_out, attendee_latitude_out, attendee_longitude_out, images_out AS img_out, MIN(site_schedule.schedule_name) AS schedule_name, MIN(site_schedule.schedule_in) AS schedule_in, MIN(site_schedule.schedule_out) AS schedule_out, MIN(profiles.profile_name) AS profile_name, MIN(profiles.nik) AS nik, (MIN(attendance.attendee_time_in) - MIN(site_schedule.schedule_in)) AS late_duration')
                ->leftJoin('profiles', 'profiles.users_id', '=', 'attendance.users_id')
                ->leftJoin('site_schedule', 'site_schedule.schedule_code', '=', 'attendance.workhour_code')
                ->orderBy('attendance.attendee_date', 'desc')
                ->groupBy('attendance.id')
                ->get();
            // return $data;

            // get images in
            foreach ($data as $key => $value) {
                $value->img_in = url('attendee/' . $value->img_in);
            }
            // get images out
            foreach ($data as $key => $value) {
                $value->img_out = url('attendee/' . $value->img_out);
            }

            foreach ($data as $key => $value) {
                $value->attendee_date = date('d-m-Y', strtotime($value->attendee_date));
            }
            // get late duration
            foreach ($data as $key => $value) {
                if ($value->attendee_time_in > $value->schedule_in) {
                    $value->late_duration = str_replace('-', '', $value->late_duration);
                } else {
                    $value->late_duration = '';
                }
            }

            foreach ($data as $key => $value) {
                $value['images'] = url('attendee/' . $value['images']);
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
                'status'    => true,
                'message'   => $err->getMessage(),
                'code'      => 200
            ]);
        }
        return response()->json($this->return, $this->return['code']);
    }

    public function add(Request $r)
    {

        $isAttend = Attendee::select('attendance.id', 'attendance.attendee_time_out')
            // ->where('attendee_date', $r->attendee_date)
            ->where('attendee_date', date('Y-m-d'))
            ->where('users_id', $r->users_id)
            ->orderBy('id', 'desc')
            ->first();

        if (!$isAttend || $isAttend->attendee_time_out != null) {
            $data = [
                'attendee_date' => $r->attendee_date,
                'attendee_time_in' => $r->attendee_time_in,
                'attendee_latitude_in' => $r->attendee_latitude_in,
                'attendee_longitude_in' => $r->attendee_longitude_in,
                'images_in' => $r->images_in,
                'workhour_code' => $r->workhour_code,
                'absence_ref_desc' => $r->absence_ref_desc,
                'absence_ref' => $r->absence_ref,
                'users_id' => $r->users_id,
                'created_by' => $r->created_by,
                'day' => $r->day
            ];
            if (!($id = (new Attendee)->add($data))) {
                $this->return = array_merge((array)$this->return, [
                    'status' => false,
                    'message' => 'Gagal menyimpan data',
                    'code' => 401
                ]);
            } else {
                $this->return = array_merge((array)$this->return, [
                    'data' => $id
                ]);
            }
        } else {
            $data = [
                'attendee_time_out' => $r->attendee_time_out,
                'attendee_latitude_out' => $r->attendee_latitude_out,
                'attendee_longitude_out' => $r->attendee_longitude_out,
                'images_out' => $r->images_out,
                'updated_by' => $r->updated_by
            ];

            if (!($id = (new Attendee)->edit($data, $isAttend->id))) {
                $this->return = array_merge((array)$this->return, [
                    'status' => false,
                    'message' => 'Gagal mengubah data',
                    'code' => 401
                ]);
            } else {
                $this->return = array_merge((array)$this->return, [
                    'data' => $id->id
                ]);
            }
        }

        return response()->json($this->return, $this->return['code']);
    }

    public function getById($id)
    {
        try {
            // get data attendee by id
            $data = Attendee::find($id);
            $data['images'] = url('attendee/' . $data['images']);

            if (count($data) > 0) {
                $this->return = array_merge((array)$this->return, [
                    'data' => $data
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

    public function getByUsersId($users_id)
    {
        try {
            // get data today attendee by users id
            $data = Attendee::select('profiles.profile_name', 'attendance.attendee_time_in', 'attendance.attendee_latitude_in', 'attendance.attendee_longitude_in', 'attendance.attendee_time_out', 'attendance.attendee_latitude_out', 'attendance.attendee_longitude_out', 'attendance.attendee_date')
                ->join('profiles', 'profiles.users_id', '=', 'attendance.users_id')
                ->where('attendance.attendee_date', date('Y-m-d'))
                ->where('attendance.users_id', $users_id)
                ->orderBy('attendee_time_in', 'desc')
                ->get();
            if (count($data) == 0) {
                $data = Absence::select('profiles.profile_name', 'mst_absence_type.absence_name', 'absence.*')
                    ->join('profiles', 'profiles.users_id', '=', 'absence.users_id')
                    ->join('mst_absence_type', 'mst_absence_type.absence_code', '=', 'absence.absence_code')
                    ->where('absence.absence_date', date('Y-m-d'))
                    ->where('approval_1', 1)
                    ->where('absence.users_id', $users_id)
                    ->orderBy('absence.id', 'desc')
                    ->get();
            }

            foreach ($data as $key => $value) {
                $value['images'] = url('attendee/' . $value['images']);
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
                'status'    => true,
                'message'   => $err->getMessage(),
                'code'      => 200
            ]);
        }
        return response()->json($this->return, $this->return['code']);
    }

    public function getAllByUserId($users_id)
    {
        try {
            // get data attendee by id
            $data = Attendee::leftJoin('users', 'users.id', '=', 'attendee.users_id')
                ->leftjoin('placement', 'placement.users_id', '=', 'users.id')
                ->leftjoin('mst_customer', 'mst_customer.customer_code', '=', 'placement.customer_code')
                ->leftjoin('profiles', 'profiles.users_id', '=', 'users.id')
                ->leftJoin('mst_workhour', 'mst_workhour.workhour_code', '=', 'attendee.workhour_code')
                ->select('profiles.nik', 'profiles.profile_name', 'customer_name', 'mst_workhour.workhour_name', 'mst_workhour.workhour_in', 'mst_workhour.workhour_out', 'attendee.*')
                ->selectRaw("TIMESTAMPDIFF(SECOND, attendee.attendee_time, CASE WHEN attendee_type = 'IN' THEN mst_workhour.workhour_in ELSE mst_workhour.workhour_out END) AS duration")
                ->where('attendee.users_id', $users_id)
                ->where('attendee.deleted_at', null)
                ->get();

            foreach ($data as $key => $value) {
                $value->duration = $value->duration / 60;
                $value->duration = floor($value->duration);
                // jika masuk lebih awal
                if ($value->attendee_type == 'IN') {
                    if ($value->duration > 0) {
                        $value->duration = '';
                    }
                }
                // jika pulang lebih cepat
                if ($value->attendee_type == 'OUT') {
                    if ($value->duration < 0) {
                        $value->duration = '';
                    } else {
                        $value->duration = -1 * abs($value->duration);
                    }
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
                'status'    => true,
                'message'   => $err->getMessage(),
                'code'      => 200
            ]);
        }
        return response()->json($this->return, $this->return['code']);
    }

    // get by tanggal
    public function getRekapByDate(Request $r)
    {
        try {
            if ($r->users_id != '') {
                $data = Attendee::selectRaw('attendance.id, attendee_date, attendance.users_id, attendee_time_in, attendee_latitude_in, attendee_longitude_in, images_in AS img_in, attendee_time_out, attendee_latitude_out, attendee_longitude_out, images_out AS img_out, MIN(site_schedule.schedule_name) AS schedule_name, site_schedule.schedule_in, site_schedule.schedule_out, MIN(profiles.profile_name) AS profile_name, MIN(profiles.nik) AS nik, MIN(attendance.attendee_time_in - site_schedule.schedule_in) AS late_duration')
                    ->leftJoin('profiles', 'profiles.users_id', '=', 'attendance.users_id')
                    ->leftJoin('site_schedule', 'site_schedule.schedule_code', '=', 'attendance.workhour_code')
                    ->whereBetween('attendee_date', [$r->from, $r->to])
                    ->where('attendance.users_id', $r->users_id)
                    ->whereRaw('site_schedule.schedule_code = attendance.workhour_code')
                    ->whereRaw('site_schedule.day = attendance.day')
                    ->where('site_schedule.customer_code', $r->customer)
                    ->groupBy('attendance.id', 'site_schedule.schedule_in', 'site_schedule.schedule_out')
                    // ->union($off)
                    ->orderBy('attendance.attendee_date', 'desc')
                    ->get();
            } else {
                $data = Attendee::selectRaw('attendance.id, attendee_date, attendance.users_id, attendee_time_in, attendee_latitude_in, attendee_longitude_in, images_in AS img_in, attendee_time_out, attendee_latitude_out, attendee_longitude_out, images_out AS img_out, MIN(site_schedule.schedule_name) AS schedule_name, site_schedule.schedule_in, site_schedule.schedule_out, MIN(profiles.profile_name) AS profile_name, MIN(profiles.nik) AS nik, MIN(attendance.attendee_time_in - site_schedule.schedule_in) AS late_duration')
                    ->leftJoin('profiles', 'profiles.users_id', '=', 'attendance.users_id')
                    ->leftJoin('site_schedule', 'site_schedule.schedule_code', '=', 'attendance.workhour_code')
                    ->leftJoin('placement', 'placement.users_id', '=', 'attendance.users_id')
                    ->leftJoin('mst_customer', 'mst_customer.customer_code', '=', 'placement.customer_code')
                    ->whereBetween('attendee_date', [$r->from, $r->to])
                    ->where('mst_customer.customer_code', $r->customer)
                    ->where('site_schedule.customer_code', $r->customer)
                    ->whereRaw('site_schedule.schedule_code = attendance.workhour_code')
                    ->whereRaw('site_schedule.day = attendance.day')
                    // ->orderBy('attendance.users_id', 'asc')
                    ->orderBy('attendance.attendee_date', 'desc')
                    ->groupBy('attendance.id', 'site_schedule.schedule_in', 'site_schedule.schedule_out')
                    ->get();
            }
            // return $data;

            // get images in
            foreach ($data as $key => $value) {
                $value->img_in = url('attendee/' . $value->img_in);
            }
            // get images out
            foreach ($data as $key => $value) {
                $value->img_out = url('attendee/' . $value->img_out);
            }

            foreach ($data as $key => $value) {
                $value->attendee_date = date('d-m-Y', strtotime($value->attendee_date));
            }
            // get late duration
            foreach ($data as $key => $value) {
                if ($value->attendee_time_in > $value->schedule_in) {
                    $value->late_duration = str_replace('-', '', $value->late_duration);
                } else {
                    $value->late_duration = '';
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
                'status'    => true,
                'message'   => $err->getMessage(),
                'code'      => 200
            ]);
        }
        return response()->json($this->return, $this->return['code']);
    }

    // get by periode
    public function getRekapByPeriode(Request $r)
    {

        try {
            $absence = Absence::selectRaw(
                "absence.id,
                absence_detail.absence_date,
                absence.users_id,
                '00:00:00',
                absence.absence_latitude,
                absence.absence_longitude,
                '00:00:00',
                absence.absence_description,
                '',
                MIN(mst_absence_type.absence_name),
                '00:00:00',
                MIN(profiles.profile_name) AS profile_name,
                MIN(profiles.nik) AS nik,
                '00:00:00',
                MIN (mst_absence_type.absence_name) AS absence_name,
                null,
                MIN(approval_1) AS approval_1"
            )
                ->leftJoin('profiles', 'profiles.users_id', '=', 'absence.users_id')
                ->leftJoin('mst_absence_type', 'mst_absence_type.absence_code', '=', 'absence.absence_code')
                ->leftJoin('absence_detail', 'absence_detail.absence_id', '=', 'absence.id')
                ->whereBetween('absence_detail.absence_date', [$r->start, $r->end])
                ->where('absence.users_id', $r->users_id)
                ->where('absence.absence_code', '!=', 'MTA003')
                ->groupBy('absence.id', 'absence_detail.id');

            $holiday = Holiday::selectRaw(
                "holiday.id,
                holiday.holiday_date,
                null, 
                '00:00:00', 
                '', 
                '', 
                '00:00:00', 
                '', 
                '', 
                holiday.holiday_name, 
                '00:00:00', 
                '', 
                '',
                '00:00:00',
                holiday_name,
                null,
                null"
            )
                ->whereBetween('holiday.holiday_date', [$r->start, $r->end]);

            $attendee = Attendee::selectRaw(
                "attendance.id,
                attendee_date,
                attendance.users_id,
                attendee_time_in,
                attendee_latitude_in,
                attendee_longitude_in,
                attendee_time_out,
                attendee_latitude_out,
                attendee_longitude_out,
                MIN(mst_workhour.workhour_name) AS workhour_name,
                MIN(mst_workhour.workhour_in) AS workhour_in,
                MIN(profiles.profile_name) AS profile_name,
                MIN(profiles.nik) AS nik,
                MIN(attendance.attendee_time_in - mst_workhour.workhour_in) AS late_duration,
                '' AS deskripsi,
                absence_ref AS absence_ref,
                null AS approval_1"
            )
                ->leftJoin('profiles', 'profiles.users_id', '=', 'attendance.users_id')
                ->leftJoin('workhour', 'workhour.users_id', '=', 'attendance.users_id')
                ->leftJoin('mst_workhour', 'mst_workhour.workhour_code', '=', 'workhour.workhour_code')
                ->whereBetween('attendee_date', [$r->start, $r->end])
                ->where('attendance.users_id', $r->users_id)
                ->groupBy('attendance.id')
                ->union($absence)
                ->union($holiday)
                ->orderBy('attendee_date', 'attendance.id')
                ->get();

            $data = $attendee;

            // get late duration
            foreach ($data as $key => $value) {
                if ($value->attendee_time_in > $value->workhour_in) {
                    $value->late_duration = $value->late_duration;
                } else {
                    $value->late_duration = '';
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
                'status'    => true,
                'message'   => $err->getMessage(),
                'code'      => 200
            ]);
        }
        return response()->json($this->return, $this->return['code']);
    }

    public function getScheduleOut(Request $r)
    {
        try {
            $data = Attendee::select('attendee.*')
                ->where('users_id', $r->id)
                ->where('deleted_at', null)
                ->orderBy('id', 'desc')
                ->first();

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

    public function upload(Request $r)
    {
        try {
            if (!File::isDirectory(base_path('public/attendee'))) {
                //MAKA FOLDER TERSEBUT AKAN DIBUAT
                File::makeDirectory(base_path('public/attendee'));
            }

            // convert base64 to image
            if (!file_put_contents(base_path('public/attendee/' . $r->name), base64_decode($r->img))) {
                throw new Exception("Gagal Menyimpan Data");
            } else {
                $this->return = array_merge((array)$this->return, [
                    'data' => ''
                ]);
            }
        } catch (\Throwable $err) {
            $this->return = array_merge((array)$this->return, [
                'status'    => false,
                'message'   => $err->getMessage(),
                'code'      => 401
            ]);
        }
        return response()->json($this->return, $this->return['code']);
    }

    public function ExportExcel($user, $customer_data, $r)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');
        try {
            $spreadSheet = new Spreadsheet();
            if ($r->has('users_id')) {
                // if get by users id
                $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
                $spreadSheet->getActiveSheet()->setCellValue('A1', 'NIK');
                $spreadSheet->getActiveSheet()->setCellValue('A2', 'Nama');
                $spreadSheet->getActiveSheet()->setCellValue('A3', 'Perusahaan');
                $spreadSheet->getActiveSheet()->setCellValue('B1', ': ' . $user->nik);
                $spreadSheet->getActiveSheet()->setCellValue('B2', ': ' . $user->profile_name);
                $spreadSheet->getActiveSheet()->setCellValue('B3', ': ' . $user->customer_name);

                $spreadSheet->getActiveSheet()->setCellValue('A5', 'Hari');
                $spreadSheet->getActiveSheet()->setCellValue('B5', 'Tanggal');
                $spreadSheet->getActiveSheet()->setCellValue('C5', 'Jadwal Masuk');
                $spreadSheet->getActiveSheet()->setCellValue('D5', 'Absensi Masuk');
                $spreadSheet->getActiveSheet()->setCellValue('E5', 'Absensi Pulang');
                $spreadSheet->getActiveSheet()->setCellValue('F5', 'Terlambat');
                $spreadSheet->getActiveSheet()->setCellValue('G5', 'Pulang Cepat');
                $spreadSheet->getActiveSheet()->setCellValue('H5', 'Lembur');
                $spreadSheet->getActiveSheet()->setCellValue('I5', 'Multiplikasi');
                $spreadSheet->getActiveSheet()->setCellValue('J5', 'Jam Efektif');
                $spreadSheet->getActiveSheet()->setCellValue('K5', 'Alasan');
                $spreadSheet->getActiveSheet()->setCellValue('L5', 'Keterangan');

                $count = 6;
                foreach ($customer_data as $data) {
                    $spreadSheet->getActiveSheet()->setCellValue('A' . $count, $data->day);
                    $spreadSheet->getActiveSheet()->setCellValue('B' . $count, $data->attendee_date);
                    $spreadSheet->getActiveSheet()->setCellValue('C' . $count, $data->schedule_in . ' - ' . $data->schedule_out);
                    $spreadSheet->getActiveSheet()->setCellValue('D' . $count, $data->attendee_time_in);
                    $spreadSheet->getActiveSheet()->setCellValue('E' . $count, $data->attendee_time_out);
                    $spreadSheet->getActiveSheet()->setCellValue('F' . $count, $data->late_duration);
                    $spreadSheet->getActiveSheet()->setCellValue('G' . $count, $data->pulang_cepat);
                    $spreadSheet->getActiveSheet()->setCellValue('H' . $count, $data->lembur);
                    $spreadSheet->getActiveSheet()->setCellValue('I' . $count, $data->multiplikasi);
                    $spreadSheet->getActiveSheet()->setCellValue('J' . $count, $data->jam_efektif);
                    $spreadSheet->getActiveSheet()->setCellValue('K' . $count, $data->alasan);
                    $spreadSheet->getActiveSheet()->setCellValue('L' . $count, $data->keterangan);

                    $count = $count + 1;
                }
            } else {
                // if get all by customer
                $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
                $spreadSheet->getActiveSheet()->setCellValue('A1', 'Perusahaan ');
                $spreadSheet->getActiveSheet()->setCellValue('B1', ': ' . $user->customer_name);

                $spreadSheet->getActiveSheet()->setCellValue('A5', 'NIP');
                $spreadSheet->getActiveSheet()->setCellValue('B5', 'Nama');
                $spreadSheet->getActiveSheet()->setCellValue('C5', 'Hari');
                $spreadSheet->getActiveSheet()->setCellValue('D5', 'Tanggal');
                $spreadSheet->getActiveSheet()->setCellValue('E5', 'Jadwal Masuk');
                $spreadSheet->getActiveSheet()->setCellValue('F5', 'Absensi Masuk');
                $spreadSheet->getActiveSheet()->setCellValue('G5', 'Absensi Pulang');
                $spreadSheet->getActiveSheet()->setCellValue('H5', 'Terlambat');
                $spreadSheet->getActiveSheet()->setCellValue('I5', 'Pulang Cepat');
                $spreadSheet->getActiveSheet()->setCellValue('J5', 'Lembur');
                $spreadSheet->getActiveSheet()->setCellValue('K5', 'Multiplikasi');
                $spreadSheet->getActiveSheet()->setCellValue('L5', 'Jam Efektif');
                $spreadSheet->getActiveSheet()->setCellValue('M5', 'Alasan');
                $spreadSheet->getActiveSheet()->setCellValue('N5', 'Keterangan');

                $count = 6;
                foreach ($customer_data as $data) {
                    $spreadSheet->getActiveSheet()->setCellValue('A' . $count, $data->nik);
                    $spreadSheet->getActiveSheet()->setCellValue('B' . $count, $data->profile_name);
                    $spreadSheet->getActiveSheet()->setCellValue('C' . $count, $data->day);
                    $spreadSheet->getActiveSheet()->setCellValue('D' . $count, $data->attendee_date);
                    $spreadSheet->getActiveSheet()->setCellValue('E' . $count, $data->schedule_in . ' - ' . $data->schedule_out);
                    $spreadSheet->getActiveSheet()->setCellValue('F' . $count, $data->attendee_time_in);
                    $spreadSheet->getActiveSheet()->setCellValue('G' . $count, $data->attendee_time_out);
                    $spreadSheet->getActiveSheet()->setCellValue('H' . $count, $data->late_duration);
                    $spreadSheet->getActiveSheet()->setCellValue('I' . $count, $data->pulang_cepat);
                    $spreadSheet->getActiveSheet()->setCellValue('J' . $count, $data->lembur);
                    $spreadSheet->getActiveSheet()->setCellValue('K' . $count, $data->multiplikasi);
                    $spreadSheet->getActiveSheet()->setCellValue('L' . $count, $data->jam_efektif);
                    $spreadSheet->getActiveSheet()->setCellValue('M' . $count, $data->alasan);
                    $spreadSheet->getActiveSheet()->setCellValue('N' . $count, $data->keterangan);

                    $count = $count + 1;
                }
            }


            // check folder is exists
            if (!File::isDirectory(base_path('public/excel'))) {
                // if not exists then create folder
                File::makeDirectory(base_path('public/excel'));
            }

            if (!File::isDirectory(base_path('public/excel/kehadiran'))) {
                File::makeDirectory(base_path('public/excel/kehadiran'));
            }

            $Excel_writer = new Xls($spreadSheet);
            $path = base_path('public/excel/kehadiran/rp_kehadiran_') . str_replace(' ', '_', ($r->has('users_id') ? $user->profile_name : $user->customer_name)) . '_tgl_' . $r->from . '_sd_' . $r->to . '.xls';
            $Excel_writer->save($path);
            // exit();
        } catch (Exception $e) {
            return;
        }
    }

    // export daftar kehadiran
    public function exportData(Request $r)
    {
        if (!$r->has('users_id')) {
            $absence = Absence::selectRaw(
                "to_char(absence_detail.absence_date, 'Day'),
            absence_detail.absence_date,
            absence.users_id,
            '00:00:00',
            absence.absence_latitude,
            absence.absence_longitude,
            '00:00:00',
            absence.absence_description,
            '',
            '',
            '00:00:00',
            '00:00:00',
            MIN(profiles.profile_name) AS profile_name,
            MIN(profiles.nik) AS nik,
            '00:00:00',
            '00:00:00',
            '00:00:00',
            '00:00:00',
            '00:00:00',
            MIN(mst_absence_type.absence_name),
            ''"
            )
                ->leftJoin('profiles', 'profiles.users_id', '=', 'absence.users_id')
                ->leftJoin('mst_absence_type', 'mst_absence_type.absence_code', '=', 'absence.absence_code')
                ->leftJoin('absence_detail', 'absence_detail.absence_id', '=', 'absence.id')
                ->leftJoin('placement', 'placement.users_id', '=', 'absence.users_id')
                ->leftJoin('mst_customer', 'mst_customer.customer_code', '=', 'placement.customer_code')
                ->whereBetween('absence_detail.absence_date', [$r->from, $r->to])
                // ->where('absence.users_id', $value->users_id)
                ->where('mst_customer.customer_code', $r->customer)
                ->groupBy('absence.id', 'absence_detail.id');

            // $holiday = Holiday::selectRaw(
            //     "to_char(holiday.holiday_date, 'Day'),
            // holiday.holiday_date, 
            // null, 
            // '00:00:00', 
            // '', 
            // '', 
            // '00:00:00', 
            // '', 
            // '', 
            // holiday.holiday_name, 
            // '00:00:00', 
            // '00:00:00', 
            // '', 
            // '', 
            // '00:00:00', 
            // '00:00:00', 
            // '00:00:00', 
            // '00:00:00', 
            // '00:00:00', 
            // holiday.holiday_name,
            // ''"
            // )
            //     ->whereBetween('holiday.holiday_date', [$r->from, $r->to]);

            $attendee = Attendee::selectRaw(
                "to_char(attendee_date, 'Day') AS day,
            attendee_date,
            attendance.users_id,
            attendee_time_in,
            attendee_latitude_in,
            attendee_longitude_in,
            attendee_time_out,
            attendee_latitude_out,
            attendee_longitude_out,
            MIN(site_schedule.schedule_name) AS schedule_name,
            MIN(site_schedule.schedule_in) AS schedule_in,
            MIN(site_schedule.schedule_out) AS schedule_out,
            MIN(profiles.profile_name) AS profile_name,
            MIN(profiles.nik) AS nik,
            MIN(attendance.attendee_time_in - site_schedule.schedule_in) AS late_duration,
            MIN(attendance.attendee_time_out - attendance.attendee_time_in) AS jam_efektif,
            MIN(attendance.attendee_time_out - site_schedule.schedule_out) AS lembur,
            MIN( site_schedule.schedule_out - attendance.attendee_time_out) AS pulang_cepat,
            '00:00:00' AS multiplikasi,
            null AS keterangan,
            absence_ref_desc AS alasan"
            )
                ->leftJoin('profiles', 'profiles.users_id', '=', 'attendance.users_id')
                ->leftJoin('placement', 'placement.users_id', '=', 'attendance.users_id')
                ->leftJoin('mst_customer', 'mst_customer.customer_code', 'placement.customer_code')
                ->leftJoin('site_schedule', 'site_schedule.schedule_code', '=', 'attendance.workhour_code')
                ->whereBetween('attendee_date', [$r->from, $r->to])
                ->where('mst_customer.customer_code', $r->customer)
                ->where('site_schedule.customer_code', $r->customer)
                ->whereRaw("site_schedule.day = attendance.day")
                ->union($absence)
                // ->union($holiday)
                ->groupBy('attendance.id')
                ->orderBy('users_id', 'asc')
                ->orderBy('attendee_date', 'desc')
                ->get();

            $data = $attendee;

            // get late duration
            foreach ($data as $key => $value) {
                $value->day = str_replace(' ', '', $value->day);
                if ($value->attendee_time_in > $value->schedule_in) {
                    $value->late_duration = $value->late_duration;
                } else {
                    $value->late_duration = '';
                }
                if ($value->attendee_time_out == null || $value->attendee_time_in == null) {
                    $value->alasan = 'M';
                }
                if ($value->day == 'Saturday' || $value->day == 'Sunday') {
                    $value->keterangan = 'OFF';
                }
            }
        } else {
            $absence = Absence::selectRaw(
                "to_char(absence_detail.absence_date, 'Day'),
            absence_detail.absence_date,
            absence.users_id,
            '00:00:00',
            absence.absence_latitude,
            absence.absence_longitude,
            '00:00:00',
            absence.absence_description,
            '',
            '',
            '00:00:00',
            '00:00:00',
            MIN(profiles.profile_name) AS profile_name,
            MIN(profiles.nik) AS nik,
            '00:00:00',
            '00:00:00',
            '00:00:00',
            '00:00:00',
            '00:00:00',
            MIN(mst_absence_type.absence_name),
            ''"
            )
                ->leftJoin('profiles', 'profiles.users_id', '=', 'absence.users_id')
                ->leftJoin('mst_absence_type', 'mst_absence_type.absence_code', '=', 'absence.absence_code')
                ->leftJoin('absence_detail', 'absence_detail.absence_id', '=', 'absence.id')
                ->leftJoin('placement', 'placement.users_id', '=', 'absence.users_id')
                ->leftJoin('mst_customer', 'mst_customer.customer_code', '=', 'placement.customer_code')
                ->whereBetween('absence_detail.absence_date', [$r->from, $r->to])
                ->where('absence.users_id', $r->users_id)
                ->where('mst_customer.customer_code', $r->customer)
                ->groupBy('absence.id', 'absence_detail.id');

            $holiday = Holiday::selectRaw(
                "to_char(holiday.holiday_date, 'Day'),
            holiday.holiday_date, 
            null, 
            '00:00:00', 
            '', 
            '', 
            '00:00:00', 
            '', 
            '', 
            holiday.holiday_name, 
            '00:00:00', 
            '00:00:00', 
            '', 
            '', 
            '00:00:00', 
            '00:00:00', 
            '00:00:00', 
            '00:00:00', 
            '00:00:00', 
            holiday.holiday_name,
            ''"
            )
                ->whereBetween('holiday.holiday_date', [$r->from, $r->to]);

            $attendee = Attendee::selectRaw(
                "to_char(attendee_date, 'Day') AS day,
            attendee_date,
            attendance.users_id,
            attendee_time_in,
            attendee_latitude_in,
            attendee_longitude_in,
            attendee_time_out,
            attendee_latitude_out,
            attendee_longitude_out,
            MIN(site_schedule.schedule_name) AS schedule_name,
            MIN(site_schedule.schedule_in) AS schedule_in,
            MIN(site_schedule.schedule_out) AS schedule_out,
            MIN(profiles.profile_name) AS profile_name,
            MIN(profiles.nik) AS nik,
            MIN(attendance.attendee_time_in - site_schedule.schedule_in) AS late_duration,
            MIN(attendance.attendee_time_out - attendance.attendee_time_in) AS jam_efektif,
            MIN(attendance.attendee_time_out - site_schedule.schedule_out) AS lembur,
            MIN( site_schedule.schedule_out - attendance.attendee_time_out) AS pulang_cepat,
            '00:00:00' AS multiplikasi,
            null AS keterangan,
            absence_ref_desc AS alasan"
            )
                ->leftJoin('profiles', 'profiles.users_id', '=', 'attendance.users_id')
                ->leftJoin('placement', 'placement.users_id', '=', 'attendance.users_id')
                ->leftJoin('mst_customer', 'mst_customer.customer_code', 'placement.customer_code')
                ->leftJoin('site_schedule', 'site_schedule.schedule_code', '=', 'attendance.workhour_code')
                ->whereBetween('attendee_date', [$r->from, $r->to])
                ->where('attendance.users_id', $r->users_id)
                ->where('mst_customer.customer_code', $r->customer)
                ->whereRaw("site_schedule.day = attendance.day")
                ->union($absence)
                ->union($holiday)
                ->groupBy('attendance.id')
                ->orderBy('attendee_date')
                ->get();

            $weekend = DB::select(
                "SELECT
            to_char(CURRENT_DATE + i, 'Day') AS day,
            (CURRENT_DATE + i) AS attendee_date,
            '' AS users_id,
            '00:00:00' AS attendee_time_in,
            '' AS attendee_latitude_in,
            '' AS attendee_longitude_in,
            '00:00:00' AS attendee_time_out,
            '' AS attendee_latitude_out,
            '' AS attendee_longitude_out,
            '' AS workhour_name,
            '00:00:00' AS schedule_in,
            '00:00:00' AS schedule_out,
            '' AS profile_name,
            '' AS nik,
            '00:00:00' AS late_duration,
            '00:00:00' AS jam_efektif,
            '00:00:00' AS lembur,
            '00:00:00' AS pulang_cepat,
            '00:00:00' AS multiplikasi,
            '' AS keterangan,
            '' AS alasan
            FROM generate_series(DATE '" . $r->from . "'- CURRENT_DATE, 
            DATE '" . $r->to . "' - CURRENT_DATE ) i"
            );

            $weekend = array_map(function ($a) use ($attendee) {
                foreach ($attendee as $data) {
                    if ($a->attendee_date === $data->attendee_date) {
                        return $data;
                    }
                }
                return $a;
            }, $weekend);

            $data = $weekend;
        }

        // get late duration
        foreach ($data as $key => $value) {
            $value->day = str_replace(' ', '', $value->day);
            if ($value->attendee_time_in > $value->schedule_in) {
                $value->late_duration = $value->late_duration;
            } else {
                $value->late_duration = '';
            }
            if ($value->attendee_time_out == null || $value->attendee_time_in == null) {
                $value->alasan = 'M';
            }
            if ($value->day == 'Saturday' || $value->day == 'Sunday') {
                $value->keterangan = 'OFF';
            }
        }

        if ($r->has('users_id')) {
            $user = Attendee::leftJoin('profiles', 'profiles.users_id', '=', 'attendance.users_id')
                ->leftJoin('placement', 'placement.users_id', '=', 'attendance.users_id')
                ->leftJoin('mst_customer', 'mst_customer.customer_code', '=', 'placement.customer_code')
                ->where('attendance.users_id', $r->users_id)
                ->select('profiles.profile_name', 'profiles.nik', 'mst_customer.customer_name')
                ->orderBy('attendance.id', 'DESC')->first();
            // return $data;
        } else {
            $user = Attendee::leftJoin('profiles', 'profiles.users_id', '=', 'attendance.users_id')
                ->leftJoin('placement', 'placement.users_id', '=', 'attendance.users_id')
                ->leftJoin('mst_customer', 'mst_customer.customer_code', '=', 'placement.customer_code')
                ->where('mst_customer.customer_code', $r->customer)
                ->select('mst_customer.customer_name')->first();
        }

        // export excel
        $this->ExportExcel($user, $data, $r);
        // setup filename
        $this->return = array_merge((array)$this->return, [
            'data' => url('excel/kehadiran/rp_kehadiran_' . str_replace(' ', '_', ($r->has('users_id') ? $user->profile_name : $user->customer_name)) . '_tgl_' . $r->from . '_sd_' . $r->to . '.xls')
        ]);

        return response()->json($this->return, $this->return['code']);
    }







    // summary report
    public function summary(Request $r)
    {
        try {
            $data = Attendee::selectRaw("profiles.nik, profiles.profile_name, attendance.users_id, COUNT(attendance.id) AS total_hari, 0 AS total_terlambat,
            (SELECT COUNT(late)
            FROM (SELECT count(attendance.id)
                    FROM attendance
                    LEFT JOIN site_schedule ON site_schedule.schedule_code = attendance.workhour_code
                    WHERE attendance.attendee_time_in > site_schedule.schedule_in
                    AND attendee_date BETWEEN '" . $r->dateFrom . "' AND '" . $r->dateTo . "'
                    AND site_schedule.day = attendance.day
                    AND site_schedule.customer_code = '" . $r->customer_code . "'
                    AND users.id = attendance.users_id
                    GROUP BY attendee_date) AS late)
            AS total_terlambat
            ")
                ->leftJoin('users', 'users.id', '=', 'attendance.users_id')
                ->leftJoin('placement', 'placement.users_id', '=', 'attendance.users_id')
                ->leftJoin('mst_customer', 'mst_customer.customer_code', '=', 'placement.customer_code')
                ->leftJoin('profiles', 'profiles.users_id', '=', 'attendance.users_id')
                ->whereBetween('attendance.attendee_date', [$r->dateFrom, $r->dateTo])
                ->where('mst_customer.customer_code', $r->customer_code)
                ->groupBy('users.id', 'profiles.nik', 'profiles.profile_name', 'attendance.users_id')
                ->orderBy('users.id', 'desc')
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
                'status'    => true,
                'message'   => $err->getMessage(),
                'code'      => 200
            ]);
        }
        return response()->json($this->return, $this->return['code']);
    }

    public function exportExcelSummary($customer, $customer_data, $r)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');
        try {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->setCellValue('A1', 'Perusahaan');
            $spreadSheet->getActiveSheet()->setCellValue('B1', ': ' . $customer->customer_name);

            $spreadSheet->getActiveSheet()->setCellValue('A3', 'NIP');
            $spreadSheet->getActiveSheet()->setCellValue('B3', 'Nama');
            $spreadSheet->getActiveSheet()->setCellValue('C3', 'Total Hari Kerja');
            $spreadSheet->getActiveSheet()->setCellValue('D3', 'Total Hari Terlambat');

            $count = 4;
            foreach ($customer_data as $data) {
                $spreadSheet->getActiveSheet()->setCellValue('A' . $count, $data->nik);
                $spreadSheet->getActiveSheet()->setCellValue('B' . $count, $data->profile_name);
                $spreadSheet->getActiveSheet()->setCellValue('C' . $count, $data->total_hari);
                $spreadSheet->getActiveSheet()->setCellValue('D' . $count, $data->total_terlambat);

                $count = $count + 1;
            }

            // check folder is exists
            if (!File::isDirectory(base_path('public/excel'))) {
                // if not exists then create folder
                File::makeDirectory(base_path('public/excel'));
            }
            if (!File::isDirectory(base_path('public/excel/summary'))) {
                File::makeDirectory(base_path('public/excel/summary'));
            }

            $Excel_writer = new Xls($spreadSheet);
            $path = base_path('public/excel/summary/rp_summary_') . str_replace(' ', '_', $customer->customer_name) . '_tgl_' . $r->dateFrom . '_sd_' . $r->dateTo . '.xls';
            $Excel_writer->save($path);
            // exit();
        } catch (Exception $e) {
            return;
        }
    }

    public function exportDataSummary(Request $r)
    {
        $data = Attendee::selectRaw("profiles.nik, profiles.profile_name, attendance.users_id, COUNT(attendance.id) AS total_hari, 0 AS total_terlambat,
            (SELECT COUNT(late)
            FROM (SELECT count(attendance.id)
                    FROM attendance
                    LEFT JOIN site_schedule ON site_schedule.schedule_code = attendance.workhour_code
                    WHERE attendance.attendee_time_in > site_schedule.schedule_in
                    AND attendee_date BETWEEN '" . $r->dateFrom . "' AND '" . $r->dateTo . "'
                    AND site_schedule.day = attendance.day
                    AND site_schedule.customer_code = '" . $r->customer_code . "'
                    AND users.id = attendance.users_id
                    GROUP BY attendee_date) AS late)
            AS total_terlambat
            ")
            ->leftJoin('users', 'users.id', '=', 'attendance.users_id')
            ->leftJoin('placement', 'placement.users_id', '=', 'attendance.users_id')
            ->leftJoin('mst_customer', 'mst_customer.customer_code', '=', 'placement.customer_code')
            ->leftJoin('profiles', 'profiles.users_id', '=', 'attendance.users_id')
            ->whereBetween('attendance.attendee_date', [$r->dateFrom, $r->dateTo])
            ->where('mst_customer.customer_code', $r->customer_code)
            ->groupBy('users.id', 'profiles.nik', 'profiles.profile_name', 'attendance.users_id')
            ->orderBy('users.id', 'desc')
            ->get();

        $customer = MasterCustomer::select('customer_name')
            ->where('customer_code', $r->customer_code)
            ->first();

        // export excel
        $this->exportExcelSummary($customer, $data, $r);
        // setup filename
        $this->return = array_merge((array)$this->return, [
            'data' => url('excel/summary/rp_summary_' . str_replace(' ', '_', $customer->customer_name) . '_tgl_' . $r->dateFrom . '_sd_' . $r->dateTo . '.xls')
        ]);

        return response()->json($this->return, $this->return['code']);
    }
}