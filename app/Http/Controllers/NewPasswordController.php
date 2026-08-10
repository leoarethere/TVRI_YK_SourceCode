<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class NewPasswordController extends Controller
{
    /**
     * Menampilkan tampilan reset kata sandi.
     */
    public function create(Request $request)
    {
        return view('auth.reset-password', [
            'request' => $request,
            'title' => 'Reset Kata Sandi',
        ]);
    }

    /**
     * Menangani permintaan kata sandi baru yang masuk.
     */
    public function store(Request $request)
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

                $this->revokeDatabaseSessions($user);
                event(new PasswordReset($user));

                $loginRoute = $user->isPegawai() ? 'pegawai.login' : 'login';
            }
        );

        return $status == Password::PASSWORD_RESET
            ? redirect()->route($loginRoute)->with('success', __($status))
            : back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => [__('passwords.invalid')]]);
    }

    /**
     * Hapus seluruh sesi database milik pengguna setelah kata sandi direset.
     */
    private function revokeDatabaseSessions(User $user): void
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        DB::connection(config('session.connection'))
            ->table(config('session.table', 'sessions'))
            ->where('user_id', $user->getAuthIdentifier())
            ->delete();
    }
}
