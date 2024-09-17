<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ComponentTestController extends Controller
{
    public function showComponettest1()
    {
        return view('tests.component-test1');
    }

    public function showComponettest2()
    {
        return view('tests.component-test2');
    }
}
