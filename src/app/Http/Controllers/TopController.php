<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TopController extends Controller
{
    /**
     * TOP画面を表示する
     */
    public function index()
    {
        return view('top');
    }
}
