<?php

declare(strict_types=1);

namespace Maispace\Member\Tests\Unit\Domain\Model;

use Maispace\Member\Domain\Model\Member;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Member::class)]
class MemberTest extends TestCase
{
    private Member $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new Member();
    }

    #[Test]
    public function nameIsInitiallyEmpty(): void
    {
        self::assertSame('', $this->subject->getName());
    }

    #[Test]
    public function setNameStoresName(): void
    {
        $this->subject->setName('Jane Doe');
        self::assertSame('Jane Doe', $this->subject->getName());
    }

    #[Test]
    public function statusIsInitiallyInactive(): void
    {
        self::assertSame(Member::STATUS_INACTIVE, $this->subject->getStatus());
    }

    #[Test]
    public function setStatusStoresStatus(): void
    {
        $this->subject->setStatus(Member::STATUS_ACTIVE);
        self::assertSame(Member::STATUS_ACTIVE, $this->subject->getStatus());
    }

    #[Test]
    public function isActiveReturnsTrueWhenStatusIsActive(): void
    {
        $this->subject->setStatus(Member::STATUS_ACTIVE);
        self::assertTrue($this->subject->isActive());
    }

    #[Test]
    public function isActiveReturnsFalseWhenStatusIsInactive(): void
    {
        $this->subject->setStatus(Member::STATUS_INACTIVE);
        self::assertFalse($this->subject->isActive());
    }

    #[Test]
    public function isActiveReturnsFalseWhenStatusIsSuspended(): void
    {
        $this->subject->setStatus(Member::STATUS_SUSPENDED);
        self::assertFalse($this->subject->isActive());
    }

    #[Test]
    public function entryDateIsInitiallyNull(): void
    {
        self::assertNull($this->subject->getEntryDate());
    }

    #[Test]
    public function setEntryDateStoresDate(): void
    {
        $date = new \DateTimeImmutable('2024-01-15');
        $this->subject->setEntryDate($date);
        self::assertSame($date, $this->subject->getEntryDate());
    }

    #[Test]
    public function interestsIsInitiallyEmpty(): void
    {
        self::assertSame('', $this->subject->getInterests());
    }

    #[Test]
    public function setInterestsStoresInterests(): void
    {
        $this->subject->setInterests('music,sports,reading');
        self::assertSame('music,sports,reading', $this->subject->getInterests());
    }

    #[Test]
    public function getInterestsArrayReturnsEmptyArrayWhenEmpty(): void
    {
        self::assertSame([], $this->subject->getInterestsArray());
    }

    #[Test]
    public function getInterestsArrayReturnsParsedArray(): void
    {
        $this->subject->setInterests('music, sports, reading');
        self::assertSame(['music', 'sports', 'reading'], $this->subject->getInterestsArray());
    }

    #[Test]
    public function getInterestsArrayFiltersEmptyEntries(): void
    {
        $this->subject->setInterests('music,,sports');
        self::assertSame(['music', 'sports'], $this->subject->getInterestsArray());
    }

    #[Test]
    public function feUserIsInitiallyNull(): void
    {
        self::assertNull($this->subject->getFeUser());
    }

    #[Test]
    public function photoStorageIsInitiallyEmpty(): void
    {
        self::assertCount(0, $this->subject->getPhoto());
    }
}
