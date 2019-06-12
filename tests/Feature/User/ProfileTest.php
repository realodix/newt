<?php

namespace Tests\Feature\User;

use App\User;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    protected function getRoute($value)
    {
        return route('user.edit', $value);
    }

    protected function postRoute($value)
    {
        return route('user.update', \Hashids::connection(\App\User::class)->encode($value));
    }

    /** @test */
    public function users_can_access_their_own_profile_page()
    {
        $this->loginAsAdmin();

        $response = $this->get($this->getRoute($this->admin()->name));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_other_users_profile_pages()
    {
        $this->loginAsAdmin();

        $response = $this->get($this->getRoute($this->user()->name));
        $response->assertStatus(200);
    }

    /** @test */
    public function non_admin_cant_access_other_users_profile_pages()
    {
        $this->loginAsUser();

        $response = $this->get($this->getRoute($this->admin()->name));
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_change_user_email()
    {
        $this->loginAsAdmin();

        $response = $this->from($this->getRoute($this->user()->name))
                         ->post($this->postRoute($this->user()->id), [
                             'email' => 'new_email_user@urlhub.test',
                         ]);

        $response
            ->assertRedirect($this->getRoute($this->user()->name))
            ->assertSessionHas(['flash_success']);

        $this->assertSame('new_email_user@urlhub.test', $this->user()->email);
    }

    /** @test */
    public function non_admin_cant_change_other_users_email()
    {
        $this->loginAsUser();

        $user2 = factory(User::class)->create(['email' => 'user2@urlhub.test']);

        $response = $this->from($this->getRoute($user2->name))
                         ->post($this->postRoute($user2->id), [
                             'email' => 'new_email_user2@urlhub.test',
                         ]);

        $response->assertStatus(403);
        $this->assertSame('user2@urlhub.test', $user2->email);
    }

    /** @test */
    public function validation_email_required()
    {
        $this->loginAsAdmin();

        $response = $this->from($this->getRoute($this->admin()->name))
                         ->post($this->postRoute($this->admin()->id), [
                             'email' => '',
                         ]);

        $response
            ->assertRedirect($this->getRoute($this->admin()->name))
            ->assertSessionHasErrors('email');
    }

    /** @test */
    public function validation_email_invalid_format()
    {
        $this->loginAsAdmin();

        $response = $this->from($this->getRoute($this->admin()->name))
                         ->post($this->postRoute($this->admin()->id), [
                             'email' => 'invalid_format',
                         ]);

        $response
            ->assertRedirect($this->getRoute($this->admin()->name))
            ->assertSessionHasErrors('email');
    }

    /** @test */
    public function validation_email_max_length()
    {
        $this->loginAsAdmin();

        $response = $this->from($this->getRoute($this->admin()->name))
                         ->post($this->postRoute($this->admin()->id), [
                             // 255 + 9
                             'email' => str_repeat('a', 255).'@mail.com',
                         ]);

        $response
            ->assertRedirect($this->getRoute($this->admin()->name))
            ->assertSessionHasErrors('email');
    }

    /** @test */
    public function validation_email_unique()
    {
        $this->loginAsAdmin();

        $response = $this->from($this->getRoute($this->admin()->name))
                         ->post($this->postRoute($this->admin()->id), [
                             'email' => $this->user()->email,
                         ]);

        $response
            ->assertRedirect($this->getRoute($this->admin()->name))
            ->assertSessionHasErrors('email');
    }
}
