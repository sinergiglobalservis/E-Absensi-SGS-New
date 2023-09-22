<?php

namespace App\Http\Controllers\Transactions;

// get master of controller
use App\Http\Controllers\Controller;

// get models
use App\Models\Transaction\{Holiday};

// get library of process
use Request;
use Exception;

use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
            // get data holiday
            $data = Holiday::where('deleted_at', null)
                ->orderBy('holiday_date', 'ASC')
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

    // show by date range
    public function getByDateRange(Request $r)
    {
        try {
            // get data holiday
            $data = Holiday::where('deleted_at', null)
                ->whereBetween('holiday_date', [$r->start, $r->end])
                ->orderBy('holiday_date', 'ASC')
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

    public function add(Request $r)
    {
        // Validation holiday date exists
        if ((new Holiday)->isExists($r)) {
            $this->return = array_merge((array)$this->return, [
                'status' => false,
                'message' => 'Tanggal telah terdaftar',
                'code' => 401
            ]);

            return response()->json($this->return, $this->return['code']);
        }
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

    public function getCode()
    {
        $kode =  '001';

        $count = Holiday::count();

        if ($count > 0) {
            $kode = str_pad($kode + $count, 3, '0', STR_PAD_LEFT);
            return 'H' . $kode;
        } else {
            return 'H' . $kode;
        }
    }

    public function edit(Request $r)
    {
        try {
            if (!($sql = (new Holiday)->edit($r->all(), $r->id))) {
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
        if (!($sql = (new Holiday)->del($r->id))) {
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

    // upload schedule with insert multiple from excel
    public function uploadHoliday(Request $r)
    {
        unset($r['appAccess']);
        foreach ($r->post() as $key => $val) {
            $data = [
                'holiday_date' => $val['holiday_date'],
                'holiday_name' => $val['holiday_name'],
                'holiday_national' => $val['holiday_national'],
                'created_by' => $val['created_by'],
            ];
            $add = (new Holiday)->add($data);
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
    public function ExportExcel()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');
        try {
            $spreadSheet = new Spreadsheet();
            // workhsheet 1
            $spreadSheet->getActiveSheet()->setTitle("Jadwal");
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(14);
            $spreadSheet->getActiveSheet()->setCellValue('A1', 'nama_hari_libur');
            $spreadSheet->getActiveSheet()->setCellValue('B1', 'tanggal');
            $spreadSheet->getActiveSheet()->setCellValue('C1', 'libur_nasional');

            $spreadSheet->getActiveSheet()->setCellValue('A2', 'HUT INDONESIA');
            $spreadSheet->getActiveSheet()->setCellValue('B2', 'Format Tanggal : 2023-08-17');
            $spreadSheet->getActiveSheet()->setCellValue('C2', '1 = Libur Nasional, 0 = Cuti Bersama');

            // check folder is exists
            if (!File::isDirectory(base_path('public/excel'))) {
                // if not exists then create folder
                File::makeDirectory(base_path('public/excel'));
            }

            if (!File::isDirectory(base_path('public/excel/format'))) {
                File::makeDirectory(base_path('public/excel/format'));
            }

            $Excel_writer = new Xls($spreadSheet);
            $path = base_path('public/excel/format/format_upload_hari_libur.xls');
            $Excel_writer->save($path);
            $this->return = array_merge((array)$this->return, [
                'data' => url('excel/format/format_upload_hari_libur.xls')
            ]);
            // exit();
        } catch (Exception $e) {
            return;
        }
    }

    public function exportData(Request $r)
    {
        $this->ExportExcel();
        // setup filename
        $this->return = array_merge((array)$this->return, [
            'data' => url('excel/format/format_upload_hari_libur.xls')
        ]);

        return response()->json($this->return, $this->return['code']);
    }
}