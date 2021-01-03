<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Commande;
use App\Form\CommandeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeController extends AbstractController
{
    /**
     * @Route("/product/{slug}/commander", name="commande_create")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function commander(Product $product, Request $request, EntityManagerInterface $manager): Response
    {
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $user = $this->getUser();

            $commande->setCommander($user)
                    ->addProduct($product)
                    ->setAmount($product->getPrice() * $commande->getQuantity())
                    ;
            
            $product_qunatity = $product->getQuantity() - $commande->getQuantity();

            if($product_qunatity < 0) 
            {
                $this->addFlash(
                    'warning',
                    "Vous avez depassé la qunatité disponible au strok !"
                );
            }
            else
            {
                $product->setQuantity($product_qunatity);
                
                $manager->persist($product);
                $manager->persist($commande);
                $manager->flush();
    
                return $this->redirectToRoute('commande_show', ['id' => $commande->getId(), 'withAlert' => true]);
            }
        }

        return $this->render('commande/commander.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Permet d'afficher la page d'une commande
     *
     * @Route("/commande/{id}", name="commande_show")
     * 
     * @return Response
     */
    public function show(Commande $commande)
    {
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }
}
