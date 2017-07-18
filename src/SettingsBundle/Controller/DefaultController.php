<?php

namespace SettingsBundle\Controller;

use SettingsBundle\Entity\Logs;
use SettingsBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class DefaultController extends Controller
{
    public function indexAction()
    {
       if ($this->getUser())
       {
           return $this->redirectToRoute('settings_dashboard');
       }else
        {
            throw  new AuthenticationException() ;
        }
    }

    public function logsAction()
    {

        if ($this->getUser())
        {
            $logs=$this->getDoctrine()->getRepository("SettingsBundle:Logs")->findAll();
            return $this->render("SettingsBundle:Default:logs.html.twig",array("logs"=>$logs));
        }
        else
        {
            throw  new AuthenticationException() ;
        }
    }

    public function userListAction()
    {
        if ($this->getUser())
        {
            $users=$this->findAllOtherUsers($this->getUser());
            return $this->render("SettingsBundle:Default:listUsers.html.twig",array("users"=>$users));
        }
        else
        {
            throw  new AuthenticationException() ;
        }
    }

    public function dashboardAction()
    {
        if ($this->getUser())
        {
            return $this->render("SettingsBundle:Default:dashboard.html.twig");
        }
        else
        {
            throw  new AuthenticationException() ;
        }

    }

    public function deleteUserAction(User $user=null,Request $request)
    {
        if ($this->getUser())
        {
            if ($user)
            {
                $em=$this->getDoctrine()->getManager();
                $ip = $request->getClientIp();
                if($ip == 'unknown'){
                    $ip = $_SERVER['REMOTE_ADDR'];
                }
                $log=new Logs();
                $log->setUser($this->getUser()->getUsername());
                $log->setIp($ip);
                $log->setDate(new \DateTime());
                $log->setDescription("Delete user:'".$user->getUsername()."'");
                $log->setType("Users");
                $em->persist($log);
                $em->flush();
                $em->remove($user);
                $em->flush();
                $this->get('session')->getFlashBag()->set('success', 'User removed successfully.');
            }
            else
            {
                $this->get('session')->getFlashBag()->set('error', 'User does not exist.');
            }

            return $this->redirectToRoute("settings_listUsers");

        }
        else
        {
            throw  new AuthenticationException() ;
        }
    }

    public function editUserAction(User $user ,Request $request )
    {
        $userManager = $this->get('fos_user.user_manager');
        $newEmail=$userManager->findUserByEmail($request->get("email"));
        $newUsername=$userManager->findUserByUsername($request->get("username"));
        $em=$this->getDoctrine()->getManager();
        if ($this->getUser())
        {

            $formFactory = $this->get('fos_user.registration.form.factory');
            $form = $formFactory->createForm();
            $form->setData($user);
            $form->handleRequest($request);
            if ($form->isSubmitted())
            {
                if ($form->isValid())
                {
                    $this->get('session')->getFlashBag()->clear();
                    $userManager->updateUser($user);
                    $this->get('session')->getFlashBag()->set('success', 'Password updated successfully');
                }
            }
            else
                {
                    if ($request->isMethod("post"))
                    {
                        if($newEmail && ($user->getEmail()!=$request->get("email")))
                        {
                            $this->get('session')->getFlashBag()->clear();
                            $this->get('session')->getFlashBag()->set('error', 'The email is already used.');

                        }

                        if($newUsername && ($user->getUsername()!=$request->get("username")))
                        {
                            $this->get('session')->getFlashBag()->clear();
                            $this->get('session')->getFlashBag()->add('error', 'The username is already used.');

                        }
                        else
                        {
                            $this->get('session')->getFlashBag()->clear();
                            $user->setFirstname($request->get("firstname"));
                            $user->setLastname($request->get("lastname"));
                            $user->setUsername($request->get("username"));
                            $user->setEmail($request->get("email"));
                            $userManager->updateUser($user);
                            $this->get('session')->getFlashBag()->set('success', 'User updated successfully');
                            $ip = $request->getClientIp();
                            if($ip == 'unknown'){
                                $ip = $_SERVER['REMOTE_ADDR'];
                            }
                            $log=new Logs();
                            $log->setUser($this->getUser()->getUsername());
                            $log->setIp($ip);
                            $log->setDate(new \DateTime());
                            $log->setDescription("Edit user:'".$user->getUsername()."'");
                            $log->setType("Users");
                            $em->persist($log);
                            $em->flush();
                        }

                    }
                }

            return $this->render("SettingsBundle:Default:editUser.html.twig",array("user"=>$user,'form' => $form->createView()));

        }else
        {
            throw  new AuthenticationException() ;
        }
    }
    public function addUserAction(Request $request)
    {

        if ($this->getUser())
        {
            $em=$this->getDoctrine()->getManager();
            $formFactory = $this->get('fos_user.registration.form.factory');
            $userManager = $this->get('fos_user.user_manager');
            $user = $userManager->createUser();
            $form = $formFactory->createForm();
            $form->setData($user);
            $form->handleRequest($request);

            if ($form->isSubmitted())
            {
                if ($form->isValid())
                {
                    $user->setFirstname($request->get("firstname"));
                    $user->setLastname($request->get("lastname"));
                    $user->addRole("ROLE_ADMIN");
                    $user->setEnabled(true);
                    $userManager->updateUser($user);
                    $this->get('session')->getFlashBag()->set('success', 'User added successfully');
                    $ip = $request->getClientIp();
                    if($ip == 'unknown'){
                        $ip = $_SERVER['REMOTE_ADDR'];
                    }
                    $log=new Logs();
                    $log->setUser($this->getUser()->getUsername());
                    $log->setIp($ip);
                    $log->setDate(new \DateTime());
                    $log->setDescription("Add user:'".$user->getUsername()."'");
                    $log->setType("Users");
                    $em->persist($log);
                    $em->flush();
                }
            }

            return $this->render("SettingsBundle:Default:addUser.html.twig",array(
            'form' => $form->createView()));

        }else
        {
            throw  new AuthenticationException() ;
        }
    }

    public function changeUserStatusAction($id,Request $request)
    {
        if ($this->getUser())
            {
                $em=$this->getDoctrine()->getManager();
                $user=$em->getRepository("SettingsBundle:User")->find($id);
                $log=new Logs();
                $log->setUser($this->getUser()->getUsername());
                if ($user->isEnabled())
                {
                    $user->setEnabled(false);
                    $log->setDescription("Disable user:'".$user->getUsername()."'");


                }
                else
                {
                    $user->setEnabled(true);
                    $log->setDescription("Enable user:'".$user->getUsername()."'");

                }
                $em->flush();
                $em->persist($user);

                $ip = $request->getClientIp();
                if($ip == 'unknown'){
                    $ip = $_SERVER['REMOTE_ADDR'];
                }
                $log->setIp($ip);
                $log->setDate(new \DateTime());
                $log->setType("Users");
                $em->persist($log);
                $em->flush();
                return $this->redirectToRoute("settings_listUsers");

            }
        else
            {
                throw  new AuthenticationException() ;
            }

        }

    public function findUsersByRole($role)
    {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->select('u')
            ->from("SettingsBundle:User", 'u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"'.$role.'"%');

        return $qb->getQuery()->getResult();
    }
    public function findAllOtherUsers($Me)
    {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->select('u')
            ->from("SettingsBundle:User", 'u')
            ->where('u.id != :me')
            ->setParameter('me',$Me);

        return $qb->getQuery()->getResult();
    }


    public function accountAction(Request $request)
    {

        if ($this->getUser())
        {
            $userManager = $this->get('fos_user.user_manager');
            $formFactory = $this->get('fos_user.profile.form.factory');
            $form = $formFactory->createForm();
            $form->setData($this->getUser());

            $form->handleRequest($request);
            if(!$form->isValid())
            {
                $userManager->reloadUser($this->getUser());

            }

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getUser()->setFirstname($request->get("firstname"));
                $this->getUser()->setLastname($request->get("lastname"));
                $userManager->updateUser($this->getUser());
                $this->get('session')->getFlashBag()->clear();
                $this->get('session')->getFlashBag()->set('success', 'Account updated successfully');
                $em=$this->getDoctrine()->getManager();
                $ip = $request->getClientIp();
                if($ip == 'unknown'){
                    $ip = $_SERVER['REMOTE_ADDR'];
                }
                $log=new Logs();
                $log->setUser($this->getUser()->getUsername());
                $log->setIp($ip);
                $log->setDate(new \DateTime());
                $log->setDescription("Edit user:'".$this->getUser()->getUsername()."'");
                $log->setType("Users");
                $em->persist($log);
                $em->flush();
            }
//            form2
            $formFactory2 = $this->get('fos_user.change_password.form.factory');
            $form2 = $formFactory2->createForm();
            $form2->setData($this->getUser());
            $form2->handleRequest($request);
            if(!$form2->isValid())
            {
                $userManager->reloadUser($this->getUser());

            }
            if ($form2->isSubmitted() && $form2->isValid()) {
                $userManager->updateUser($this->getUser());
                $this->get('session')->getFlashBag()->clear();
                $this->get('session')->getFlashBag()->set('success', 'Passwords updated successfully');
                $em=$this->getDoctrine()->getManager();
                $ip = $request->getClientIp();
                if($ip == 'unknown'){
                    $ip = $_SERVER['REMOTE_ADDR'];
                }
                $log=new Logs();
                $log->setUser($this->getUser()->getUsername());
                $log->setIp($ip);
                $log->setDate(new \DateTime());
                $log->setDescription("Edit user:'".$this->getUser()->getUsername()."' change password");
                $log->setType("Users");
                $em->persist($log);
                $em->flush();
            }

            return $this->render('SettingsBundle:Default:editAccount.html.twig', array(
                'form' => $form->createView(), 'form2' => $form2->createView()
            ));
        }
        else
        {
            throw  new AuthenticationException() ;
        }

    }
}
