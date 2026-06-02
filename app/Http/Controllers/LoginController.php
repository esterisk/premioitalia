<?php

namespace App\Http\Controllers;

use App\Models\Convention;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LoginController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function login(Request $request)
    {
        if (auth()->guest()) {
            return redirect()->route('home');
        } else {
            return redirect()->intended(route('vote'));
        }
    }

    public function byemail($id, $token)
    {
        $user = User::find($id);
        if ($user && $user->user_status > 0 && $user->token == $token) {
            auth()->loginUsingId($user->id);
        }
        if (auth()->guest()) {
            return redirect()->route('home');
        } else {
            return redirect()->route('vote');
        }
    }

    public function logout(Request $request)
    {
        if (! auth()->guest()) {
            auth()->logout();
        }

        return redirect()->route('home');
    }

    public function unsubscribe($id, $token)
    {
        $user = User::find($id);
        if ($user && $user->token == $token) {
            return view('unsubscribed', ['status' => 'ask', 'user' => $user]);
        } else {
            return view('unsubscribed', ['status' => 'error']);
        }
    }

    public function unsubscribeConfirm()
    {
        $id = request()->uid;
        $token = request()->token;
        if (! $id || ! $token || ! ($user = User::find($id)) || ($user->token != $token)) {
            return view('unsubscribed', ['status' => 'error']);
        }
        $user->unsubscribe();
        if ($user->id == auth()->id()) {
            auth()->logout();
        }

        return view('unsubscribed', ['status' => 'confirm']);
    }

    public function richiesta()
    {
        $data = [
            'dati' => ['nome' => '', 'cognome' => '', 'email' => '', 'convention' => '', 'messaggio' => ''],
            'conventions' => Convention::validePerVoto()->get(),
        ];

        return view('richiesta', $data);
    }
}
