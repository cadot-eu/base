<?php
//Here for add your Code //end of your code

namespace App\Form;

use App\Entity\Parametres;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
//Here for add your Code //end of your code

class ParametresType extends AbstractType
{
    //Here for add your Code //end of your code

    
    public function buildForm(FormBuilderInterface $builder, array $AtypeOption)
    {
         //Here for add your Code //end of your code

        
        //Here for add your Code //end of your code

         $builder->add('nom',null,
array (
  'attr' => 
  array (
    'data-controller' => 'base--ckeditor',
    'data-base--ckeditor-toolbar-value' => $AtypeOption["data"]->getTypenom(),
  ),
))
->add('valeur',null,
array (
  'attr' => 
  array (
    'data-controller' => 'ckeditor',
    'data-base--ckeditor-toolbar-value' => $AtypeOption["data"]->getTypevaleur(),
  ),
))
->add('aide',null,
array (
  'attr' => 
  array (
    'data-controller' => 'base--readonlyroot',
    'data-base--readonlyroot-code-value' => $AtypeOption["username"],
  ),
))

->add('typenom',HiddenType::class,
array (
  'attr' => 
  array (
    'data-controller' => 'base--hiddenroot',
    'data-base--hiddenroot-code-value' => $AtypeOption["username"],
  ),
))

->add('typevaleur',HiddenType::class,
array (
  'attr' => 
  array (
    'data-controller' => 'base--hiddenroot',
    'data-base--hiddenroot-code-value' => $AtypeOption["username"],
  ),
))

->add('slug',HiddenType::class,
array (
  'required' => false,
  'label' => 'lien',
  'attr' => 
  array (
    'data-controller' => 'base--hiddenroot',
    'data-base--hiddenroot-code-value' => $AtypeOption["username"],
  ),
));
        //Here for add your Code //end of your code

        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        //Here for add your Code //end of your code

        $resolver->setDefaults([
            'data_class' => Parametres::class,
            'username'=>'',
            //Here for add your Code //end of your code

        ]);
        $resolver->setAllowedTypes('username', 'string');
    }
}
