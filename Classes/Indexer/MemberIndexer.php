<?php

declare(strict_types=1);

namespace Maispace\MaiMember\Indexer;

use Maispace\MaiBase\Backend\CustomPageModule\DoktypeIds;
use Maispace\MaiMember\Domain\Model\Member;
use Maispace\MaiSearch\Domain\Dto\SearchResult;
use Maispace\MaiSearch\Domain\Model\IndexingContext;
use Maispace\MaiSearch\Domain\Service\SearchResultFormatterInterface;
use Maispace\MaiSearch\Indexer\AbstractIndexer;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;

/**
 * Indexes Member doktype pages. Emits SiteRouter page URLs as the public URL.
 * Accepts both Extbase Member models (full reindex) and stdClass page rows (queue).
 */
class MemberIndexer extends AbstractIndexer implements SearchResultFormatterInterface
{
    private const TABLE_NAME = 'pages';

    public function getType(): string
    {
        return 'member';
    }

    public function getTableName(): string
    {
        return self::TABLE_NAME;
    }

    public function supports(string $table): bool
    {
        return $table === self::TABLE_NAME;
    }

    public function indexAll(IndexingContext $context): void
    {
        foreach ($this->getRecordsForIndexing($context) as $record) {
            $this->indexRecord($record, $context);
        }
    }

    public function indexRecord(object $record, IndexingContext $context): void
    {
        $page = $this->normalizePageRecord($record);
        if ($page === null) {
            return;
        }

        if ((int) ($page->doktype ?? 0) !== DoktypeIds::MEMBER) {
            return;
        }

        $document = $this->createDocument(
            type: $this->getType(),
            uid: (int) $page->uid,
            title: $this->resolveTitle($page),
            content: $this->buildContent($page),
            url: $this->buildUrl($page),
            crdate: $this->resolveDate($page),
            boost: $this->getBoost($this->getType()),
        );

        $this->sendDocument($document, $context->languageCode);
    }

    public function removeRecord(int $uid, string $table): void
    {
        if ($table !== self::TABLE_NAME) {
            return;
        }

        $this->removeDocument($uid);
    }

    protected function buildContent(object $record): string
    {
        $page = $this->normalizePageRecord($record);
        if ($page === null) {
            return '';
        }

        $parts = array_filter([
            $this->resolveTitle($page),
            (string) ($page->tx_maimember_position ?? $page->position ?? ''),
            (string) ($page->tx_maimember_email ?? $page->email ?? ''),
            (string) ($page->tx_maimember_phone ?? $page->phone ?? ''),
            $this->fetchPageContentText((int) $page->uid),
        ]);

        return implode(' ', $parts);
    }

    /**
     * Public URL = SiteRouter page UID (Member page is the detail target).
     */
    protected function buildUrl(object $record): string
    {
        $page = $this->normalizePageRecord($record);
        if ($page === null) {
            return '';
        }

        $pageUid = (int) $page->uid;
        if ($pageUid <= 0) {
            return '';
        }

        try {
            $site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId($pageUid);
            $uri = $site->getRouter()->generateUri($pageUid);

            return (string) $uri;
        } catch (\Throwable) {
            return (string) ($page->slug ?? '');
        }
    }

