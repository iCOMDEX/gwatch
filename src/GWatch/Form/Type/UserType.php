<?php
namespace GWatch\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array(
                'constraints' => new Assert\NotBlank(),
            ))
            ->add('password', RepeatedType::class, array(
                'type'            => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options'         => array('required' => false),
                'first_options'   => array('label' => 'Password'),
                'second_options'  => array('label' => 'Repeat Password'),
                'required' => FALSE,
            ))
            ->add('mail', EmailType::class, array(
                'constraints' => array(new Assert\NotBlank(), new Assert\Email()),
            ))
            ->add('role', ChoiceType::class, array(
                'choices' => array('ROLE_USER' => 'User', 'ROLE_ADMIN' => 'Admin')
            ))
            ->add('save', SubmitType::class);
    }
    public function getName()
    {
        return 'user';
    }
}