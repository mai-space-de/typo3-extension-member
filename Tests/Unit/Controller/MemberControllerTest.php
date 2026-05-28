<?php

declare(strict_types=1);

namespace Maispace\MaiMember\Tests\Unit\Controller;

use Maispace\MaiBase\Controller\AbstractActionController;
use Maispace\MaiMember\Controller\MemberController;
use Maispace\MaiMember\Domain\Repository\MemberRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

final class MemberControllerTest extends TestCase
{
    #[Test]
    public function controllerExtendsAbstractActionController(): void
    {
        self::assertTrue(
            is_subclass_of(MemberController::class, AbstractActionController::class),
        );
    }

    #[Test]
    public function constructorAcceptsMemberRepository(): void
    {
        $params = (new \ReflectionMethod(MemberController::class, '__construct'))
            ->getParameters();

        $names = array_map(static fn(\ReflectionParameter $p) => $p->getName(), $params);
        self::assertContains('memberRepository', $names);

        $repoParam = array_values(array_filter(
            $params,
            static fn(\ReflectionParameter $p) => $p->getName() === 'memberRepository',
        ))[0];

        $type = $repoParam->getType();
        self::assertInstanceOf(\ReflectionNamedType::class, $type);
        self::assertSame(MemberRepository::class, $type->getName());
    }

    // ── listAction ────────────────────────────────────────────────────────────

    #[Test]
    public function listActionMethodExists(): void
    {
        self::assertTrue(
            method_exists(MemberController::class, 'listAction'),
        );
    }

    #[Test]
    public function listActionReturnsResponseInterface(): void
    {
        $returnType = (new \ReflectionMethod(MemberController::class, 'listAction'))
            ->getReturnType();

        self::assertInstanceOf(\ReflectionNamedType::class, $returnType);
        self::assertSame(ResponseInterface::class, $returnType->getName());
    }

    // ── detailAction ──────────────────────────────────────────────────────────

    #[Test]
    public function detailActionMethodExists(): void
    {
        self::assertTrue(
            method_exists(MemberController::class, 'detailAction'),
        );
    }

    #[Test]
    public function detailActionReturnsResponseInterface(): void
    {
        $returnType = (new \ReflectionMethod(MemberController::class, 'detailAction'))
            ->getReturnType();

        self::assertInstanceOf(\ReflectionNamedType::class, $returnType);
        self::assertSame(ResponseInterface::class, $returnType->getName());
    }

    #[Test]
    public function detailActionAcceptsNullableMemberParameter(): void
    {
        $params = (new \ReflectionMethod(MemberController::class, 'detailAction'))
            ->getParameters();

        $names = array_map(static fn(\ReflectionParameter $p) => $p->getName(), $params);
        self::assertContains('member', $names);

        $memberParam = array_values(array_filter(
            $params,
            static fn(\ReflectionParameter $p) => $p->getName() === 'member',
        ))[0];

        $type = $memberParam->getType();
        self::assertInstanceOf(\ReflectionNamedType::class, $type);
        self::assertSame(
            \Maispace\MaiMember\Domain\Model\Member::class,
            $type->getName(),
        );
        self::assertTrue($memberParam->allowsNull());
    }

    #[Test]
    public function detailActionParameterHasDefaultNull(): void
    {
        $params = (new \ReflectionMethod(MemberController::class, 'detailAction'))
            ->getParameters();

        $memberParam = array_values(array_filter(
            $params,
            static fn(\ReflectionParameter $p) => $p->getName() === 'member',
        ))[0];

        self::assertTrue($memberParam->isDefaultValueAvailable());
        self::assertNull($memberParam->getDefaultValue());
    }

    // ── isFrontendUserLoggedIn visibility ─────────────────────────────────────

    #[Test]
    public function isFrontendUserLoggedInIsPrivate(): void
    {
        $method = new \ReflectionMethod(MemberController::class, 'isFrontendUserLoggedIn');
        self::assertTrue($method->isPrivate());
    }

    #[Test]
    public function isFrontendUserLoggedInReturnsBool(): void
    {
        $returnType = (new \ReflectionMethod(MemberController::class, 'isFrontendUserLoggedIn'))
            ->getReturnType();

        self::assertInstanceOf(\ReflectionNamedType::class, $returnType);
        self::assertSame('bool', $returnType->getName());
    }
}
