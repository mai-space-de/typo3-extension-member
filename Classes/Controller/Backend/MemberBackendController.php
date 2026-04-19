<?php

declare(strict_types=1);

namespace Maispace\MaiMember\Controller\Backend;

use Maispace\MaiBase\Controller\Backend\AbstractBackendController;
use Maispace\MaiBase\Controller\Traits\ResponseHelpersTrait;
use Maispace\MaiMember\Domain\Model\Application;
use Maispace\MaiMember\Domain\Repository\ApplicationRepository;
use Maispace\MaiMember\Domain\Repository\MemberRepository;
use Maispace\MaiMember\Service\ApplicationService;
use Maispace\MaiMember\Service\MemberMailer;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Imaging\IconFactory;

#[AsController]
class MemberBackendController extends AbstractBackendController
{
    use ResponseHelpersTrait;

    public function __construct(
        ModuleTemplateFactory $moduleTemplateFactory,
        IconFactory $iconFactory,
        private readonly MemberRepository $memberRepository,
        private readonly ApplicationRepository $applicationRepository,
        private readonly ApplicationService $applicationService,
        private readonly MemberMailer $memberMailer,
    ) {
        parent::__construct($moduleTemplateFactory, $iconFactory);
    }

    public function indexAction(): ResponseInterface
    {
        $moduleTemplate = $this->createModuleTemplate();
        $this->addShortcutButton(
            $moduleTemplate,
            'mai_member',
            'Members',
        );

        $this->assignMultiple($moduleTemplate, [
            'members' => $this->memberRepository->findAll(),
            'applications' => $this->applicationRepository->findPending(),
        ]);

        return $this->renderModuleResponse($moduleTemplate, 'Index');
    }

    public function approveAction(Application $application): ResponseInterface
    {
        $this->applicationService->approve($application);
        $this->memberMailer->sendApplicationApproved($application);
        $this->flashSuccess(
            'Application approved and member record created.',
            $application->getFullName(),
        );

        return $this->redirect('index');
    }

    public function rejectAction(Application $application): ResponseInterface
    {
        $this->applicationService->reject($application);
        $this->memberMailer->sendApplicationRejected($application);
        $this->flashInfo(
            'Application marked as rejected.',
            $application->getFullName(),
        );

        return $this->redirect('index');
    }

    public function exportCsvAction(): ResponseInterface
    {
        $members = $this->memberRepository->findAll();

        $rows = [['first_name', 'last_name', 'email', 'phone', 'status', 'join_date']];
        foreach ($members as $member) {
            $rows[] = [
                $member->getFirstName(),
                $member->getLastName(),
                $member->getEmail(),
                $member->getPhone(),
                $member->getStatus(),
                $member->getJoinDate() > 0 ? date('Y-m-d', $member->getJoinDate()) : '',
            ];
        }

        return $this->csvResponse($rows, 'members.csv');
    }
}
