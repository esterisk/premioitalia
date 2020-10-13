<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Annata;

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
        return view('home', [ 'annata' => $annata, 'user' => \Auth::user() ]);
    }
}
