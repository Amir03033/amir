<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switch(Request $request)
    {
        $locale = $request->input('locale', 'nl');

        if (in_array($locale, ['nl', 'en'])) {
            Session::put('locale', $locale);
            App::setLocale($locale);
        }

        // Geef alle vertalingen terug zodat Alpine.js de UI direct kan updaten
        return response()->json([
            'success' => true,
            'locale' => $locale,
            'messages' => trans('messages')
        ]);
    }
}