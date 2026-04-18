<?php

declare(strict_types=1);

namespace Maispace\MaiMember\Domain\Repository;

use Maispace\MaiMember\Domain\Model\Application;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * @extends Repository<Application>
 */
class ApplicationRepository extends Repository
{
    /**
     * @var array<string, string>
     */
    protected $defaultOrderings = [
        'submittedAt' => QueryInterface::ORDER_DESCENDING,
    ];

    /**
     * @return QueryResultInterface<Application>
     */
    public function findByStatus(string $status): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->matching($query->equals('status', $status));
        return $query->execute();
    }

    /**
     * @return QueryResultInterface<Application>
     */
    public function findPending(): QueryResultInterface
    {
        return $this->findByStatus('pending');
    }
}
