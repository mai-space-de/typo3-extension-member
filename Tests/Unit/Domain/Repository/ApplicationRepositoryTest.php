<?php

declare(strict_types=1);

namespace Maispace\MaiMember\Tests\Unit\Domain\Repository;

use Maispace\MaiMember\Domain\Repository\ApplicationRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

final class ApplicationRepositoryTest extends TestCase
{
    #[Test]
    public function applicationRepositoryExtendsExtbaseRepository(): void
    {
        self::assertInstanceOf(Repository::class, $this->createPartialMock(ApplicationRepository::class, []));
    }

    #[Test]
    public function defaultOrderingsContainsSubmittedAtDescending(): void
    {
        $repository = $this->createPartialMock(ApplicationRepository::class, []);

        $reflection = new \ReflectionProperty(ApplicationRepository::class, 'defaultOrderings');
        $reflection->setAccessible(true);
        $orderings = $reflection->getValue($repository);

        self::assertArrayHasKey('submittedAt', $orderings);
        self::assertSame(QueryInterface::ORDER_DESCENDING, $orderings['submittedAt']);
    }

    #[Test]
    public function defaultOrderingsHasExactlyOneSortKey(): void
    {
        $repository = $this->createPartialMock(ApplicationRepository::class, []);

        $reflection = new \ReflectionProperty(ApplicationRepository::class, 'defaultOrderings');
        $reflection->setAccessible(true);
        $orderings = $reflection->getValue($repository);

        self::assertCount(1, $orderings);
    }

    // ── findByEmail ─────────────────────────────────────────────────────────

    #[Test]
    public function findByEmailMethodExists(): void
    {
        self::assertTrue(method_exists(ApplicationRepository::class, 'findByEmail'));
    }

    #[Test]
    public function findByEmailAcceptsStringParameter(): void
    {
        $method = new \ReflectionMethod(ApplicationRepository::class, 'findByEmail');
        $params = $method->getParameters();

        self::assertCount(1, $params);
        self::assertSame('email', $params[0]->getName());

        $type = $params[0]->getType();
        self::assertInstanceOf(\ReflectionNamedType::class, $type);
        self::assertSame('string', $type->getName());
    }

    #[Test]
    public function findByEmailReturnsNullableApplication(): void
    {
        $method = new \ReflectionMethod(ApplicationRepository::class, 'findByEmail');
        $returnType = $method->getReturnType();

        self::assertInstanceOf(\ReflectionNamedType::class, $returnType);
        self::assertTrue($returnType->allowsNull());
    }
}
