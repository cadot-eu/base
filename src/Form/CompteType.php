<?php
//Here for add your Code //end of your code

namespace App\Form;

use App\Entity\Compte;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
//Here for add your Code //end of your code

class CompteType extends AbstractType
{
    //Here for add your Code //end of your code

    public function buildForm(FormBuilderInterface $builder, array $AtypeOption)
    {
        //Here for add your Code //end of your code

         $builder->add('email',null,
array (
  'attr' => 
  array (
  ),
))
->add('roles',ChoiceType::class,
array (
  'multiple' => true,
  'expanded' => true,
  'choices' => 
  array (
    0 => 'ROLE_USER',
    1 => 'ROLE_ADMIN',
    2 => 'ROLE_EDITEUR',
  ),
  'attr' => 
  array (
    'data-controller' => 'onecheckbox',
  ),
))
->add('password',null,
array (
  'attr' => 
  array (
  ),
))

->add('isVerified',ChoiceType::class,
array (
  'choices' => 
  array (
    0 => '<i class="bi bi-toggle-off"></i>',
    1 => '<i class="bi bi-toggle-on"></i>',
  ),
  'attr' => 
  array (
  ),
))
->add('createdAt',null,
array (
  'widget' => 'single_text',
  'attr' => 
  array (
  ),
))
->add('updatedAt',null,
array (
  'widget' => 'single_text',
  'attr' => 
  array (
  ),
));
        //Here for add your Code //end of your code

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        //Here for add your Code //end of your code

        $resolver->setDefaults([
            'data_class' => Compte::class,
            //Here for add your Code //end of your code

        ]);
    }
}
