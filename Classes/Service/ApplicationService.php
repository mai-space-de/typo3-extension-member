<?php

declare(strict_types=1);

namespace Maispace\MaiMember\Service;

use Maispace\MaiBase\Backend\CustomPageModule\DoktypeIds;
use Maispace\MaiMember\Domain\Model\Application;
use Maispace\MaiMember\Domain\Model\Member;
use Maispace\MaiMember\Domain\Repository\ApplicationRepository;
use Maispace\MaiMember\Domain\Repository\MemberRepository;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Crypto\Random;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

class ApplicationService
{
    /** Parent page for new Member doktype pages (Aktive Mitglieder). */
    private const int DEFAULT_PARENT_PID = 5;

    private bool $createFeUserEnabled;

    public function __construct(
        private readonly ApplicationRepository $applicationRepository,
        private readonly MemberRepository $memberRepository,
        private readonly PersistenceManagerInterface $persistenceManager,
        private readonly ConnectionPool $connectionPool,
        private readonly Random $random,
        private readonly ?LoggerInterface $logger = null,
    ) {
        $this->createFeUserEnabled = $this->resolveCreateFeUserEnabled();
    }

    private function resolveCreateFeUserEnabled(): bool
    {
        try {
            return ExtensionManagementUtility::isLoaded('mai_account');
        } catch (\Error) {
            return false;
        }
    }

    /** @internal for testing only */
    public function enableFeUserCreation(): void
    {
        $this->createFeUserEnabled = true;
    }

    /** @internal for testing only */
    public function disableFeUserCreation(): void
    {
        $this->createFeUserEnabled = false;
    }

    public function approve(Application $application, int $memberStoragePid = 0): Member
    {
        $parentPid = $memberStoragePid > 0 ? $memberStoragePid : self::DEFAULT_PARENT_PID;
        $fullName = $application->getFullName();
        $slugSegment = $this->buildSlugSegment($fullName, $application->getEmail());

        $member = new Member();
        $member->setPid($parentPid);
        $member->setDoktype(DoktypeIds::MEMBER);
        $member->setTitle($fullName);
        $member->setSlug('/ueber-den-verein/aktive-mitglieder/' . $slugSegment);
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

        if ($this->createFeUserEnabled) {
            $this->createFeUserForMember($member);
        }

        return $member;
    }

    public function reject(Application $application): void
    {
        $application->setStatus('rejected');
        $this->applicationRepository->update($application);
        $this->persistenceManager->persistAll();
    }

    protected function createFeUserForMember(Member $member): void
    {
        $email = $member->getEmail();
        if ($email === '') {
            return;
        }

        $password = $this->random->generateRandomHexString(16);
        $memberUid = $member->getUid();
        $pid = $member->getPid();

        try {
            $registrationService = GeneralUtility::makeInstance('Maispace\\MaiAccount\\Service\\RegistrationService');
            $result = $registrationService->register(
                $email,
                $member->getEmail(),
                $password,
                $member->getFirstName(),
                $member->getLastName(),
                $pid,
            );

            $feUserUid = $result['uid'];
            $registrationService->confirm($result['token']);

            $this->connectionPool
                ->getConnectionForTable('fe_users')
                ->update('fe_users', ['tx_maiaccount_member_uid' => $memberUid], ['uid' => $feUserUid]);

            $this->connectionPool
                ->getConnectionForTable('pages')
                ->update('pages', ['tx_maimember_fe_user' => $feUserUid], ['uid' => $memberUid]);
        } catch (\Throwable $e) {
            $this->logger?->error(
                'Failed to create fe_user for member {uid}: {message}',
                ['uid' => $memberUid, 'message' => $e->getMessage()],
            );
        }
    }

    private function buildSlugSegment(string $fullName, string $email): string
    {
        $base = strtolower(trim($fullName));
        $base = preg_replace('/[^a-z0-9]+/i', '-', $base) ?? '';
        $segment = trim($base, '-');
        if ($segment !== '') {
            return $segment;
        }

        $parts = explode('@', $email, 2);
        $emailLocal = strtolower($parts[0] !== '' ? $parts[0] : 'member');
        $emailLocal = preg_replace('/[^a-z0-9]+/i', '-', $emailLocal) ?? 'member';

        return trim($emailLocal, '-') ?: 'member';
    }
}
