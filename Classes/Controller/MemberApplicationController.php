<?php

declare(strict_types=1);

namespace Maispace\MaiMember\Controller;

use Maispace\MaiMember\Domain\Model\MemberApplication;
use Maispace\MaiMember\Domain\Repository\MemberApplicationRepository;
use Maispace\MaiMember\Service\ApplicationService;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class MemberApplicationController extends ActionController
{
    public function __construct(
        private readonly MemberApplicationRepository $applicationRepository,
        private readonly ApplicationService $applicationService
    ) {}

    /**
     * Show the application form.
     */
    public function newAction(): ResponseInterface
    {
        $application = new MemberApplication();
        $this->view->assign('application', $application);
        return $this->htmlResponse();
    }

    /**
     * Process the submitted application form.
     */
    public function createAction(MemberApplication $application): ResponseInterface
    {
        $this->applicationRepository->add($application);
        return $this->redirect('confirmation');
    }

    /**
     * Show the confirmation page after a successful application submission.
     */
    public function confirmationAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }
}
