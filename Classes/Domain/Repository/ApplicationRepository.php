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
     * @var array<non-empty-string, 'ASC'|'DESC'>
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

    public function findByEmail(string $email): ?Application
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('email', $email),
                $query->in('status', ['pending', 'approved']),
            ),
        );
        $query->setLimit(1);

        /** @var QueryResultInterface<Application> $result */
        $result = $query->execute();

        return $result->getFirst();
    }
}
