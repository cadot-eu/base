<?php
//Here for add your Code //end of your code

namespace App\Form;

use App\Entity\Compte;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
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
    'client' => 'ROLE_USER',
    'administrateur' => 'ROLE_ADMIN',
  ),
  'attr' => 
  array (
    'data-controller' => 'base--onecheckbox',
  ),
))

->add('prixCercueil',MoneyType::class,
array (
  'attr' => 
  array (
  ),
))

->add('prixPerso',MoneyType::class,
array (
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
