<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
//        $phpinfo = phpinfo();
        
        $url = 'http://108.61.207.200/ZGpwr';
        $parse = parse_url($url);
        
        $router = $this->get('router');
        $routes = $router->getRouteCollection();
        

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
            'base_url' => "http://" . $_SERVER['SERVER_NAME'],
            'parse_url' => "http://" . $parse['host'],
            'routes' => $routes
//            'phpinfo' => $phpinfo,
        ));
    }
    
    
    
    
    
    /**
     * @Route("/contact", name="contact")
     */
    public function contactAction()
    {
        
        return new Response("contact");
    }
    
    
    /**
     * @Route("/about-us", name="about-us")
     */
    public function aboutusAction()
    {
        return new Response("about-us");
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
