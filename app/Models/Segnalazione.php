<?php

namespace App\Models;

use App\Enums\StatoCandidatoEnum;
use App\Enums\StatoSegnalazioneEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Segnalazione extends Model
{
    protected $table = 'segnalazioni';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    public function elettore()
    {
        return $this->belongsTo(User::class);
    }

    public function voto()
    {
        return $this->belongsTo(Voto::class);
    }

    public function categoria()
    {
        return $this->belogsTo(Categoria::class, 'segnalazione_categoria_id');
    }

    public function candidato()
    {
        return $this->belongsTo(Candidato::class);
    }

    public function scopeCorrente($query)
    {
        return $query->whereSegnalazioneAnno(Annata::corrente()->anno);
    }

    public function scopeValide($query)
    {
        return $query->whereStato(StatoSegnalazioneEnum::Valida->value);
    }

    public function salva($campi)
    {
        $descrizione = substr(implode(' - ', array_values($campi)), 0, 250);
        $identificativo = md5($this->segnalazione_anno.'-'.$this->segnalazione_categoria_id.'-'.Str::slug($descrizione));

        if (! ($candidato = Candidato::whereIdentificativo($identificativo)->first())) {
            $candidato = new Candidato;
            $candidato->categoria_id = $this->segnalazione_categoria_id;
            $candidato->campi = json_encode($campi);
            $candidato->anno = $this->segnalazione_anno;
            $candidato->descrizione = $descrizione;
            $candidato->identificativo = $identificativo;
            $candidato->save();
        }
        $this->categoria_originale_id = $this->segnalazione_categoria_id;
        $this->candidato_originale_id = $candidato->getKey();
        $this->candidato_id = $candidato->spostato_in ?: $candidato->getKey();
        if ($candidato->stato == StatoCandidatoEnum::Escluso->value) {
            $this->candidato_id = 0;
        }

        $this->save();
    }

    public static function verificaMassimi($categoria_id)
    {
        // imposta tutte su valido
        Segnalazione::where('segnalazione_categoria_id', $categoria_id)
            ->corrente()
            ->update(['stato' => StatoSegnalazioneEnum::Valida->value]);

        // imposta su escluso quelli che hanno candidato nullo
        Segnalazione::where('segnalazione_categoria_id', $categoria_id)
            ->corrente()
            ->where('candidato_id', 0)
            ->update(['stato' => StatoSegnalazioneEnum::Esclusa->value]);

        // carica elenco elettori che hanno votato per questa categoria
        $elettori = Segnalazione::where('segnalazione_categoria_id', $categoria_id)->corrente()->valide()->pluck('user_id');

        // verifica per ogni elettore
        foreach ($elettori as $elettore_id) {

            // verifica che non voti più di una volta per lo stesso candidato
            $segnalazioni = Segnalazione::where('segnalazione_categoria_id', $categoria_id)
                ->where('user_id', $elettore_id)->corrente()->valide()
                ->orderBy('id', 'asc')
                ->get();
            $candidatoSegnalato = [];
            foreach ($segnalazioni as $segnalazione) {
                if (isset($candidatoSegnalato[$segnalazione->candidato_id])) {
                    $segnalazione->stato = StatoSegnalazioneEnum::Esclusa->value;
                    $segnalazione->save();
                } else {
                    $candidatoSegnalato[$segnalazione->candidato_id] = true;
                }
            }

            // verifica che non voti più di 3 candidati
            $count_segnalazioni = Segnalazione::where('segnalazione_categoria_id', $categoria_id)
                ->where('user_id', $elettore_id)->corrente()->valide()
                ->count();
            if ($count_segnalazioni > config('premioitalia.numero_segnalazioni')) {
                Segnalazione::where('segnalazione_categoria_id', $categoria_id)
                    ->where('user_id', $elettore_id)->corrente()->valide()
                    ->orderBy('id', 'desc')
                    ->limit($count_segnalazioni - config('premioitalia.numero_segnalazioni'))
                    ->update(['stato' => StatoSegnalazioneEnum::Esclusa->value]);
            }

        }
    }
}
