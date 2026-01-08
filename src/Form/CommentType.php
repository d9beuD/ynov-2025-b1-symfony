<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\DataTransformer\PostToIdTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function __construct(
        protected PostToIdTransformer $postToIdTransformer,
    ) {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content')
            ->add('post', HiddenType::class, [
                'required' => false,
                'data' => null,
            ])
            ->add('parent', HiddenType::class, [
                'required' => false,
                'data' => null,
            ])
        ;

        $builder->get('post')
            ->addModelTransformer($this->postToIdTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
