<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{
    /**
     * @Route("/user/dashboard", name="dashboard")
     */
    public function indexAction(Request $request)
    {
        return $this->render('dashboard/layout.html.twig', array(
            
        ));
    }
}
