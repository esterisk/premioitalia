<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Convention extends Model
{
    /**
     * Raggio, in metri, entro il quale una richiesta si considera confermata geograficamente.
     */
    public const RAGGIO_APPLICAZIONE = 500;

    protected $table = 'convention';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'location' => 'array',
        ];
    }

    public function albo()
    {
        return $this->hasMany(Albo::class, 'anno', 'anno');
    }

    public function serie()
    {
        return $this->belongsTo(ConventionSeries::class, 'serie_id');
    }

    public function scopeValidePerVoto($query)
    {
        return $query->where('codice', '<>', '')->orderBy('anno', 'desc');
    }

    /**
     * Convention attualmente in corso di svolgimento.
     */
    public function scopeInCorso($query)
    {
        $oggi = date('Y-m-d');

        return $query->whereDate('date_start', '<=', $oggi)
            ->whereDate('date_end', '>=', $oggi)
            ->orderBy('date_start', 'desc');
    }

    public function latitudine(): ?float
    {
        return self::gradi($this->location['lat'] ?? null, 90);
    }

    public function longitudine(): ?float
    {
        return self::gradi($this->location['lng'] ?? null, 180);
    }

    /**
     * Una coordinata vale solo se è un numero entro l'intervallo ammesso: un campo
     * lasciato vuoto non deve diventare lo zero del golfo di Guinea.
     */
    private static function gradi(mixed $valore, float $massimo): ?float
    {
        if (! is_numeric($valore) || abs((float) $valore) > $massimo) {
            return null;
        }

        return (float) $valore;
    }

    public function hasLocation(): bool
    {
        return $this->latitudine() !== null && $this->longitudine() !== null;
    }

    /**
     * Distanza in metri fra la location della convention e le coordinate indicate.
     */
    public function distanzaDa(?float $latitudine, ?float $longitudine): ?float
    {
        if ($latitudine === null || $longitudine === null || ! $this->hasLocation()) {
            return null;
        }

        return static::distanzaFraCoordinate($this->latitudine(), $this->longitudine(), $latitudine, $longitudine);
    }

    /**
     * Verifica se le coordinate indicate sono entro il raggio (in metri) dalla location della convention.
     */
    public function coordinateVicine(?float $latitudine, ?float $longitudine, int $raggio = self::RAGGIO_APPLICAZIONE): bool
    {
        $distanza = $this->distanzaDa($latitudine, $longitudine);

        return $distanza !== null && $distanza <= $raggio;
    }

    /**
     * Distanza in metri fra due coordinate geografiche (formula dell'emisenoverso).
     */
    public static function distanzaFraCoordinate(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $raggioTerrestre = 6371000;

        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLng = deg2rad($lng2 - $lng1);

        $a = sin($deltaLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($deltaLng / 2) ** 2;

        return $raggioTerrestre * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
