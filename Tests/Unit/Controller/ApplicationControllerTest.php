<?php

declare(strict_types=1);

namespace Maispace\MaiMember\Tests\Unit\Controller;

use Maispace\MaiBase\Controller\AbstractActionController;
use Maispace\MaiMember\Controller\ApplicationController;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

final class ApplicationControllerTest extends TestCase
{
    #[Test]
    public function controllerExtendsAbstractActionController(): void
    {
        self::assertTrue(
            is_subclass_of(ApplicationController::class, AbstractActionController::class),
        );
    }

    // ── formAction ───────────────────────────────────────────────────────────

    #[Test]
    public function formActionMethodExists(): void
    {
        self::assertTrue(
            method_exists(ApplicationController::class, 'formAction'),
        );
    }

    #[Test]
    public function formActionReturnsResponseInterface(): void
    {
        $returnType = (new \ReflectionMethod(ApplicationController::class, 'formAction'))
            ->getReturnType();

        self::assertInstanceOf(\ReflectionNamedType::class, $returnType);
        self::assertSame(ResponseInterface::class, $returnType->getName());
    }

    // ── submitAction ─────────────────────────────────────────────────────────

    #[Test]
    public function submitActionMethodExists(): void
    {
        self::assertTrue(
            method_exists(ApplicationController::class, 'submitAction'),
        );
    }

    #[Test]
    public function submitActionReturnsResponseInterface(): void
    {
        $returnType = (new \ReflectionMethod(ApplicationController::class, 'submitAction'))
            ->getReturnType();

        self::assertInstanceOf(\ReflectionNamedType::class, $returnType);
        self::assertSame(ResponseInterface::class, $returnType->getName());
    }

    #[Test]
    public function submitActionAcceptsApplicationParameter(): void
    {
        $params = (new \ReflectionMethod(ApplicationController::class, 'submitAction'))
            ->getParameters();

        $names = array_map(static fn(\ReflectionParameter $p) => $p->getName(), $params);
        self::assertContains('application', $names);

        $applicationParam = array_values(array_filter(
            $params,
            static fn(\ReflectionParameter $p) => $p->getName() === 'application',
        ))[0];

        $type = $applicationParam->getType();
        self::assertInstanceOf(\ReflectionNamedType::class, $type);
        self::assertSame(
            \Maispace\MaiMember\Domain\Model\Application::class,
            $type->getName(),
        );
    }
}
