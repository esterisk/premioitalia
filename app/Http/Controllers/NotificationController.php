<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Annata;
use App\User;
use App\Voto;

ini_set('memory_limit', '2048M');
set_time_limit(3600);

class NotificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function mandaInvitoSingolo(Request $request)
    {
		$inviate = 0; $time = time();
		try {
			$email = trim($request->email);
			if (!$email) throw new \Exception('missing-email');
			if (!($user = User::findByEmail($email))) throw new \Exception('email-not-found');
			if (!$user->valid()) throw new \Exception('user-invalid');
			$inviate += $user->sendInvitation(false);
			$result = [ 'status' => 'success', 'inviate' => $inviate, 'tempo' => $time, 'invii_per_minuto' => $inviate ? round($inviate/$time*60,2) : 0 ];
		} catch (\Exception $e) {
			$result = [ 'status' => 'error', 'error' => $e->getMessage() ];
		}

        return response()->json($result);
    }
    
	public function mailingInviti()
	{
		$inviate = 0; $time = time();
		$elettori = User::isValid()->get();
		foreach ($elettori as $user) {
			$inviate += $user->sendInvitation(true);
		}
		$time = time() - $time;
		$result = [ 'status' => 'success', 'inviate' => $inviate, 'tempo' => $time, 'invii_per_minuto' => $inviate ? round($inviate/$time*60,2) : 0 ];
        return response()->json($result);
	}

	public function mailingSollecito()
	{
		$inviate = 0; $time = time();
		$annata = Annata::corrente();
		
		$voti_non_inviati = Voto::whereAnno($annata->anno)->whereFase($annata->fase())->whereStato('preparazione')->pluck('user_id');
		$elettori = User::isValid()->whereIn('id',$voti_non_inviati)->get();

		foreach ($elettori as $user) {
			$inviate += $user->sendSollecitoInvio(true);
		}
		$time = time() - $time;
		$result = [ 'status' => 'success', 'inviate' => $inviate, 'tempo' => $time, 'invii_per_minuto' => $inviate ? round($inviate/$time*60,2) : 0 ];
        return response()->json($result);
	}
    
}
