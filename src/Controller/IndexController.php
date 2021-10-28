<?php

namespace App\Controller;

use App\Service\AccessRightService;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    public function __construct(
        protected Security $security,
        protected AccessRightService $accessRightService
    ) { }

    #[Route('/index', name: 'index')]
    public function index(): Response
    {
        echo '----------------------------------------';
        echo 'MacroDump@'.__FILE__.':'.(__LINE__+1).PHP_EOL;
        dump($this->accessRightService->getPermissionsList());
        die('MacroDump@'.__FILE__.':'.__LINE__.PHP_EOL);
        echo '----------------------------------------';

       echo '----------------------------------------';
       echo 'MacroDump@'.__FILE__.':'.(__LINE__+1).PHP_EOL;
       dump(
            $this->accessRightService->checkUserHasAccess(
                $this->security->getUser(),
                'FirstModule',
                'firstFunction'
            )
        );
       die('MacroDump@'.__FILE__.':'.__LINE__.PHP_EOL);
       echo '----------------------------------------';

        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
}
