<?php

declare(strict_types=1);

namespace Maispace\Member\Tests\Unit\Domain\Model;

use Maispace\Member\Domain\Model\MemberApplication;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(MemberApplication::class)]
class MemberApplicationTest extends TestCase
{
    private MemberApplication $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new MemberApplication();
    }

    #[Test]
    public function applicantNameIsInitiallyEmpty(): void
    {
        self::assertSame('', $this->subject->getApplicantName());
    }

    #[Test]
    public function setApplicantNameStoresName(): void
    {
        $this->subject->setApplicantName('John Doe');
        self::assertSame('John Doe', $this->subject->getApplicantName());
    }

    #[Test]
    public function emailIsInitiallyEmpty(): void
    {
        self::assertSame('', $this->subject->getEmail());
    }

    #[Test]
    public function setEmailStoresEmail(): void
    {
        $this->subject->setEmail('john@example.com');
        self::assertSame('john@example.com', $this->subject->getEmail());
    }

    #[Test]
    public function motivationIsInitiallyEmpty(): void
    {
        self::assertSame('', $this->subject->getMotivation());
    }

    #[Test]
    public function setMotivationStoresMotivation(): void
    {
        $this->subject->setMotivation('I want to join because...');
        self::assertSame('I want to join because...', $this->subject->getMotivation());
    }

    #[Test]
    public function statusIsInitiallyPending(): void
    {
        self::assertSame(MemberApplication::STATUS_PENDING, $this->subject->getStatus());
    }

    #[Test]
    public function isPendingReturnsTrueWhenStatusIsPending(): void
    {
        self::assertTrue($this->subject->isPending());
    }

    #[Test]
    public function isPendingReturnsFalseAfterStatusChange(): void
    {
        $this->subject->setStatus(MemberApplication::STATUS_APPROVED);
        self::assertFalse($this->subject->isPending());
    }

    #[Test]
    public function isApprovedReturnsTrueWhenStatusIsApproved(): void
    {
        $this->subject->setStatus(MemberApplication::STATUS_APPROVED);
        self::assertTrue($this->subject->isApproved());
    }

    #[Test]
    public function isRejectedReturnsTrueWhenStatusIsRejected(): void
    {
        $this->subject->setStatus(MemberApplication::STATUS_REJECTED);
        self::assertTrue($this->subject->isRejected());
    }

    #[Test]
    public function isActiveReturnsTrueWhenStatusIsActive(): void
    {
        $this->subject->setStatus(MemberApplication::STATUS_ACTIVE);
        self::assertTrue($this->subject->isActive());
    }

    #[Test]
    public function memberIsInitiallyNull(): void
    {
        self::assertNull($this->subject->getMember());
    }

    #[Test]
    public function documentsStorageIsInitiallyEmpty(): void
    {
        self::assertCount(0, $this->subject->getDocuments());
    }
}
