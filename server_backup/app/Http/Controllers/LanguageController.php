<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switchLang($lang)
    {
        $availableLocales = ['zh_TW', 'zh_CN', 'en'];
        
        if (in_array($lang, $availableLocales)) {
            Session::put('locale', $lang);
            app()->setLocale($lang);
        }
        
        return redirect()->back();
    }
}
