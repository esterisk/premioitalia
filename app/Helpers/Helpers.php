<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

function _current_user()
{
    /** @var App\Models\User $user */
    $user = Auth::user();
    return $user;
}

function _current_userid()
{
    if ($user = _current_user()) return $user->getKey();
    else return null;
}

function euro($number)
{
    return Number::format($number, precision: 2, locale: 'it_IT');
}

function _date($date, $format)
{
    return Carbon::parse($date)->locale(config('app.locale'))->translatedFormat($format);
}

function _dateAdd($date, $add)
{
    return Carbon::parse($date)->addDays($add)->format('Y-m-d');
}

function _dmY($date = null, $zeros = true)
{
    if (!$date) $date = date('Y-m-d');
    if ($zeros) return _date($date, 'd/m/Y');
    else return _date($date, 'j/n/Y');
}

function _d_mm_Y($date = null, $zeros = true)
{
    return _date($date, 'j F Y');
}

function _d_mm($date = null, $zeros = true)
{
    return _date($date, 'j F');
}

function _relativeDate($when, ?string $timezone = null): string
{
    $tz  = $timezone ?: config('app.timezone', 'Europe/Rome');
    $dt  = $when instanceof Carbon
        ? $when->copy()->tz($tz)
        : Carbon::parse($when)->tz($tz);

    $now = Carbon::now($tz);

    // Oggi → solo ora
    if ($dt->isSameDay($now)) {
        return $dt->format('H:i');
    }

    // Ieri → "ieri alle HH:mm"
    if ($dt->isYesterday()) {
        return 'ieri alle ' . $dt->format('H:i');
    }

    // Stesso anno → "d/m alle HH:mm"
    if ($dt->isSameYear($now)) {
        return $dt->format('j/n') . ' alle ' . $dt->format('H:i');
    }

    // Anno diverso → "d/m/Y alle HH:mm"
    return $dt->format('j/n/Y') . ' alle ' . $dt->format('H:i');
}

function _Hi($when, $zeros = true)
{
    $tz  = config('app.timezone', 'Europe/Rome');
    $dt  = $when instanceof Carbon
        ? $when->copy()->tz($tz)
        : Carbon::parse($when, $tz)->tz($tz);

    return $dt->format('H:i');
}

function noP($string)
{
    return preg_replace('#</?p>#', '', $string);
}

function br2p($string)
{
    return preg_replace('#<p>\&nbsp;</p>#', '', 
        preg_replace('#<p> *</p>#', '', 
        preg_replace('#<br\s*/?>#', '</p><p>', 
        $string)));
}

function _urlDomain($url)
{
    return explode('/', $url)[2];
}

function _dateNull($date)
{
    return !$date || $date == '0000-00-00';
}

/**
 * Implodes an array into a string with a given separator, excluding empty values.
 *
 * @param array $array The array to implode.
 * @param string $separator The separator to use between array elements. Default is ', '.
 * @return string The imploded string.
 */

function _implodeExisting(array $array, string $separator = ', '): string
{
    return implode($separator, array_filter($array));
}

/**
 * Retrieves a shared view data item by its key.
 *
 * This function accesses the shared data in the view environment and returns the value
 * associated with the specified key.
 *
 * @param string $key The key of the shared data item to retrieve.
 * @return mixed The value of the shared data item corresponding to the given key.
 */

function _shared($key)
{
    static $data = null;
    if (!$data) $data = View::getShared();
    return $data[$key];
}

function _rayq($on = true)
{
    if ($on) ray()->showQueries();
    else ray()->stopShowingQueries();
}

function _route($withDomain = false)
{
    $route = URL::realCurrentRoute();
    if (!$withDomain) {
        $route = str_replace(_site('routes'),'',$route);
    }
    return $route;
}