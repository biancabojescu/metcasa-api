<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    public const AVATAR_DISK = 'public';

    public const AVATAR_DIR = 'avatars';

    public function __construct(private UserRepositoryInterface $users) {}

    public function update(User $user, array $data, ?UploadedFile $avatar, bool $removeAvatar): User
    {
        $attributes = [];

        foreach (['name', 'email', 'phone'] as $key) {
            if (array_key_exists($key, $data)) {
                $attributes[$key] = $data[$key] === '' ? null : $data[$key];
            }
        }

        if ($removeAvatar) {
            $this->deleteAvatar($user);
            $attributes['avatar'] = null;
        } elseif ($avatar) {
            $this->deleteAvatar($user);
            $attributes['avatar'] = $avatar->store(self::AVATAR_DIR, self::AVATAR_DISK);
        }

        // Resetam verificarea de email daca s-a schimbat
        if (! empty($attributes['email']) && $attributes['email'] !== $user->email) {
            $attributes['email_verified_at'] = null;
        }

        $updated = $this->users->update($user, $attributes);

        if (isset($attributes['email_verified_at']) && $attributes['email_verified_at'] === null) {
            $updated->sendEmailVerificationNotification();
        }

        return $updated;
    }

    private function deleteAvatar(User $user): void
    {
        if ($user->avatar) {
            Storage::disk(self::AVATAR_DISK)->delete($user->avatar);
        }
    }
}
