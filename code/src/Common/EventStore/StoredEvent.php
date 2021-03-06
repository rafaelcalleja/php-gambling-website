<?php

namespace Gambling\Common\EventStore;

final class StoredEvent
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $aggregateId;

    /**
     * @var string
     */
    private $payload;

    /**
     * @var \DateTimeImmutable
     */
    private $occurredOn;

    /**
     * StoredEvent constructor.
     *
     * @param int                $id
     * @param string             $name
     * @param string             $aggregateId
     * @param string             $payload
     * @param \DateTimeImmutable $occurredOn
     */
    public function __construct(
        int $id,
        string $name,
        string $aggregateId,
        string $payload,
        \DateTimeImmutable $occurredOn
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->aggregateId = $aggregateId;
        $this->payload = $payload;
        $this->occurredOn = $occurredOn;
    }

    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function aggregateId(): string
    {
        return $this->aggregateId;
    }

    /**
     * @return string
     */
    public function payload(): string
    {
        return $this->payload;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
