<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Image;
use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
	public function load(ObjectManager $manager)
	{
		$faker = Factory::create('FR-fr');

		for ($i = 1; $i <= 30; $i++)
		{
			$product = new Product();

			$title 			= $faker->sentence();
			$coverImage 	= $faker->imageUrl(1000, 350);
			$description 	= "<p>".join("</p><p>", $faker->paragraphs(3)). "</p>"; //  c'est une liste 

			$product->setTitle($title)
					->setDescription($description)
					->setPrice(mt_rand(40, 150))
					->setCoverImage("https://picsum.photos/id/".mt_rand(1, 1000)."/1000/350")
					->setQuantity(mt_rand(1, 50));
			
			for ($j = 1; $j <= mt_rand(2, 5); $j++)
			{	
				$image = new Image();

				$image->setUrl("https://picsum.photos/id/".mt_rand(1, 1000)."/".mt_rand(200, 1000)."/".mt_rand(200, 1000))
					  ->setCaption($faker->sentence())
					  ->setProduct($product);
					
				$manager->persist($image);
			}
			$manager->persist($product);
		}

		$manager->flush();
	}
}
