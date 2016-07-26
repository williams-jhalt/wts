<?php

namespace AppBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('name', TextType::class)
                ->add('releaseDate', DateType::class, array('widget' => 'single_text', 'disabled' => true))
                ->add('stockQuantity', IntegerType::class, array('disabled' => true))
                ->add('price', MoneyType::class, array('currency' => 'USD', 'disabled' => true))
                ->add('barcode', TextType::class, array('disabled' => true))
                ->add('manufacturer', EntityType::class, array(
                    'class' => 'AppBundle:Manufacturer',
                    'choice_label' => 'name'
                ))
                ->add('type', EntityType::class, array(
                    'class' => 'AppBundle:ProductType',
                    'choice_label' => 'name'
                ))
                ->add('detail', ProductDetailType::class);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Product',
        ));
    }

}
