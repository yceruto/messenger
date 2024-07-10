<?php

namespace OpenSolid\Tests\Messenger\Middleware;

use OpenSolid\Messenger\Middleware\NextMiddleware;
use PHPUnit\Framework\TestCase;
use OpenSolid\Messenger\Middleware\MiddlewareStack;
use OpenSolid\Messenger\Middleware\Middleware;
use OpenSolid\Messenger\Model\Envelope;
use OpenSolid\Tests\Messenger\Fixtures\MyMessage;

class MiddlewareStackTest extends TestCase
{
    public function testHandle(): void
    {
        $middleware1 = new class() implements Middleware {
            public function handle(Envelope $envelope, NextMiddleware $next): void
            {
                $envelope->result = '1';
                $next->handle($envelope);
            }
        };
        $middleware2 = new class() implements Middleware {
            public function handle(Envelope $envelope, NextMiddleware $next): void
            {
                $envelope->result .= '2';
                $next->handle($envelope);
            }
        };
        $middleware3 = new class() implements Middleware {
            public function handle(Envelope $envelope, NextMiddleware $next): void
            {
                $envelope->result .= '3';
                $next->handle($envelope);
            }
        };
        $stack = new MiddlewareStack([$middleware1, $middleware2, $middleware3]);
        $envelope = Envelope::wrap(new MyMessage());
        $stack->handle($envelope);

        $this->assertSame('123', $envelope->result);
    }
}
