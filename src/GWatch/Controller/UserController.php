<?php
namespace GWatch\Controller;
use GWatch\Entity\User;
use GWatch\Form\Type\UserType;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;



use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class UserController
{
/*    public function meAction(Request $request, Application $app)
    {
        $token = $app['security']->getToken();
        $user = $token->getUser();
        $now = new \DateTime();
        $interval = $now->diff($user->getCreatedAt());
        $memberSince = $interval->format('%d days %H hours %I minutes ago');
        $limit = 60;
        $likes = $app['repository.like']->findAllByUser($user->getId(), $limit);
        // Divide artists into groups of 6.
        $groupSize = 6;
        $groupedLikes = array();
        $progress = 0;
        while ($progress < $limit) {
            $groupedLikes[] = array_slice($likes, $progress, $groupSize);
            $progress += $groupSize;
        }
        $data = array(
            'user' => $user,
            'memberSince' => $memberSince,
  //          'groupedLikes' => $groupedLikes,
        );
        return $app['twig']->render('profile.html.twig', $data);
    }
    
*/
    public function loginAction(Request $request, Application $app)
    {
        $form = $app['form.factory']->createBuilder(FormType::class, $values, array(
    		 
    		'method' => 'post',
		))
            ->add('username', TextType::class, array('label' => 'Username', 'data' => $app['session']->get('_security.last_username')))
            ->add('password', PasswordType::class, array('label' => 'Password'))
            ->add('login', SubmitType::class)
            ->getForm();
        $data = array(
            'form'  => $form->createView(),
            'error' => '',//$app['security.last_error']($request),
        );
        return $app['twig']->render('login.html.twig', $data);
    }
    
    
    
    public function logoutAction(Request $request, Application $app)
    {
        $app['session']->clear();
        return $app->redirect($app['url_generator']->generate('homepage'));
    }
        public function addAction(Request $request, Application $app)
    {
        $user = new User();
        $form = $app['form.factory']->create(new UserType(), $user);
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $app['repository.user']->save($user);
                $message = 'The user ' . $user->getUsername() . ' has been saved.';
                $app['session']->getFlashBag()->add('success', $message);
                // Redirect to the edit page.
                $redirect = $app['url_generator']->generate('admin_user_edit', array('user' => $user->getId()));
                return $app->redirect($redirect);
            }
        }
        $data = array(
            'form' => $form->createView(),
            'title' => 'Add new user',
        );
        return $app['twig']->render('form.html.twig', $data);
    }
    
    
    
    public function editAction(Request $request, Application $app)
    {
        $user = $request->attributes->get('user');
        if (!$user) {
            $app->abort(404, 'The requested user was not found.');
        }
        $form = $app['form.factory']->create(new UserType(), $user);
        if ($request->isMethod('POST')) {
            $previousPassword = $user->getPassword();
            $form->bind($request);
            if ($form->isValid()) {
                // If an empty password was entered, restore the previous one.
                $password = $user->getPassword();
                if (!$password) {
                    $user->setPassword($previousPassword);
                }
                $app['repository.user']->save($user);
                $message = 'The user ' . $user->getUsername() . ' has been saved.';
                $app['session']->getFlashBag()->add('success', $message);
            }
        }
        $data = array(
            'form' => $form->createView(),
            'title' => 'Edit user ' . $user->getUsername(),
        );
        return $app['twig']->render('form.html.twig', $data);
    }
    
    
    
    public function deleteAction(Request $request, Application $app)
    {
        $user = $request->attributes->get('user');
        if (!$user) {
            $app->abort(404, 'The requested user was not found.');
        }
        $app['repository.user']->delete($user->getId());
        return $app->redirect($app['url_generator']->generate('admin_users'));
    }
    
    
    
}