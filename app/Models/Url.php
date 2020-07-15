<?php

namespace App\Models;

use App\Http\Traits\Hashidable;
use Embed\Embed;
use GeoIp2\Database\Reader;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use RandomLib\Factory as RandomLibFactory;
use Spatie\Url\Url as SpatieUrl;

class Url extends Model
{
    use Hashidable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'keyword',
        'is_custom',
        'long_url',
        'meta_title',
        'clicks',
        'ip',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id'   => 'int',
        'is_custom' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Eloquent: Relationships
    |--------------------------------------------------------------------------
    | Database tables are often related to one another. Eloquent relationships
    | are defined as methods on Eloquent model classes.
    */

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withDefault([
            'name' => 'Guest',
        ]);
    }

    public function visit()
    {
        return $this->hasMany('App\Models\Visit');
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

    // Mutator
    public function setUserIdAttribute($value)
    {
        if ($value == 0) {
            $this->attributes['user_id'] = null;
        } else {
            $this->attributes['user_id'] = $value;
        }
    }

    public function setLongUrlAttribute($value)
    {
        $this->attributes['long_url'] = rtrim($value, '/');
    }

    public function setMetaTitleAttribute($value)
    {
        if (Str::startsWith($value, 'http')) {
            $this->attributes['meta_title'] = $this->getRemoteTitle($value);
        } else {
            $this->attributes['meta_title'] = $value;
        }
    }

    // Accessor
    public function getShortUrlAttribute()
    {
        return url('/'.$this->attributes['keyword']);
    }

    /*
    |--------------------------------------------------------------------------
    | UrlHub Functions
    |--------------------------------------------------------------------------
    */

    public function shortUrlCount()
    {
        return self::count('keyword');
    }

    /**
     * @param int $id
     */
    public function shortUrlCountOwnedBy($id = null)
    {
        return self::whereUserId($id)->count('keyword');
    }

    public function clickCount(): int
    {
        return self::sum('clicks');
    }

    /**
     * @param int $id
     */
    public function clickCountOwnedBy($id = null): int
    {
        return self::whereUserId($id)->sum('clicks');
    }

    /**
     * @codeCoverageIgnore
     * Make sure the random string generated by randomStringGenerator() is
     * truly unique.
     */
    public function randomKey()
    {
        $randomKey = $this->randomStringGenerator();

        // If it is already used (not available), find the next available
        // string.
        $generatedRandomKey = self::whereKeyword($randomKey)->first();
        while ($generatedRandomKey) {
            $randomKey = $this->randomStringGenerator();
            $generatedRandomKey = self::whereKeyword($randomKey)->first();
        }

        return $randomKey;
    }

    /**
     * @codeCoverageIgnore
     * Generate random strings using RandomLib.
     *
     * @return string
     */
    public function randomStringGenerator()
    {
        $alphabet = uHub('hash_char');
        $length = uHub('hash_length');

        $factory = new RandomLibFactory();
        $randomString = $factory->getMediumStrengthGenerator()->generateString($length, $alphabet);

        return $randomString;
    }

    /**
     * Counts the maximum number of random strings that can be generated by a
     * random string generator.
     *
     * @return int
     */
    public function keyCapacity()
    {
        $alphabet = strlen(uHub('hash_char'));
        $length = uHub('hash_length');

        // Untuk kebutuhan di saat pengujian, dimana saat pengujian dibutuhkan
        // nilai yang dikembalikan adalah 0. Dalam produksi, kondisi ini tidak
        // diperlukan karena sudah dilakukan validasi untuk tidak mengembalikan
        // angka 0, maka kedepannya Kami mencoba untuk memanipulasi data yang
        // dikembalikan.
        if ($length == 0) {
            return 0;
        }

        return pow($alphabet, $length);
    }

    /**
     * Count the remaining random strings that can still be generated by a
     * random string generator.
     *
     * @return int
     */
    public function keyRemaining()
    {
        $keyCapacity = $this->keyCapacity();
        $randomKey = self::whereIsCustom(false)->count();
        $customKey = $this->customKeyCount();

        $numberOfUsedKey = $randomKey + $customKey;

        return max(($keyCapacity - $numberOfUsedKey), 0);
    }

    public function keyRemainingInPercent()
    {
        $keyCapacity = $this->keyCapacity();
        $numberOfCustomKey = $this->customKeyCount();

        return remainingPercentage($numberOfCustomKey, $keyCapacity);
    }

    public function customKeyCount()
    {
        $hashLength = uHub('hash_length');
        $customKey = self::whereIsCustom(true)
            ->whereRaw('LENGTH(keyword) = ?', [$hashLength])
            ->whereRaw("keyword REGEXP '[a-zA-Z0-9]{".$hashLength."}'")
            ->count();

        return $customKey;
    }

    /**
     * This function returns a string: either the page title as defined in
     * HTML, or "{domain_name} - No Title" if not found.
     *
     * @param string $url
     * @return string
     */
    public function getRemoteTitle($url)
    {
        try {
            $embed = Embed::create($url);
            $title = $embed->title;
        } catch (\Exception $e) {
            $title = $this->getDomain($url).' - No Title';
        }

        return $title;
    }

    /**
     * Get Domain from external url.
     *
     * Extract the domain name using the classic parse_url() and then look
     * for a valid domain without any subdomain (www being a subdomain).
     * Won't work on things like 'localhost'.
     *
     * @param string $url
     * @return mixed
     */
    public function getDomain($url)
    {
        $url = SpatieUrl::fromString($url);

        return urlRemoveScheme($url->getHost());
    }

    /**
     * IP Address to Identify Geolocation Information. If it fails, because
     * DB-IP Lite databases doesn't know the IP country, we will set it to
     * Unknown.
     */
    public function ipToCountry($ip)
    {
        try {
            // @codeCoverageIgnoreStart
            $reader = new Reader(database_path().'/dbip-country-lite-2020-07.mmdb');
            $record = $reader->country($ip);
            $countryCode = $record->country->isoCode;
            $countryName = $record->country->name;

            return compact('countryCode', 'countryName');
            // @codeCoverageIgnoreEnd
        } catch (\Exception $e) {
            $countryCode = 'N/A';
            $countryName = 'Unknown';

            return compact('countryCode', 'countryName');
        }
    }
}
