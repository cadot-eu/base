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

        $builder->add(
            'id',
            null,
        )
            ->add(
                'email',
                null,
            )

            ->add(
                'roles',
                ChoiceType::class,
                ["multiple" => true, "expanded" => true, "attr" => ["data-controller" => "onecheckbox"]]
            )
            ->add(
                'password',
                null,
            )

            ->add(
                'isVerified',
                ChoiceType::class,
            )
            ->add(
                'deletedAt',
                null,
            )
            ->add(
                'createdAt',
                null,
            )
            ->add(
                'updatedAt',
                null,
            );
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
