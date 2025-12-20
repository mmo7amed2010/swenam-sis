<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switchLanguage($locale)
    {
        $availableLocales = ['en', 'ar'];

        if (! in_array($locale, $availableLocales)) {
            abort(404);
        }

        Session::put('locale', $locale);
        App::setLocale($locale);

        return redirect()->back();
    }
}
