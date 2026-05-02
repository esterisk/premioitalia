<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Annata;
use App\Models\User;
use App\Models\Voto;

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
		$inviate = 0;
		$time = time();
		try {
			$email = trim($request->email);
			if (!$email) throw new \Exception('missing-email');
			if (!($user = User::findByEmail($email))) throw new \Exception('email-not-found');
			if (!$user->valid()) throw new \Exception('user-invalid');
			$inviate += $user->sendAccess(false);
			$result = ['status' => 'success', 'inviate' => $inviate, 'tempo' => $time, 'invii_per_minuto' => $inviate ? round($inviate / $time * 60, 2) : 0];
		} catch (\Exception $e) {
			$result = ['status' => 'error', 'error' => $e->getMessage()];
		}

		return response()->json($result);
	}

	public function mailingInviti()
	{
		$inviate = 0;
		$time = time();
		$elettori = User::isValid()->get();
		foreach ($elettori as $user) {
			$inviate += $user->sendAccess(true);
		}
		$time = time() - $time;
		$result = ['status' => 'success', 'inviate' => $inviate, 'tempo' => $time, 'invii_per_minuto' => $inviate ? round($inviate / $time * 60, 2) : 0];
		return response()->json($result);
	}

	public function mailingSollecito()
	{
		$inviate = 0;
		$time = time();
		$annata = Annata::corrente();

		$voti_non_inviati = Voto::whereAnno($annata->anno)->whereFase($annata->fase())->whereStato('preparazione')->pluck('user_id');
		$elettori = User::isValid()->whereIn('id', $voti_non_inviati)->get();

		if (!empty(request()->email)) {
			if ($user = User::findByEmail(request()->email)) $user->sendSollecitoInvio(true);
		} else {
			foreach ($elettori as $user) {
				$inviate += $user->sendSollecitoInvio(true);
			}
		}

		$time = time() - $time + 1;
		$result = ['status' => 'success', 'inviate' => $inviate, 'tempo' => $time, 'invii_per_minuto' => $inviate ? round($inviate / $time * 60, 2) : 0];
		return response()->json($result);
	}

	public function mailingSpecialNotice()
	{
		$inviate = 0;
		$time = time();
		$annata = Annata::corrente();

		$voti_inviati = Voto::whereAnno($annata->anno)->whereFase($annata->fase())->whereStato('inviato')->pluck('user_id');
		$elettori = User::isValid()->whereIn('id', $voti_inviati)->get();

		foreach ($elettori as $user) {
			$inviate += $user->sendSpecialNotice(true);
		}
		$time = time() - $time;
		$result = ['status' => 'success', 'inviate' => $inviate, 'tempo' => $time, 'invii_per_minuto' => $inviate ? round($inviate / $time * 60, 2) : 0];
		return response()->json($result);
	}

	public function pixel($u, $m)
	{
		$user_id = intval($u);
		if ($m && $user_id && ($user = User::find($user_id))) {
			$user->invitation_open = $m;
			$user->save();
		}
		return response()->file(public_path('images/pixel.gif'), ['Content-Type' => 'image/gif']);
	}
}
