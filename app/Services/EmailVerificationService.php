<?php

namespace App\Services;

use App\Exceptions\Api\ApiException;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Auth\Events\Verified;
use Symfony\Component\HttpFoundation\Response;

class EmailVerificationService
{
    public function __construct(private UserRepositoryInterface $users) {}

    /**
     * @return array{message_key: string, already_verified: bool}
     *
     * @throws ApiException
     */
    public function verify(int $userId, string $hash): array
    {
        /** @var User $user */
        $user = $this->users->findOrFail($userId);

        if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            throw new ApiException('email_verification.invalid_link', Response::HTTP_FORBIDDEN);
        }

        if ($user->hasVerifiedEmail()) {
            return ['message_key' => 'email_verification.already_verified', 'already_verified' => true];
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return ['message_key' => 'email_verification.verified', 'already_verified' => false];
    }

    /**
     * @throws ApiException
     */
    public function resendFor(User $user): string
    {
        if ($user->hasVerifiedEmail()) {
            throw new ApiException('email_verification.already_verified', Response::HTTP_BAD_REQUEST);
        }

        $user->sendEmailVerificationNotification();

        return 'email_verification.resent';
    }
}
