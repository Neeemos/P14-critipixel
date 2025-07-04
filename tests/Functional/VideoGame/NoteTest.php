<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Model\Entity\Review;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

final class NoteTest extends FunctionalTestCase
{

    public function testAddNoteWithInvalidRating(): void
    {
        $this->login('user+0@email.com');

        $this->client->request('POST', '/jeu-video-49', [
            'review' => [
                'rating' => '6',
                'comment' => 'Commentaire valide',
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertSelectorExists('select[name="review[rating]"].is-invalid');
    }


    public function testAddNote(): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);

        $this->login('user+0@email.com');
        $this->get('/jeu-video-49');
        self::assertResponseIsSuccessful();
        $this->client->submitForm('Poster', [
            'review[rating]' => '3',
            'review[comment]' => 'TESTUNITAIRE'
        ]);
        self::assertResponseRedirects('/jeu-video-49', 302);
        $review = $this->getEntityManager()->getRepository(Review::class)
            ->findOneBy(['comment' => 'TESTUNITAIRE', 'rating' => '3']);
        self::assertInstanceOf(Review::class, $review);
    }
    public function testFormAddnoteView(): void
    {
        $this->login('user+0@email.com');
        $this->get('/jeu-video-49');
        self::assertResponseIsSuccessful();
        self::assertSelectorNotExists('form[name="review"]');
    }
    public function testAddNoteWithTooLongComment(): void
    {
        $this->login('user+0@email.com');
        $this->get('/jeu-video-0');
        self::assertResponseIsSuccessful();

        $longComment = str_repeat('a', 250);

        $this->client->submitForm('Poster', [
            'review[rating]' => '5',
            'review[comment]' => $longComment,
        ]);

        self::assertResponseStatusCodeSame(422); 
    }

        public function testFormAddnoteViewUnlogin(): void
    {
        $this->get('/jeu-video-49');
        self::assertResponseIsSuccessful();
        self::assertSelectorNotExists('form[name="review"]');
    }

        public function testAddNoteWithNoLogin(): void
    {
        $this->client->request('POST', '/jeu-video-49', [
            'review' => [
                'rating' => '5',
                'comment' => 'Commentaire valide',
                'token' => 'testtoken',
            ],
        ]);

        self::assertResponseStatusCodeSame(422); /// Pb devrait être 401 unauthorized
    }

        public function testFormAddnoteViewNoLogin(): void
    {
        $this->get('/jeu-video-49');
        self::assertResponseIsSuccessful();
        self::assertSelectorNotExists('form[name="review"]');
    }

}
