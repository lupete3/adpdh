<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Services\ContactDelivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;

class ContactController extends Controller
{
    public function show()
    {
        return view('adpdh.contact', [
            'settings' => DB::table('settings')->where('key', 'like', 'adpdh.%')->pluck('value', 'key'),
            'footerSections' => CmsPage::where('key', 'index')->first()?->sections()->with('contents')->get() ?? collect(),
        ]);
    }

    public function store(Request $request, ContactDelivery $delivery)
    {
        $key = 'contact:'.hash('sha256', (string) $request->ip());
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return redirect()->route('contact')->withInput($request->only('name', 'email', 'subject', 'message'))
                ->withErrors(['delivery' => 'Trop de tentatives. Veuillez patienter quelques minutes avant de réessayer.']);
        }
        RateLimiter::hit($key, 600);

        $data = $request->validate(ContactDelivery::RULES, [
            'required' => 'Ce champ est obligatoire.',
            'email' => 'Saisissez une adresse e-mail valide.',
            'min' => 'Ce champ doit contenir au moins :min caractères.',
            'max' => 'Ce champ ne doit pas dépasser :max caractères.',
            'not_regex' => 'Ce champ ne doit pas contenir de saut de ligne.',
        ]);

        try {
            $delivery->send($data);
        } catch (Throwable $exception) {
            // Avoid logging message content, visitor details or SMTP credentials.
            Log::error('Contact email delivery failed.', ['exception_type' => get_class($exception)]);

            return redirect()->route('contact')->withInput($data)->withErrors([
                'delivery' => 'Votre message n’a pas pu être envoyé. Réessayez plus tard ou écrivez directement à contact@adpdh.org.',
            ]);
        }

        return redirect()->route('contact')->with('success', 'Votre message a été transmis à notre service de messagerie. Merci de nous avoir contactés.');
    }
}
