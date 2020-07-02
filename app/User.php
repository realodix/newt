<?php

namespace App;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Traits\Hashidable;
use Creativeorange\Gravatar\Facades\Gravatar;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravolt\Avatar\Facade as Avatar;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Hashidable;
    use HasRoles;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
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

    /*
    |--------------------------------------------------------------------------
    | Eloquent: Relationships
    |--------------------------------------------------------------------------
    | Database tables are often related to one another. Eloquent relationships
    | are defined as methods on Eloquent model classes.
    */

    public function url()
    {
        return $this->hasMany('App\Models\Url');
    }

    /*
    |--------------------------------------------------------------------------
    | Eloquent: Mutators
    |--------------------------------------------------------------------------
    |
    | Accessors and mutators allow you to format Eloquent attribute values when
    | you retrieve or set them on model instances.
    |
    */

    /**
     * @codeCoverageIgnore
     * Accessors
     */
    public function getAvatarAttribute()
    {
        // Check if Gravatar has an avatar for the given email address
        if (Gravatar::exists($this->email) == true) {
            // Get the gravatar url
            return Gravatar::get($this->email);
        }

        // Create unique avatar based on their email
        return Avatar::create(Str::title($this->email))->toBase64();
    }

    /*
    |--------------------------------------------------------------------------
    | UrlHub Functions
    |--------------------------------------------------------------------------
    */

    public function userCount()
    {
        return self::count();
    }

    /*
     * Count the number of guests in the url column based on IP and grouped
     * by ip.
     */
    public function guestCount()
    {
        return Url::select('ip', DB::raw('count(*) as total'))
                    ->whereNull('user_id')
                    ->groupBy('ip')
                    ->get()
                    ->count();
    }
}
