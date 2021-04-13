<?php
setlocale(LC_ALL, 'it_IT');
View::share('annata', \App\Annata::corrente());

Route::get('/', 'HomeController@home')->name('home');
Route::get('/login', 'LoginController@login')->name('login');
Route::get('/register', 'LoginController@login')->name('register');
Route::get('/entra/{id}/{token}', 'LoginController@byemail')->name('login-by-email');
Route::get('/unsubscribe/{id}/{token}', 'LoginController@unsubscribe')->name('unsubscribe');
//Route::get('/api/redo1', 'VoteController@redo1')->name('login-by-email');

/*
Route::get('/elettore/ammissione', 'ElettoreController@admission')->name('admission');
Route::get('/elettore/conferma', 'ElettoreController@confirm')->name('confirm');

Route::get('/proposte', 'ProposteController@gallery')->name('proposals');
Route::get('/proposte/inserisci', 'ProposteController@create')->name('proposals-create');
Route::post('/proposte/invia', 'ProposteController@save')->name('proposals-save');

Route::get('/categorie', 'PagesController@category')->name('category');
Route::get('/regolamento', 'PagesController@rules')->name('rules');
Route::get('/privacy', 'PagesController@privacy')->name('privacy');
Route::get('/albo', 'PagesController@roll')->name('roll');
*/

Route::get('/api/manda-invito', 'NotificationController@mandaInvitoSingolo')->name('manda-invito');

Route::group([ 'middleware' => ['api']], function() {
	Route::post('/api/manda-invito/tutti', 'NotificationController@mailingInviti')->name('manda-invito-tutti');
	Route::post('/api/manda-sollecito', 'NotificationController@mailingSollecito')->name('manda-sollecito');
	Route::post('/api/calcola/vincitori', 'ApiController@calcolaVincitori');
	Route::post('/api/salva/vincitori', 'ApiController@salvaVincitori');
});

Route::group([ 'middleware' => ['web','auth']], function() {
//	Route::get('/elettore', 'ElettoreController@profile')->name('profile');
//	Route::get('/elettore/rinuncia', 'ElettoreController@dismiss')->name('dismiss');
	
	Route::get('/voto', 'VoteController@vote')->name('vote');
	Route::get('/voto/conferma', 'VoteController@confirm')->name('vote-confirm');
	Route::post('/voto', 'VoteController@save')->name('vote');
	Route::get('/logout', 'LoginController@logout')->name('logout');
	Route::get('/api/convention/{convention_id}/{no?}', 'ApiController@participate')->name('participate');	
	
	Route::get('/calcolo-finale', 'VoteController@finale');
});

Route::get('/home', function() { return Redirect::to('/'); } );
Route::get('/privacy', 'PagesController@privacy')->name('privacy');
Route::get('/comitato', 'PagesController@comitato')->name('comitato');
Route::get('/storia', 'PagesController@storia')->name('storia');
Route::get('/sistema-di-voto', 'PagesController@sistemavoto')->name('sistemavoto');
Route::get('/come-candidarsi', 'PagesController@comecandidarsi')->name('comecandidarsi');
Route::get('/chi-vota', 'PagesController@chivota')->name('chivota');
Route::get('/calendario', 'PagesController@calendario')->name('calendario');
Route::get('/regolamento/{vers?}', 'PagesController@regolamento')->name('regolamento');
Route::get('/regolamento.html', function() { return Redirect::route('regolamento'); } );
Route::get('/finalisti', 'PagesController@finalisti')->name('finalisti');
Route::get('/italcon', 'PagesController@italcon')->name('italcon');
Route::get('/albo', 'PagesController@albo')->name('albo');
Route::get('/albo/{anno?}', 'PagesController@alboAnno')->name('albo-anno')->where('anno','[0-9]+');
Route::get('/albo/{raggruppamento?}', 'PagesController@alboRaggruppamento')->name('albo-raggruppamento')->where('raggruppamento','[a-z-]+');
Route::get('/richiesta', 'LoginController@richiesta')->name('richiesta');
Route::post('/richiesta', 'LoginController@richiesta-salva');

Route::get('/candidature', 'CandidaturaController@index')->name('candidature');
Route::get('/candidature/{categoria}', 'CandidaturaController@categoria')->name('candidature-categoria');
Route::post('/candidature/{categoria}', 'CandidaturaController@inserisci')->name('candidature-categoria-post');

Route::get('/donazione/grazie', 'PagesController@grazie')->name('grazie');
Route::get('/donazione', 'PagesController@donazione')->name('donazione');
