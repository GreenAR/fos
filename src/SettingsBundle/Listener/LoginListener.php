<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 7/11/17
 * Time: 10:47 AM
 */

namespace SettingsBundle\Listener;


use Psr\Container\ContainerInterface;
use SettingsBundle\Entity\Logs;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{
    private $securityContext;
    protected $container;



    public function __construct(AuthorizationCheckerInterface $securityContext, ContainerInterface  $container)
    {
        $this->securityContext = $securityContext;
        $this->container       = $container;
    }

    /**
     * Do the magic.
     *
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        if ($this->securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
            // user has just logged in
        }

        if ($this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            // user has logged in using remember_me cookie
        }
        $ip = $event->getRequest()->getClientIp();
        if($ip == 'unknown'){
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $em=$this->container->get('doctrine')->getManager();
        $log=new Logs();
        $user = $event->getAuthenticationToken()->getUser();
        $log->setUser($user->getUsername());
        $log->setIp($ip);
        $log->setDate(new \DateTime());
        $log->setDescription("login");
        $log->setType("login");
        $em->persist($log);
        $em->flush();



    }
}