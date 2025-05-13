<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\News;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $categories = [];
        for ($i = 1; $i <= 5; $i++) {
            $category = new Category();
            $category->setTitle($faker->city());
            $manager->persist($category);
            $categories[] = $category;
        }

        for ($i = 1; $i <= 10; $i++) {
            $news = new News();
            $news->setTitle($faker->sentence(6, true))
                ->setContent($faker->paragraph(10, true))
                ->setShortDescription($faker->sentence(15, true))
                ->setCreatedAt($faker->dateTimeBetween('-1 year', 'now'))
                ->setViews($faker->numberBetween(0, 1000));

            foreach ($faker->randomElements($categories, $faker->numberBetween(1, 3)) as $category) {
                $news->addCategory($category);
            }

            $manager->persist($news);

            for ($j = 1; $j <= $faker->numberBetween(1, 5); $j++) {
                $comment = new Comment();
                $comment->setContent($faker->sentence(10, true))
                    ->setCreatedAt($faker->dateTimeBetween('-1 year', 'now'))
                    ->setNews($news);
                $manager->persist($comment);
            }
        }

        $manager->flush();
    }
}