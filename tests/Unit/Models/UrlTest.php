<?php

namespace Tests\Unit\Models;

use App\Models\Url;
use App\Models\Visit;
use Tests\TestCase;

class UrlTest extends TestCase
{
    protected $url;

    public function setUp(): void
    {
        parent::setUp();

        $this->url = new Url();

        factory(Url::class)->create([
            'user_id' => $this->admin()->id,
            'clicks'  => 10,
        ]);

        factory(Url::class, 2)->create([
            'user_id' => null,
            'clicks'  => 10,
        ]);

        config()->set('urlhub.hash_char', 'abc');
    }

    /**
     * @test
     * @group u-model
     */
    public function belongs_to_user()
    {
        $url = factory(Url::class)->create([
            'user_id' => $this->admin()->id,
        ]);

        $this->assertTrue($url->user()->exists());
    }

    /**
     * @test
     * @group u-model
     */
    public function default_guest_name()
    {
        $url = factory(Url::class)->create([
            'user_id' => null,
        ]);

        $this->assertSame('Guest', $url->user->name);
    }

    /**
     * @test
     * @group u-model
     */
    public function has_many_url_stat()
    {
        $url = factory(Url::class)->create();

        factory(Visit::class)->create([
            'url_id' => $url->id,
        ]);

        $this->assertTrue($url->visit()->exists());
    }

    /**
     * The default guest id must be null.
     *
     * @test
     * @group u-model
     */
    public function default_guest_id()
    {
        $longUrl = 'https://example.com';

        $this->post(route('createshortlink'), [
            'long_url' => $longUrl,
        ]);

        $url = Url::whereLongUrl($longUrl)->first();

        $this->assertSame(null, $url->user_id);
    }

    /**
     * @test
     * @group u-model
     */
    public function setUserIdAttribute_must_be_null()
    {
        $url = factory(Url::class)->create([
            'user_id' => 0,
        ]);

        $this->assertEquals(null, $url->user_id);
    }

    /**
     * @test
     * @group u-model
     */
    public function setLongUrlAttribute()
    {
        $url = factory(Url::class)->create([
            'long_url' => 'http://example.com/',
        ]);

        $this->assertSame(
            $url->long_url,
            'http://example.com'
        );
    }

    /**
     * @test
     * @group u-model
     */
    public function getShortUrlAttribute()
    {
        $url = Url::whereUserId($this->admin()->id)->first();

        $this->assertSame(
            $url->short_url,
            url('/'.$url->keyword)
        );
    }

    /**
     * @test
     * @group u-model
     */
    public function total_short_url()
    {
        $this->assertSame(
            3,
            $this->url->shortUrlCount()
        );
    }

    /**
     * @test
     * @group u-model
     */
    public function total_short_url_by_me()
    {
        $this->assertSame(
            1,
            $this->url->shortUrlCountOwnedBy($this->admin()->id)
        );
    }

    /**
     * @test
     * @group u-model
     */
    public function total_short_url_by_guest()
    {
        $this->assertSame(
            2,
            $this->url->shortUrlCountOwnedBy()
        );
    }

    /**
     * @test
     * @group u-model
     */
    public function total_clicks()
    {
        $this->assertSame(
            30,
            $this->url->clickCount()
        );
    }

    /**
     * @test
     * @group u-model
     */
    public function total_clicks_by_me()
    {
        $this->assertSame(
            10,
            $this->url->clickCountOwnedBy($this->admin()->id)
        );
    }

    /**
     * The number of guests is calculated based on a unique IP.
     *
     * @test
     * @group u-model
     */
    public function total_clicks_by_guest()
    {
        $this->assertSame(
            20,
            $this->url->clickCountOwnedBy()
        );
    }

    /**
     * @test
     * @group u-model
     * @dataProvider keywordCapacityProvider
     */
    public function keywordCapacity($hashLength, $expected)
    {
        config()->set('urlhub.hash_length', $hashLength);

        $this->assertSame($expected, $this->url->keywordCapacity());
    }

    public function keywordCapacityProvider()
    {
        return [
            [1, 3], // (3^1)
            [2, 9], // $alphabet_length^$hashLength or 3^2
        ];
    }

    /**
     * @test
     * @group u-model
     */
    public function keywordRemaining()
    {
        factory(Url::class, 5)->create();

        config()->set('urlhub.hash_length', 1);

        // 3 - 5 - (2+1) = must be 0
        $this->assertSame(0, $this->url->keywordRemaining());

        config()->set('urlhub.hash_length', 2);

        // (3^2) - 5 - (2+1) = 1
        $this->assertSame(1, $this->url->keywordRemaining());
    }

    /**
     * @test
     * @group u-model
     */
    public function getRemoteTitle()
    {
        $longUrl = 'https://github123456789.com';

        $this->assertSame('github123456789.com - No Title', $this->url->getRemoteTitle($longUrl));
    }

    /**
     * @test
     * @group u-model
     * @dataProvider getDomainProvider
     */
    public function get_domain($expected, $actutal)
    {
        $this->assertEquals($expected, $this->url->getDomain($actutal));
    }

    public function getDomainProvider()
    {
        return [
            ['foo.com', 'http://foo.com/foo/bar?name=taylor'],
            ['foo.com', 'https://foo.com/foo/bar?name=taylor'],
            ['foo.com', 'http://www.foo.com/foo/bar?name=taylor'],
            ['foo.com', 'https://www.foo.com/foo/bar?name=taylor'],
            ['bar.foo.com', 'http://bar.foo.com/foo/bar?name=taylor'],
            ['bar.foo.com', 'https://bar.foo.com/foo/bar?name=taylor'],
            ['bar.foo.com', 'http://www.bar.foo.com/foo/bar?name=taylor'],
            ['bar.foo.com', 'https://www.bar.foo.com/foo/bar?name=taylor'],
        ];
    }

    /**
     * @test
     * @group u-model
     */
    public function ipToCountryWithKnownIp()
    {
        $countries = $this->url->ipToCountry('8.8.8.8');

        $this->assertEquals('US', $countries['countryCode']);
    }

    /**
     * @test
     * @group u-model
     */
    public function ipToCountryWithUnknownIp()
    {
        $countries = $this->url->ipToCountry('127.0.0.1');

        $this->assertEquals('N/A', $countries['countryCode']);
        $this->assertEquals('Unknown', $countries['countryName']);
    }
}
