<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;


// Global library usable


class Controller extends BaseController
{
    // Default variable data
    public $return,
        $curTimestamp;

    public function __construct(){
        $this->return = (array)[
            'status' => true,
            'message' => 'Berhasil',
            'data' => [],
            'code' => 200
        ];

        $this->curTimestamp = date("Y-m-d H:i:s");
    }

    public function __destruct(){
        $this->__construct();
    }
}
