<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DistributionLogsController extends Controller
{
    public function index(Request $request)
    {
        return $this->handle($this->getService('Distribution')->getLogList());
    }
}
