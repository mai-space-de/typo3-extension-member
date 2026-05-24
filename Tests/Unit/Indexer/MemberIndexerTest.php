<?php

declare(strict_types=1);

namespace Maispace\MaiMember\Tests\Unit\Indexer;

use Maispace\MaiMember\Domain\Model\Member;
use Maispace\MaiMember\Indexer\MemberIndexer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MemberIndexerTest extends TestCase
{
    private MemberIndexer $subject;

    protected function setUp(): void
    {
        $this->subject = new MemberIndexer();
    }

    #[Test]
    public function getTypeReturnsMember(): void
    {
        self::assertSame('member', $this->subject->getType());
    }

    #[Test]
    public function supportsMemberTable(): void
    {
        self::assertTrue($this->subject->supports('tx_maimember_member'));
    }

    #[Test]
    public function doesNotSupportOtherTables(): void
    {
        self::assertFalse($this->subject->supports('tx_mainews_news'));
        self::assertFalse($this->subject->supports('pages'));
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
        $member->setFirstName('John');
        $member->setLastName('Doe');
        $member->setEmail('john@example.com');
        $member->setPhone('+49 123 456789');

        $content = $this->invokeBuildContent($member);

        self::assertStringContainsString('John Doe', $content);
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
