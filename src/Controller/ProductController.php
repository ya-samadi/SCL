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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * @IsGranted("ROLE_USER")
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

            $product->setOwner($this->getUser());

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
     * @Security("is_granted('ROLE_USER') and user === product.getOwner()", message="Ce produit ne vous appartient pas, vous ne pouvez pas la modifier")
     * 
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

    /**
     * Permet de supprimer le produit
     *
     * @Route("/product/{slug}/delete", name="products_delete")
     * @Security("is_granted('ROLE_USER') and user === product.getOwner()", message="Vous n'avez pas le droit d'accéder à cette ressource")
     * 
     */
    public function delete(Product $product, EntityManagerInterface $manager): Response
    {
        $manager->remove($product);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le produit <stronge>{$product->getTitle()}</strong> a bien été supprimée !"
        );

        return $this->redirectToRoute("products_index");
    }
}
