<?php

declare(strict_types=1);

namespace Maispace\MaiMember\Tests\Unit\Domain\Model;

use Maispace\MaiMember\Domain\Model\Member;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MemberTest extends TestCase
{
    // ── Default values ──────────────────────────────────────────────────────

    #[Test]
    public function defaultFirstNameIsEmptyString(): void
    {
        $subject = new Member();
        self::assertSame('', $subject->getFirstName());
    }

    #[Test]
    public function defaultLastNameIsEmptyString(): void
    {
        $subject = new Member();
        self::assertSame('', $subject->getLastName());
    }

    #[Test]
    public function defaultEmailIsEmptyString(): void
    {
        $subject = new Member();
        self::assertSame('', $subject->getEmail());
    }

    #[Test]
    public function defaultPhoneIsEmptyString(): void
    {
        $subject = new Member();
        self::assertSame('', $subject->getPhone());
    }

    #[Test]
    public function defaultStatusIsActive(): void
    {
        $subject = new Member();
        self::assertSame('active', $subject->getStatus());
    }

    #[Test]
    public function defaultJoinDateIsZero(): void
    {
        $subject = new Member();
        self::assertSame(0, $subject->getJoinDate());
    }

    #[Test]
    public function defaultImageIsNull(): void
    {
        $subject = new Member();
        self::assertNull($subject->getImage());
    }

    #[Test]
    public function defaultFeUserIsNull(): void
    {
        $subject = new Member();
        self::assertNull($subject->getFeUser());
    }

    // ── isActive ────────────────────────────────────────────────────────────

    #[Test]
    public function isActiveReturnsTrueWhenStatusIsActive(): void
    {
        $subject = new Member();
        self::assertTrue($subject->isActive());
    }

    #[Test]
    public function isActiveReturnsFalseWhenStatusIsInactive(): void
    {
        $subject = new Member();
        $subject->setStatus('inactive');
        self::assertFalse($subject->isActive());
    }

    #[Test]
    public function isActiveReturnsFalseWhenStatusIsSuspended(): void
    {
        $subject = new Member();
        $subject->setStatus('suspended');
        self::assertFalse($subject->isActive());
    }

    // ── getFullName ─────────────────────────────────────────────────────────

    #[Test]
    public function getFullNameReturnsBothNames(): void
    {
        $subject = new Member();
        $subject->setFirstName('Jane');
        $subject->setLastName('Doe');
        self::assertSame('Jane Doe', $subject->getFullName());
    }

    #[Test]
    public function getFullNameReturnsOnlyFirstNameWhenLastNameIsEmpty(): void
    {
        $subject = new Member();
        $subject->setFirstName('Jane');
        self::assertSame('Jane', $subject->getFullName());
    }

    #[Test]
    public function getFullNameReturnsOnlyLastNameWhenFirstNameIsEmpty(): void
    {
        $subject = new Member();
        $subject->setLastName('Doe');
        self::assertSame('Doe', $subject->getFullName());
    }

    #[Test]
    public function getFullNameReturnsEmptyStringWhenBothNamesAreEmpty(): void
    {
        $subject = new Member();
        self::assertSame('', $subject->getFullName());
    }

    // ── firstName getter / setter ───────────────────────────────────────────

    #[Test]
    public function setFirstNameStoresTheValue(): void
    {
        $subject = new Member();
        $subject->setFirstName('John');
        self::assertSame('John', $subject->getFirstName());
    }

    #[Test]
    public function setFirstNameOverwritesPreviousValue(): void
    {
        $subject = new Member();
        $subject->setFirstName('John');
        $subject->setFirstName('Jane');
        self::assertSame('Jane', $subject->getFirstName());
    }

    // ── lastName getter / setter ────────────────────────────────────────────

    #[Test]
    public function setLastNameStoresTheValue(): void
    {
        $subject = new Member();
        $subject->setLastName('Smith');
        self::assertSame('Smith', $subject->getLastName());
    }

    #[Test]
    public function setLastNameOverwritesPreviousValue(): void
    {
        $subject = new Member();
        $subject->setLastName('Smith');
        $subject->setLastName('Jones');
        self::assertSame('Jones', $subject->getLastName());
    }

    // ── email getter / setter ───────────────────────────────────────────────

    #[Test]
    public function setEmailStoresTheValue(): void
    {
        $subject = new Member();
        $subject->setEmail('jane@example.com');
        self::assertSame('jane@example.com', $subject->getEmail());
    }

    #[Test]
    public function setEmailOverwritesPreviousValue(): void
    {
        $subject = new Member();
        $subject->setEmail('old@example.com');
        $subject->setEmail('new@example.com');
        self::assertSame('new@example.com', $subject->getEmail());
    }

    // ── phone getter / setter ───────────────────────────────────────────────

    #[Test]
    public function setPhoneStoresTheValue(): void
    {
        $subject = new Member();
        $subject->setPhone('+49 221 123456');
        self::assertSame('+49 221 123456', $subject->getPhone());
    }

    // ── status getter / setter ──────────────────────────────────────────────

    #[Test]
    public function setStatusStoresTheValue(): void
    {
        $subject = new Member();
        $subject->setStatus('inactive');
        self::assertSame('inactive', $subject->getStatus());
    }

    // ── joinDate getter / setter ────────────────────────────────────────────

    #[Test]
    public function setJoinDateStoresTheValue(): void
    {
        $subject = new Member();
        $subject->setJoinDate(1700000000);
        self::assertSame(1700000000, $subject->getJoinDate());
    }

    // ── instance isolation ──────────────────────────────────────────────────

    #[Test]
    public function twoInstancesHaveIndependentFirstNames(): void
    {
        $subject1 = new Member();
        $subject2 = new Member();
        $subject1->setFirstName('Alice');
        self::assertSame('', $subject2->getFirstName());
    }

    #[Test]
    public function twoInstancesHaveIndependentStatuses(): void
    {
        $subject1 = new Member();
        $subject2 = new Member();
        $subject1->setStatus('inactive');
        self::assertSame('active', $subject2->getStatus());
    }
}
