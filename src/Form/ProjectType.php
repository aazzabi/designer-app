<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('client', EntityType::class, array(
                'class' => User::class,
                'expanded' => true,
//                'choices' => $this->userRepository->findAllClients(),
                'query_builder' => function (UserRepository $er) {
                    return $er->createQueryBuilder('u')
//                        ->where('u.roles IN (:role)')
//                        ->where('u.roles IN (:role)')
//                        ->setParameter('role', 'ROLE_CLIENT')
                        ->orderBy('u.id', 'ASC');
                },
                'choice_label' => function ($u) {
                    return '#' . $u->getId() . ' (' . $u->getUsername() . ')';
                },
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
