<?php

namespace Tests\Unit\Models;

use App\Models\Url;
use App\Models\UrlStat;
use Tests\TestCase;

class UrlStatTest extends TestCase
{
    /**
     * @test
     * @group u-model
     */
    public function belongs_to_url()
    {
        $urlStat = factory(UrlStat::class)->create([
            'url_id' => function () {
                return factory(Url::class)->create()->id;
            },
        ]);

        $this->assertTrue($urlStat->url()->exists());
    }
}
