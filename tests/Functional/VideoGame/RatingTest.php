<?php

namespace App\Tests\VideoGame\Rating;

use App\Factory\VideoGameFactory;
use App\Rating\RatingHandler;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;


class RatingTest extends KernelTestCase
{
    use Factories;

    private RatingHandler $ratingHandler;

    protected function setUp(): void
    {
        $this->ratingHandler = self::getContainer()->get(RatingHandler::class);
    }
    public function testCalculateAverageWithNoReviews(): void
    {
        $videoGame = VideoGameFactory::repository()->findOneBy(['title' => 'TEST_NO_REVIEWS']);
        $this->assertNotNull($videoGame, 'Le jeu sans reviews devrait exister');

        $this->ratingHandler->calculateAverage($videoGame->_real());
        $this->assertNull($videoGame->_real()->getAverageRating());
    }

    public function testCalculateAverageWithSingleReview(): void
    {
        $videoGame = VideoGameFactory::repository()->findOneBy(['title' => 'TEST_SINGLE_REVIEW']);
        $this->assertNotNull($videoGame, 'Le jeu avec une review devrait exister');

        $this->ratingHandler->calculateAverage($videoGame->_real());
        $this->assertEquals(4, $videoGame->_real()->getAverageRating());
    }

    public function testCalculateAverageWithMultipleReviews(): void
    {
        $videoGame = VideoGameFactory::repository()->findOneBy(['title' => 'TEST_MULTIPLE_REVIEWS']);
        $this->assertNotNull($videoGame, 'Le jeu avec plusieurs reviews devrait exister');

        $this->ratingHandler->calculateAverage($videoGame->_real());
        $this->assertEquals(4, $videoGame->_real()->getAverageRating());
    }
    public function testCountRatingsPerValueWithNoReviews(): void
    {
        $videoGame = VideoGameFactory::repository()->findOneBy(['title' => 'TEST_NO_REVIEWS']);
        $this->assertNotNull($videoGame);


        $this->ratingHandler->countRatingsPerValue($videoGame->_real());

        $ratingsCount = $videoGame->_real()->getNumberOfRatingsPerValue();

        $this->assertEquals(0, $ratingsCount->getNumberOfOne());
        $this->assertEquals(0, $ratingsCount->getNumberOftwo());
        $this->assertEquals(0, $ratingsCount->getNumberOfThree());
        $this->assertEquals(0, $ratingsCount->getNumberOfFour());
        $this->assertEquals(0, $ratingsCount->getNumberOfFive());
    }

    public function testCountRatingsPerValueWithSingleReview(): void
    {
        $videoGame = VideoGameFactory::repository()->findOneBy(['title' => 'TEST_SINGLE_REVIEW']);
        $this->assertNotNull($videoGame);

        $this->ratingHandler->countRatingsPerValue($videoGame->_real());
        $ratingsCount = $videoGame->_real()->getNumberOfRatingsPerValue();

        $this->assertEquals(0, $ratingsCount->getNumberOfOne());
        $this->assertEquals(0, $ratingsCount->getNumberOftwo());
        $this->assertEquals(0, $ratingsCount->getNumberOfThree());
        $this->assertEquals(1, $ratingsCount->getNumberOfFour());
        $this->assertEquals(0, $ratingsCount->getNumberOfFive());
    }

    public function testCountRatingsPerValueWithMultipleReviews(): void
    {
        $videoGame = VideoGameFactory::repository()->findOneBy(['title' => 'TEST_MULTIPLE_REVIEWS']);
        $this->assertNotNull($videoGame);

        $this->ratingHandler->countRatingsPerValue($videoGame->_real());
        $ratingsCount = $videoGame->_real()->getNumberOfRatingsPerValue();

        $this->assertEquals(0, $ratingsCount->getNumberOfOne());
        $this->assertEquals(1, $ratingsCount->getNumberOftwo());
        $this->assertEquals(1, $ratingsCount->getNumberOfThree());
        $this->assertEquals(1, $ratingsCount->getNumberOfFour());
        $this->assertEquals(2, $ratingsCount->getNumberOfFive());
    }
}
