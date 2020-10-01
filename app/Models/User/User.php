<?php

namespace App\Models\User;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'permission', 'company_id', 'cpf', 'otp'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute($value) {
        $this->attributes['password'] = bcrypt($value);
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    public function balance()
    {
        return $this->hasOne('App\Models\User\Balance');
    }

    public function purchaseHistory()
    {
        return $this->hasMany('App\Models\User\Purchase')->limit(15)->orderBy('id', 'desc');
    }

    public function rechargeHistory()
    {
        return $this->hasMany('App\Models\User\Recharge')->limit(15);
    }

    public function getPermissionAttribute($value) {
        switch ($value) {
            case 6:
                return 'vital-admin';
            case 5:
                return 'vital-manager';
            case 4:
                return 'general-manager';
            case 3:
                return 'manager';
            case 2:
                return 'user';
            default:
                return 'customer';
        }
    }

    public function setPermissionAttribute($value) {
        switch ($value) {
            case 'vital-admin':
                $this->attributes['permission'] = 6;
                break;
            case 'vital-manager':
                $this->attributes['permission'] = 5;
                break;
            case 'general-manager':
                $this->attributes['permission'] = 4;
                break;
            case 'manager':
                $this->attributes['permission'] = 3;
                break;
            case 'user':
                $this->attributes['permission'] = 2;
                break;
            default:
                $this->attributes['permission'] = 1;
                break;
        }
    }

    public function routeNotificationForTwilio()
    {
        return "+55{$this->phone}";
    }
}
