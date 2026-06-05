<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidato extends Model
{
    protected $table = 'candidati';

    protected $primaryKey = 'id';

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

    public function simili()
    {
        return self::where('categoria_id', $this->categoria_id)
            ->where('anno', $this->anno)
            ->where('id', '!=', $this->id)
            ->where('spostato_in', 0)
            ->orderByRaw('MATCH(descrizione) AGAINST(? IN NATURAL LANGUAGE MODE) DESC', [$this->descrizione])
            ->selectRaw('descrizione, MATCH(descrizione) AGAINST(? IN NATURAL LANGUAGE MODE) AS similarity', [$this->descrizione])
            ->limit(5)
            ->get();
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
}
