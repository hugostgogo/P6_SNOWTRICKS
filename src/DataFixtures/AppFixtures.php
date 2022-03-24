<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Photo;
use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Faker\Factory;

class AppFixtures extends Fixture
{
    private $faker;

    private $userPasswordHasherInterface;

    private $categories;

    private $users;

    public function __construct(
        UserPasswordHasherInterface $userPasswordHasherInterface
    ) {
        $this->faker = Factory::create();
        $this->userPasswordHasherInterface = $userPasswordHasherInterface;

        $this->tricksNumber = rand(20, 30);

        $this->categories = ['Rotation', 'Flip', 'SLide', 'Grab'];

        $this->users = [
            [
                'username' => 'SuperAdmin',
                'email' => 'superadmin@example.com',
                'password' => '123',
                'avatar' => '/images/\default_user_image.png',
                'roles' => ['ROLE_SUPERADMIN', 'ROLE_ADMIN'],
            ],
            [
                'username' => 'Admin',
                'email' => 'admin@example.com',
                'password' => '123',
                'avatar' => '/images/\default_user_image.png',
                'roles' => ['ROLE_ADMIN'],
            ],
            [
                'username' => 'User',
                'email' => 'user@example.com',
                'password' => '123',
                'avatar' => '/images/\default_user_image.png',
                'roles' => ['ROLE_USER'],
            ],
        ];
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->users as $userData) {
            $user = new User();
            $avatar = new Photo();
            $avatar->setPath($userData['avatar'])->setDescription('Avatar');
            $user
                ->setEmail($userData['email'])
                ->setPassword(
                    $this->userPasswordHasherInterface->hashPassword(
                        $user,
                        $userData['password']
                    )
                )
                ->setUsername($userData['username'])
                ->setAvatar($avatar)
                ->setRoles($userData['roles']);
            $this->addReference($user->getUsername(), $user);
            $manager->persist($user);
        }

        foreach ($this->categories as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $this->addReference($categoryName, $category);
            $manager->persist($category);
        }

        foreach (range(1, $this->tricksNumber) as $index) {
            $trick = new Trick();

            $trick->addCategory(
                $this->getReference(
                    $this->categories[rand(0, count($this->categories) - 1)]
                )
            );

            $trick->addCategory(
                $this->getReference(
                    $this->categories[rand(0, count($this->categories) - 1)]
                )
            );

            $cover = new Photo();
            $cover
                ->setPath(
                    $this->faker->imageUrl(640, 480, "Trick $index", false)
                )
                ->setDescription("Trick $index");

            $manager->persist($cover);

            foreach (range(0, rand(0, 8)) as $photosIndex) {
                $photo = new Photo();
                $photo
                    ->setPath(
                        $this->faker->imageUrl(
                            640,
                            480,
                            "Photo $photosIndex",
                            false
                        )
                    )
                    ->setDescription("Photo $photosIndex")
                    ->setTrick($trick);

                $manager->persist($photo);
            }

            foreach (range(0, rand(0, 25)) as $commentsIndex) {
                $comment = new Comment();
                $comment
                    ->setContent($this->faker->paragraph())
                    ->setTrick($trick)
                    ->setAuthor($this->getReference('User'));

                $manager->persist($comment);
            }

            $trick
                ->setName($this->faker->word())
                ->setDescription($this->faker->paragraph())
                ->setCover($cover);

            $manager->persist($trick);
        }

        $manager->flush();
    }
}
