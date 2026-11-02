<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use YieldStudio\LaravelExpoNotifier\Models\ExpoToken;
use Illuminate\Contracts\Auth\CanResetPassword;
use App\Traits\RequiresSubscription;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable implements FilamentUser, CanResetPassword, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, RequiresSubscription;
    use HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'referral_code',
        'referred_by',
        'is_admin',
        'is_director',
        'is_partner',
        'disabled',
        'disabled_at',
    ];

    protected $with = ['profile', 'validSubscription'];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasPermissionTo('access admin panel');
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function auditions()
    {
        return $this->hasMany(Audition::class);
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class, 'admin_countries', 'user_id', 'country_id');
    }

    //@deprecated 
    // DO NOT TOUCH WILL BREAK  ROLES & PERMISSIONS SEEDER
    public function isCountryAdmin()
    {
        return $this->is_admin && $this->countries()->count() > 0;
    }


    public function getNameAttribute($value)
    {
        return $value ?? $this->email;
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function profiles()
    {
        return $this->hasMany(Profile::class, 'user_id');
    }

    public function profile()
    {
        return $this->hasOne(Profile::class)->where('is_default', true);
    }

    public function mainProfile()
    {;
        return $this->hasOne(Profile::class)->orderBy('created_at', 'asc');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by')->with('mainProfile.castingImages');;
    }

    public function hasMonitoringPrivileges()
    {
        return $this->hasRole('super admin');
    }

    public function paymentDetails()
    {
        return $this->hasOne(PaymentDetail::class);
    }

    public function referralRewards()
    {
        return $this->hasMany(RewardReferral::class, 'referred_id');
    }

    public function canCreateProfile()
    {
        return $this->hasAccessToPremiumFeatures($this->id)
            && $this->profiles()
            ->count() < config('premium-feature-limits.profiles_per_account');
    }

    public function canSwitchProfiles()
    {
        return $this->hasAccessToPremiumFeatures($this->id);
    }

    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function removeDefaultProfile()
    {
        $this->profile()->update(['is_default' => false]);
    }

    public function expoToken(): MorphOne
    {
        return $this->morphOne(ExpoToken::class, 'owner');
    }

    public function scopeToken($query)
    {
        return $query->with(['expoToken' => function ($query) {
            $query->latest();
        }]);
    }


    public function manageAccountRequestToken()
    {
        return $this->hasOne(UserManageAccountRequestToken::class);
    }

    public function userSubscriptions()
    {
        return $this->belongsToMany(Subscription::class, 'user_subscriptions')->withPivot('start_date', 'end_date', 'is_active')->latest('pivot_start_date');
    }

    public function isSubscribed()
    {
        return $this->validSubscription->first() && !$this->validSubscription->first()->is_free;
    }

    public function validSubscription()
    {
        return $this->userSubscriptions()->where('end_date', '>', now())->where('is_active', true)->latest('pivot_start_date');
    }

    protected static function booted()
    {
        static::updating(function (User $user) {
            if ($user->isDirty('disabled')) {
                if ($user->disabled && is_null($user->getOriginal('disabled_at'))) {
                    $user->disabled_at = now();
                } elseif (!$user->disabled) {
                    $user->disabled_at = null;
                }
            }
        });
    }
}
