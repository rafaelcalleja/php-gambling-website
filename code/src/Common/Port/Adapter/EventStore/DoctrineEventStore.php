<?php

namespace Gambling\Common\Port\Adapter\EventStore;

use Doctrine\DBAL\Connection;
use Gambling\Common\Domain\DomainEvent;
use Gambling\Common\EventStore\EventStore;
use Gambling\Common\EventStore\StoredEvent;

final class DoctrineEventStore implements EventStore
{
    private const SELECT = 'e.id, e.name, BIN_TO_UUID(e.aggregateId) as aggregateId, e.payload, e.occurredOn';

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $table;

    /**
     * DoctrineEventStore constructor.
     *
     * @param Connection $connection
     * @param string     $table
     */
    public function __construct(Connection $connection, string $table)
    {
        $this->connection = $connection;
        $this->table = $table;
    }

    /**
     * @inheritdoc
     */
    public function storedEventsSince(int $id, int $limit): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select(self::SELECT)
            ->from($this->table, 'e')
            ->where('e.id > :id')
            ->setParameter(':id', $id)
            ->setMaxResults($limit)
            ->execute()
            ->fetchAll();

        return $this->transformRowsToStoredEvents($rows);
    }

    /**
     * @inheritdoc
     */
    public function storedEventsByAggregateId(string $aggregateId, int $sinceId = 0): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select(self::SELECT)
            ->from($this->table, 'e')
            ->where('e.aggregateId = :aggregateId')
            ->andWhere('e.id > :id')
            ->setParameter(':aggregateId', $aggregateId, 'uuid_binary')
            ->setParameter(':id', $sinceId)
            ->execute()
            ->fetchAll();

        return $this->transformRowsToStoredEvents($rows);
    }

    /**
     * @inheritdoc
     */
    public function append(DomainEvent $domainEvent): void
    {
        $this->connection->insert($this->table, [
            'name'        => $domainEvent->name(),
            'aggregateId' => $domainEvent->aggregateId(),
            'payload'     => $domainEvent->payload(),
            'occurredOn'  => $domainEvent->occurredOn()
        ], [
            'string',
            'uuid_binary',
            'json',
            'datetime_immutable'
        ]);
    }

    /**
     * Transform the sql row to a stored event instance.
     *
     * @param array $rows
     *
     * @return StoredEvent[]
     */
    private function transformRowsToStoredEvents(array $rows): array
    {
        return array_map(function ($row) {
            return new StoredEvent(
                $row['id'],
                $row['name'],
                $row['aggregateId'],
                $row['payload'],
                new \DateTimeImmutable($row['occurredOn'])
            );
        }, $rows);
    }
}
