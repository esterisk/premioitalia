<?php

namespace App\Models;

use App\Mail\Invito;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Invitation extends Model
{
    use HasFactory;

    protected $casts = [
        'expiration' => 'datetime'
    ];
    protected $guarded = ['id'];


    static public function CreateFromText($text, $convention_id)
    {
        $text = str_replace(["\r\n", "\r", "\n", "<br />", "<br/>", "<br>"], "|", $text);
        $text = preg_replace('|\s+|', ' ', $text);
        $text = preg_replace('|[<>;:,#\"«»“”\(\)\[\]\{\}%]|', '', $text);
        $lines = explode("|", $text);
        $results = [];

        foreach ($lines as $line) if ($line) {
            $parse_error = null;
            $invitation = self::parseLine($line, $parse_error);

            if (!$invitation) {
                $results[$line] = $parse_error;
            } else {
                if ($user = self::checkExistingEmail($invitation->email)) {
                    //$user->sendRegistrationExistingConfirmation($invitation->firstname . ' ' . $invitation->lastname);
                    $results[$line] = 'Errore: Email '.$invitation->email.' gia registrata';
                    $user->sendAccess(false);
                } elseif ($user = self::checkExistingName($invitation->firstname, $invitation->lastname)) {
                    $results[$line] = 'Errore: Nome '.$invitation->firstname.' '.$invitation->lastname.' gia registrato con email '.$user->email;
                } else {
                    $invitation->convention_id = $convention_id;
                    $invitation->save();
                    $invitation->send();
                    $results[$invitation->firstname . ' ' . $invitation->lastname . ' <' . $invitation->email . '>'] = 'Invito spedito';
                }
            }
        }
        return $results;
    }

    static private function parseLine($line, &$error)
    {
        $words = explode(' ', $line);
        $names = [];
        $email = null;
        $lastname = null;
        foreach ($words as $word) {
            if (preg_match('|^.*\@.+\.[a-z]+$|', $word)) {
                $email = $word;
            } else {
                $names[] = $word;
            }
        }
        if (count($names) > 1) {
            $lastname = array_pop($names);
        }
        if (in_array(strtolower($names[count($names) - 1]),['de','di'])) {
            $lastname = array_pop($names).' '.$lastname;
        }
        $firstname = implode(' ', $names);
        if (!$email) $error = 'no valid email';
        if (!$firstname) $error = 'no first name';
        if (!$lastname) $error = 'no last name';
        if ($error) return null;
        return self::createInvitation($firstname, $lastname, $email, null);
    }

    static public function checkExistingEmail($email)
    {
        if ($user = User::whereEmail($email)->first()) return $user;
        return false;
    }

    static public function checkExistingName($firstname, $lastname)
    {
        if ($user = User::whereName($firstname . ' ' . $lastname)->first()) return $user;
        return false;
    }

    static public function createInvitation($firstname, $lastname, $email, $convention_id)
    {
        $invitation = new self([
            'firstname' => ucfirst($firstname),
            'lastname' => ucfirst($lastname),
            'email' => $email,
            'convention_id' => $convention_id,
            'status' => 'created',
        ]);
        $invitation->expiration = now()->addDays(30);
        $invitation->token = uniqid();
        return $invitation;
    }

    public function send()
    {
        $user = new User();
        $user->name = $this->firstname . ' ' . $this->lastname;
        $user->firstname = $this->firstname;
        $user->lastname  = $this->lastname;
        $user->email     = $this->email;
        Mail::to($user)->send(new Invito($user, $this->token, $this->convention));
    }

    static public function check($request, $check_email = false)
    {
        if (
            !isset($request->token)
            || !($invitation = Invitation::where('token', '=', $request->token)->first())
        ) throw new \Exception('invalid');
        if ($check_email && (!isset($request->email) || $invitation->email != $request->email)) throw new Exception('invalid');
        if ($invitation->status != 'created') throw new Exception('registered');
        if (self::checkExistingEmail($invitation->email)) throw new Exception('registered');
        return $invitation;
    }

    public function confirm()
    {
        $user = new User();
        $user->name = $this->firstname . ' ' . $this->lastname;
        $user->firstname = $this->firstname;
        $user->lastname  = $this->lastname;
        $user->email     = $this->email;
        $user->password  = '';
        $user->email_verified = true;
        $user->remember_token = md5(uniqid());
        $user->save();

        $this->status = 'accepted';
        $this->save();

        $annata = Annata::corrente();
        if (in_array($annata->fase(), ['fase1', 'fase2'])) {
            $user->sendAccess(false); 
        }
        return $user;
    }


    /* RELATIONS */

    public function convention()
    {
        return $this->belongsTo(Convention::class);
    }
}
