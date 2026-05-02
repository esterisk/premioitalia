<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Annata;
use App\Models\Convention;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {
        $annata = Annata::corrente();
        return view('home', ['annata' => $annata, 'user' => auth()->user(), 'italcon' => Convention::where('italcon', '>', 0)->where('anno', $annata->anno)->first()]);
    }
}