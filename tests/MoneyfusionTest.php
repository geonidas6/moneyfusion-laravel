<?php

namespace Sefako\Moneyfusion\Tests;

use Illuminate\Support\Facades\Http;
use Sefako\Moneyfusion\Facades\Moneyfusion;
use Sefako\Moneyfusion\MoneyfusionServiceProvider;

class MoneyfusionTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [MoneyfusionServiceProvider::class];
    }

    /** @test */
    public function it_can_make_a_payment()
    {
        Http::fake([
            '*' => Http::response(['status' => true, 'url' => 'https://example.com']),
        ]);

        $response = Moneyfusion::makePayment(['amount' => 100]);

        $this->assertTrue($response['status']);
        $this->assertEquals('https://example.com', $response['url']);
    }

    /** @test */
    public function it_can_check_a_payment_status()
    {
        Http::fake([
            '*' => Http::response(['status' => 'paid']),
        ]);

        $response = Moneyfusion::checkPaymentStatus('some-token');

        $this->assertEquals('paid', $response['status']);
    }
}
