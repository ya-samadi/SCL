<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminProductController extends AbstractController
{
    /**
     * @Route("/admin/products", name="admin_products_index")
     */
    public function index(ProductRepository $repo): Response
    {
        return $this->render('admin/product/index.html.twig', [
            'products' => $repo->findAll(),
        ]);
    }
}
