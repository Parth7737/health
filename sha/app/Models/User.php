<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'hospital_id',
        'userid',
        'password',
        'role_id',
        'entity_type',
        'approved_by',
        'approved_date',
        'gender',
        'avatar',
        'mobile_no'
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

    public function role() {
        return $this->belongsTo('App\Models\Role', 'role_id');
    }

    public function remarks()
    {
        return $this->morphMany('App\Models\PreauthRemark', 'remarkable');
    }

    public function notifications()
    {
        return $this->hasMany('App\Models\Notifications');
    }
    public function hospital()
    {
        return $this->belongsTo(Hospitals::class, 'hospital_id');
    }
    public function entityType()
    {
        return $this->belongsTo(EntityType::class, 'entity_type');
    }

    public function getProfileImageAttribute() {
        if($this->avatar) {
            return asset('public/storage/'.$this->avatar); 
        } else {
            return "https://ui-avatars.com/api/?name=".$this->name."&background=random";
        }
    }

}
