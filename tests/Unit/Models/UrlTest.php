<?php

namespace Tests\Unit\Models;

use App\Url;
use App\UrlStat;
use Tests\TestCase;

/**
 * @coversDefaultClass App\Url
 */
class UrlTest extends TestCase
{
    protected $url;

    public function setUp():void
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

        config()->set('urlhub.hash_alphabet', 'abc');
    }

    /**
     * @test
     * @covers ::user
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
     * @covers ::user
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
     * @covers ::urlStat
     */
    public function has_many_url_stat()
    {
        $url = factory(Url::class)->create();

        factory(UrlStat::class)->create([
            'url_id' => $url->id,
        ]);

        $this->assertTrue($url->urlStat()->exists());
    }

    /**
     * The default guest id must be null.
     *
     * @test
     * @covers ::setUserIdAttribute
     */
    public function default_guest_id()
    {
        $long_url = 'https://example.com';

        $this->post(route('createshortlink'), [
            'long_url' => $long_url,
        ]);

        $url = Url::whereLongUrl($long_url)->first();

        $this->assertSame(null, $url->user_id);
    }

    /**
     * @test
     * @covers ::setUserIdAttribute
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
     * @covers ::setLongUrlAttribute
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
     * @covers ::getShortUrlAttribute
     */
    public function getShortUrlAttribute()
    {
        $url = Url::whereUserId($this->admin()->id)->first();

        $this->assertSame(
            $url->short_url,
            url('/'.$url->url_key)
        );
    }

    /**
     * @test
     * @covers ::totalShortUrl
     */
    public function total_short_url()
    {
        $this->assertSame(
            3,
            $this->url->totalShortUrl()
        );
    }

    /**
     * @test
     * @covers ::totalShortUrlById
     */
    public function total_short_url_by_me()
    {
        $this->assertSame(
            1,
            $this->url->totalShortUrlById($this->admin()->id)
        );
    }

    /**
     * @test
     * @covers ::totalShortUrlById
     */
    public function total_short_url_by_guest()
    {
        $this->assertSame(
            2,
            $this->url->totalShortUrlById()
        );
    }

    /**
     * @test
     * @covers ::totalClicks
     */
    public function total_clicks()
    {
        $this->assertSame(
            30,
            $this->url->totalClicks()
        );
    }

    /**
     * @test
     * @covers ::totalClicksById
     */
    public function total_clicks_by_me()
    {
        $this->assertSame(
            10,
            $this->url->totalClicksById($this->admin()->id)
        );
    }

    /**
     * The number of guests is calculated based on a unique IP.
     *
     * @test
     * @covers ::totalClicksById
     */
    public function total_clicks_by_guest()
    {
        $this->assertSame(
            20,
            $this->url->totalClicksById()
        );
    }

    /**
     * @test
     * @covers ::url_key_capacity
     * @dataProvider urlKeyCapacityProvider
     */
    public function url_key_capacity($size1, $size2, $expected)
    {
        config()->set('urlhub.hash_size_1', $size1);
        config()->set('urlhub.hash_size_2', $size2);

        $this->assertSame($expected, $this->url->url_key_capacity());
    }

    public function urlKeyCapacityProvider()
    {
        return [
            [0, 0, 0],
            [1, 2, 12], // (3^1)+(3^2)
            [2, 2, 9], // $alphabet_length^$hash_size_1 or 3^2

            [1, -2, 3],
            [-1, 2, 0],
            [-1, -2, 0],

            [2.7, 3, 36], // (3^2)+(3^3)
            [2, 3.7, 36], // (3^2)+(3^3)

            ['string', 2, 0],
            [2, 'string', 9], // $alphabet_length^$hash_size_1 or 3^2
            ['string', 'string', 0],
        ];
    }

    /**
     * @test
     * @covers ::url_key_remaining
     */
    public function url_key_remaining()
    {
        factory(Url::class, 5)->create();

        config()->set('urlhub.hash_size_1', 1);
        config()->set('urlhub.hash_size_2', 0);

        // 3 - 5 = must be 0
        $this->assertSame(0, $this->url->url_key_remaining());

        config()->set('urlhub.hash_size_1', 2);

        // (3^2) - 5 - (2+1) = 1
        $this->assertSame(1, $this->url->url_key_remaining());
    }

    /**
     * @test
     * @covers ::getDomain
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
            ['foo.com', 'http://bar.foo.com/foo/bar?name=taylor'],
            ['foo.com', 'https://bar.foo.com/foo/bar?name=taylor'],
            ['foo.com', 'http://www.bar.foo.com/foo/bar?name=taylor'],
            ['foo.com', 'https://www.bar.foo.com/foo/bar?name=taylor'],
        ];
    }
}
