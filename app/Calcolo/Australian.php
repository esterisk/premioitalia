<?php

namespace App\Calcolo;

use App\Models\Annata;
use App\Models\Candidato;
use App\Models\Preferenza;
use App\Models\Categoria;
use App\Models\ConteggioFinale;

class Australian
{
	public $annata;
	public $conteggio;
	public $vincitori;
	public $secondi;
	public $terzi;
	public $elaborazione;
	public $elaborazione_2;
	public $elaborazione_3;
	public $candidati;
	public $validi;
	public $preferenze_vincitore;
	public $calcolo_secondo;
	public $calcolo_terzo;
	public $voti;
	public $conti_candidati;
	public $archivio_voti;

	public function calcolaVincitori($save = false, $anno = false)
	{
		$categorie = Categoria::attive();
		if (!$anno) $this->annata = Annata::corrente();
		else $this->annata = Annata::whereAnno($anno);
		if (!$this->annata) return false;

		$result = [];
		$totale_votanti = $this->annata->voti()->fase2()->inviato()->count();


		foreach ($categorie as $categoria) {
			$categoria_id = $categoria->getKey();
			$this->caricaCandidati($categoria_id, true);

			$this->conteggio = ConteggioFinale::whereAnno($this->annata->anno)->whereCategoriaId($categoria_id)->firstOrNew([
				'anno' => $this->annata->anno,
				'categoria_id' => $categoria_id,
			]);
			$this->caricaVoti($categoria_id, true);
			$this->conteggio->totale_votanti = $totale_votanti;
			$this->conteggio->finalisti = count($this->candidati);
			$this->conteggio->elenco_finalisti = $this->candidati;
			//$this->conteggio->conti_candidati = $this->conti_candidati;
			//$this->conteggio->indicatori = $this->indicatori($this->candidati);
			$this->conteggio->vincitori = '';
			$this->conteggio->elaborazione = '';
			$this->conteggio->secondi = '';
			$this->conteggio->elaborazione_2 = '';
			$this->conteggio->terzi = '';
			$this->conteggio->elaborazione_3 = '';
			$this->vincitori = [];
			$this->secondi = [];
			$this->terzi = [];
			$this->elaborazione = [];
			$this->elaborazione_2 = [];
			$this->elaborazione_3 = [];

			$contoValidi = $this->validi->count();
			while ($contoValidi > 0) {
				$this->contaVoti();
				$this->elaborazione[] = $this->validi->toArray();
				$contoValidi = $this->eliminaUltimo();
			}
			$this->vincitori = $this->validi->keys()->toArray();

			if (count($this->vincitori) > 1) {
				//$this->untie($this->vincitori);
				$this->elaborazione[] = $this->validi->toArray();
			}

			if (isset($this->vincitori[0])) $this->preferenze_vincitore = $this->validi[$this->vincitori[0]];
			$this->conteggio->percentuale_votanti = round($this->conteggio->votanti / $this->conteggio->totale_votanti * 100);
			if (isset($this->vincitori[0])) $this->conteggio->preferenze_vincitore = $this->elaborazione[0][$this->vincitori[0]];
			$this->conteggio->preferenze_valide = $this->conteggio->preferenze;

			// SECONDA POSIZIONE

			if ($this->conteggio->elenco_finalisti->count() > 1 && count($this->vincitori) < 3) {
				$this->calcolo_secondo = true;
				$this->caricaCandidati($categoria_id);
				$this->caricaVoti($categoria_id);
				$contoValidi = $this->validi->count();

				foreach ($this->vincitori as $key) $this->elimina($key);
				if (count($this->vincitori) == 1) {
					while ($contoValidi > 0) {
						$this->contaVoti();
						$this->elaborazione_2[] = $this->validi->toArray();
						$contoValidi = $this->eliminaUltimo();
					}
					$this->secondi = $this->validi->keys()->toArray();
				}

				// TERZA POSIZIONE 

				if ($this->conteggio->elenco_finalisti->count() > 2 && (count($this->vincitori) + count($this->secondi) < 3)) {
					$this->caricaCandidati($categoria_id);
					$this->caricaVoti($categoria_id);
					$contoValidi = $this->validi->count();

					foreach ($this->vincitori as $key) $this->elimina($key);
					foreach ($this->secondi as $key) $this->elimina($key);

					while ($contoValidi > 0) {
						$this->contaVoti();
						$this->elaborazione_3[] = $this->validi->toArray();
						$contoValidi = $this->eliminaUltimo();
					}
					$this->terzi = $this->validi->keys()->toArray();
				}
			}

			$this->conteggio->elenco_finalisti = $this->conteggio->elenco_finalisti->implode(',');
			$this->conteggio->elaborazione = json_encode($this->elaborazione);
			$this->conteggio->vincitori = implode(',', $this->vincitori);
			$this->conteggio->elaborazione_2 = json_encode($this->elaborazione_2);
			$this->conteggio->secondi = implode(',', $this->secondi);
			$this->conteggio->elaborazione_3 = json_encode($this->elaborazione_3);
			$this->conteggio->terzi = implode(',', $this->terzi);

			$result[$categoria_id] = $this->conteggio;

			if ($save) {
				Candidato::whereAnno($this->annata->anno)->whereCategoriaId($categoria_id)->where('finalista', '>', 1)->update(['finalista' => 1]);
				$this->conteggio->save();
				foreach ($this->vincitori as $id) {
					Candidato::where('id', $id)->update(['finalista' => 2, 'posizione' => 1]);
				}
				foreach ($this->secondi as $id) {
					Candidato::where('id', $id)->update(['posizione' => 2]);
				}
				foreach ($this->terzi as $id) {
					Candidato::where('id', $id)->update(['posizione' => 3]);
				}
			}
		}
		return $result;
	}

