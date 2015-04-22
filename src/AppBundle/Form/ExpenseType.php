<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;

class ExpenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('createdAt', 'date', array(
            'description' => 'When the expense was reported',
            #'date_widget' => 'single_text',
            'html5' => true,
            'input' => 'datetime',
        ));
        $builder->add('amount', 'money', array(
            'description' => 'Cost of expense',
            'currency' => 'BRL',   // Brazilian 'Real' currency
        ));
        $builder->add('description', 'text', array(
            'description' => 'Description of expense',
        ));
        $builder->add('comment', 'textarea', array(
            'description' => 'Additional comment',
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'         => 'AppBundle\Entity\Expense',
            'intention'          => 'expense',
            'translation_domain' => 'AppBundle',
            'csrf_protection'    => false,     // @TODO security
        ));
    }

    public function getName()
    {
        return 'expense';
    }
}
