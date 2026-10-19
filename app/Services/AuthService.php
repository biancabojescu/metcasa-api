<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Token-uri pentru sesiuni "fara remember-me" expira in 24h.
     * Cu remember-me bifat: token fara expirare (ramane valid pana la logout).
     */
    private const DEFAULT_TOKEN_TTL_HOURS = 24;

    public function __construct(private UserRepositoryInterface $users) {}

    /**
     * @return array{user: User, token: string, expires_at: ?string}
     */
    public function register(array $data, string $deviceName = 'web', bool $remember = true): array
    {
        /** @var User $user */
        $user = $this->users->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone' => $data['phone'] ?? null,
        ]);

        event(new Registered($user));

        return $this->issueToken($user, $deviceName, $remember);
    }

    /**
     * @return array{user: User, token: string, expires_at: ?string}
     *
     * @throws ValidationException
     */
    public function login(string $email, string $password, string $deviceName = 'web', bool $remember = false): array
    {
        $user = $this->users->findByEmail($email);

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('responses.auth.invalid_credentials')],
            ]);
        }

        return $this->issueToken($user, $deviceName, $remember);
    }

    /**
     * @return array{user: User, token: string, expires_at: ?string}
     */
    private function issueToken(User $user, string $deviceName, bool $remember): array
    {
        $expiresAt = $remember ? null : Carbon::now()->addHours(self::DEFAULT_TOKEN_TTL_HOURS);

        $token = $user->createToken($deviceName, ['*'], $expiresAt);

        return [
            'user' => $user,
            'token' => $token->plainTextToken,
            'expires_at' => $expiresAt?->toIso8601String(),
        ];
    }

    public function logoutCurrentToken(User $user): void
    {
        $token = $user->currentAccessToken();
        if ($token instanceof \Laravel\Sanctum\PersonalAccessToken) {
            $token->delete();
        }
    }
}
