<?php

namespace App\Controller;

use App\Repository\PageRepository;
use App\Repository\ProductRepository;
use App\Repository\SettingRepository;
use App\Repository\SlidersRepository;
use App\Repository\CategoryRepository;
use App\Repository\CollectionsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
private $repoProduct;

public function __construct(ProductRepository $repoProduct)
{
        $this->repoProduct = $repoProduct;
}

#[Route('/', name: 'app_home')]
public function index(
    SettingRepository $settingRepo,
    SlidersRepository $slidersRepo,
    CollectionsRepository $collectionsRepo,
    CategoryRepository $categoryRepo,
    PageRepository $pageRepo,
    Request $request): Response
    {
        $session = $request->getSession();
        $data = $settingRepo->findAll();
        $sliders = $slidersRepo->findAll();
        $collections = $collectionsRepo->findBy(['isMega'=>false]);
        $megaCollections = $collectionsRepo->findBy(['isMega'=>true]);
        $categories = $categoryRepo->findBy(['isMega'=>true]);
        // dd($data);


        $session->set("setting", $data[0]);
        $headerPages = $pageRepo->findBy(['isHead' => true]);
        $footerPages = $pageRepo->findBy(['isFoot' => true]);

        $session->set("headerPages", $headerPages);
        $session->set("footerPages", $footerPages);
        $session->set("categories", $categories);
        $session->set("megaCollections", $megaCollections);

        
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'sliders' => $sliders,
            'collections' => $collections,
            'productBestSeller' => $this->repoProduct->findBy(['isBestSeller'=>true]),
            'productNewArrival' => $this->repoProduct->findBy(['isNewArrival'=>true]),
            'productFeatured' => $this->repoProduct->findBy(['isFeatured'=>true]),
            'productSpecialOffer' => $this->repoProduct->findBy(['isSpecialOffer'=>true])
        ]);
    }

    #[Route('/product/{slug}', name: 'app_show_product_by_slug')]
    public function showProduct(string $slug)
    {
        $product = $this->repoProduct->findOneBy(["slug"=>$slug]);

        if(!$product){
            // error
            return $this->redirectToRoute('app_error');
        }

        return $this->render('product/show_product_by_slug.html.twig', [
            'product'=> $product
        ]);


    }
    #[Route('/error', name: 'app_error')]
    public function errorPage()
    {

        return $this->render('page/not-found.html.twig', [
            'controller_name' => 'PageController'
        ]);
    }

}
