<?php

namespace Gambling\Common\Application;

use PHPUnit\Framework\TestCase;

final class RetryApplicationLifeCycleTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldThrowAnExceptionIfNumberOfRetriesIsLowerThanOne(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        /** @var ApplicationLifeCycle $applicationLifeCycle */
        $applicationLifeCycle = $this->createMock(ApplicationLifeCycle::class);

        new RetryApplicationLifeCycle(
            $applicationLifeCycle,
            0,
            \RuntimeException::class
        );
    }

    /**
     * @test
     */
    public function itShouldRetry(): void
    {
        $actionToCall = function () {
            // No op
        };

        $applicationLifeCycle = $this->createMock(ApplicationLifeCycle::class);

        // The first two times when "run" is called, an exception is thrown.
        $applicationLifeCycle
            ->expects($this->at(0))
            ->method('run')
            ->willThrowException(
                new \RuntimeException()
            );
        $applicationLifeCycle
            ->expects($this->at(1))
            ->method('run')
            ->willThrowException(
                new \RuntimeException()
            );
        $applicationLifeCycle
            ->expects($this->at(2))
            ->method('run')
            ->with($actionToCall);

        // Expect that "run" is called three times.
        $applicationLifeCycle
            ->expects($this->exactly(3))
            ->method('run');

        /** @var ApplicationLifeCycle $applicationLifeCycle */
        $retryApplicationLifeCycle = new RetryApplicationLifeCycle(
            $applicationLifeCycle,
            3,
            \RuntimeException::class
        );

        $retryApplicationLifeCycle->run($actionToCall);
    }

    /**
     * @test
     */
    public function itShouldThrowTheApplicationExceptionWhenNumberOfRetriesAreReached(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Custom exception');

        $applicationLifeCycle = $this->createMock(ApplicationLifeCycle::class);

        // "run" always throws an exception.
        $applicationLifeCycle
            ->expects($this->any())
            ->method('run')
            ->willThrowException(
                new \RuntimeException('Custom exception')
            );

        // Expect that "run" is called three times.
        $applicationLifeCycle
            ->expects($this->exactly(3))
            ->method('run');

        /** @var ApplicationLifeCycle $applicationLifeCycle */
        $retryApplicationLifeCycle = new RetryApplicationLifeCycle(
            $applicationLifeCycle,
            3,
            \RuntimeException::class
        );

        $retryApplicationLifeCycle->run(function () {
            // No op
        });
    }

    /**
     * @test
     */
    public function itShouldThrowTheApplicationExceptionIfItsNotTheConfiguredException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Custom exception');

        $applicationLifeCycle = $this->createMock(ApplicationLifeCycle::class);

        // "run" always throws an exception.
        $applicationLifeCycle
            ->expects($this->any())
            ->method('run')
            ->willThrowException(
                new \RuntimeException('Custom exception')
            );

        // Expect that "run" is called one time.
        $applicationLifeCycle
            ->expects($this->exactly(1))
            ->method('run');

        /** @var ApplicationLifeCycle $applicationLifeCycle */
        $retryApplicationLifeCycle = new RetryApplicationLifeCycle(
            $applicationLifeCycle,
            3,
            \InvalidArgumentException::class
        );

        $retryApplicationLifeCycle->run(function () {
            // No op
        });
    }
}
