<?php

namespace App\Http\Controllers\Transactions;

// get master of controller
use App\Http\Controllers\Controller;

// get models
use App\Models\Transaction\{Absence, AbsenceDetail, Profiles, Holiday};

// get library of process
use Request;
use Exception;

use Illuminate\Support\Facades\File;

class AbsenceController extends Controller
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
            $data = Absence::leftJoin('mst_absence_type AS msta', 'msta.absence_code', '=', 'absence.absence_code')
                ->leftJoin('absence_detail', 'absence_detail.absence_id', '=', 'absence.id')
                ->leftJoin('users', 'users.id', '=', 'absence.users_id')
                ->leftJoin('profiles', 'users.id', '=', 'profiles.users_id')
                ->leftJoin('placement', 'placement.users_id', '=', 'users.id')
                ->leftJoin('mst_customer', 'mst_customer.customer_code', '=', 'placement.customer_code')
                ->select('profiles.profile_name', 'profiles.nik', 'msta.absence_name', 'mst_customer.customer_name', 'msta.absence_long_date', 'absence.absence_description', 'absence.absence_attachment', 'absence.absence_date', 'absence.absence_latitude', 'absence.absence_longitude', 'absence.id', 'absence.approval_1', 'absence.approval_2')
                ->selectRaw('MIN(absence_detail.absence_date) AS absence_start_date, MAX(absence_detail.absence_date) AS absence_end_date')
                ->where('absence.deleted_at', null)
                ->groupBy('profiles.id', 'msta.id', 'mst_customer.id', 'absence.id')
                ->orderBy('absence.id', 'desc')
                ->get();

            foreach ($data as $key => $value) {
                if ($value['absence_attachment'] != '') {
                    $value['absence_attachment'] = url('absence/' . $value['absence_attachment']);
                }
            }

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

    // get data absence by id user
    public function getByIdUsers($id)
    {
        try {
            $data = Absence::leftJoin('mst_absence_type AS msta', 'msta.absence_code', '=', 'absence.absence_code')
                ->leftJoin('absence_detail', 'absence_detail.absence_id', '=', 'absence.id')
                ->leftJoin('users', 'users.id', '=', 'absence.users_id')
                ->leftJoin('profiles', 'users.id', '=', 'profiles.users_id')
                ->leftJoin('placement', 'placement.users_id', '=', 'users.id')
                ->leftJoin('mst_customer', 'mst_customer.customer_code', '=', 'placement.customer_code')
                ->select('profiles.profile_name', 'profiles.nik', 'msta.absence_name', 'mst_customer.customer_name', 'absence.absence_description', 'absence_detail.absence_date', 'absence.approval_1')
                ->where('absence.deleted_at', null)
                ->where('users.id', $id)
                ->orderBy('absence_detail.absence_date', 'desc')
                ->get();

            foreach ($data as $key => $value) {
                if ($value['absence_attachment'] != '') {
                    $value['absence_attachment'] = url('absence/' . $value['absence_attachment']);
                }
            }

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

    // upload images from base64
    public function upload(Request $r)
    {
        try {
            if (!File::isDirectory(base_path('public/absence'))) {
                //MAKA FOLDER TERSEBUT AKAN DIBUAT
                File::makeDirectory(base_path('public/absence'));
            }
            // check if request has file
            if (!file_put_contents(base_path('public/absence/' . $r->name), base64_decode($r->imgDocs))) {
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
                'code'      => 200
            ]);
        }

        return response()->json($this->return, $this->return['code']);
    }

    // upload file
    public function uploadTypeFile(Request $r)
    {
        // check if request has file
        if ($r->hasFile('imgDocs')) {
            // $filename = $r->name;
            // path documents saved
            $destination_path = base_path('public/absence/');
            // $oriFilenameArr = explode('.', $oriFilename);
            // $file_ext = end($oriFilenameArr);
            $image = $r->name;

            if ($r->file('imgDocs')->move($destination_path, $image)) {
                // if upload success
                $this->return = array_merge((array)$this->return, [
                    'data' => ''
                ]);
            } else {
                // if upload failed
                $this->return = array_merge((array)$this->return, [
                    'status' => false,
                    'message' => 'Gagal mengunggah file',
                    'code' => 401
                ]);
            }
        } else {
            // if file not found
            $this->return = array_merge((array)$this->return, [
                'status' => false,
                'message' => 'Error. File not found',
                'code' => 401
            ]);
        }

        return response()->json($this->return, $this->return['code']);
    }


    public function add(Request $r)
    {
        if ($r->has('absence_attachment')) {
            $r->merge(['absence_attachment' => str_replace(' ', '_', $r->absence_attachment)]);
        }

        if (is_array($r->absence_date)) {
            $abs_date = $r->absence_date[0];
        } else {
            $abs_date = $r->absence_date;
        }
        $absence = [
            'absence_code' => $r->absence_code,
            'absence_description' => $r->absence_description,
            'absence_attachment' => $r->absence_attachment,
            'absence_latitude' => $r->absence_latitude,
            'absence_longitude' => $r->absence_longitude,
            'approval_1' => $r->approval_1,
            'approval_2' => $r->approval_2,
            'total_leave' => $r->absence_duration,
            // permasalahan ada di array absence date
            'absence_date' => $abs_date,
            'users_id' => $r->users_id,
            'created_by' => $r->created_by,
        ];

        if (!($id = (new Absence)->add($absence))) {
            $this->return = array_merge((array)$this->return, [
                'status' => false,
                'message' => 'Gagal menambahkan data',
                'code' => 400
            ]);
        } else {
            $saldoCutiTahunan = Profiles::select('remaining_leave', 'id')->where('users_id', $r->users_id)->first();
            // cuti minus jika saldo kosong
            $saldoAkhir = $saldoCutiTahunan->remaining_leave - $r->absence_duration;
            $sisaSaldo = (new Profiles)->edit(['remaining_leave' => $saldoAkhir], $saldoCutiTahunan->id);

            // if ($r->absence_code == 'MTA005') {
            //     // get saldo awal cuti besar
            //     $saldoCutiBesar = Profiles::select('cuti_besar', 'id')->where('users_id', $r->users_id)->first();
            //     $saldoCutiTahunan = Profiles::select('remaining_leave', 'id')->where('users_id', $r->users_id)->first();
            //     // jika saldo cuti besar 0 maka potong saldo cuti tahunan
            //     if ($saldoCutiBesar == 0) {
            //         // potong saldo cuti
            //         $saldoAkhir = $saldoCutiTahunan->remaining_leave - $r->absence_duration;
            //         $sisaSaldo = (new Profiles)->edit(['remaining_leave' => $saldoAkhir], $saldoCutiTahunan->id);
            //     }
            //     // jika saldo cuti besar lebih dari 0 dan kurang dari total yang diajukan
            //     else if ($saldoCutiBesar > 0 && $saldoCutiBesar < $r->absence_duration) {
            //         $saldoAkhir = $saldoCutiTahunan->remaining_leave - $r->absence_duration;
            //         $sisaSaldo = (new Profiles)->edit(['remaining_leave' => $saldoAkhir], $saldoCutiTahunan->id);
            //     } else {
            //         $saldoAkhir = $saldoCutiBesar->cuti_besar - $r->absence_duration;
            //         $sisaSaldo = (new Profiles)->edit(['cuti_besar' => $saldoAkhir], $saldoCutiBesar->id);
            //     }
            // }
            // // jika cuti bersama
            // else if ($r->absence_code == 'MTA006') {
            //     // get data saldo cuti
            //     $saldoCutiTahunan = Profiles::select('remaining_leave', 'id')->where('users_id', $r->users_id)->first();
            //     if ($saldoCutiTahunan == 0) {
            //         // cuti minus jika saldo kosong
            //         $saldoAkhir = $saldoCutiTahunan->remaining_leave - $r->absence_duration;
            //         $sisaSaldo = (new Profiles)->edit(['remaining_leave' => $saldoAkhir], $saldoCutiTahunan->id);
            //     } else {
            //         // potong saldo cuti
            //         $saldoAkhir = $saldoCutiTahunan->remaining_leave - $r->absence_duration;
            //         $sisaSaldo = (new Profiles)->edit(['remaining_leave' => $saldoAkhir], $saldoCutiTahunan->id);
            //     }
            // } else {
            //     // get data saldo cuti
            //     $saldoCutiTahunan = Profiles::select('remaining_leave', 'id')->where('users_id', $r->users_id)->first();
            //     if ($saldoCutiTahunan == 0) {
            //         // cuti minus jika saldo kosong
            //         $saldoAkhir = $saldoCutiTahunan->remaining_leave - $r->absence_duration;
            //         $sisaSaldo = (new Profiles)->edit(['remaining_leave' => $saldoAkhir], $saldoCutiTahunan->id);
            //     } else {
            //         // potong saldo cuti
            //         $saldoAkhir = $saldoCutiTahunan->remaining_leave - $r->absence_duration;
            //         $sisaSaldo = (new Profiles)->edit(['remaining_leave' => $saldoAkhir], $saldoCutiTahunan->id);
            //     }
            // }

            // input detail absence
            if (is_array($r->absence_date)) {
                foreach ($r->absence_date as $key => $value) {
                    $isHoliday = Holiday::where('holiday_date', $value)->first();
                    // jika bukan hari libur nasional atau cuti bersama
                    if (!$isHoliday) {
                        $detail = [
                            'absence_id' => $id,
                            'absence_date' => $value,
                            'created_by' => $r->created_by
                        ];
                        $save = (new AbsenceDetail)->add($detail);
                    }
                }
            } else {
                $detail = [
                    'absence_id' => $id,
                    'absence_date' => $r->absence_date,
                    'created_by' => $r->created_by
                ];
                $save = (new AbsenceDetail)->add($detail);
            }
            $this->return = array_merge((array)$this->return, [
                'status' => true,
                'message' => 'Berhasil',
                'data'  => $id,
                'code' => 200
            ]);
        }

        return response()->json($this->return, $this->return['code']);
    }

    // public function addIzin(Request $r)
    // {
    //     // return $r;
    //     if ($r->has('absence_attachment')) {
    //         $r->merge(['absence_attachment' => str_replace(' ', '_', $r->absence_attachment)]);
    //     }

    //     if (!($id = (new Absence)->add($r->all()))) {
    //         $this->return = array_merge((array)$this->return, [
    //             'status' => false,
    //             'message' => 'Gagal menambahkan data',
    //             'code' => 401
    //         ]);
    //     } else {
    //         $this->return = array_merge((array)$this->return, [
    //             'status' => true,
    //             'message' => 'Submit cuti sukses',
    //             'data'  => $id,
    //             'code' => 200
    //         ]);
    //     }

    //     return response()->json($this->return, $this->return['code']);
    // }

    public function edit(Request $r)
    {
        if (!($sql = (new Absence)->edit($r->all(), $r->id))) {
            $this->return = array_merge((array)$this->return, [
                'status' => false,
                'message' => 'Gagal menyimpan data',
                'code' => 401
            ]);
        } else {
            $profiles = Profiles::where('nik', $r->nik)->first();
            $getTotalCuti = Absence::where('id', $r->id)->first();
            if ($r->approval_1 == 2) {
                $saldoKembali = $profiles->remaining_leave + $getTotalCuti->total_leave;
                $save = (new Profiles)->edit(['remaining_leave' => $saldoKembali], $profiles->id);
            }

            $this->return = array_merge((array)$this->return, [
                'data' => $sql->id
            ]);
        }

        return response()->json($this->return, $this->return['code']);
    }
}
