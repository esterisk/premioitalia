<?php

namespace App\Calcolo;

use App\Models\Annata;
use App\Models\Candidato;
use App\Models\Categoria;
use App\Models\Conteggio;
use App\Models\Segnalazione;

class Nominees
{
    public $anno;

    public $annata;

    public $totale_votanti;

    public $dryRun;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct($dryRun = false)
    {
        $this->dryRun = $dryRun;
        $this->annata = Annata::corrente();
        $this->anno = $this->annata->anno;
    }

    /**
     * @return array<int, array{categoria: Categoria, conteggio: Conteggio, finalisti: array<int, Candidato>}>
     */
    public function calcolaFinalisti(): array
    {
        $categorie = Categoria::where('attiva', 1)->orderBy('ordine')->get();
        $risultati = [];
        $this->totale_votanti = Segnalazione::where('segnalazione_anno', $this->anno)->distinct('user_id')->count('user_id');

        foreach ($categorie as $categoria) {
            $risultati[$categoria->id] = $this->eseguiCategoria($categoria);
        }

        return $risultati;
    }

    public function inizializzaConteggio($categoria)
    {
        $conteggio = new Conteggio;
        $conteggio->fill([
            'id' => 0,
            'anno' => $this->anno,
            'categoria_id' => $categoria->id,
            'segnalazioni' => Segnalazione::where('segnalazione_anno', $this->anno)
                ->where('segnalazione_categoria_id', $categoria->id)
                ->count(),
            'candidati' => 0,
            'segnalazioni_valide' => 0,
            'candidati_validi' => 0,
            'votanti' => Segnalazione::where('segnalazione_anno', $this->anno)
                ->where('segnalazione_categoria_id', $categoria->id)
                ->distinct('user_id')
                ->count('user_id'),
            'totale_votanti' => $this->totale_votanti,
            'categoria_valida' => 10,
            'media' => 0,
            'voti_minimi' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $conteggio->percentuale_votanti = $this->totale_votanti > 0
            ? round($conteggio->votanti / $this->totale_votanti * 100)
            : 0;

        return $conteggio;
    }

    public function raccogliCandidati($categoria)
    {
        return $categoria->candidati()
            ->where('anno', $this->anno)
            ->where('stato', 'valido')
            ->withCount(['segnalazioni' => function ($query) {
                $query->where('stato', 0);
            }])
            ->orderByDesc('segnalazioni_count')
            ->get();
    }

    public function massimiMinimi(&$conteggio, $candidati)
    {
        $max = 0;
        $min = 9999;
        $divisore = 0;
        $dividendo = 0;

        foreach ($candidati as $c) {
            if ($c->segnalazioni_count > 0) {
                $conteggio->candidati++;
                if ($c->segnalazioni_count > 2) {
                    $conteggio->candidati_validi++;
                    $conteggio->segnalazioni_valide += $c->segnalazioni_count;
                }
                $divisore++;
                $dividendo += $c->segnalazioni_count;
                if ($c->segnalazioni_count > $max) {
                    $max = $c->segnalazioni_count;
                }
                if ($c->segnalazioni_count < $min) {
                    $min = $c->segnalazioni_count;
                }
            }
        }
        if ($max > 0) {
            $divisore--;
            $dividendo -= $max;
        }
        if ($min < 9999) {
            $divisore--;
            $dividendo -= $min;
        }
        $conteggio->media = $divisore != 0 ? $dividendo / $divisore : 0;
        ray($conteggio->media, $conteggio->candidati_validi, $conteggio->segnalazioni_valide, $conteggio->votanti, $conteggio->totale_votanti);
        $conteggio->voti_minimi = ceil($conteggio->media) + 1;
    }

    public function selezionaFinalisti($categoria, $candidati, &$conteggio)
    {
        $finalisti = [];
        $numero_finalisti = 0;
        $ultimo_finalista = 0;

        foreach ($candidati as $c) {
            if ($c->segnalazioni_count >= $conteggio->voti_minimi &&
                ($numero_finalisti < 5 || $c->segnalazioni_count >= $ultimo_finalista)) {
                $finalisti[] = $c;
                $ultimo_finalista = $c->segnalazioni_count;
                $numero_finalisti++;
            }
        }

        if ($conteggio->candidati_validi == 1 && $conteggio->percentuale_votanti < 30) {
            $conteggio->categoria_valida = 0;
        }
        if ($conteggio->percentuale_votanti < 10) {
            $conteggio->categoria_valida = 5;
        }

        if (! $this->dryRun) {
            Candidato::where('categoria_id', $categoria->id)
                ->where('anno', $this->anno)
                ->update(['finalista' => 0]);
            foreach ($finalisti as $f) {
                $f->finalista = 1;
                $f->save();
            }
        }

        return $finalisti;
    }

    /**
     * @return array{categoria: Categoria, conteggio: Conteggio, finalisti: array<int, Candidato>}
     */
    public function eseguiCategoria($categoria): array
    {
        $conteggio = $this->inizializzaConteggio($categoria);
        $candidati = $this->raccogliCandidati($categoria);
        $this->massimiMinimi($conteggio, $candidati);
        $finalisti = $this->selezionaFinalisti($categoria, $candidati, $conteggio);

        if (! $this->dryRun) {
            Conteggio::where('anno', $this->anno)
                ->where('categoria_id', $categoria->id)
                ->delete();
            $conteggio->save();
        }

        return [
            'categoria' => $categoria,
            'conteggio' => $conteggio,
            'finalisti' => $finalisti,
        ];
    }
}
