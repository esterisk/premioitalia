<?php

namespace App\Providers;
use Form;

use Illuminate\Support\ServiceProvider;

class FormGroupServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Form::component('textGroup','components.text', ['params','errors']);
        Form::component('submitGroup','components.submit', ['params']);
        Form::component('selectGroup','components.select', ['params','errors']);
        Form::component('hiddenGroup','components.hidden', ['params','errors']);
        Form::component('textareaGroup','components.textarea', ['params','errors']);
        Form::component('radioGroup','components.radiocheckbox', ['params','errors','type'=>'radio']);
        Form::component('checkboxGroup','components.radiocheckbox', ['params','errors','type'=>'checkbox']);
        Form::component('nazioneGroup','components.nazione', ['params','errors','type'=>'checkbox']);
        Form::component('captchaGroup','components.captcha', ['params','errors']);
        Form::component('menuLine','components.menu', ['params']);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
