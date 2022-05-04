<?php

//Here for add your Code //end of your code
namespace App\Form;

use App\Entity\Parametres;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

//Here for add your Code //end of your code
class ParametresType extends AbstractType
{
    //Here for add your Code //end of your code
    public function buildForm(FormBuilderInterface $builder, array $AtypeOption)
    {
        //Here for add your Code //end of your code
        $builder
            ->add('nom', null)
            ->add('valeur', null, [
                'attr' => [
                    'data-controller' => 'ckeditor',
                ],
            ]);
        //Here for add your Code //end of your code
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        //Here for add your Code //end of your code
        $resolver->setDefaults([
            'data_class' => Parametres::class,
            //Here for add your Code //end of your code
        ]);
    }
}
