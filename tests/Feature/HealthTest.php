<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthTest extends TestCase
{
    public function test_health_endpoint_is_available(): void
    {
        $this->get('/up')->assertOk();
    }
}
