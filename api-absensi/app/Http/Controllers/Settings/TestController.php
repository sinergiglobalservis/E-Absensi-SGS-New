<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;

// get models
use App\Models\{Test};
use App\Models\Master\{MasterSchedule, MasterScheduleDetail};
use App\Models\Transaction\{Schedule, Attendee};

use Request;
use Mail;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function get()
    {
        $return = [
            "status" => true,
            "message" => "Berhasil",
            "data" => []
        ];

        $return['data'] = Test::all();

        return response()->json($return, 200);
    }

    public function verif(Request $r)
    {
        // return $r;
        $mail = (new Mail)->to('rizkymoss@yahoo.com')
            ->from(env('MAIL_USERNAME'), 'INFO Admin (no-reply)')
            ->subject('[KBP] User Validation')
            ->body(
                '<h1>Selamat Bergabung</h1>
                    <h4>Aplikasi Absensi</h4>
                    <br \/><br \/>
                    <a target="_blank" href="' . env("WEB_URL") . 'verify?token=s8d9a7s897fds89f89asd87d9f8789asdhjahfkj==340jka">[ Klik disini untuk mendapatkan kredensial anda ]</a>'
            )
            ->send();
        if ($mail) {
            $this->return = array_merge((array)$this->return, [
                'status'    => false,
                'message'   => 'Berhasil',
                'data'      => [],
                'code'      => 200
            ]);
        }
    }

    public function trial(Request $r)
    {
        // non shift
        $data = MasterScheduleDetail::selectRaw('mscd.schedule_name, msc.mst_schedule_code, mscd.schedule_code, mscd.schedule_in, mscd.schedule_out, mscd.day')
            ->from('mst_schedule_detail AS mscd')
            ->leftJoin('mst_schedule AS msc', 'mscd.mst_schedule_code', '=', 'msc.mst_schedule_code')
            ->leftJoin('schedule AS sc', 'mscd.mst_schedule_code', '=', 'sc.schedule_code')
            ->where('sc.users_id', $r->users_id)
            ->whereRaw("'" . $r->now . "' between schedule_start_date AND schedule_end_date")
            ->where('mscd.day', $r->day)
            ->get();

        if (count($data) == 0) {
            // shift
            $data = MasterScheduleDetail::selectRaw('mscd.schedule_name, msc.mst_schedule_code, mscd.schedule_code, mscd.schedule_in, mscd.schedule_out, mscd.day')
                ->from('mst_schedule_detail AS mscd')
                ->leftJoin('mst_schedule AS msc', 'mscd.mst_schedule_code', '=', 'msc.mst_schedule_code')
                ->leftJoin('schedule AS sc', 'mscd.mst_schedule_code', '=', 'sc.schedule_code')
                ->where('sc.users_id', $r->users_id)
                ->whereRaw("'" . $r->now . "' between schedule_start_date AND schedule_end_date")
                ->get();
        }

        $this->return = array_merge((array)$this->return, [
            'status'    => false,
            'message'   => 'Berhasil',
            'data'      => $data,
            'code'      => 200
        ]);

        return response()->json($this->return, $this->return['code']);
    }
}
