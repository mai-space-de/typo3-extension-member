<?php

declare(strict_types=1);

namespace Maispace\MaiMember\Tests\Unit\Domain\Model;

use Maispace\MaiMember\Domain\Model\Application;
use Maispace\MaiMember\Domain\Model\Member;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ApplicationTest extends TestCase
{
    // ── Default values ──────────────────────────────────────────────────────

    #[Test]
    public function defaultFirstNameIsEmptyString(): void
    {
        $subject = new Application();
        self::assertSame('', $subject->getFirstName());
    }

    #[Test]
    public function defaultLastNameIsEmptyString(): void
    {
        $subject = new Application();
        self::assertSame('', $subject->getLastName());
    }

    #[Test]
    public function defaultEmailIsEmptyString(): void
    {
        $subject = new Application();
        self::assertSame('', $subject->getEmail());
    }

    #[Test]
    public function defaultMessageIsEmptyString(): void
    {
        $subject = new Application();
        self::assertSame('', $subject->getMessage());
    }

    #[Test]
    public function defaultStatusIsPending(): void
    {
        $subject = new Application();
        self::assertSame('pending', $subject->getStatus());
    }

    #[Test]
    public function defaultSubmittedAtIsZero(): void
    {
        $subject = new Application();
        self::assertSame(0, $subject->getSubmittedAt());
    }

    #[Test]
    public function defaultMemberIsNull(): void
    {
        $subject = new Application();
        self::assertNull($subject->getMember());
    }

    // ── Status check methods ────────────────────────────────────────────────

    #[Test]
    public function isPendingReturnsTrueForDefaultStatus(): void
    {
        $subject = new Application();
        self::assertTrue($subject->isPending());
    }

    #[Test]
    public function isPendingReturnsFalseWhenStatusIsApproved(): void
    {
        $subject = new Application();
        $subject->setStatus('approved');
        self::assertFalse($subject->isPending());
    }

    #[Test]
    public function isPendingReturnsFalseWhenStatusIsRejected(): void
    {
        $subject = new Application();
        $subject->setStatus('rejected');
        self::assertFalse($subject->isPending());
    }

    #[Test]
    public function isApprovedReturnsFalseForDefaultStatus(): void
    {
        $subject = new Application();
        self::assertFalse($subject->isApproved());
    }

    #[Test]
    public function isApprovedReturnsTrueWhenStatusIsApproved(): void
    {
        $subject = new Application();
        $subject->setStatus('approved');
        self::assertTrue($subject->isApproved());
    }

    #[Test]
    public function isRejectedReturnsFalseForDefaultStatus(): void
    {
        $subject = new Application();
        self::assertFalse($subject->isRejected());
    }

    #[Test]
    public function isRejectedReturnsTrueWhenStatusIsRejected(): void
    {
        $subject = new Application();
        $subject->setStatus('rejected');
        self::assertTrue($subject->isRejected());
    }

    // ── getFullName ─────────────────────────────────────────────────────────

    #[Test]
    public function getFullNameReturnsBothNames(): void
    {
        $subject = new Application();
        $subject->setFirstName('Jane');
        $subject->setLastName('Doe');
        self::assertSame('Jane Doe', $subject->getFullName());
    }

    #[Test]
    public function getFullNameReturnsOnlyFirstNameWhenLastNameIsEmpty(): void
    {
        $subject = new Application();
        $subject->setFirstName('Jane');
        self::assertSame('Jane', $subject->getFullName());
    }

    #[Test]
    public function getFullNameReturnsOnlyLastNameWhenFirstNameIsEmpty(): void
    {
        $subject = new Application();
        $subject->setLastName('Doe');
        self::assertSame('Doe', $subject->getFullName());
    }

    #[Test]
    public function getFullNameReturnsEmptyStringWhenBothNamesAreEmpty(): void
    {
        $subject = new Application();
        self::assertSame('', $subject->getFullName());
    }

    // ── firstName getter / setter ───────────────────────────────────────────

    #[Test]
    public function setFirstNameStoresTheValue(): void
    {
        $subject = new Application();
        $subject->setFirstName('John');
        self::assertSame('John', $subject->getFirstName());
    }

    // ── lastName getter / setter ────────────────────────────────────────────

    #[Test]
    public function setLastNameStoresTheValue(): void
    {
        $subject = new Application();
        $subject->setLastName('Smith');
        self::assertSame('Smith', $subject->getLastName());
    }

    // ── email getter / setter ───────────────────────────────────────────────

    #[Test]
    public function setEmailStoresTheValue(): void
    {
        $subject = new Application();
        $subject->setEmail('jane@example.com');
        self::assertSame('jane@example.com', $subject->getEmail());
    }

    // ── message getter / setter ─────────────────────────────────────────────

    #[Test]
    public function setMessageStoresTheValue(): void
    {
        $subject = new Application();
        $subject->setMessage('I would like to join.');
        self::assertSame('I would like to join.', $subject->getMessage());
    }

    #[Test]
    public function setMessageOverwritesPreviousValue(): void
    {
        $subject = new Application();
        $subject->setMessage('First message');
        $subject->setMessage('Updated message');
        self::assertSame('Updated message', $subject->getMessage());
    }

    // ── status getter / setter ──────────────────────────────────────────────

    #[Test]
    public function setStatusStoresTheValue(): void
    {
        $subject = new Application();
        $subject->setStatus('approved');
        self::assertSame('approved', $subject->getStatus());
    }

    // ── submittedAt getter / setter ─────────────────────────────────────────

    #[Test]
    public function setSubmittedAtStoresTheValue(): void
    {
        $subject = new Application();
        $subject->setSubmittedAt(1700000000);
        self::assertSame(1700000000, $subject->getSubmittedAt());
    }

    // ── member getter / setter ──────────────────────────────────────────────

    #[Test]
    public function setMemberStoresMemberInstance(): void
    {
        $subject = new Application();
        $member = new Member();
        $subject->setMember($member);
        self::assertSame($member, $subject->getMember());
    }

    #[Test]
    public function setMemberAcceptsNull(): void
    {
        $subject = new Application();
        $member = new Member();
        $subject->setMember($member);
        $subject->setMember(null);
        self::assertNull($subject->getMember());
    }

    // ── instance isolation ──────────────────────────────────────────────────

    #[Test]
    public function twoInstancesHaveIndependentStatuses(): void
    {
        $subject1 = new Application();
        $subject2 = new Application();
        $subject1->setStatus('approved');
        self::assertSame('pending', $subject2->getStatus());
    }

    #[Test]
    public function twoInstancesHaveIndependentMembers(): void
    {
        $subject1 = new Application();
        $subject2 = new Application();
        $subject1->setMember(new Member());
        self::assertNull($subject2->getMember());
    }
}
