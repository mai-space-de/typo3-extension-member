<?php

declare(strict_types=1);

namespace Maispace\MaiMember\Tests\Unit\Service;

use Maispace\MaiMember\Domain\Model\Application;
use Maispace\MaiMember\Domain\Model\Member;
use Maispace\MaiMember\Domain\Repository\ApplicationRepository;
use Maispace\MaiMember\Domain\Repository\MemberRepository;
use Maispace\MaiMember\Service\ApplicationService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

final class ApplicationServiceTest extends TestCase
{
    private ApplicationRepository&MockObject $applicationRepository;
    private MemberRepository&MockObject $memberRepository;
    private PersistenceManagerInterface&MockObject $persistenceManager;
    private ApplicationService $subject;

    protected function setUp(): void
    {
        $this->applicationRepository = $this->createMock(ApplicationRepository::class);
        $this->memberRepository = $this->createMock(MemberRepository::class);
        $this->persistenceManager = $this->createMock(PersistenceManagerInterface::class);

        $this->subject = new ApplicationService(
            $this->applicationRepository,
            $this->memberRepository,
            $this->persistenceManager,
        );
    }

    // ── approve ─────────────────────────────────────────────────────────────

    #[Test]
    public function approveChangesApplicationStatusToApproved(): void
    {
        $application = $this->buildApplication('Jane', 'Doe', 'jane@example.com');
        self::assertSame('pending', $application->getStatus());

        $this->memberRepository->expects(self::once())->method('add');
        $this->applicationRepository->expects(self::once())->method('update');
        $this->persistenceManager->expects(self::once())->method('persistAll');

        $this->subject->approve($application);

        self::assertSame('approved', $application->getStatus());
    }

    #[Test]
    public function approveAssociatesNewMemberWithApplication(): void
    {
        $application = $this->buildApplication('Jane', 'Doe', 'jane@example.com');

        $this->memberRepository->method('add');
        $this->applicationRepository->method('update');
        $this->persistenceManager->method('persistAll');

        $this->subject->approve($application);

        self::assertInstanceOf(Member::class, $application->getMember());
    }

    #[Test]
    public function approveReturnsNewlyCreatedMember(): void
    {
        $application = $this->buildApplication('Jane', 'Doe', 'jane@example.com');

        $this->memberRepository->method('add');
        $this->applicationRepository->method('update');
        $this->persistenceManager->method('persistAll');

        $member = $this->subject->approve($application);

        self::assertInstanceOf(Member::class, $member);
    }

    #[Test]
    public function approveCreatesActiveMember(): void
    {
        $application = $this->buildApplication('Jane', 'Doe', 'jane@example.com');

        $this->memberRepository->method('add');
        $this->applicationRepository->method('update');
        $this->persistenceManager->method('persistAll');

        $member = $this->subject->approve($application);

        self::assertSame('active', $member->getStatus());
    }

    #[Test]
    public function approveCopiesToApplicantNameToMember(): void
    {
        $application = $this->buildApplication('Jane', 'Doe', 'jane@example.com');

        $this->memberRepository->method('add');
        $this->applicationRepository->method('update');
        $this->persistenceManager->method('persistAll');

        $member = $this->subject->approve($application);

        self::assertSame('Jane', $member->getFirstName());
        self::assertSame('Doe', $member->getLastName());
    }

    #[Test]
    public function approveCopiesToApplicantEmailToMember(): void
    {
        $application = $this->buildApplication('Jane', 'Doe', 'jane@example.com');

        $this->memberRepository->method('add');
        $this->applicationRepository->method('update');
        $this->persistenceManager->method('persistAll');

        $member = $this->subject->approve($application);

        self::assertSame('jane@example.com', $member->getEmail());
    }

    #[Test]
    public function approveSetsMemberJoinDate(): void
    {
        $application = $this->buildApplication('Jane', 'Doe', 'jane@example.com');

        $this->memberRepository->method('add');
        $this->applicationRepository->method('update');
        $this->persistenceManager->method('persistAll');

        $before = time();
        $member = $this->subject->approve($application);
        $after = time();

        self::assertGreaterThanOrEqual($before, $member->getJoinDate());
        self::assertLessThanOrEqual($after, $member->getJoinDate());
    }

    #[Test]
    public function approveUsesMemberStoragePidWhenProvided(): void
    {
        $application = $this->buildApplication('Jane', 'Doe', 'jane@example.com');

        $this->memberRepository->method('add');
        $this->applicationRepository->method('update');
        $this->persistenceManager->method('persistAll');

        $member = $this->subject->approve($application, 42);

        self::assertSame(42, $member->getPid());
    }

    // ── reject ──────────────────────────────────────────────────────────────

    #[Test]
    public function rejectChangesApplicationStatusToRejected(): void
    {
        $application = $this->buildApplication('John', 'Smith', 'john@example.com');
        self::assertSame('pending', $application->getStatus());

        $this->applicationRepository->expects(self::once())->method('update');
        $this->persistenceManager->expects(self::once())->method('persistAll');

        $this->subject->reject($application);

        self::assertSame('rejected', $application->getStatus());
    }

    #[Test]
    public function rejectDoesNotCreateMemberRecord(): void
    {
        $application = $this->buildApplication('John', 'Smith', 'john@example.com');

        $this->memberRepository->expects(self::never())->method('add');
        $this->applicationRepository->method('update');
        $this->persistenceManager->method('persistAll');

        $this->subject->reject($application);
    }

    #[Test]
    public function rejectPersistsChanges(): void
    {
        $application = $this->buildApplication('John', 'Smith', 'john@example.com');

        $this->applicationRepository->method('update');
        $this->persistenceManager->expects(self::once())->method('persistAll');

        $this->subject->reject($application);
    }

    // ── helpers ─────────────────────────────────────────────────────────────

    private function buildApplication(string $firstName, string $lastName, string $email): Application
    {
        $application = new Application();
        $application->setFirstName($firstName);
        $application->setLastName($lastName);
        $application->setEmail($email);

        return $application;
    }
}
