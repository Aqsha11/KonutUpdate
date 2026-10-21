<?php

namespace App\Models;

use App\Notifications\VerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'slug',
        'bio',
        'email',
        'password',
        'role_id',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Slug profil publik (/penulis/{slug}) dibuat otomatis dari nama bila kosong.
     */
    protected static function booted(): void
    {
        static::saving(function (self $user) {
            if (blank($user->slug)) {
                $user->slug = static::uniqueSlug((string) $user->name, $user->getKey());
            }
        });
    }

    public static function uniqueSlug(string $name, ?int $exceptId = null): string
    {
        $base = Str::slug($name) ?: 'penulis';
        $slug = $base;
        $suffix = 1;

        while (static::query()
            ->where('slug', $slug)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->exists()) {
            $suffix++;
            $slug = $base.'-'.$suffix;
        }

        return $slug;
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->avatar ? Storage::url($this->avatar) : null);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    /**
     * Artikel yang ditulis oleh akun ini sebagai penulis (bukan admin pemuat).
     */
    public function writtenPosts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    /**
     * URL profil penulis publik.
     */
    public function publicProfileUrl(): ?string
    {
        return $this->slug ? route('authors.show', $this->slug) : null;
    }

    public function hasPermission(string $slug): bool
    {
        if ($this->role?->slug === 'super_admin') {
            return true;
        }

        return $this->role?->permissions()->where('permissions.slug', $slug)->exists() ?? false;
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail);
    }
}