    protected function getRecordsForIndexing(IndexingContext $context): iterable
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::TABLE_NAME);

        $queryBuilder
            ->select('*')
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq(
                    'doktype',
                    $queryBuilder->createNamedParameter(DoktypeIds::MEMBER, Connection::PARAM_INT),
                ),
                $queryBuilder->expr()->eq(
                    'deleted',
                    $queryBuilder->createNamedParameter(0, Connection::PARAM_INT),
                ),
            );

        if ($context->languageUid !== null) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->eq(
                    'sys_language_uid',
                    $queryBuilder->createNamedParameter($context->languageUid, Connection::PARAM_INT),
                ),
            );
        }

        $rows = $queryBuilder
            ->setMaxResults($context->batchSize)
            ->setFirstResult($context->offset)
            ->executeQuery()
            ->fetchAllAssociative();

        if ($rows === []) {
            return [];
        }

        $dataMapper = GeneralUtility::makeInstance(DataMapper::class);

        return $dataMapper->map(Member::class, $rows);
    }

    public function formatResult(array $solrDoc): SearchResult
    {
        return new SearchResult(
            type: $this->getType(),
            title: $solrDoc['title_s'] ?? '',
            snippet: $this->buildSnippet($solrDoc),
            url: $solrDoc['url_s'] ?? '',
            icon: $this->getIcon($this->getType()),
            date: $this->parseDate($solrDoc),
            score: (float) ($solrDoc['score'] ?? 0.0),
        );
    }

    public function getIcon(string $type): string
    {
        return 'content-member';
    }

    private function normalizePageRecord(object $record): ?\stdClass
    {
        if ($record instanceof Member) {
            $obj = new \stdClass();
            $obj->uid = (int) $record->getUid();
            $obj->doktype = $record->getDoktype();
            $obj->title = $record->getTitle() !== '' ? $record->getTitle() : $record->getFullName();
            $obj->slug = $record->getSlug();
            $obj->tx_maimember_first_name = $record->getFirstName();
            $obj->tx_maimember_last_name = $record->getLastName();
            $obj->tx_maimember_position = $record->getPosition();
            $obj->position = $record->getPosition();
            $obj->tx_maimember_email = $record->getEmail();
            $obj->email = $record->getEmail();
            $obj->tx_maimember_phone = $record->getPhone();
            $obj->phone = $record->getPhone();
            $obj->tx_maimember_join_date = $record->getJoinDate();

            return $obj;
        }

        if ($record instanceof \stdClass && isset($record->uid)) {
            return $record;
        }

        return null;
    }

    private function resolveTitle(\stdClass $page): string
    {
        $title = trim((string) ($page->title ?? ''));
        if ($title !== '') {
            return $title;
        }

        return trim(
            trim((string) ($page->tx_maimember_first_name ?? ''))
            . ' '
            . trim((string) ($page->tx_maimember_last_name ?? '')),
        );
    }

    private function resolveDate(\stdClass $page): \DateTime
    {
        $timestamp = (int) ($page->tx_maimember_join_date ?? $page->tstamp ?? $page->crdate ?? 0);
        if ($timestamp > 0) {
            $dt = new \DateTime();
            $dt->setTimestamp($timestamp);

            return $dt;
        }

        return new \DateTime();
    }

    private function fetchPageContentText(int $pageUid): string
    {
        if ($pageUid <= 0) {
            return '';
        }

        try {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getQueryBuilderForTable('tt_content');

            $rows = $queryBuilder
                ->select('header', 'bodytext')
                ->from('tt_content')
                ->where(
                    $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pageUid, Connection::PARAM_INT)),
                    $queryBuilder->expr()->eq('hidden', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)),
                    $queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)),
                )
                ->executeQuery()
                ->fetchAllAssociative();
        } catch (\Throwable) {
            return '';
        }

        $parts = [];
        foreach ($rows as $row) {
            if (!empty($row['header'])) {
                $parts[] = strip_tags((string) $row['header']);
            }

            if (!empty($row['bodytext'])) {
                $parts[] = strip_tags((string) $row['bodytext']);
            }
        }

        return implode(' ', $parts);
    }

    private function buildSnippet(array $solrDoc): string
    {
        $content = $solrDoc['content_t'] ?? '';

        return mb_substr(strip_tags($content), 0, 200);
    }

    private function parseDate(array $solrDoc): ?\DateTime
    {
        if (empty($solrDoc['crdate_dt'])) {
            return null;
        }

        try {
            return new \DateTime($solrDoc['crdate_dt']);
        } catch (\Exception) {
            return null;
        }
    }
}
