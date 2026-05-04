<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    private Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    // ── Configuración inicial: muestra QR ─────────────────────────────────────

    public function setup(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->two_factor_confirmed_at) {
            return redirect()->route('admin.profile.edit')
                ->with('info', 'La autenticación de dos factores ya está activa.');
        }

        // Generar secreto si no existe aún
        if (!$user->two_factor_secret) {
            $secret = $this->google2fa->generateSecretKey();
            $user->update(['two_factor_secret' => Crypt::encryptString($secret)]);
        } else {
            $secret = Crypt::decryptString($user->two_factor_secret);
        }

        $qrUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return view('auth.two-factor-setup', compact('qrUrl', 'secret'));
    }

    // ── Confirmar código y activar 2FA ────────────────────────────────────────

    public function enable(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|digits:6']);

        $user   = $request->user();
        $secret = Crypt::decryptString($user->two_factor_secret);

        if (!$this->google2fa->verifyKey($secret, $request->code)) {
            return back()->withErrors(['code' => 'Código incorrecto. Intente de nuevo.']);
        }

        $user->update(['two_factor_confirmed_at' => now()]);

        return redirect()->route('admin.profile.edit')
            ->with('success', 'Autenticación de dos factores activada correctamente.');
    }

    // ── Desactivar 2FA ────────────────────────────────────────────────────────

    public function disable(Request $request): RedirectResponse
    {
        $request->validateWithBag('twoFactorDisable', ['password' => 'required|current_password']);

        $request->user()->update([
            'two_factor_secret'       => null,
            'two_factor_confirmed_at' => null,
        ]);

        return redirect()->route('admin.profile.edit')
            ->with('success', 'Autenticación de dos factores desactivada.');
    }

    // ── Pantalla de verificación tras login ───────────────────────────────────

    public function challenge(): View|RedirectResponse
    {
        if (session('two_factor_authenticated')) {
            return redirect()->intended(route('dashboard'));
        }

        if (!session('two_factor_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    // ── Validar código en el challenge ────────────────────────────────────────

    public function verify(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|digits:6']);

        $userId = session('two_factor_user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $user   = \App\Models\User::findOrFail($userId);
        $secret = Crypt::decryptString($user->two_factor_secret);

        if (!$this->google2fa->verifyKey($secret, $request->code)) {
            return back()->withErrors(['code' => 'Código incorrecto.']);
        }

        // Completar el login
        Auth::login($user);
        session()->forget('two_factor_user_id');
        session(['two_factor_authenticated' => true]);

        return redirect()->intended(route('dashboard'));
    }
}
