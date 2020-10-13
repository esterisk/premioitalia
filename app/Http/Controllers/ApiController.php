<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Convention;
use App\Partecipazioni;
use App\Annata;
use App\Calcolo\Australian;

class ApiController extends Controller
{
	function participate($convention_id, $no = false)
	{
    	if (!($user = \Auth::user())) return response()->json([ 'status' => 'error', 'error' => 'Devi fare login' ]);
    	if (!($convention = Convention::find($convention_id))) return response()->json([ 'status' => 'error', 'error' => 'Convention non trovata' ]);
    	
    	if (!($partecipazione = $user->partecipazioni()->whereConventionId($convention->getKey())->first())) {
	    	$partecipazione = new Partecipazioni();
    		$partecipazione->user_id = $user->getKey();
    		$partecipazione->convention_id = $convention->getKey();
    		$partecipazione->certificato = 0;
    	}
    	$partecipazione->partecipato = ($no == false);
		$partecipazione->save();
		return response()->json([ 'status' => 'success', 'convention_id' => $partecipazione->convention_id, 'partecipato' => ( $partecipazione->partecipato ? 'yes' : 'no' ) ]);
	}
	
	function calcolaVincitori()
	{
		$calcolo = new Australian();
		$response = $calcolo->calcolaVincitori();
		return response()->json($response);
	}
	
	function salvaVincitori()
	{
		$calcolo = new Australian();
    	$response = $calcolo->calcolaVincitori(true);
    	return response()->json($response);
	}
	
}

