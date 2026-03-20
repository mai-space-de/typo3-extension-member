<?php

declare(strict_types=1);

namespace Maispace\MaiMember\Controller\Backend;

use Maispace\MaiMember\Domain\Model\MemberApplication;
use Maispace\MaiMember\Domain\Repository\MemberApplicationRepository;
use Maispace\MaiMember\Service\ApplicationService;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class MemberApplicationController extends ActionController
{
    public function __construct(
        private readonly MemberApplicationRepository $applicationRepository,
        private readonly ApplicationService $applicationService,
        private readonly ModuleTemplateFactory $moduleTemplateFactory
    ) {}

    /**
     * List all pending applications.
     */
    public function indexAction(): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);

        $moduleTemplate->assignMultiple([
            'pendingApplications' => $this->applicationRepository->findPending(),
            'approvedApplications' => $this->applicationRepository->findApproved(),
        ]);

        return $moduleTemplate->renderResponse('Index');
    }

    /**
     * Show a single application's details.
     */
    public function showAction(MemberApplication $application): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $moduleTemplate->assign('application', $application);
        return $moduleTemplate->renderResponse('Show');
    }

    /**
     * Approve a pending application (transition: pending → approved).
     */
    public function approveAction(MemberApplication $application): ResponseInterface
    {
        try {
            $this->applicationService->approveApplication($application);
            $this->addFlashMessage(
                'Die Bewerbung wurde genehmigt und ein Mitgliedsdatensatz erstellt.',
                'Bewerbung genehmigt'
            );
        } catch (\InvalidArgumentException $e) {
            $this->addFlashMessage(
                $e->getMessage(),
                'Fehler',
                \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::ERROR
            );
        }

        return $this->redirect('index');
    }

    /**
     * Activate an approved application (transition: approved → active).
     */
    public function activateAction(MemberApplication $application): ResponseInterface
    {
        try {
            $this->applicationService->activateMember($application);
            $this->addFlashMessage(
                'Das Mitglied wurde aktiviert.',
                'Mitglied aktiviert'
            );
        } catch (\InvalidArgumentException|\RuntimeException $e) {
            $this->addFlashMessage(
                $e->getMessage(),
                'Fehler',
                \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::ERROR
            );
        }

        return $this->redirect('index');
    }

    /**
     * Reject a pending application.
     */
    public function rejectAction(MemberApplication $application): ResponseInterface
    {
        try {
            $this->applicationService->rejectApplication($application);
            $this->addFlashMessage(
                'Die Bewerbung wurde abgelehnt.',
                'Bewerbung abgelehnt',
                \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::WARNING
            );
        } catch (\InvalidArgumentException $e) {
            $this->addFlashMessage(
                $e->getMessage(),
                'Fehler',
                \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::ERROR
            );
        }

        return $this->redirect('index');
    }
}
