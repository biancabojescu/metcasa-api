<?php

namespace App\Services;

use App\Exceptions\Api\ApiException;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class PasswordResetService
{
    /**
     * Trimite link de resetare. Pentru securitate, raspunsul e mereu pozitiv
     * indiferent daca emailul exista in baza - astfel nu permitem enumerare de conturi.
     */
    public function sendResetLink(string $email): string
    {
        $status = Password::sendResetLink(['email' => $email]);

        if ($status === Password::RESET_THROTTLED) {
            throw new ApiException('password_reset.throttled', Response::HTTP_TOO_MANY_REQUESTS);
        }

        // RESET_LINK_SENT sau INVALID_USER - oricum returnam acelasi mesaj.
        return 'password_reset.link_sent';
    }

    /**
     * @throws ApiException
     */
    public function reset(string $email, string $token, string $password): string
    {
        $status = Password::reset(
            ['email' => $email, 'password' => $password, 'token' => $token],
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                $user->tokens()->delete();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return 'password_reset.success';
        }

        throw new ApiException('password_reset.invalid_token', Response::HTTP_BAD_REQUEST);
    }
}
