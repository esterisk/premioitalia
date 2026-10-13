<?php

namespace App\Calcolo;

class Condorcet
{
    public $matches;
    public $candidates;
    public $annata;
    public $result;
    public $votes;
    public $categorySlugs;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->matches = [];
        $this->candidates = [];
        $this->annata = null;
        $this->result = [];
        $this->votes = [];
        $this->categorySlugs = [];

    }

    public function calcolaVincitori($save = false, $anno = false)
    {
        if (! $this->caricaAnnata($anno)) {
            return false;
        }

        $categorie = Categoria::attive();
        foreach ($categorie as $categoria) {
            $this->categorySlugs[$categoria->slug] = $categoria->getKey();
            $this->result[$categoria->getKey()] = $this->calcolaCategoria($categoria, $save);
            $this->candidates[$categoria->getKey()] = $categoria->finalisti($this->annata->anno)->pluck('id')->toArray();
        }

        return $this->result;
    }

    /**
     * Carica l'annata da elaborare, per anno o corrente.
     */
    public function caricaAnnata($anno = false): ?Annata
    {
        if (! $anno) {
            $this->annata = Annata::corrente();
        } else {
            $this->annata = Annata::whereAnno($anno)->first();
        }

        return $this->annata;
    }

    public function elaboraVoti() 
    {
        $voti = $this->annata->voti()->fase2()->inviato()->get();
        foreach ($voti as $voto) {
            $v = json_decode($voto->dati, true);
            foreach ($v as $key => $value) if (isset($this->categorySlugs[$key])) {
                $this->votes[$this->categorySlugs[$key]][] = $value;
            }
        }
    }

    public function calcolaCategoria($categoria, $save = false)
    {
        $finalisti = $this->candidates[$categoria->getKey()] ?? [];
        foreach ($finalisti as $finalista) {
            foreach ($finalisti as $altroFinalista) {
                $matches[$finalista][$altroFinalista] = 0; 
            }
        }

        foreach ($this->votes[$categoria->getKey()] ?? [] as $voto) {
            if (isset($voto["1"]) || isset($matches[$voto["1"]])) {
                foreach ($matches[$voto["1"]] as $k => $v) {
                    if ($k == $voto["1"]) {
                        continue;
                    }
                    $matches[$voto["1"]][$k]++;
                }
            }
            if (isset($voto["2"]) || isset($matches[$voto["2"]])) {
                foreach ($matches[$voto["2"]] as $k => $v) {
                    if ($k == $voto["2"] || $k == $voto["1"]) {
                        continue;
                    }
                    $matches[$voto["2"]][$k]++;
                }
            }
            if (isset($voto["3"]) || isset($matches[$voto["3"]])) {
                foreach ($matches[$voto["3"]] as $k => $v) {
                    if ($k == $voto["3"] || $k == $voto["2"] || $k == $voto["1"]) {
                        continue;
                    }
                    $matches[$voto["3"]][$k]++;
                }
            }
        }
        // Implementazione del calcolo per una singola categoria con i voti caricati
        $this->matches[$categoria->getKey()] = $matches;
        
    }

}
