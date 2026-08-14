<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserSessionService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    public function __construct(private readonly UserSessionService $sessions) {}

    /**
     * Menampilkan tampilan reset kata sandi.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', [
            'request' => $request,
            'title' => 'Reset Kata Sandi',
        ]);
    }

    /**
     * Menangani permintaan kata sandi baru yang masuk.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => [
                'required',
                'confirmed',
                PasswordRule::min(8)->letters()->mixedCase()->numbers()->symbols(),
            ],
        ]);

        $loginRoute = 'login';

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) use (&$loginRoute) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                $this->sessions->revokeAll($user);
                event(new PasswordReset($user));

                $loginRoute = $user->isPegawai() ? 'pegawai.login' : 'login';
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route($loginRoute)->with('success', __($status))
            : back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => [__('passwords.invalid')]]);
    }
}
