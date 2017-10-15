<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'telephone', 'email', 'password', 'address_1', 'address_2', 'address_3', 'city', 'postcode',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function approve()
    {
        $this->approved = true;
    }

    public function block()
    {
        $this->blocked = true;
    }

    public function unblock()
    {
        $this->blocked = false;
    }

    public static function admin()
    {
        return self::where('is_admin', 1)->firstOrFail();
    }
}
