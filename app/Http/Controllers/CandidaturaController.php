<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Annata;
use App\Categoria;
use App\Candidatura;
use Exception;

class CandidaturaController extends Controller
{
    public function index()
    {
        return view('candidatura_index', [ 'page_title' => 'Candidature spontanee' ] );
    }

    public function categoria(Categoria $categoria)
    {
        return view('candidatura_categoria', [ 'categoria' => $categoria, 'page_title' => 'Candidature spontanee: '.$categoria->nome  ]);
    }

    public function inserisci(Categoria $categoria, Request $request)
    {
		$annata = Annata::corrente();
		if (!$annata->candidatureAperte()) return redirect()->route('home');

		$result = Candidatura::salva($annata, $categoria, $request);
		return response()->json($result);
    }


}

