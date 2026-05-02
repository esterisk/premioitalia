<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CandidaturaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Auth::routes();

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

setlocale(LC_ALL, 'it_IT');

Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::get('/register', [LoginController::class, 'login'])->name('register');
Route::get('/entra/{id}/{token}', [LoginController::class, 'byemail'])->name('login-by-email');
Route::get('/unsubscribe/{id}/{token}', [LoginController::class, 'unsubscribe'])->name('unsubscribe');
Route::post('/unsubscribe', [LoginController::class, 'unsubscribeConfirm'])->name('unsubscribe-confirm');

Route::get('/api/invito-iscrizione', [InvitationController::class, 'send'])->name('invito-iscrizione');
Route::get('/iscrizione', [RegisterController::class, 'confirm'])->name('iscrizione');
Route::post('/iscrizione/conferma', [RegisterController::class, 'store'])->name('iscrizione.conferma');

//Route::get('/api/redo1', 'VoteController@redo1')->name('login-by-email');

/*
Route::get('/elettore/ammissione', [ ElettoreController::class, 'admission' ])->name('admission');
Route::get('/elettore/conferma', [ ElettoreController::class, 'confirm' ])->name('confirm');

Route::get('/proposte', [ ProposteController::class, 'gallery' ])->name('proposals');
Route::get('/proposte/inserisci', [ ProposteController::class, 'create' ])->name('proposals-create');
Route::post('/proposte/invia', [ ProposteController::class, 'save' ])->name('proposals-save');

Route::get('/categorie', [ PagesController::class, 'category' ])->name('category');
Route::get('/regolamento', [ PagesController::class, 'rules' ])->name('rules');
Route::get('/privacy', [ PagesController::class, 'privacy' ])->name('privacy');
Route::get('/albo', [ PagesController::class, 'roll' ])->name('roll');
*/

Route::get('/api/manda-invito', [NotificationController::class, 'mandaInvitoSingolo'])->name('manda-invito');

Route::group(['middleware' => ['api']], function () {
    Route::post('/api/manda-invito/tutti', [NotificationController::class, 'mailingInviti'])->name('manda-invito-tutti');
    Route::post('/api/manda-sollecito', [NotificationController::class, 'mailingSollecito'])->name('manda-sollecito');
    Route::post('/api/calcola/vincitori', [ApiController::class, 'calcolaVincitori']);
    Route::post('/api/salva/vincitori', [ApiController::class, 'salvaVincitori']);
    Route::post('/api/pubblica/risultati', [ApiController::class, 'pubblicaRisultati']);
    Route::post('/api/consolida/albo', [ApiController::class, 'consolidaAlbo']);
    Route::post('/api/albonome/raccogli', [ApiController::class, 'raccogliAlboNome']);
    Route::post('/api/manda-avviso-speciale', [NotificationController::class, 'mailingSpecialNotice'])->name('manda-avviso-speciale');
});

Route::group(['middleware' => ['web', 'auth']], function () {
    //	Route::get('/elettore', [ ElettoreController::class, 'profile' ])->name('profile');
    //	Route::get('/elettore/rinuncia', [ ElettoreController::class, 'dismiss' ])->name('dismiss');

    Route::get('/voto', [VoteController::class, 'vote'])->name('vote');
    Route::get('/voto/conferma', [VoteController::class, 'confirm'])->name('vote-confirm');
    Route::post('/voto', [VoteController::class, 'save'])->name('vote.store');
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/api/convention/{convention_id}/{no?}', [ApiController::class, 'participate'])->name('participate');

    Route::get('/calcolo-finale/{anno?}', [VoteController::class, 'finale']);
});

Route::get('/home', function () {
    return redirect()->to('/');
});
Route::get('/privacy', [PagesController::class, 'privacy'])->name('privacy');
Route::get('/comitato', [PagesController::class, 'comitato'])->name('comitato');
Route::get('/storia', [PagesController::class, 'storia'])->name('storia');
Route::get('/sistema-di-voto', [PagesController::class, 'sistemavoto'])->name('sistemavoto');
Route::get('/come-candidarsi', [PagesController::class, 'comecandidarsi'])->name('comecandidarsi');
Route::get('/chi-vota', [PagesController::class, 'chivota'])->name('chivota');
Route::get('/calendario', [PagesController::class, 'calendario'])->name('calendario');
Route::get('/regolamento/{vers?}', [PagesController::class, 'regolamento'])->name('regolamento');
Route::get('/regolamento.html', function () {
    return redirect()->route('regolamento');
});
Route::get('/finalisti', [PagesController::class, 'finalisti'])->name('finalisti');
Route::get('/italcon', [PagesController::class, 'italcon'])->name('italcon');
Route::get('/albo', [PagesController::class, 'albo'])->name('albo');
Route::get('/albo/{anno?}', [PagesController::class, 'alboAnno'])->name('albo-anno')->where('anno', '[0-9]+');
Route::get('/albo/{raggruppamento?}', [PagesController::class, 'alboRaggruppamento'])->name('albo-raggruppamento')->where('raggruppamento', '[a-z-]+');
Route::get('/richiesta', [LoginController::class, 'richiesta'])->name('richiesta');
Route::post('/richiesta', [LoginController::class, 'richiesta-salva']);

Route::get('/candidature', [CandidaturaController::class, 'index'])->name('candidature');
Route::get('/candidature/{categoria}', [CandidaturaController::class, 'categoria'])->name('candidature-categoria');
Route::post('/candidature/{categoria}', [CandidaturaController::class, 'inserisci'])->name('candidature-categoria-post');

Route::get('/donazione/grazie', [PagesController::class, 'grazie'])->name('grazie');
Route::get('/donazione', [PagesController::class, 'donazione'])->name('donazione');
Route::get('/pixel/{u}/{m}', [NotificationController::class, 'pixel'])->name('pixel');
