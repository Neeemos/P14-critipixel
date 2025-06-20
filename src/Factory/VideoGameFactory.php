<?php

namespace App\Factory;

use App\Model\Entity\Tag;
use App\Model\Entity\VideoGame;
use App\Rating\RatingHandler;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<VideoGame>
 */
final class VideoGameFactory extends PersistentProxyObjectFactory
{
    /** @var int */
    private static int $counter;
    private RatingHandler $ratingHandler;

    public function __construct(
        RatingHandler $ratingHandler
    ) {
        $this->ratingHandler = $ratingHandler;
    }

    public static function class(): string
    {
        return VideoGame::class;
    }
   /** @return array<string, mixed> ***/
    protected function defaults(): array
    {
        $count = self::getAndIncrementGameNumber();

        return [
            'title' => sprintf('Jeu vidéo %d', $count),
            'description' => self::faker()->paragraphs(10, true),
            'releaseDate' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'test' => self::faker()->paragraphs(6, true),
            'rating' => self::faker()->numberBetween(1, 5),
            'imageName' => sprintf('video_game_%d.png', self::$counter),
            'imageSize' => 2_098_872,
        ];
    }
    private static function initializeCounter(): void
    {
        if (!isset(self::$counter)) {
            self::$counter = 0;
        }
    }

    private static function getAndIncrementGameNumber(): int
    {
        self::initializeCounter();
        return self::$counter++;
    }
    protected function initialize(): static
    {
        return $this
            ->beforeInstantiate(function (array $attributes): array {
                // Crée des tags s'ils n'existent pas
                if (TagFactory::count() < 5) {
                    TagFactory::createMany(5 - TagFactory::count());
                }
                return $attributes;
            })
            ->afterInstantiate(function (VideoGame $videoGame, array $attributes): void {
                $this->addTags($videoGame);
            })
            ->afterPersist(function (VideoGame $videoGame, array $attributes): void {
                if (!str_contains($videoGame->getTitle(), 'TEST_')) {
                    ReviewFactory::createMany(
                        self::faker()->numberBetween(1, 5),
                        ['videoGame' => $videoGame]
                    );
                }
                $this->updateRatingStats($videoGame);
            });
    }

    private function updateRatingStats(VideoGame $videoGame): void
    {
        $this->ratingHandler->countRatingsPerValue($videoGame);
        $this->ratingHandler->calculateAverage($videoGame);

        self::repository()->find($videoGame->getId());
    }

    public static function createNoReviewGame(): VideoGame
    {
        $factory = self::new();
        $game = $factory->create(['title' => 'TEST_NO_REVIEWS'])->_real();
        $factory->updateRatingStats($game);
        $game->getTags()->clear();

        $tag =  TagFactory::createOne([
            'name' => 'TAG_NO_REVIEWS'
        ])->_real();
        $game->addTag($tag);

        return $game;
    }

    public static function createSingleReviewGame(): VideoGame
    {
        $factory = self::new();
        $game = $factory->create(['title' => 'TEST_SINGLE_REVIEW'])->_real();
        $game->getTags()->clear();

        ReviewFactory::createOne([
            'videoGame' => $game,
            'rating' => 4
        ]);

        $tagNoReviews = TagFactory::findOrCreate(['name' => 'TAG_NO_REVIEWS'])->_real();
        $tagGame0 = TagFactory::findOrCreate(['name' => 'TAG_GAME_0'])->_real();

        $game->addTag($tagNoReviews);
        $game->addTag($tagGame0);

        $factory->updateRatingStats($game);

        return $game;
    }


    public static function createMultipleReviewsGame(): VideoGame
    {
        $factory = self::new();
        $game = $factory->create(['title' => 'TEST_MULTIPLE_REVIEWS'])->_real();

        $ratings = [5, 3, 4, 2, 5];
        foreach ($ratings as $rating) {
            ReviewFactory::createOne([
                'videoGame' => $game,
                'rating' => $rating
            ]);
        }
        $factory->addTags($game);
        $factory->updateRatingStats($game);
        return $game;
    }
    public static function createGameN49(): VideoGame
    {

        $factory = self::new();
        $game = $factory->create(['title' => 'Jeu vidéo 49', 'rating' => 4, 'imageName' => 'video_game_49.png'])->_real();

        $ratings = [5, 3, 4, 2, 5];
        foreach ($ratings as $rating) {
            ReviewFactory::createOne([
                'videoGame' => $game,
                'rating' => $rating
            ]);
        }
        $factory->addTags($game);
        $factory->updateRatingStats($game);
        return $game;
    }
    public static function createGameN0(): VideoGame
    {

        $factory = self::new();
        $game = $factory->create(['title' => 'Jeu vidéo 0', 'rating' => 4, 'imageName' => 'video_game_0.png'])->_real();

        $ratings = [5, 3, 4, 2, 5];
        foreach ($ratings as $rating) {
            ReviewFactory::createOne([
                'videoGame' => $game,
                'rating' => $rating
            ]);
        }
        $factory->addTags($game);
        $factory->updateRatingStats($game);
        return $game;
    }

    public static function addTags(VideoGame $videoGame): void
    {
        $tags = TagFactory::randomRange(1, 5);
        foreach ($tags as $tag) {
            $videoGame->addTag($tag->_real());
        }
    }
}
