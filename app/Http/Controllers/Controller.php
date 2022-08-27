<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Log;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function insertLog($data, $output=0){
        if(!(env('APP_DEBUG') && $output)){
            $log = new Log();
            $log->ip = $_SERVER['REMOTE_ADDR'];
            if(isset($data['action'])){
                $log->action = $data['action'];
            }
            if(isset($data['input'])){
                $log->input = $data['input'];
            }
            if(isset($data['output'])){
                $log->output = $data['output'];
            }
            if($output){
                $log->tipo = 1;
            }
            $log->created_at = date('Y-m-d H:i:s');
            $log->save();
        }
    }
}
