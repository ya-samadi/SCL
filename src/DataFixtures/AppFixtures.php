<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Comment;
use App\Entity\Product;
use App\Entity\Commande;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
	private $encoder;

	public function __construct(UserPasswordEncoderInterface $encoder)
	{
		$this->encoder = $encoder;
	}

	public function load(ObjectManager $manager)
	{
		$faker = Factory::create('FR-fr');
		
		// Gestion des roles

		$adminRole = new Role();
		$adminRole->setTitle('ROLE_ADMIN');
		$manager->persist($adminRole);

		$adminUser = new User();
		$adminUser->setFirstName('Yahya')
				->setLastName('SAMADI')
				->setEmail('yasamadi@hotmail.com')
				->setHash($this->encoder->encodePassword($adminUser, 'root'))
				->setPicture('https://randomuser.me/api/portraits/men/25.jpg')
				->setIntroduction($faker->sentence())
				->setDescription("<p>".join("</p><p>", $faker->paragraphs(3)). "</p>")
				->addUserRole($adminRole);
				
		$manager->persist($adminUser);

		// Gestion des utilisateurs
		$users = [];
		$genres = ['male', 'female'];

		for ($i = 1; $i <= 10; $i++)
		{
			$user = new User();

			$genre = $faker->randomElement($genres);
			
			$picture = 'https://randomuser.me/api/portraits/';
			$pictureId = mt_rand(1, 99).'.jpg';

			$picture .= ($genre == "male" ? 'men/' : 'women/').$pictureId;
			
			$hash = $this->encoder->encodePassword($user, 'password');

			$user->setFirstName($faker->firstname($genre))
				->setLastName($faker->lastname)
				->setEmail($faker->email)
				->setIntroduction($faker->sentence())
				->setDescription("<p>".join("</p><p>", $faker->paragraphs(3)). "</p>")
				->setHash($hash)
				->setPicture($picture);

			$manager->persist($user);
			$users[] = $user;
		}

		// Gestion des produits
		for ($i = 1; $i <= 30; $i++)
		{
			$product = new Product();

			$title 			= $faker->sentence();
			$coverImage 	= $faker->imageUrl(1000, 350);
			$description 	= "<p>".join("</p><p>", $faker->paragraphs(5)). "</p>"; //  c'est une liste 

			$user = $users[mt_rand(0, count($users) - 1)];

			$product->setTitle($title)
					->setDescription($description)
					->setPrice(mt_rand(40, 150))
					->setCoverImage("https://picsum.photos/id/".mt_rand(1, 1000)."/1000/350")
					->setQuantity(mt_rand(1, 50))
					->setOwner($user);
			
			for ($j = 1; $j <= mt_rand(2, 5); $j++)
			{
				$image = new Image();

				$image->setUrl("https://picsum.photos/id/".mt_rand(1, 1000)."/".mt_rand(200, 1000)."/".mt_rand(200, 1000))
					  ->setCaption($faker->sentence())
					  ->setProduct($product);
					
				$manager->persist($image);
			}

			// Gestion des commandes
			for ($j = 1; $j <= mt_rand(0, 10); $j++)
			{
				$commande = new Commande();

				$createdAt = $faker->dateTimeBetween('-6 mounths');
				$duration = mt_rand(15, 30);
				$livraisonDate = (clone $createdAt)->modify("+$duration days");
				$quantity = mt_rand(1, $product->getQuantity());
				$amount = $quantity * $product->getPrice();
				$commander = $users[mt_rand(0, count($users) - 1)];
				$comment = $faker->paragraph();

				$commande->setCommander($commander)
						->addProduct($product)
						->setQuantity($quantity)
						->setCreatedAt($createdAt)
						->setLivraisonDate($livraisonDate)
						->setAmount($amount)
						->setComment($comment)
				;
						
				$manager->persist($commande);

				if (mt_rand(0,1))
				{
					$comment = new Comment();
					$comment->setContent($faker->paragraph())
							->setRating(mt_rand(1, 5))
							->setAuthor($commander)
							->setProduct($product);
							
					$manager->persist($comment);
				}
			}

			$manager->persist($product);
		}

		$manager->flush();
	}
}