	public function untie($candidates)
	{
		$candidates = $this->untieOtherChoices($candidates, $this->voti);
		if (count($candidates) == 1) return $candidates;
		$candidates = $this->untieLowerInitialOf($candidates);
		return $candidates;
	}


/**
 * This function takes an array of candidate IDs and returns an array of
 * candidate IDs, choosing the one with the lowest number of votes, in
 * case of a tie.
 *
 * This function is used in the spareggio method to break a tie.
 *
 * @param array $ultimi array of candidate IDs
 * @return array array of candidate IDs
 */
	public function untieOtherChoices($candidates, $votes)
	{
		$candidates_count = count($candidates);
		$votes =$votes->filter(function ($voto) use ($candidates) {
			foreach ($candidates as $c) {
				if ($voto->search($c) !== false) return true;
			}
			return false;
		});

		$minimum = 999;
		$last = [];
		foreach ($candidates as $v) {
			$points = $this->voti->filter(function ($voto) use ($v) {
				return $voto->search($v) !== false;
			})->count();
			if ($points < $minimum) {
				$minimum = $points;
				$last = [$v];
			} else if ($points == $minimum) {
				$last[] = $v;
			}
		}
		if (count($last) == 1) return $last;
		if (count($last) < $candidates_count) {
			return $this->untieOtherChoices($last, $votes);
		}
	}

/**
 * Counts the votes for each candidate and updates the validi collection.
 *
 * This function initializes the vote count for each candidate to zero,
 * then iterates through the voti collection to count the first-choice
 * votes for each candidate. Finally, it sorts the validi collection in
 * descending order by vote count.
 */

	function contaVoti()
	{
		$this->validi->each(function ($v, $k) {
			$this->validi[$k] = 0;
		});
		foreach ($this->voti as &$voto) {
			if (!empty($voto[0])) $this->validi[$voto[0]] = $this->validi->get($voto[0]) + 1;
		}
		$this->validi = $this->validi->sort()->reverse();
	}

	
	/**
	 * Elimina l'ultimo candidato in lista, se ce n'  pi  di uno con lo stesso numero di voti.
	 * Se ci sono due o pi  candidati con lo stesso numero di voti, elimina il candidato che ha ottenuto
	 * la minore quantit  di voti al primo turno.
	 *
	 * @return int Numero di candidati rimasti
	 */
	function eliminaUltimo()
	{
		$max = $this->validi->max();
		$min = $this->validi->min();
		if ($max == $min) {
			$firsts = $this->validi->filter(function ($v, $k) use ($min) {
				return $v == $min;
			})->keys();
			if (count($firsts) > 1) {
				ray($firsts);
				ray(json_encode($this->voti));
				$lasts = $this->untie($firsts);
				ray($lasts);
				foreach ($lasts as $last) $this->elimina($last);
				return $this->validi->count();
			}
			else return false;
		}
		else {
			$lasts = $this->validi->filter(function ($v, $k) use ($min) {
				return $v == $min;
			})->keys();
			if (count($lasts) > 1) {
				$lasts = $this->untie($lasts);
			}
			foreach ($lasts as $last) $this->elimina($last);
		}
		return $this->validi->count();
	}

