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
}
