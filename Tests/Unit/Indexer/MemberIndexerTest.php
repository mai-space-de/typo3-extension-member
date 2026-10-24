<?php

declare(strict_types=1);

namespace Maispace\MaiMember\Tests\Unit\Indexer;

use Maispace\MaiBase\Backend\CustomPageModule\DoktypeIds;
use Maispace\MaiMember\Domain\Model\Member;
use Maispace\MaiMember\Indexer\MemberIndexer;
use Maispace\MaiSearch\Domain\Model\IndexingContext;
use Maispace\MaiSearch\Domain\Service\SearchBackendInterface;
use Maispace\MaiSearch\Service\BackendRegistry;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class MemberIndexerTest extends TestCase
{
    private MemberIndexer $subject;
    private BackendRegistry&MockObject $backendRegistry;
    private SearchBackendInterface&MockObject $activeBackend;

    protected function setUp(): void
    {
        $this->subject = new MemberIndexer();

        $this->activeBackend = $this->createMock(SearchBackendInterface::class);
        $this->backendRegistry = $this->createMock(BackendRegistry::class);
        $this->backendRegistry->method('getActive')->willReturn($this->activeBackend);
        $this->subject->injectBackendRegistry($this->backendRegistry);
    }

    #[Test]
    public function removeRecordDelegatesToActiveBackend(): void
    {
        $this->activeBackend
            ->expects(self::once())
            ->method('removeDocument')
            ->with('member', 42);

        $this->subject->removeRecord(42, 'pages');
    }

    #[Test]
    public function removeRecordIsNoOpForUnsupportedTable(): void
    {
        $this->activeBackend->expects(self::never())->method('removeDocument');

        $this->subject->removeRecord(42, 'tx_maimember_member');
    }

    #[Test]
    public function getTypeReturnsMember(): void
    {
        self::assertSame('member', $this->subject->getType());
    }

    #[Test]
    public function supportsPagesTable(): void
    {
        self::assertTrue($this->subject->supports('pages'));
    }

    #[Test]
    public function doesNotSupportOtherTables(): void
    {
        self::assertFalse($this->subject->supports('tx_maimember_member'));
        self::assertFalse($this->subject->supports('tx_mainews_news'));
        self::assertFalse($this->subject->supports('tt_content'));
    }

    #[Test]
    public function getIconReturnsExpectedValue(): void
    {
        self::assertSame('content-member', $this->subject->getIcon('member'));
    }

    #[Test]
    public function buildContentReturnsMemberInformation(): void
    {
        $member = new Member();
        $member->setDoktype(DoktypeIds::MEMBER);
        $member->setFirstName('John');
        $member->setLastName('Doe');
        $member->setPosition('Schirmherr');
        $member->setEmail('john@example.com');
        $member->setPhone('+49 123 456789');

        $content = $this->invokeBuildContent($member);

        self::assertStringContainsString('John Doe', $content);
        self::assertStringContainsString('Schirmherr', $content);
        self::assertStringContainsString('john@example.com', $content);
        self::assertStringContainsString('+49 123 456789', $content);
    }

    #[Test]
    public function buildContentReturnsEmptyStringForNonMemberRecord(): void
    {
        $content = $this->invokeBuildContent(new \stdClass());

        self::assertSame('', $content);
    }

    #[Test]
    public function indexRecordAcceptsStdClassMemberPageFromQueue(): void
    {
        $page = new \stdClass();
        $page->uid = 42;
        $page->doktype = DoktypeIds::MEMBER;
        $page->title = 'Queued Member';
        $page->tx_maimember_position = 'Chair';
        $page->tx_maimember_join_date = 1700000000;
        $page->slug = '/aktive-mitglieder/queued';

        $this->activeBackend
            ->expects(self::once())
            ->method('indexDocument')
            ->with(self::callback(static function (object $document): bool {
                return $document->type === 'member'
                    && $document->uid === 42
                    && $document->title === 'Queued Member';
            }));

        $this->subject->indexRecord($page, new IndexingContext(core: 'core_de'));
    }

    #[Test]
    public function indexRecordSkipsNonMemberDoktypeStdClass(): void
    {
        $page = new \stdClass();
        $page->uid = 7;
        $page->doktype = 1;
        $page->title = 'Standard';

        $this->activeBackend->expects(self::never())->method('indexDocument');

        $this->subject->indexRecord($page, new IndexingContext(core: 'core_de'));
    }

    #[Test]
    public function formatResultReturnsSearchResultWithCorrectType(): void
    {
        $solrDoc = [
            'title_s' => 'John Doe',
            'content_t' => 'John Doe john@example.com',
            'url_s' => '/members',
            'score' => 2.5,
        ];

        $result = $this->subject->formatResult($solrDoc);

        self::assertSame('member', $result->type);
        self::assertSame('John Doe', $result->title);
        self::assertSame('/members', $result->url);
        self::assertSame('content-member', $result->icon);
        self::assertSame(2.5, $result->score);
    }

    #[Test]
    public function formatResultDefaultsToEmptyStringsWhenFieldsAreMissing(): void
    {
        $result = $this->subject->formatResult([]);

        self::assertSame('', $result->title);
        self::assertSame('', $result->url);
        self::assertSame(0.0, $result->score);
        self::assertNull($result->date);
    }

    private function invokeBuildContent(object $record): string
    {
        $reflection = new \ReflectionMethod($this->subject, 'buildContent');
        $reflection->setAccessible(true);

        /** @var string $result */
        return $reflection->invoke($this->subject, $record);
    }
}
