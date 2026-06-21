<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class TopController extends Controller
{
    /**
     * TOP画面を表示する
     *
     * @return View
     */
    public function index(): View
    {
        return view('top');
    }
}
