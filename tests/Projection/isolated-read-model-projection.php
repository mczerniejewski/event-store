<?php

declare(strict_types=1);

use Prooph\EventStore\InMemoryEventStore;
use Prooph\EventStore\Projection\InMemoryProjectionManager;
use Prooph\EventStore\Projection\ReadModel;
use Prooph\EventStore\Projection\ReadModelProjector;
use Prooph\EventStore\Stream;
use Prooph\EventStore\StreamName;

require __DIR__ . '/../../vendor/autoload.php';

$readModel = new class() implements ReadModel {
    public function init(): void
    {
    }

    public function isInitialized(): bool
    {
        return true;
    }

    public function reset(): void
    {
    }

    public function delete(): void
    {
    }

    public function stack(string $operation, ...$args): void
    {
    }

    public function persist(): void
    {
    }
};

$eventStore = new InMemoryEventStore();
$eventStore->create(new Stream(new StreamName('user-123'), new ArrayIterator([])));

$projectionManager = new InMemoryProjectionManager($eventStore);
$projection = $projectionManager->createReadModelProjection(
    'test_projection',
    $readModel,
    [
        ReadModelProjector::OPTION_PCNTL_DISPATCH => true,
    ]
);
pcntl_signal(SIGQUIT, function () use ($projection) {
    $projection->stop();
    exit(SIGUSR1);
});
$projection
    ->fromStream('user-123')
    ->whenAny(function () {
    })
    ->run();