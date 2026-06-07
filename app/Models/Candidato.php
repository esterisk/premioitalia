<?php

namespace App\Models;

use App\Enums\StatoCandidatoEnum;
use Illuminate\Database\Eloquent\Model;

class Candidato extends Model
{
    protected $table = 'candidati';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function segnalazioni()
    {
        return $this->hasMany(Segnalazione::class);
    }

    public function spostatoIn()
    {
        return $this->belongsTo(self::class, 'spostato_in');
    }

    public function getVotiAttribute()
    {
        return $this->segnalazioni()->count();
    }

    public function sigla($disambigua = 0)
    {
        static $disambiguatori = ['', 'X', 'Y', 'Z', 'W', 'K', 'Q', 'J', 'A', 'B', 'C'];
        $campi = json_decode($this->campi, true);
        if (! empty($campi['autore'])) {
            $v = $campi['autore'];
        } elseif (! empty($campi['nome'])) {
            $v = $campi['nome'];
        } elseif (! empty($campi['titolo'])) {
            $v = $campi['titolo'];
        }
        $sigla = strtoupper(preg_replace('|^([a-zA-Z])[^, ]*[, ]([a-zA-Z0-9]).+$|', '$1$2', $v));
        if (strlen($sigla) > 2) {
            $sigla = substr($sigla, 0, 2);
        }
        if (strlen($sigla) < 2) {
            $sigla = substr(rand(10, 99), 0, 2);
        }
        if ($disambigua > 0) {
            $sigla = substr($sigla, 0, 1).$disambiguatori[$disambigua];
        }

        return $sigla;
    }

	public function setCampiAttribute($value)
	{
		if (!is_array($value)) {
			$value = json_decode($value, true);
		}
		$this->attributes['campi'] = json_encode($value);
		$descrizione = substr(implode(' - ', array_values($value)), 0, 250);
		$this->attributes['descrizione'] = $descrizione;
	}

    public function descrizione_ricca()
    {
        $campi = json_decode($this->campi, true);
        $campi_ricchi = [];
        foreach ($campi as $label => $value) {
            if (in_array($label, ['titolo', 'testata'])) {
                $campi_ricchi[] = '<em>'.$value.'</em>';
            } elseif (in_array($label, ['url'])) {
                $campi_ricchi[] = '<a href="'.$value.'">'.$value.'</a>';
            } else {
                $campi_ricchi[] = $value;
            }
        }

        return implode(', ', $campi_ricchi);
    }

    public function scopeFinalista($query)
    {
        return $query->where('finalista', '>', 0);
    }

    public function scopeCorrente($query)
    {
        return $query->whereAnno(Annata::corrente()->anno);
    }

    public function scopeValido($query)
    {
        return $query->corrente()->where('stato', StatoCandidatoEnum::Valido->value);
    }

    public function scopeNuovo($query)
    {
        return $query->valido()->where('verificato', 0);
    }

	public function motivazione()
	{
		return $this->belongsTo(Motivazione::class, 'motivazione_id');
	}

    public function querySimili($quanti)
    {
        return self::where('categoria_id', $this->categoria_id)
            ->where('anno', $this->anno)
            ->where('id', '!=', $this->id)
            ->where('stato', StatoCandidatoEnum::Valido->value)
            ->orderByRaw('MATCH(descrizione) AGAINST(? IN NATURAL LANGUAGE MODE) DESC', [$this->descrizione])
            ->selectRaw('id, descrizione, MATCH(descrizione) AGAINST(? IN NATURAL LANGUAGE MODE) AS similarity', [$this->descrizione])
            ->limit($quanti);
    }

    public function simili($quanti = 5)
    {
        return $this->querySimili($quanti)->get();
    }

    public function similiOptions($quanti = 5)
    {
        return $this->querySimili($quanti)->pluck('descrizione', 'id');
    }

    public function getSimiliAttribute()
    {
        $simili = [];
        foreach ($this->simili() as $simile) {
            if ($simile->similarity > 5) {
                $color = '#666';
                if ($simile->similarity > 20) {
                    $color = 'red';
                } elseif ($simile->similarity > 10) {
                    $color = 'orange';
                }

                $simili[] = '<span style="color:'.$color.';">'.$simile->descrizione.' ('.round($simile->similarity, 1).')</span>';
            }
        }

        return $simili;
    }

    public function spostaIn($candidato_id, $motivo = '')
    {
        $this->spostato_in = $candidato_id;
        $this->stato = StatoCandidatoEnum::Spostato->value;
        $this->save();

        Segnalazione::where('candidato_id', $this->id)->update(['candidato_id' => $candidato_id]);
        Segnalazione::verificaMassimi($this->categoria_id);
        Candidato::updateSegnalazioni($this->categoria_id);
    }

    public function cambiaCategoria($categoria_id)
    {
        $old_categoria_id = $this->categoria_id;
        $this->categoria_id = $categoria_id;
        $this->save();

        Segnalazione::where('candidato_id', $this->id)->update(['segnalazione_categoria_id' => $categoria_id]);
        Segnalazione::verificaMassimi($old_categoria_id);
        Segnalazione::verificaMassimi($categoria_id);
        Candidato::updateSegnalazioni($old_categoria_id);
        Candidato::updateSegnalazioni($categoria_id);
    }

    public static function updateSegnalazioni($categoria_id = null)
    {
        if ($categoria_id) {
            $categorie = [$categoria_id];
        } else {
            $categorie = Categoria::active()->pluck('id');
        }

        $subquery = Segnalazione::selectRaw('COUNT(*)')
            ->whereColumn('candidato_id', 'candidati.id')
            ->corrente()
            ->valide()
            ->toBase();

        self::whereIn('categoria_id', $categorie)
            ->update(['segnalazioni' => $subquery]);
    }

	public function needLowering()
	{
		$campi = json_decode($this->campi, true);
		foreach ($campi as $label => $value) {
			if (($label != 'url') && preg_match('/[A-Z][A-Z]+/', $value)) {
				return true;
			}
		}
		return false;
	}

	public function minuscole()
	{
		$campi = json_decode($this->campi, true);
		foreach ($campi as $label => $value) {
			$campi[$label] = Str::of($value)->lower()->ucwords();
		}
		$this->campi = $campi;
		return $this;
	}

    public function escludi($motivazione_id)
    {
        $this->motivazione()->associate($motivazione_id);
        $this->motivo_esclusione = Motivazione::find($motivazione_id)->motivazione;
        $this->stato = StatoCandidatoEnum::Escluso->value;
        $this->save();
    }

    public function separaDa()
    {
        Segnalazione::where('candidato_originale_id', $this->id)->update(['candidato_id' => $this->id]);
        $this->spostato_in = null;
        $this->stato = StatoCandidatoEnum::Valido->value;
        Segnalazione::verificaMassimi($this->categoria_id);
        Candidato::updateSegnalazioni($this->categoria_id);
        $this->save();
    }

    public function riammetti()
    {
        if ($this->stato == 'escluso') {
            $this->motivazione()->dissociate();
            $this->motivo_esclusione = null;
            $this->stato = StatoCandidatoEnum::Valido->value;
        } elseif ($this->stato == 'spostato') {
            $this->sepoaraDa();
        }
        $this->save();
    }
}
