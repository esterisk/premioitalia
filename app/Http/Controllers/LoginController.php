<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Annata;
use App\User;
use \Auth;

class LoginController extends Controller
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
    public function login(Request $request)
    {
		if (Auth::guest()) return redirect()->route('home');
        else return redirect()->intended(route('vote'));
    }
    
    public function byemail($id, $token)
    {
		$user = User::find($id);
		if ($user && $user->user_status > 0 && $user->token == $token) {
			\Auth::loginUsingId($user->id);
		}
		if (Auth::guest()) return redirect()->route('home');
        else return redirect()->route('vote');
    }
    
    public function logout(Request $request)
    {
		if (!Auth::guest()) Auth::logout();
		return redirect()->route('home');
    }
    
    public function unsubscribe($id, $token)
    {
		$user = User::find($id);
		if ($user && $user->token == $token) {
			$user->unsubscribe();
			Auth::logout();
		}
        return view('unsubscribed');
    }
    
    public function richiesta()
    {
	    	$data = [ 
	    		'dati' => [ 'nome' => '', 'cognome' => '', 'email' => '', 'convention' => '', 'messaggio' => '' ],
	    		'conventions' => \App\Convention::validePerVoto()->get()
	    	];
        return view('richiesta', $data);
    }
    

}
