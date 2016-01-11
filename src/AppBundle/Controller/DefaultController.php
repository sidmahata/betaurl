<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
//        $phpinfo = phpinfo();

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
//            'phpinfo' => $phpinfo,
        ));
    }
    
    /**
     * @Route("/{shortcode}", name="shortcode_redirect")
     */
    public function redirectAction(Request $request, $shortcode)
    {
        
        // Using Utils Service app.UrlRESTUtil to get Url by shortcode
        $data = $this->get('app.UrlRESTUtil')->getUrlByShortcode($shortcode);

        if(!$data){
            throw $this->createNotFoundException('The Url does not exist');
        }

        return $this->redirect($data);
        
    }
}
