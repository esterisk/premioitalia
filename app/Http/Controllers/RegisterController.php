<?php

namespace App\Http\Controllers;

use App\Models\Annata;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function confirm(Request $request)
    {
        try {
            $data = ['error' => null, 'user' => null];
            if (!$request->hasValidSignature()) throw new \Exception('expired');
            $invitation = Invitation::check($request);
            $data['invitation'] = $invitation;
        } catch (\Exception $e) {
            $data['error'] = $e->getMessage();
        }

        return view('register.confirm', $data);
    }

    public function store(Request $request)
    {
        try {
            $data = ['error' => null, 'user' => null];
            $invitation = Invitation::check($request, true);
            $data['invitation'] = $invitation;
            $data['user'] = $invitation->confirm();
        } catch (\Exception $e) {
            $data['error'] = $e->getMessage();
        }

        return view('register.success', $data);
    }

}
