<?php

declare(strict_types=1);

namespace SimpleBus\Message;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use SimpleBus\Message\Id\UuidGenerator;
use SimpleBus\Message\Serialization\ContentType;
use SimpleBus\Message\Serialization\JsonSerializer;
use Thesis\Amqp\DeliveryMode;

#[CoversClass(MessageFactory::class)]
final class MessageFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $messageFactory = new MessageFactory(
            serializer: new JsonSerializer(),
            messageIdGenerator: new UuidGenerator(),
        );

        $message = $messageFactory->create(
            new Envelope(
                new Order(1, 'info@gmail.com'),
            ),
        );

        self::assertSame(DeliveryMode::Persistent, $message->deliveryMode);
        self::assertSame([], $message->headers);
        self::assertSame(ContentType::Json->value, $message->contentType);
        self::assertSame('order', $message->type);
        self::assertNotNull($message->messageId);
        self::assertTrue(Uuid::isValid($message->messageId));
        self::assertSame('{"id":1,"email":"info@gmail.com"}', $message->body);
    }
}

final readonly class Order implements TypedPayload
{
    public function __construct(public int $id, public string $email)
    {
    }

    public static function type(): string
    {
        return 'order';
    }
}
