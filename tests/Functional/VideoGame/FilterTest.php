<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Tests\Functional\FunctionalTestCase;

final class FilterTest extends FunctionalTestCase
{
    public function testShouldListTenVideoGames(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $this->client->clickLink('2');
        self::assertResponseIsSuccessful();
    }

    public function testShouldFilterVideoGamesBySearch(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $this->client->submitForm('Filtrer', ['filter[search]' => 'Jeu vidéo 49'], 'GET');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(1, 'article.game-card');
    }
    public function testFilterBySpecificTag(): void
    {
        $crawler = $this->client->request('GET', '/');
        self::assertResponseIsSuccessful();
        $tagLabel = $crawler->filter('label:contains("TAG_NO_REVIEWS")');
        self::assertCount(1, $tagLabel, 'Le tag TAG_NO_REVIEWS devrait être présent dans le DOM');

        $tagCheckboxId = $tagLabel->attr('for');
        $tagCheckbox = $crawler->filter("#{$tagCheckboxId}");
        $tagValue = $tagCheckbox->attr('value');

        $this->client->request('GET', '/', [
            'filter' => [
                'search' => '',
                'tags' => [$tagValue],
            ],
            'page' => '1',
            'limit' => '10',
            'sorting' => 'ReleaseDate',
            'direction' => 'Descending',
        ]);

        self::assertResponseIsSuccessful();
        self::assertSelectorCount(2, 'article.game-card');
    }

    public function testFilterByMultipleTag(): void
    {
        $crawler = $this->client->request('GET', '/');
        self::assertResponseIsSuccessful();


        $tagNames = ['TAG_NO_REVIEWS', 'TAG_GAME_0'];
        $tagValues = [];

        foreach ($tagNames as $tagName) {
            $label = $crawler->filter("label:contains(\"{$tagName}\")");
            self::assertCount(1, $label, "Le tag {$tagName} devrait être présent dans le DOM");

            $checkboxId = $label->attr('for');
            $checkbox = $crawler->filter("#{$checkboxId}");
            $tagValues[] = $checkbox->attr('value');
        }

        $this->client->request('GET', '/', [
            'filter' => [
                'search' => '',
                'tags' => $tagValues,
            ],
            'page' => '1',
            'limit' => '10',
            'sorting' => 'ReleaseDate',
            'direction' => 'Descending',
        ]);

        self::assertResponseIsSuccessful();

        self::assertSelectorExists('article.game-card');
        self::assertSelectorCount(1, 'article.game-card');
    }
    public function testFilterWithNonExistentTag(): void
    {
        $this->client->request('GET', '/', [
            'filter' => [
                'search' => '',
                'tags' => ['non-existent-tag-id-fdsfdsfsdfdsfsd'],
            ],
            'page' => '1',
            'limit' => '10',
            'sorting' => 'ReleaseDate',
            'direction' => 'Descending',
        ]);

        self::assertResponseIsSuccessful();

        self::assertSelectorCount(10, 'article.game-card', 'Tag inexistant on retourne tous les jeux');
    }
}
