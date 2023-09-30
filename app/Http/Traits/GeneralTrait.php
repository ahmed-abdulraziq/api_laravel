<?php

namespace App\Http\Traits;

trait GeneralTrait {
    public function error($msg = 'error', $status = '404') {
        return [
            'status' => $status,
            'msg' => $msg
        ];
    }

    public function success($data = '', $msg = 'ok', $status = '200') {
        return [
            'data' => $data,
            'status' => $status,
            'msg' => $msg
        ];
    }
}