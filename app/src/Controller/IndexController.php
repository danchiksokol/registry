<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\UsersType;
use App\Services\XlsxService;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="main_page")
     */
    public function index(Request $request, UsersRepository $users)
    {
        $search = $request->get('search', null);
        $parameters = [
            'users' => $users->findByAllFields(trim($search)),
            'search' => $search,
        ];

        return $this->render('index/index.html.twig', $parameters);
    }

    /**
     * @Route("/active/{id}", name="setActive")
     */
    public function active(Users $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(Users::class)->find($id);
        $user->setActive(1);
        $em->persist($user);
        $em->flush();
        $em->clear();

        return $this->redirect('/');
    }

    /**
     * @Route("/create", name = "createUser")
     */
    public function create(Request $request)
    {
        $user = new Users();

        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect('/');
        }

        return $this->render(
            'user/form.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/update/{id}", name="update_user")
     */
    public function update(Request $request, Users $user)
    {
        $form = $this->createForm(
            UsersType::class,
            $user,
            [
                'action' => $this->generateUrl(
                    'update_user',
                    [
                        'id' => $user->getId(),
                    ]
                ),
                'method' => 'POST',
            ]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect('/');
        }

        return $this->render(
            'user/form.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/getXlsx", name="download_xlsx")
     * @param XlsxService $xlsxService
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function getXlsx(XlsxService $xlsxService, UsersRepository $users)
    {
        $users = $users->findAll();
        return $xlsxService->generate($users);
    }

}
