<?php

namespace App\Http\Controllers;

use App\Models\Convention;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function show(): View
    {
        return view('applicazione', [
            'page_title' => 'Richiesta di iscrizione',
            'convention' => Convention::inCorso()->first(),
        ]);
    }
}
