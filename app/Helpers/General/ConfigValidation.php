<?php

namespace App\Helpers\General;

/**
 * @codeCoverageIgnore
 */
class ConfigValidation
{
    /**
     * Validate all of the config related to the library.
     *
     * @return bool
     * @throws ValidationException
     */
    public function validateConfig(): bool
    {
        return $this->guest()
               && $this->guest_register()
               && $this->guest_show_stat()
               && $this->hash_char()
               && $this->hash_length()
               && $this->redirect_status_code()
               && $this->redirect_cache_lifetime();
    }

    public function guest()
    {
        if (! is_bool(config('urlhub.guest'))) {
            throw new \Exception('The "guest" config variable must be a boolean.');
        }

        return true;
    }

    public function guest_register()
    {
        if (! is_bool(config('urlhub.guest_register'))) {
            throw new \Exception('The "guest_register" config variable must be a boolean.');
        }

        return true;
    }

    public function guest_show_stat()
    {
        if (! is_bool(config('urlhub.guest_show_stat'))) {
            throw new \Exception('The "guest_show_stat" config variable must be a boolean.');
        }

        return true;
    }

    public function hash_char()
    {
        if (! ctype_alnum(config('urlhub.hash_char'))) {
            throw new \Exception('The "hash_char" config variable  may only contain letters and numbers.');
        }

        return true;
    }

    public function hash_length()
    {
        $hashLength = config('urlhub.hash_length');

        if (! is_int($hashLength)) {
            throw new \Exception('The "hash_length" config variable is not a valid integer.');
        }

        if ($hashLength < 1) {
            throw new \Exception('The "hash_length" config variable must be 1 or above.');
        }

        return true;
    }

    public function redirect_status_code()
    {
        $rsc = config('urlhub.redirect_status_code');

        if ($rsc < 300 || $rsc > 399) {
            throw new \Exception('The "redirect_status_code" config variable is not valid.');
        }

        return true;
    }

    public function redirect_cache_lifetime()
    {
        $rcl = config('redirect_cache_lifetime');

        if ($rcl < 0) {
            throw new \Exception('The "redirect_cache_lifetime" config variable is not valid.');
        }

        return true;
    }
}
