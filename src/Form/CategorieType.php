<?php
//Here for add your Code //end of your code

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

//Here for add your Code //end of your code

class CategorieType extends AbstractType
{
    //Here for add your Code //end of your code

    public function buildForm(FormBuilderInterface $builder, array $AtypeOption)
    {
        //Here for add your Code //end of your code

         $builder->add('id',null,
array (
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
->add('description',null,
array (
  'attr' => 
  array (
  ),
))
->add('article',null,
array (
  'attr' => 
  array (
  ),
))
->add('slug',null,
array (
  'attr' => 
  array (
  ),
))
->add('createdAt',null,
array (
  'attr' => 
  array (
  ),
))
->add('updatedAt',null,
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
            'data_class' => Categorie::class,
            //Here for add your Code //end of your code

        ]);
    }
}
