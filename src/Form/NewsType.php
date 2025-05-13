<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\News;
use App\Form\DataTransformer\FileToStringTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class NewsType extends AbstractType
{
    private FileToStringTransformer $fileToStringTransformer;
    public function __construct(FileToStringTransformer $fileToStringTransformer)
    {
        $this->fileToStringTransformer = $fileToStringTransformer;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'];

        $builder
            ->add('title', TextType::class, [
                'label' => 'Title',
                'constraints' => [
                    new NotBlank(['message' => 'The title is required.']),
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Content',
                'constraints' => [
                    new NotBlank(['message' => 'The content is required.']),
                ],
            ])
            ->add('shortDescription', TextareaType::class, [
                'label' => 'Short Description',
                'constraints' => [
                    new NotBlank(['message' => 'The short description is required.']),
                ],
            ])
            ->add('createdAt', DateTimeType::class, [
                'label' => 'Created At',
                'constraints' => [
                    new NotBlank(['message' => 'The created date is required.']),
                ],
            ])
            ->add('picture', FileType::class, [
                'label' => 'Picture',
                'required' => !$isEdit,
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
                'multiple' => true,
                'expanded' => false,
                'label' => 'Categories',
                'attr' => ['class' => 'form-control'],
            ]);

        $builder->get('picture')->addModelTransformer($this->fileToStringTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => News::class,
            'is_edit' => false,
        ]);
    }
}