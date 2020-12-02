<?php

namespace DTApi\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use App\Traits\customTrait;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests, customTrait;

    protected function makeResponse($data, $message, $type, $code) {
       return [
           "Exceptions" => '',
           "Status" => $code,
           "ResultType" => $type,
           "Message" => $message,
           "Data" => $data ? $data : [],
       ];
   }
}
