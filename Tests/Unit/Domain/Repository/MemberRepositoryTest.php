<?php

declare(strict_types=1);

namespace Maispace\MaiMember\Tests\Unit\Domain\Repository;

use Maispace\MaiMember\Domain\Repository\MemberRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

final class MemberRepositoryTest extends TestCase
{
    #[Test]
    public function memberRepositoryExtendsExtbaseRepository(): void
    {
        self::assertInstanceOf(Repository::class, $this->createPartialMock(MemberRepository::class, []));
    }

    #[Test]
    public function defaultOrderingsContainsLastNameAscending(): void
    {
        $repository = $this->createPartialMock(MemberRepository::class, []);

        $reflection = new \ReflectionProperty(MemberRepository::class, 'defaultOrderings');
        $reflection->setAccessible(true);
        $orderings = $reflection->getValue($repository);

        self::assertArrayHasKey('lastName', $orderings);
        self::assertSame(QueryInterface::ORDER_ASCENDING, $orderings['lastName']);
    }

    #[Test]
    public function defaultOrderingsContainsFirstNameAscending(): void
    {
        $repository = $this->createPartialMock(MemberRepository::class, []);

        $reflection = new \ReflectionProperty(MemberRepository::class, 'defaultOrderings');
        $reflection->setAccessible(true);
        $orderings = $reflection->getValue($repository);

        self::assertArrayHasKey('firstName', $orderings);
        self::assertSame(QueryInterface::ORDER_ASCENDING, $orderings['firstName']);
    }

    #[Test]
    public function defaultOrderingsHasExactlyTwoSortKeys(): void
    {
        $repository = $this->createPartialMock(MemberRepository::class, []);

        $reflection = new \ReflectionProperty(MemberRepository::class, 'defaultOrderings');
        $reflection->setAccessible(true);
        $orderings = $reflection->getValue($repository);

        self::assertCount(2, $orderings);
    }
}
