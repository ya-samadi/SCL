<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/products", name="products_index")
     */
    public function index(ProductRepository $repo): Response
    {
        $products = $repo->findAll();
        
        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * Permet de créer un produit
     *
     * @Route("/product/new", name="products_create")
     * 
     */
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            foreach($product->getImages() as $image)
            {
                $image->setProduct($product);
                $manager->persist($image);
            }

            $manager->persist($product);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$product->getTitle()}</strong> a bien été enregistée !"
            );

            return $this->redirectToRoute('products_show', [
                'slug' => $product->getSlug()
            ]);
        }
        
        return $this->render('product/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

     /**
     * Permet d'afficher le formulaire d'édition
     *
     * @Route("/product/{slug}/edit", name="products_edit")
     */
    public function edit(Product $product, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid())
        {
            foreach($product->getImages() as $image)
            {
                $image->setProduct($product);
                $manager->persist($image);
            }

            $manager->persist($product);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les modifications de l'annonce <strong>{$product->getTitle()}</strong> ont bien été enregistée !"
            );

            return $this->redirectToRoute('products_show', [
                'slug' => $product->getSlug()
            ]);
        }
        
        return $this->render('product/edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }

    /**
     * Permet d'afficher un seul produit
     *
     * @Route("/product/{slug}", name="products_show")
     */
    public function show(Product $product): Response
    {
        // $product = $repo->findOneBySlug($slug);
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }
}
