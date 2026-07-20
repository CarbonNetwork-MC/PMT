<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles, Notifiable;
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function ownedProjects(): HasMany {
        return $this->hasMany(Project::class, 'owner_uuid', 'uuid');
    }

    public function projects() {
        return Project::query()
            ->where('owner_uuid', $this->uuid)
            ->orWhereHas('members', function ($q) {
                $q->where('user_uuid', $this->uuid);
            });
    }

    public function projectsWhereAdmin() {
        return $this->projects()
            ->whereHas('members', function ($q) {
                $q->where('user_uuid', $this->uuid)
                    ->whereHas('role', function ($q) {
                        $q->whereIn('slug', ['admin']);
                    });
            });
    }

    public function projectsWhereMember() {
        return $this->projects()
            ->whereHas('members', function ($q) {
                $q->where('user_uuid', $this->uuid)
                    ->whereHas('role', function ($q) {
                        $q->whereIn('slug', ['member']);
                    });
            });
    }

    public function sessions(): HasMany {
        return $this->hasMany(DatabaseSession::class, 'user_id', 'uuid');
    }
}
