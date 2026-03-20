<?php

declare(strict_types=1);

namespace Maispace\MaiMember\Service;

use Maispace\MaiMember\Domain\Model\Member;
use Maispace\MaiMember\Domain\Model\MemberApplication;
use Maispace\MaiMember\Domain\Repository\MemberApplicationRepository;
use Maispace\MaiMember\Domain\Repository\MemberRepository;
use Maispace\MaiMember\Event\MemberApprovedEvent;
use Maispace\MaiMember\Event\MemberStatusChangedEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

class ApplicationService
{
    public function __construct(
        private readonly MemberApplicationRepository $applicationRepository,
        private readonly MemberRepository $memberRepository,
        private readonly PersistenceManagerInterface $persistenceManager,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {}

    /**
     * Approve a pending application and create an active member record.
     *
     * Transition: pending → approved
     */
    public function approveApplication(MemberApplication $application): Member
    {
        if (!$application->isPending()) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Cannot approve application %d: expected status pending (%d), got %d.',
                    $application->getUid() ?? 0,
                    MemberApplication::STATUS_PENDING,
                    $application->getStatus()
                ),
                1_700_000_001
            );
        }

        $application->setStatus(MemberApplication::STATUS_APPROVED);
        $this->applicationRepository->update($application);

        $member = new Member();
        $member->setName($application->getApplicantName());
        $member->setStatus(Member::STATUS_INACTIVE);
        $member->setEntryDate(new \DateTimeImmutable());
        $member->setInterests('');

        $this->memberRepository->add($member);
        $application->setMember($member);
        $this->applicationRepository->update($application);
        $this->persistenceManager->persistAll();

        $this->eventDispatcher->dispatch(new MemberApprovedEvent($member, $application));

        return $member;
    }

    /**
     * Activate an approved application's member.
     *
     * Transition: approved → active
     */
    public function activateMember(MemberApplication $application): Member
    {
        if (!$application->isApproved()) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Cannot activate application %d: expected status approved (%d), got %d.',
                    $application->getUid() ?? 0,
                    MemberApplication::STATUS_APPROVED,
                    $application->getStatus()
                ),
                1_700_000_002
            );
        }

        $member = $application->getMember();
        if ($member === null) {
            throw new \RuntimeException(
                sprintf('Application %d has no associated member.', $application->getUid() ?? 0),
                1_700_000_003
            );
        }

        $previousStatus = $member->getStatus();
        $member->setStatus(Member::STATUS_ACTIVE);
        $this->memberRepository->update($member);

        $application->setStatus(MemberApplication::STATUS_ACTIVE);
        $this->applicationRepository->update($application);
        $this->persistenceManager->persistAll();

        $this->eventDispatcher->dispatch(new MemberStatusChangedEvent($member, $previousStatus, Member::STATUS_ACTIVE));

        return $member;
    }

    /**
     * Reject a pending application.
     */
    public function rejectApplication(MemberApplication $application): void
    {
        if (!$application->isPending()) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Cannot reject application %d: expected status pending (%d), got %d.',
                    $application->getUid() ?? 0,
                    MemberApplication::STATUS_PENDING,
                    $application->getStatus()
                ),
                1_700_000_004
            );
        }

        $application->setStatus(MemberApplication::STATUS_REJECTED);
        $this->applicationRepository->update($application);
        $this->persistenceManager->persistAll();
    }

    /**
     * Change a member's status and dispatch the status-changed event.
     */
    public function changeMemberStatus(Member $member, int $newStatus): void
    {
        $previousStatus = $member->getStatus();
        if ($previousStatus === $newStatus) {
            return;
        }

        $member->setStatus($newStatus);
        $this->memberRepository->update($member);
        $this->persistenceManager->persistAll();

        $this->eventDispatcher->dispatch(new MemberStatusChangedEvent($member, $previousStatus, $newStatus));
    }
}
