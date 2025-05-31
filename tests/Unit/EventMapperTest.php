<?php

namespace Bhekor\LaravelBrevo\Tests\Unit;

use Bhekor\LaravelBrevo\Tests\TestCase;
use Bhekor\LaravelBrevo\Webhooks\EventMapper;

/**
 * @covers \Bhekor\LaravelBrevo\Webhooks\EventMapper
 */
class EventMapperTest extends TestCase
{
    private EventMapper $mapper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mapper = new EventMapper();
    }

    /** @test */
    public function it_maps_known_events()
    {
        $this->assertEquals(
            'Bhekor\LaravelBrevo\Events\EmailDelivered',
            $this->mapper->map('delivered')
        );

        $this->assertEquals(
            'Bhekor\LaravelBrevo\Events\EmailOpened',
            $this->mapper->map('opened')
        );
    }

    /** @test */
    public function it_returns_null_for_unknown_events()
    {
        $this->assertNull($this->mapper->map('unknown_event'));
    }
}