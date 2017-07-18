<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 7/18/17
 * Time: 1:16 PM
 */

namespace SettingsBundle\EventListener;


use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class ResettingListener implements EventSubscriberInterface
{
    private $router;
    private $security;
    private $session;

    public function __construct(UrlGeneratorInterface $router, AuthorizationChecker $security,Session $session) {
        $this->router = $router;
        $this->security = $security;
        $this->session=$session;
    }

    public static function getSubscribedEvents() {
        return [
            FOSUserEvents::RESETTING_RESET_SUCCESS => 'onResettingResetSuccess',
        ];
    }


    public function onResettingResetSuccess(FormEvent $event) {
        $this->session->getFlashBag()->add('reset', 'Your password has been reset successfully.');
        $url = $this->router->generate('settings_dashboard');
        $event->setResponse(new RedirectResponse($url));

    }
}