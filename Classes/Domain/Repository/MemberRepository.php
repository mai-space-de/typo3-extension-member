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
    protected $defaultOrderings = [
        'name' => QueryInterface::ORDER_ASCENDING,
    ];

    /**
     * Find members by status.
     *
     * @return QueryResultInterface<Member>
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
     * Find members that have a specific interest (comma-separated list search).
     *
     * @return QueryResultInterface<Member>
     */
    public function findByInterest(string $interest): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->matching(
            $query->like('interests', '%' . $interest . '%')
        );
        return $query->execute();
    }

    /**
     * Find active members optionally filtered by interest.
     *
     * @return QueryResultInterface<Member>
     */
    public function findActiveMembers(string $interest = ''): QueryResultInterface
    {
        $query = $this->createQuery();

        $constraints = [
            $query->equals('status', Member::STATUS_ACTIVE),
        ];

        if ($interest !== '') {
            $constraints[] = $query->like('interests', '%' . $interest . '%');
        }

        $query->matching($query->logicalAnd(...$constraints));
        return $query->execute();
    }

    /**
     * Find members filtered by status and/or interest.
     *
     * @return QueryResultInterface<Member>
     */
    public function findFiltered(?int $status = null, string $interest = ''): QueryResultInterface
    {
        $query = $this->createQuery();
        $constraints = [];

        if ($status !== null) {
            $constraints[] = $query->equals('status', $status);
        }

        if ($interest !== '') {
            $constraints[] = $query->like('interests', '%' . $interest . '%');
        }

        if ($constraints !== []) {
            $query->matching($query->logicalAnd(...$constraints));
        }

        return $query->execute();
    }
}
