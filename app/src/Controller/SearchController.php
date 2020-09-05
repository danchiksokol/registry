<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search")
     */
    public function index()
    {
        return $this->render(
            'search/index.html.twig',
            [
                'controller_name' => 'SearchController',
            ]
        );
    }

    public function searchBar()
    {
        $form = $this->createFormBuilder(null)->add('query', SearchType::class)
            ->add(
                'search',
                SubmitType::class,
                ['attr' => ['class' => 'btn btn-ptymary']]
            )->getForm();

        return $this->render(
            'search/searchBar.html.twig',
            ['form' => $form->createView()]
        );
    }
}
