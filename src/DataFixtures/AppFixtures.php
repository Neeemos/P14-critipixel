<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Factory\VideoGameFactory;
use App\Factory\UserFactory;
use App\Factory\TagFactory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createMany(10);
        TagFactory::createMany(20);
        VideoGameFactory::createGameN49();
        VideoGameFactory::createGameN0();
        VideoGameFactory::createMany(45);
        VideoGameFactory::createMultipleReviewsGame();
        VideoGameFactory::createNoReviewGame();
        VideoGameFactory::createSingleReviewGame();

        $manager->flush();
    }
}
