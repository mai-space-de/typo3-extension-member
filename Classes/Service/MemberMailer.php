<?php

declare(strict_types=1);

namespace Maispace\MaiMember\Service;

use Maispace\MaiMail\Service\MailService;
use Maispace\MaiMember\Domain\Model\Application;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class MemberMailer
{
    public function __construct(
        private readonly MailService $mailService,
        private readonly ViewFactoryInterface $viewFactory,
    ) {}

    public function sendApplicationReceived(Application $application): void
    {
        if ($application->getEmail() === '') {
            return;
        }

        $view = $this->createView();
        $view->assign('application', $application);

        $subject = (string) (LocalizationUtility::translate('email.applicationReceived.subject', 'mai_member')
            ?: 'We received your application');

        $this->mailService->queue($application->getEmail(), $subject, $view->render('ApplicationReceived'));
    }

    public function sendApplicationApproved(Application $application): void
    {
        if ($application->getEmail() === '') {
            return;
        }

        $view = $this->createView();
        $view->assign('application', $application);

        $subject = (string) (LocalizationUtility::translate('email.applicationApproved.subject', 'mai_member')
            ?: 'Your application has been approved');

        $this->mailService->queue($application->getEmail(), $subject, $view->render('ApplicationApproved'));
    }

    public function sendApplicationRejected(Application $application): void
    {
        if ($application->getEmail() === '') {
            return;
        }

        $view = $this->createView();
        $view->assign('application', $application);

        $subject = (string) (LocalizationUtility::translate('email.applicationRejected.subject', 'mai_member')
            ?: 'Update on your application');

        $this->mailService->queue($application->getEmail(), $subject, $view->render('ApplicationRejected'));
    }

    private function createView(): ViewInterface
    {
        return $this->viewFactory->create(new ViewFactoryData(
            templateRootPaths: ['EXT:mai_member/Resources/Private/Templates/Email/'],
            partialRootPaths: ['EXT:mai_member/Resources/Private/Partials/'],
            layoutRootPaths: ['EXT:mai_member/Resources/Private/Layouts/'],
            format: 'html',
        ));
    }
}
