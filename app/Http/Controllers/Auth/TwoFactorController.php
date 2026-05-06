<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    private const RECOVERY_CODE_COUNT  = 8;
    private const RECOVERY_CODE_LENGTH = 10; // 5+hyphen+5

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

    // ── Confirmar código y activar 2FA (genera códigos de recuperación) ───────

    public function enable(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|digits:6']);

        $user   = $request->user();
        $secret = Crypt::decryptString($user->two_factor_secret);

        if (!$this->google2fa->verifyKey($secret, $request->code, 1)) {
            Log::warning('2FA enable failed: wrong code', ['user_id' => $user->id, 'ip' => $request->ip()]);
            return back()->withErrors(['code' => 'Código incorrecto. Intenta de nuevo.']);
        }

        $plainCodes  = $this->generateRecoveryCodes();
        $hashedCodes = array_map(fn ($c) => hash('sha256', $c), $plainCodes);

        $user->update([
            'two_factor_confirmed_at'   => now(),
            'two_factor_recovery_codes' => $hashedCodes,
        ]);

        // Los códigos en texto plano se muestran UNA sola vez vía sesión flash
        return redirect()
            ->route('two-factor.recovery-codes')
            ->with('recovery_codes', $plainCodes);
    }

    // ── Mostrar códigos de recuperación (solo después de activar) ─────────────

    public function showRecoveryCodes(Request $request): View|RedirectResponse
    {
        if (!session()->has('recovery_codes')) {
            return redirect()->route('admin.profile.edit');
        }

        return view('auth.two-factor-recovery-codes', [
            'codes' => session('recovery_codes'),
        ]);
    }

    // ── Regenerar códigos de recuperación ────────────────────────────────────

    public function regenerateRecoveryCodes(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (!$user->two_factor_confirmed_at) {
            return redirect()->route('admin.profile.edit');
        }

        $plainCodes  = $this->generateRecoveryCodes();
        $hashedCodes = array_map(fn ($c) => hash('sha256', $c), $plainCodes);

        $user->update(['two_factor_recovery_codes' => $hashedCodes]);

        Log::info('2FA recovery codes regenerated', ['user_id' => $user->id]);

        return redirect()
            ->route('two-factor.recovery-codes')
            ->with('recovery_codes', $plainCodes);
    }

    // ── Desactivar 2FA ────────────────────────────────────────────────────────

    public function disable(Request $request): RedirectResponse
    {
        $request->validateWithBag('twoFactorDisable', ['password' => 'required|current_password']);

        $request->user()->update([
            'two_factor_secret'         => null,
            'two_factor_confirmed_at'   => null,
            'two_factor_recovery_codes' => null,
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

    // ── Validar código TOTP en el challenge ───────────────────────────────────

    public function verify(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|digits:6']);

        $userId = session('two_factor_user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $user   = \App\Models\User::findOrFail($userId);
        $secret = Crypt::decryptString($user->two_factor_secret);

        if (!$this->google2fa->verifyKey($secret, $request->code, 1)) {
            Log::warning('2FA challenge failed: wrong code', ['user_id' => $user->id, 'ip' => $request->ip()]);
            return back()->withErrors(['code' => 'Código incorrecto.']);
        }

        $this->completeLogin($user);

        return redirect()->intended(route('dashboard'));
    }

    // ── Validar código de recuperación en el challenge ────────────────────────

    public function verifyRecovery(Request $request): RedirectResponse
    {
        $request->validate(['recovery_code' => 'required|string|max:12']);

        $userId = session('two_factor_user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $user          = \App\Models\User::findOrFail($userId);
        $storedHashes  = $user->two_factor_recovery_codes ?? [];
        $inputHash     = hash('sha256', strtoupper(str_replace('-', '', $request->recovery_code)));

        // Buscar el código entre los hashes almacenados (resistente a timing)
        $matchedIndex = null;
        foreach ($storedHashes as $i => $hash) {
            if (hash_equals($hash, $inputHash)) {
                $matchedIndex = $i;
                break;
            }
        }

        if ($matchedIndex === null) {
            Log::warning('2FA recovery code invalid', ['user_id' => $user->id, 'ip' => $request->ip()]);
            return back()->withErrors(['recovery_code' => 'Código de recuperación inválido.']);
        }

        // Invalidar el código usado (uso único)
        unset($storedHashes[$matchedIndex]);
        $user->update(['two_factor_recovery_codes' => array_values($storedHashes)]);

        Log::warning('2FA recovery code used', [
            'user_id'   => $user->id,
            'ip'        => $request->ip(),
            'remaining' => count($storedHashes),
        ]);

        $this->completeLogin($user);

        $remaining = count($storedHashes);
        return redirect()->intended(route('dashboard'))
            ->with('warning', "Accediste con un código de recuperación. Te quedan {$remaining}. Genera nuevos en tu perfil.");
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function completeLogin(\App\Models\User $user): void
    {
        Auth::login($user);
        session()->forget('two_factor_user_id');
        session(['two_factor_authenticated' => true]);
    }

    private function generateRecoveryCodes(): array
    {
        return array_map(function () {
            $part1 = strtoupper(Str::random(5));
            $part2 = strtoupper(Str::random(5));
            return "{$part1}-{$part2}";
        }, range(1, self::RECOVERY_CODE_COUNT));
    }
}
