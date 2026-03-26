<?php

declare(strict_types = 1);

namespace Maispace\MaiMember\Tests\Unit\Service;

use Maispace\MaiMember\Domain\Model\Member;
use Maispace\MaiMember\Domain\Model\MemberApplication;
use Maispace\MaiMember\Domain\Repository\MemberApplicationRepository;
use Maispace\MaiMember\Domain\Repository\MemberRepository;
use Maispace\MaiMember\Event\MemberApprovedEvent;
use Maispace\MaiMember\Event\MemberStatusChangedEvent;
use Maispace\MaiMember\Service\ApplicationService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

#[CoversClass(ApplicationService::class)]
class ApplicationServiceTest extends TestCase
{
    private MemberApplicationRepository&MockObject $applicationRepository;
    private MemberRepository&MockObject $memberRepository;
    private PersistenceManagerInterface&MockObject $persistenceManager;
    private EventDispatcherInterface&MockObject $eventDispatcher;
    private ApplicationService $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->applicationRepository = $this->createMock(MemberApplicationRepository::class);
        $this->memberRepository = $this->createMock(MemberRepository::class);
        $this->persistenceManager = $this->createMock(PersistenceManagerInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->subject = new ApplicationService(
            $this->applicationRepository,
            $this->memberRepository,
            $this->persistenceManager,
            $this->eventDispatcher
        );
    }

    #[Test]
    public function approveApplicationTransitionsStatusToApproved(): void
    {
        $application = new MemberApplication();
        $application->setApplicantName('Jane Doe');
        $application->setEmail('jane@example.com');

        $this->applicationRepository->expects(self::atLeastOnce())->method('update');
        $this->memberRepository->expects(self::once())->method('add');
        $this->persistenceManager->expects(self::once())->method('persistAll');
        $this->eventDispatcher->expects(self::once())
            ->method('dispatch')
            ->with(self::isInstanceOf(MemberApprovedEvent::class));

        $member = $this->subject->approveApplication($application);

        self::assertSame(MemberApplication::STATUS_APPROVED, $application->getStatus());
        self::assertSame('Jane Doe', $member->getName());
        self::assertSame(Member::STATUS_INACTIVE, $member->getStatus());
    }

    #[Test]
    public function approveApplicationThrowsExceptionWhenNotPending(): void
    {
        $application = new MemberApplication();
        $application->setStatus(MemberApplication::STATUS_APPROVED);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1_700_000_001);

        $this->subject->approveApplication($application);
    }

    #[Test]
    public function activateMemberTransitionsStatusToActive(): void
    {
        $member = new Member();
        $member->setName('Jane Doe');
        $member->setStatus(Member::STATUS_INACTIVE);

        $application = new MemberApplication();
        $application->setStatus(MemberApplication::STATUS_APPROVED);
        $application->setMember($member);

        $this->memberRepository->expects(self::once())->method('update');
        $this->applicationRepository->expects(self::once())->method('update');
        $this->persistenceManager->expects(self::once())->method('persistAll');
        $this->eventDispatcher->expects(self::once())
            ->method('dispatch')
            ->with(self::isInstanceOf(MemberStatusChangedEvent::class));

        $result = $this->subject->activateMember($application);

        self::assertSame(Member::STATUS_ACTIVE, $result->getStatus());
        self::assertSame(MemberApplication::STATUS_ACTIVE, $application->getStatus());
    }

    #[Test]
    public function activateMemberThrowsExceptionWhenNotApproved(): void
    {
        $application = new MemberApplication();
        $application->setStatus(MemberApplication::STATUS_PENDING);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1_700_000_002);

        $this->subject->activateMember($application);
    }

    #[Test]
    public function activateMemberThrowsExceptionWhenNoMemberAssociated(): void
    {
        $application = new MemberApplication();
        $application->setStatus(MemberApplication::STATUS_APPROVED);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(1_700_000_003);

        $this->subject->activateMember($application);
    }

    #[Test]
    public function rejectApplicationTransitionsStatusToRejected(): void
    {
        $application = new MemberApplication();

        $this->applicationRepository->expects(self::once())->method('update');
        $this->persistenceManager->expects(self::once())->method('persistAll');

        $this->subject->rejectApplication($application);

        self::assertSame(MemberApplication::STATUS_REJECTED, $application->getStatus());
    }

    #[Test]
    public function rejectApplicationThrowsExceptionWhenNotPending(): void
    {
        $application = new MemberApplication();
        $application->setStatus(MemberApplication::STATUS_APPROVED);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1_700_000_004);

        $this->subject->rejectApplication($application);
    }

    #[Test]
    public function changeMemberStatusDispatchesEvent(): void
    {
        $member = new Member();
        $member->setStatus(Member::STATUS_INACTIVE);

        $this->memberRepository->expects(self::once())->method('update');
        $this->persistenceManager->expects(self::once())->method('persistAll');

        $capturedEvent = null;
        $this->eventDispatcher->expects(self::once())
            ->method('dispatch')
            ->willReturnCallback(function (object $event) use (&$capturedEvent): object {
                $capturedEvent = $event;

                return $event;
            });

        $this->subject->changeMemberStatus($member, Member::STATUS_ACTIVE);

        self::assertInstanceOf(MemberStatusChangedEvent::class, $capturedEvent);
        self::assertSame(Member::STATUS_INACTIVE, $capturedEvent->getPreviousStatus());
        self::assertSame(Member::STATUS_ACTIVE, $capturedEvent->getNewStatus());
    }

    #[Test]
    public function changeMemberStatusDoesNothingWhenStatusIsUnchanged(): void
    {
        $member = new Member();
        $member->setStatus(Member::STATUS_ACTIVE);

        $this->memberRepository->expects(self::never())->method('update');
        $this->eventDispatcher->expects(self::never())->method('dispatch');

        $this->subject->changeMemberStatus($member, Member::STATUS_ACTIVE);
    }
}
