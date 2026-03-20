<?php

declare(strict_types=1);

namespace Maispace\MaiMember\Domain\Repository;

use Maispace\MaiMember\Domain\Model\MemberApplication;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * @extends Repository<MemberApplication>
 */
class MemberApplicationRepository extends Repository
{
    protected $defaultOrderings = [
        'crdate' => QueryInterface::ORDER_DESCENDING,
    ];

    /**
     * Find applications by status.
     *
     * @return QueryResultInterface<MemberApplication>
     */
    public function findByStatus(int $status): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->matching(
            $query->equals('status', $status)
        );
        return $query->execute();
    }

    /**
     * Find all pending applications.
     *
     * @return QueryResultInterface<MemberApplication>
     */
    public function findPending(): QueryResultInterface
    {
        return $this->findByStatus(MemberApplication::STATUS_PENDING);
    }

    /**
     * Find all approved applications.
     *
     * @return QueryResultInterface<MemberApplication>
     */
    public function findApproved(): QueryResultInterface
    {
        return $this->findByStatus(MemberApplication::STATUS_APPROVED);
    }
}
