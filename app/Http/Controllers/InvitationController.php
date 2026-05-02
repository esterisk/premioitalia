<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    public function send()
    {
        $result = Invitation::CreateFromText(request()->info, request()->convention);
        return response()->json($result);
    }
}