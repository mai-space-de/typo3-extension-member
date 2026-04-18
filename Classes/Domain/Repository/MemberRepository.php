<?php

declare(strict_types=1);

namespace Maispace\MaiMember\Domain\Repository;

use Maispace\MaiMember\Domain\Model\Member;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * @extends Repository<Member>
 */
class MemberRepository extends Repository
{
    /**
     * @var array<string, string>
     */
    protected $defaultOrderings = [
        'lastName' => QueryInterface::ORDER_ASCENDING,
        'firstName' => QueryInterface::ORDER_ASCENDING,
    ];

    /**
     * @return QueryResultInterface<Member>
     */
    public function findByStatus(string $status): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->matching($query->equals('status', $status));
        return $query->execute();
    }

    /**
     * @return QueryResultInterface<Member>
     */
    public function findActive(): QueryResultInterface
    {
        return $this->findByStatus('active');
    }
}
