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
    'client' => 'ROLE_USER',
    'administrateur' => 'ROLE_ADMIN',
    'partenaire' => 'ROLE_PARTENAIRE',
  ),
  'attr' => 
  array (
  ),
))
->add('nom',null,
array (
  'attr' => 
  array (
  ),
))

->add('situation',ChoiceType::class,
array (
  'expanded' => true,
  'choices' => 
  array (
    'inactif' => 'inactif',
    'actif' => 'actif',
  ),
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
