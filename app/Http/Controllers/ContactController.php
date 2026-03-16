<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function refreshCaptcha(Request $request): JsonResponse
    {
        $this->regenerateMathCaptcha($request);

        return response()
            ->json([
                'question' => $request->session()->get('contact_captcha_question'),
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'captcha_answer' => ['required', 'integer'],
        ]);

        $expectedAnswer = (int) $request->session()->get('contact_captcha_answer', -1);
        $providedAnswer = (int) $request->input('captcha_answer');

        if ($providedAnswer !== $expectedAnswer) {
            $this->regenerateMathCaptcha($request);

            return back()
                ->withErrors(['captcha_answer' => 'Incorrect math captcha answer. Please try again.'])
                ->withInput($request->except('captcha_answer'));
        }

        Log::info('Contact form submitted', [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'message_length' => strlen((string) $request->input('message')),
            'ip' => $request->ip(),
        ]);

        $this->regenerateMathCaptcha($request);

        return redirect()
            ->route('contact')
            ->with('success', 'Thank you for contacting us. We will get back to you soon.');
    }

    private function regenerateMathCaptcha(Request $request): void
    {
        $left = random_int(1, 9);
        $right = random_int(1, 9);

        $request->session()->put('contact_captcha_question', "{$left} + {$right}");
        $request->session()->put('contact_captcha_answer', $left + $right);
    }
}
