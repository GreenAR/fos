<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 7/11/17
 * Time: 9:25 AM
 */

namespace SettingsBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use SettingsBundle\Entity\User;

class LoadUserData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $manager2 = $this->container->get('fos_user.user_manager');
        $factory = $this->container->get('security.encoder_factory');
        $userAdmin = new User();
        $userAdmin->setUsername('superadmin');
        $userAdmin->setEmail('superadmin@superadmin.net');
        $encoder = $factory->getEncoder($userAdmin);
        $password = $encoder->encodePassword('superadmin', $userAdmin->getSalt());
        $userAdmin->setPassword($password);
        $userAdmin->addRole("ROLE_SUPER_ADMIN");
        $userAdmin->setEnabled(true);

        $manager2->updateUser($userAdmin);
    }
}
