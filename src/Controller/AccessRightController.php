<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\AccessRight;
use App\Entity\Group;
use App\Entity\Interface\HasAccessRights;
use App\Form\AccessRightType;
use App\Repository\AccessRightRepository;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use App\Service\AccessRightService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/access/right')]
class AccessRightController extends AbstractController
{
    #[Route('/', name: 'access_right_index', methods: ['GET'])]
    public function index(
        UserRepository $userRepository,
        GroupRepository $groupRepository
    ): Response {
        return $this->render('access_right/index.html.twig', [
            'users' => $userRepository->findAll(),
            'groups' => $groupRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'access_right_new', methods: ['GET','POST'])]
    public function new(
        Request $request,
        AccessRightService $accessRightService
    ): Response {
        $ownerType = $request->query->get('owner_type');
        $module = $request->query->get('module');

        $accessRight = new AccessRight();
        if (
            class_exists($ownerType)
            && is_a(new $ownerType, HasAccessRights::class)
        ) {
            $accessRight->setOwnerType($ownerType);
        }

        if (in_array(
            $module,
            array_keys($accessRightService->getPermissionsList())
        )) {
            $accessRight->setModule($module);
        }

        $form = $this->createForm(AccessRightType::class, $accessRight);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($accessRight);
            $entityManager->flush();

            return $this->redirectToRoute('access_right_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('access_right/new.html.twig', [
            'permissions' => $accessRightService->getPermissionsList(),
            'access_right' => $accessRight,
            'form' => $form,
            'disabled' => (empty($ownerType) || empty($module))
        ]);
    }

    #[Route('/{id}', name: 'access_right_show', methods: ['GET'])]
    public function show(AccessRight $accessRight): Response
    {
        return $this->render('access_right/show.html.twig', [
            'access_right' => $accessRight,
        ]);
    }

    #[Route('/{id}/edit', name: 'access_right_edit', methods: ['GET','POST'])]
    public function edit(Request $request, AccessRight $accessRight): Response
    {
        $form = $this->createForm(AccessRightType::class, $accessRight);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('access_right_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('access_right/edit.html.twig', [
            'access_right' => $accessRight,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'access_right_delete', methods: ['POST'])]
    public function delete(Request $request, AccessRight $accessRight): Response
    {
        if ($this->isCsrfTokenValid('delete'.$accessRight->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($accessRight);
            $entityManager->flush();
        }

        return $this->redirectToRoute('access_right_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/user/{id}', name: 'access_right_user', methods: ['GET', 'POST'])]
    public function listUser(
        User $user,
        AccessRightService $accessRightService
    ): Response {
        return $this->render('access_right/list.html.twig', [
            'owner' => $user,
            'access_rights' => $accessRightService->getRightsByOwner(
                $user
            ),
        ]);
    }

    #[Route('/group/{id}', name: 'access_right_group', methods: ['GET', 'POST'])]
    public function listGroup(
        Group $group,
        AccessRightService $accessRightService
    ): Response {
        return $this->render('access_right/list.html.twig', [
            'owner' => $group,
            'access_rights' => $accessRightService->getRightsByOwner(
                $group
            ),
        ]);
    }
}
