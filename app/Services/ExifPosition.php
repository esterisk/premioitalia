<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * Posizione e data di scatto ricavate dai dati EXIF di una fotografia.
 */
class ExifPosition
{
    public function __construct(
        public readonly ?float $latitudine = null,
        public readonly ?float $longitudine = null,
        public readonly ?Carbon $scattataIl = null,
    ) {}

    /**
     * Legge i dati EXIF di un file. Restituisce una posizione vuota se il file
     * non è leggibile o non contiene dati EXIF.
     */
    public static function fromFile(?string $path): self
    {
        if (! $path || ! is_readable($path) || ! function_exists('exif_read_data')) {
            return new self;
        }

        // Solo JPEG e TIFF possono contenere dati EXIF: sugli altri formati
        // exif_read_data() si limiterebbe a emettere un warning.
        if (! in_array(@exif_imagetype($path), [IMAGETYPE_JPEG, IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM], true)) {
            return new self;
        }

        try {
            $exif = @exif_read_data($path);
        } catch (\Throwable) {
            return new self;
        }

        return is_array($exif) ? self::fromExifData($exif) : new self;
    }

    /**
     * @param  array<string, mixed>  $exif
     */
    public static function fromExifData(array $exif): self
    {
        return new self(
            self::coordinata($exif['GPSLatitude'] ?? null, $exif['GPSLatitudeRef'] ?? null),
            self::coordinata($exif['GPSLongitude'] ?? null, $exif['GPSLongitudeRef'] ?? null),
            self::dataScatto($exif),
        );
    }

    public function hasCoordinate(): bool
    {
        return $this->latitudine !== null && $this->longitudine !== null;
    }

    /**
     * La foto non è più vecchia del numero di minuti indicato. Se l'EXIF non
     * riporta la data di scatto la verifica non è possibile e viene superata.
     */
    public function recente(int $minuti = 60): bool
    {
        return $this->scattataIl === null
            || $this->scattataIl->gte(Carbon::now()->subMinutes($minuti));
    }

    /**
     * Converte una coordinata EXIF (gradi, minuti, secondi come frazioni) in gradi decimali.
     */
    private static function coordinata(mixed $parti, mixed $riferimento): ?float
    {
        if (! is_array($parti) || count($parti) < 3) {
            return null;
        }

        $gradi = self::frazione($parti[0]) + self::frazione($parti[1]) / 60 + self::frazione($parti[2]) / 3600;

        if (! is_finite($gradi)) {
            return null;
        }

        return in_array(strtoupper((string) $riferimento), ['S', 'W']) ? -$gradi : $gradi;
    }

    private static function frazione(mixed $valore): float
    {
        if (is_string($valore) && str_contains($valore, '/')) {
            [$numeratore, $denominatore] = explode('/', $valore, 2);

            return (float) $denominatore == 0.0 ? 0.0 : (float) $numeratore / (float) $denominatore;
        }

        return (float) $valore;
    }

    /**
     * @param  array<string, mixed>  $exif
     */
    private static function dataScatto(array $exif): ?Carbon
    {
        $data = $exif['DateTimeOriginal'] ?? $exif['DateTimeDigitized'] ?? $exif['DateTime'] ?? null;

        if (! is_string($data) || trim($data) === '') {
            return null;
        }

        try {
            $scatto = Carbon::createFromFormat('Y:m:d H:i:s', trim($data));
        } catch (\Throwable) {
            return null;
        }

        // Le fotocamere con l'orologio non impostato scrivono "0000:00:00 00:00:00":
        // una data del genere non dice nulla sull'età dello scatto.
        return $scatto && $scatto->year > 1990 ? $scatto : null;
    }
}