	private function untieLowerInitialOf($list)
	{
		$lower = [];
		$min = 999;
		foreach ($list as $v) {
			if ($this->elaborazione[0][$v] < $min) {
				$min = $this->elaborazione[0][$v];
				$lower = [$v];
			} elseif ($this->elaborazione[0][$v] == $min) {
				$lower[] = $v;
			}
		}
		return $lower; 
	}

	private function higherInitialOf($list)
	{
		$higher = [];
		$max = 999;
		foreach ($list as $v) {
			if ($this->elaborazione[0][$v] > $max) {
				$max = $this->elaborazione[0][$v];
				$higher = [$v];
			} elseif ($this->elaborazione[0][$v] == $max) {
				$higher[] = $v;
			}
		}
		return $higher; 
	}

	function elimina($key)
	{
		$this->validi->pull($key);
		$this->rimuoviVotiEliminato($key);
	}

	function rimuoviVotiEliminato($elimina)
	{
		$this->voti->each(function (&$voto, $key) use ($elimina) {
			$this->voti[$key] = collect($voto->diff([$elimina])->values());
		});
		$this->voti = $this->voti->reject(function ($voto) {
			return $voto->count() == 0;
		});
	}

	function caricaCandidati($categoria_id, $load = false)
	{
		if ($load) {
			$this->candidati = Candidato::whereAnno($this->annata->anno)
				->whereCategoriaId($categoria_id)
				->where('finalista', '>', 0)
				->orderBy('descrizione')
				->pluck('id');
			$this->candidati[] = -1;

			$this->conti_candidati = [];
			foreach ($this->candidati as $cid) {
				$this->conti_candidati[$cid] = [
					0 => 0,
					1 => $this->annata->preferenze()
						->wherePreferenzaCategoriaId($categoria_id)
						->whereCandidato_1Id($cid)->count(),
					2 => $this->annata->preferenze()
						->wherePreferenzaCategoriaId($categoria_id)
						->whereCandidato_2Id($cid)->count(),
					3 => $this->annata->preferenze()
						->wherePreferenzaCategoriaId($categoria_id)
						->whereCandidato_3Id($cid)->count(),
				];
				$this->conti_candidati[$cid][0] =
					$this->conti_candidati[$cid][1] +
					$this->conti_candidati[$cid][2] +
					$this->conti_candidati[$cid][3];
			}
		}
		$this->validi = collect($this->candidati)->flip();
		$this->validi->each(function ($v, $k) {
			$this->validi[$k] = 0;
		});
	}


	function caricaVoti($categoria_id, $load = false)
	{
		if ($load) {
			$this->archivio_voti = collect([]);
			$this->conteggio->votanti = 0;
			$this->conteggio->preferenze = 0;

			foreach ($this->annata->preferenze()
				->wherePreferenzaCategoriaId($categoria_id)
				->where('candidato_1_id', '<>', 0)
				->get() as $pref) {
				$scelte = [$pref->candidato_1_id];
				$this->conteggio->votanti++;
				if ($pref->candidato_1_id > 0) {
					if ($pref->candidato_2_id) $scelte[] = $pref->candidato_2_id;
					if ($pref->candidato_3_id) $scelte[] = $pref->candidato_3_id;
				}
				$this->conteggio->preferenze += count($scelte);
				$this->archivio_voti[] = collect($scelte);
			}
		}
		$this->voti = clone $this->archivio_voti;
	}


	public function indicatori($finalisti)
	{
		$indicatori = [
			'd' => [],
			'c' => [],
			's' => [],
		];
		$sigle = [];

		foreach ($finalisti as $i => $cid) {
			$disambigua = 0;
			if ($cid == -1) {
				$indicatori[$cid]['d'] = 'Nessun premio';
				$indicatori[$cid]['c'] = 0;
				$indicatori[$cid]['s'] = 'np';
			} else {
				$candidato = Candidato::find($cid);
				$indicatori[$cid]['d'] = $candidato->descrizione;
				$indicatori[$cid]['c'] = $i + 1;
				do {
					$sigla = $candidato->sigla($disambigua++);
				} while (in_array($sigla, $sigle));
				$sigle[] = $sigla;
				$indicatori[$cid]['s'] = $sigla;
			}
		}
		return $indicatori;
	}
}
