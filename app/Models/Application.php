<?php

namespace App\Models;

use App\Enums\StatoApplicationEnum;
use App\Mail\ApplicazioneRegistrata;
use App\Mail\BenvenutoElettore;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Application extends Model
{
    protected $guarded = ['id'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'location' => 'array',
            'location_confirmed' => 'boolean',
            'status' => StatoApplicationEnum::class,
        ];
    }

    public function convention()
    {
        return $this->belongsTo(Convention::class, 'convention_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Richieste già arrivate dallo stesso dispositivo (stesso IP e stesso browser).
     */
    public function scopeStessoDispositivo(Builder $query, string $ipAddress, string $userAgent): Builder
    {
        return $query->where('ip_address', $ipAddress)->where('user_agent', $userAgent);
    }

    public static function dispositivoGiaUsato(string $ipAddress, string $userAgent): bool
    {
        return static::stessoDispositivo($ipAddress, $userAgent)->exists();
    }

    public function sendConfirmation(): void
    {
        Mail::to($this->email)->send(new ApplicazioneRegistrata($this));
    }

    public function isRatificabile(): bool
    {
        return $this->status !== StatoApplicationEnum::Ratificata;
    }

    /**
     * Ratifica la richiesta: registra il richiedente fra gli elettori (se non lo è già)
     * e gli invia la mail di benvenuto.
     */
    public function ratifica(): User
    {
        $user = User::findByEmail($this->email);
        $nuovo = $user === null;

        if ($nuovo) {
            $user = User::create([
                'name' => $this->name,
                'firstname' => $this->first_name,
                'lastname' => $this->last_name,
                'email' => $this->email,
                'email_verified' => true,
                'remember_token' => md5(uniqid()),
            ]);
        }

        $this->update(['user_id' => $user->id, 'status' => StatoApplicationEnum::Ratificata]);

        Mail::to($user)->send(new BenvenutoElettore($user, $this->convention));

        return $user;
    }

    protected static function booted(): void
    {
        static::saving(function (Application $application) {
            $application->name = trim($application->first_name.' '.$application->last_name);
        });
    }
}
