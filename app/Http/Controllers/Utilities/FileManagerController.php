<?php

namespace App\Http\Controllers\Utilities;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileManagerController extends Controller
{

    public function index(Request $request)
    {
        return view('utilities.filemanager');
    }
}
