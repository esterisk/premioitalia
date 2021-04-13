<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Annata;
use App\Categoria;
use App\Convention;
use App\Albo;

class PagesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function privacy()
    {
        return view('privacy', [ 'page_title' => 'Trattamento dei dati personali' ]);
    }

    public function comitato()
    {
        return view('comitato', [ 'page_title' => 'Comitato organizzatore' ]);
    }
    
    public function sistemavoto()
    {
        return view('sistema_di_voto', [ 'page_title' => 'Il sistema di voto' ]);
    }
     
    public function storia()
    {
        return view('storia', [ 'page_title' => 'Sul Premio Italia' ]);
    }
     
    public function chivota()
    {
        return view('chi_vota', [ 'page_title' => 'Chi può votare?' ]);
    }
     
    public function comecandidarsi()
    {
        return view('come_candidarsi', [ 'page_title' => 'Come ci si candida al Premio Italia' ]);
    }
     
    public function grazie()
    {
        return view('donazione.grazie', [ 'page_title' => 'Grazie per la tua donazione!' ]);
    }
     
    public function donazione()
    {
        return view('donazione.riprova', [ 'page_title' => 'Fai una donazione al Premio Italia' ]);
    }
   
    public function italcon()
    {
    	$data = [
    		'page_title' => 'Italcon e altre convention italiane',
    		'convention' => Convention::where('anno','>',0)->orderBy('anno')->orderBy('italcon','desc')->get(),
    	];
    	if ($data['user'] = \Auth::user()) {
    		$data['partecipazioni'] = $data['user']->partecipazioni()->wherePartecipato(1)->pluck('convention_id');
    	}
        return view('italcon', $data);
    }
    
    public function calendario()
    {
    	$annata = Annata::corrente();
        return view('calendario', [ 'page_title' => 'Calendario '.$annata->anno, 'annata' => $annata ]);
	}
    
    public function finalisti()
    {
    	$annata = Annata::corrente();
        return view('finalisti', [ 'page_title' => ($annata->risultati() ? 'Risultati' : 'Finalisti').' '.$annata->anno, 'annata' => $annata, 'categorie' => Categoria::attive() ]);
	}
	
    public function regolamento($versione = null)
    {
    	if (!in_array($versione, [ '1.0', '1.1', '1.2', '1.3' ])) $versione = '1.3';
        return view('regolamento', [ 'page_title' => 'Regolamento', 'versione' => str_replace('.','-',$versione) ]);
    }

	public function albo()
	{
        return view('albo', [ 
        	'page_title' => 'Albo d’oro del Premio Italia', 
        	'categorie' => Categoria::orderBy('ordine')->pluck('nome', 'id'),
        	'posizione' => false,
        	'anno' => false,
        	'raggruppamento' => false,
        	'albo' => Albo::whereFinalista(2)->with('categoria')->get()->sort([ $this, 'sortAlbo'])
        ]);
	}

	public function alboAnno($anno)
	{
	
        return view('albo', [ 
        	'page_title' => 'Albo d’oro del Premio Italia - Anno '.$anno, 
        	'categorie' => Categoria::orderBy('ordine')->pluck('nome', 'id'),
        	'posizione' => true,
        	'anno' => $anno,
        	'raggruppamento' => false,
        	'albo' => Albo::where('finalista','>',0)->whereAnno($anno)->with('categoria')->get()->sort([ $this, 'sortAlbo'])
        ]);
	}

	public function alboRaggruppamento(\App\Raggruppamento $raggruppamento)
	{
		$lista_categorie = $raggruppamento->categorie->pluck('id');
        return view('albo', [ 
        	'page_title' => 'Albo d’oro del Premio Italia - Categorie '.$raggruppamento->raggruppamento, 
        	'categorie' => Categoria::orderBy('ordine')->whereIn('id',$lista_categorie)->pluck('nome', 'id'),
        	'posizione' => true,
        	'anno' => false,
        	'raggruppamento' => $raggruppamento->slug,
        	'albo' => Albo::where('finalista','>',0)->whereIn('categoria_id',$lista_categorie)->with('categoria')->get()->sort([ $this, 'sortAlbo'])
        ]);
	}

	function sortAlbo($a, $b) 
	{
		if ($a->anno > $b->anno) return -1;
		if ($a->anno < $b->anno) return 1;
		if ($a->categoria->ordine > $b->categoria->ordine) return 1;
		if ($a->categoria->ordine < $b->categoria->ordine) return -1;
		if ($a->finalista > $b->finalista) return -1;
		if ($a->finalista < $b->finalista) return 1;
		if ($a->posizione > 0 && $a->posizione > 0) {
			if ( $a->posizione > $b->posizione) return 1;
			if ( $a->posizione < $b->posizione) return -1;
		} 
		if ($a->posizione > 0 && $a->posizione == 0) return 1;
		if ($a->posizione == 0 && $a->posizione > 0) return -1;
		if ($a->descrizione > $b->descrizione) return 1;
		if ($a->descrizione < $b->descrizione) return -1;
	}

}