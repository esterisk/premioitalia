<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Annata;
use App\Categoria;
use App\Voto;
use App\Candidato;
use Exception;
use App\Calcolo\Australian;

class VoteController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('redo1');
    }

    public function dashboard1()
    {
    	$user = \Auth::user();
        return view('dashboard1', [ 'user' => \Auth::user() ]);
    }

    public function vote()
    {
    	if (!\Config::get('premioitalia.stato')) return redirect()->route('home');
    	$this->annata = Annata::corrente();
    	switch($this->annata->fase()) {
    		case 'pre':
    		case 'spoglio1':
    		case 'spoglio2':
    		case 'finalisti':
    		case 'post':
    		return redirect()->route('home');
    		break;
    		
    		case 'fase1':
    		return $this->vote1();
			break;    		

    		case 'fase2':
    		return $this->vote2();
			break;    		
    	}
    }

    public function save(Request $request)
    {
    	$this->annata = Annata::corrente();
    	switch($this->annata->fase()) {
    		case 'pre':
    		case 'spoglio1':
    		case 'spoglio2':
    		case 'finalisti':
    		case 'post':
    		return redirect()->route('home');
    		break;
    		
    		case 'fase1':
    			$response = $this->save1($request);
    			if ($request->ajax()) {
					return response()->json($response);
    			} else {
   					return $this->vote1($response);
    			}
			break;    		

    		case 'fase2':
    			$response = $this->save2($request);
    			if ($request->ajax()) {
					return response()->json($response);
    			} else {
   					return $this->vote2($response);
    			}
			break;    		
    	}
    }
    
  
    private function vote1($response = false)
    {
    	$data['user'] = \Auth::user();
    	$data['annata'] = $this->annata;
    	$data['categorie'] = Categoria::attive();
    	$data['stato'] = 'preparazione';
    	if ($response && $response['status'] == 'success') $data['success'] = 'Voto salvato';
    	if ($response && $response['status'] == 'error') $data['error'] = $response['error'];
    	
    	$voto = $data['user']->voto($data['annata']->anno, $data['annata']->fase());
    	if ($voto) {
    		if ($voto->stato == 'preparazione') $data['voto'] = json_decode($voto->dati);
    		else {
    			$data['stato'] = $voto->stato;
    			$data['sent_at'] = $voto->sent_at;
    		}
    	}
        return view('vote1', $data);
    }

    public function save1(Request $request)
	{
    	if (!($user = \Auth::user())) {
			return [ 'status' => 'error', 'error' => 'Utente non loggato o sessione scaduta' ];
     	}
     	$annata = Annata::corrente();
     	if (($request->anno != $annata->anno) || ($request->fase != $annata->fase())) {
			return [ 'status' => 'error', 'error' => 'Anno o fase errata, voto scaduto' ];
     	}
    	
  //  	try {
	    	$voto = Voto::salva($user, $annata, $request->all());
	    	if ($voto && $request->action == 'send') {
	    		$voto->invia();
	    	}
	//    } catch (Exception $e) {
	 //   	return [ 'status' => 'error', 'error' => $e->getMessage() ];
	 //   }
		
		return [ 'status' => 'success' ];
	}

    private function vote2($response = false)
    {
    	$data['user'] = \Auth::user();
    	$data['annata'] = $this->annata;
    	$data['categorie'] = Categoria::attive();
    	$data['stato'] = 'preparazione';
    	if ($response && $response['status'] == 'success') $data['success'] = 'Voto salvato';
    	if ($response && $response['status'] == 'error') $data['error'] = $response['error'];
    	
    	$voto = $data['user']->voto($data['annata']->anno, $data['annata']->fase());
    	if ($voto) {
    		if ($voto->stato == 'preparazione') $data['voto'] = json_decode($voto->dati, true);
    		else {
    			$data['stato'] = $voto->stato;
    			$data['sent_at'] = $voto->sent_at;
    		}
    	} else {
    		$data['voto'] = null;
    	}
        return view('vote2b', $data);
    }
    
    public function save2(Request $request)
    {
    	if (!($user = \Auth::user())) {
			return [ 'status' => 'error', 'error' => 'Utente non loggato o sessione scaduta' ];
     	}
     	$annata = Annata::corrente();
     	if (($request->anno != $annata->anno) || ($request->fase != $annata->fase())) {
			return [ 'status' => 'error', 'error' => 'Anno o fase errata, voto scaduto' ];
     	}
    	
  //  	try {
	    	$voto = Voto::salva($user, $annata, $request->all());
	    	if ($voto && $request->action == 'send') {
	    		$voto->invia();
	    	}
	//    } catch (Exception $e) {
	 //   	return [ 'status' => 'error', 'error' => $e->getMessage() ];
	 //   }
		
		return [ 'status' => 'success' ];
    }

	public function redo1()
	{
		Voto::redo1();
		return response()->json([ 'status' => 'success' ]);
	}
	
	function finale()
	{
		if (!($user = \Auth::user()) || ($user->admin <= 0) ) abort(403);
		
		$calcolo = new Australian();
		$response = $calcolo->calcolaVincitori();

        return view('finale', [ 
        		'page_title' => 'Calcolo finale vincitori',
			'categorie' => Categoria::attive(),
			'conteggio' => $response,
		]);
	}

}

