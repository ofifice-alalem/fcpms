<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * BR-001: يوجد نوعان من المستخدمين (HR, Consultant).
 * BR-002: يمتلك كل مستخدم حساباً واحداً فقط.
 * BR-004: لا يسمح بتسجيل الدخول إذا كانت الحالة inactive.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
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
     * BR-002: ربط حساب المستخدم بملف الاستشاري
     */
    public function consultant(): HasOne
    {
        return $this->hasOne(Consultant::class);
    }

    /**
     * BR-004: فحص تفعيل الحساب
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
