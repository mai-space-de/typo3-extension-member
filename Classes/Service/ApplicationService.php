<?php

declare(strict_types=1);

namespace Maispace\MaiMember\Service;

use Maispace\MaiMember\Domain\Model\Application;
use Maispace\MaiMember\Domain\Model\Member;
use Maispace\MaiMember\Domain\Repository\ApplicationRepository;
use Maispace\MaiMember\Domain\Repository\MemberRepository;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

class ApplicationService
{
    public function __construct(
        private readonly ApplicationRepository $applicationRepository,
        private readonly MemberRepository $memberRepository,
        private readonly PersistenceManagerInterface $persistenceManager,
    ) {
    }

    public function approve(Application $application, int $memberStoragePid = 0): Member
    {
        $member = new Member();
        $member->setPid($memberStoragePid > 0 ? $memberStoragePid : $application->getPid());
        $member->setFirstName($application->getFirstName());
        $member->setLastName($application->getLastName());
        $member->setEmail($application->getEmail());
        $member->setStatus('active');
        $member->setJoinDate(time());

        $this->memberRepository->add($member);

        $application->setStatus('approved');
        $application->setMember($member);
        $this->applicationRepository->update($application);

        $this->persistenceManager->persistAll();

        return $member;
    }

    public function reject(Application $application): void
    {
        $application->setStatus('rejected');
        $this->applicationRepository->update($application);
        $this->persistenceManager->persistAll();
    }
}
