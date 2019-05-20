<?php

namespace Tests\Feature;

use App\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UrlTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function create_short_url()
    {
        $long_url = 'https://laravel.com';

        $response = $this->post(route('createshortlink'), [
            'long_url' => $long_url,
        ]);

        $this->assertDatabaseHas('urls', [
            'long_url' => $long_url,
        ]);
    }

    /**
     * @test
     */
    public function create_short_url_with_wrong_url()
    {
        $long_url = 'wrong-url';

        $response = $this->post(route('createshortlink'), [
            'long_url' => $long_url,
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHasErrors('long_url');
        $response->assertStatus(302);
    }

    /**
     * @test
     */
    public function create_custom_short_url()
    {
        $long_url = 'https://laravel.com';
        $custom_url_key = 'laravel';

        $response = $this->post(route('createshortlink'), [
            'long_url'       => $long_url,
            'custom_url_key' => $custom_url_key,
        ]);

        $this->assertDatabaseHas('urls', [
            'long_url' => $long_url,
            'url_key'  => $custom_url_key,
        ]);

        $response = $this->get(route('home').'/'.$custom_url_key);
        $response->assertRedirect($long_url);
    }

    /**
     * @test
     */
    public function short_url_redirect_to_original_url()
    {
        $long_url = 'https://laravel.com';

        $this->post(route('createshortlink'), [
            'long_url' => $long_url,
        ]);

        $url = Url::whereLongUrl($long_url)
                    ->first();

        $response = $this->get(route('home').'/'.$url->url_key);
        $response->assertRedirect($long_url);
    }

    /**
     * @test
     */
    public function custom_short_url_redirect_to_original_url()
    {
        $long_url = 'https://laravel.com';
        $custom_url_key = 'laravel';

        $this->post(route('createshortlink'), [
            'long_url' => $long_url,
            'url_key'  => $custom_url_key,
        ]);

        $url = Url::whereLongUrl($long_url)
                    ->first();

        $response = $this->get(route('home').'/'.$url->url_key);
        $response->assertRedirect($long_url);
    }
}
