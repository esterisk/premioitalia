<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Annata extends Model
{
    protected $table = 'annate';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    public static function corrente()
    {
        return Annata::orderBy('anno', 'desc')->first();
    }

    public static function annoCorrente()
    {
        static $anno = false;
        if (! $anno) {
            $anno = self::corrente()->anno;
        }

        return $anno;
    }

    public function fase()
    {
        $now = date('Y-m-d');
        if ($now < $this->candidature_da) {
            return 'pre';
        } elseif ($now >= $this->candidature_da && $now <= $this->candidature_a.' 23:59:59') {
            return 'fase0';
        } elseif ($now >= $this->fase_1_da && $now <= $this->fase_1_a.' 23:59:59') {
            return 'fase1';
        } elseif ($now > $this->fase_1_a.' 23:59:59' && $now < $this->fase_2_da) {
            return 'spoglio1';
        } elseif ($now >= $this->fase_2_da && $now <= $this->fase_2_a.' 23:59:59') {
            return 'fase2';
        } elseif ($now > $this->fase_2_a.' 23:59:59' && $now < $this->premiazione) {
            return 'spoglio2';
        } elseif ($now > $this->premiazione) {
            return 'post';
        }

        return 'pre';
    }

    public function candidatureAperte()
    {
        $now = date('Y-m-d');
        $user = auth()->user();

        return
            ($now >= $this->candidature_da && $now <= $this->candidature_a.' 23:59:59')
            || ($user && $user->admin == 1);
    }

    public function voti()
    {
        return $this->hasMany(Voto::class, 'anno', 'anno');
    }

    public function segnalazioni()
    {
        return $this->hasMany(Segnalazione::class, 'segnalazione_anno', 'anno');
    }

    public function preferenze()
    {
        return $this->hasMany(Preferenza::class, 'preferenza_anno', 'anno');
    }

    public function finalisti()
    {
        return $this->finalisti_pubblici > 0;
    }

    public function risultati()
    {
        return $this->risultati_pubblici > 0 || (request()->user()->admin ?? 0) == 1;
    }

    public function pubblica()
    {
        $this->risultati_pubblici = 1;
        $this->save();

        return ['status' => 'success'];
    }

    public function getConventionAttribute()
    {
        return Convention::whereAnno($this->anno)->where('italcon', '>', 0)->first();
    }

    public function getStatus()
    {
        $invitation = $this->mailingTag('F');
        $reminder = $this->mailingTag('R');
        $solicitation = $this->mailingTag('S');

        $invitations = User::isValid()->where('last_invitation', $invitation)->count();
        $reminders = User::isValid()->where('last_invitation', $reminder)->count();
        $solicitations = User::isValid()->where('last_invitation', $solicitation)->count();

        $status = [
            'year' => $this->anno,
            'phase' => $this->fase(),
            'voters' => User::isValid()->count(),
            'votes' => Voto::annata($this)->fase($this->fase())->inviato()->count(),
            'votes1' => Voto::annata($this)->fase1()->inviato()->count(),
            'votes2' => Voto::annata($this)->fase2()->inviato()->count(),
            'drafts' => Voto::annata($this)->fase($this->fase())->preparazione()->count(),
            'mailing_status' => $this->mailing_status ?: 'idle',
            'invitations' => $invitations + $reminders + $solicitations,
            'reminders' => $reminders,
            'solicitations' => $solicitations,
            'faseRimasti' => max(0, round((strtotime($this->fase_1_a.' 23:59:59') - time()) / 86400)),
            'faseGiorniTotali' => max(1, round((strtotime($this->fase_1_a) - strtotime($this->fase_1_da)) / 86400)),
        ];

        return $status;
    }

    public function accessMailingStart()
    {
        $this->mailing_status = 'access-waiting';
        $this->save();
    }

    public function accessMailingCheck()
    {
        $this->mailing_status = 'access-checking';
        $this->save();
    }

    public function mailingProblem()
    {
        $this->mailing_status = 'problem';
        // eventualmente manda mail
        $this->save();
    }

    public function accessMailingSending()
    {
        $this->mailing_status = 'access-sending';
        $this->save();
    }

    public function mailingStop()
    {
        $this->mailing_status = 'idle';
        $this->save();
    }

    public function mailingTag($wave = null)
    {
        $fase = $this->fase();
        if (! in_array($fase, ['fase1', 'fase2'])) {
            return null;
        }

        if (! $wave) {
            $dataInizioFase = $fase == 'fase1' ? $this->fase_1_da : $this->fase_2_da;
            $giorniDaInizioFase = (time() - strtotime($dataInizioFase)) / 86400;
            $wave = $giorniDaInizioFase > 7 ? 'R' : 'F';
        }

        return $this->anno.'-'.$fase.'-'.$wave;
    }

    public function mailingSollecito()
    {
        $inviate = 0;
        $time = time();

        $voti_non_inviati = Voto::whereAnno($this->anno)->whereFase($this->fase())->whereStato('preparazione')->pluck('user_id');
        $elettori = User::isValid()->whereIn('id', $voti_non_inviati)->get();

        if (! empty(request()->email)) {
            if ($user = User::findByEmail(request()->email)) {
                $user->sendSollecitoInvio(true);
            }
        } else {
            foreach ($elettori as $user) {
                $inviate += $user->sendSollecitoInvio(true);
            }
        }

        $time = time() - $time + 1;
        $result = ['status' => 'success', 'inviate' => $inviate, 'tempo' => $time, 'invii_per_minuto' => $inviate ? round($inviate / $time * 60, 2) : 0];

        return response()->json($result);
    }
}
