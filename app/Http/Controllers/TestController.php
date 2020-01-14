<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\Payment;

class TestController extends APIController
{     
    public function testing(){
        Payment::dispatch();
    }   
}
