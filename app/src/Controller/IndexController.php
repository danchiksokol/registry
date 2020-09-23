<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\UsersType;
use App\Form\UploadFileType;
use App\Services\UploadFileService;
use App\Services\XlsxService;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class IndexController extends AbstractController
{
    /**
     * @Route("/admin", name="main_page")
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
     */
    public function active(Users $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(Users::class)->find($id);
        $user->setActive(1);
        $em->persist($user);
        $em->flush();
        $em->clear();

        return $this->redirect('/admin');
    }

    /**
     * @Route("/", name = "create_page")
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


            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirect('/admin');
            }

            return $this->redirect('/');
        }

        return $this->render(
            'user/form.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/update/{id}", name="update_user")
     * @IsGranted("ROLE_ADMIN")
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

            return $this->redirect('/admin');
        }

        return $this->render(
            'user/form.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/getXlsx", name="download_xlsx")
     * @IsGranted("ROLE_ADMIN")
     * @param XlsxService $xlsxService
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function getXlsx(XlsxService $xlsxService, UsersRepository $users)
    {
        $users = $users->findAll();

        return $xlsxService->generate($users);
    }

    /**
     * @Route("/importExel", name="import_exel")
     * @IsGranted("ROLE_ADMIN")
     *
     * @param UploadFileService $fileService
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function importExel(Request $request, XlsxService $xlsxService)
    {
        $user = new Users();
        $form = $this->createForm(UploadFileType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['uploadFile']->getData();
            $rows = $xlsxService->readExel($file);;
            foreach ($rows as $row) {
                if ($row[0]) {
                    $fio = explode(" ", $row[0]);
                }
                if (isset($fio[0])) {
                    $user->setLastname($fio[0]);
                }
                if (isset($fio[1])) {
                    $user->setMiddlename($fio[1]);
                }
                if (isset($fio[2])) {
                    $user->setFirstname($fio[2]);
                }
                $user->setJob($row[1]);
                $user->setPosition($row[2]);
                $user->setPhone($row[3]);
                $user->setEmail($row[4]);
                $user->setCity($row[5]);
                $user->setActive(false);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            }

            return $this->redirect('/');
        }

        return $this->render(
            'index/upload.html.twig',
            ['form' => $form->createView()]
        );
    }

}
