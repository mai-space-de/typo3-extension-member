<?php

declare(strict_types=1);

namespace Maispace\MaiMember\Domain\Repository;

use Maispace\MaiBase\Backend\CustomPageModule\DoktypeIds;
use Maispace\MaiMember\Domain\Model\Member;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Reads Member doktype pages (123). Parent UIDs in FlexForm `pages` filter by pid.
 *
 * @extends Repository<Member>
 */
class MemberRepository extends Repository
{
    /**
     * @var array<non-empty-string, 'ASC'|'DESC'>
     */
    protected $defaultOrderings = [
        'lastName' => QueryInterface::ORDER_ASCENDING,
        'firstName' => QueryInterface::ORDER_ASCENDING,
    ];

    public function initializeObject(): void
    {
        $querySettings = $this->createQuery()->getQuerySettings();
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * @return QueryResultInterface<Member>
     */
    public function findByStatus(string $status): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $this->doktypeConstraint($query),
                $query->equals('status', $status),
            ),
        );

        return $query->execute();
    }

    /**
     * @return QueryResultInterface<Member>
     */
    public function findActive(): QueryResultInterface
    {
        return $this->findByStatus('active');
    }

    /**
     * @param list<int> $parentPageUids Parent page UIDs (Member pages live as children)
     * @return QueryResultInterface<Member>
     */
    public function findFromPages(array $parentPageUids): QueryResultInterface
    {
        $query = $this->createQuery();
        $constraints = [$this->doktypeConstraint($query)];
        if ($parentPageUids !== []) {
            $constraints[] = $query->in('pid', $parentPageUids);
        }
        $query->matching($query->logicalAnd(...$constraints));

        return $query->execute();
    }

    /**
     * @param list<int> $parentPageUids
     * @return QueryResultInterface<Member>
     */
    public function findFromPagesByStatus(array $parentPageUids, string $status): QueryResultInterface
    {
        $query = $this->createQuery();
        $constraints = [
            $this->doktypeConstraint($query),
            $query->equals('status', $status),
        ];
        if ($parentPageUids !== []) {
            $constraints[] = $query->in('pid', $parentPageUids);
        }
        $query->matching($query->logicalAnd(...$constraints));

        return $query->execute();
    }

    public function findAll(): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->matching($this->doktypeConstraint($query));

        return $query->execute();
    }

    public function findByUid($uid): ?Member
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('uid', (int) $uid),
                $this->doktypeConstraint($query),
            ),
        );
        $query->setLimit(1);
        /** @var Member|null $result */
        $result = $query->execute()->getFirst();

        return $result;
    }

    private function doktypeConstraint(QueryInterface $query): \TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface
    {
        return $query->equals('doktype', DoktypeIds::MEMBER);
    }
}
