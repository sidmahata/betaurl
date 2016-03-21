<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcherInterface;

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
    	
    	
        return new JsonResponse('about us');
    }
    
    
    /**
     * @Route("/analytics", name="analytics")
     *
     * @QueryParam(name="shortcode", default="3r3gr", description="shortcode of which analytics is required")
     * @QueryParam(name="period", default="month", description="period of analytics data required")
     * @QueryParam(name="date", default="today", description="date from which analytics data required")
     *
     * @param ParamFetcher $paramFetcher
     *
     */
     public function analyticsAction(ParamFetcher $paramFetcher)
     {
     	$shortcode = $paramFetcher->get('shortcode');
     	$period = $paramFetcher->get('period');
     	$date = $paramFetcher->get('date');
     	$piwik_token_auth = $this->container->getParameter('piwik_token_auth');
     	$piwik_domain = $this->container->getParameter('piwik_domain');
     	
     	// get session userid
     	if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
                
            $userid = $this->getUser()->getId();
        }else{
            $userid = 0;
        }
        
        // get shortcode user id
        $shortcodeUserid = $this->get('app.UrlRESTUtil')->getUseridByShortcode($shortcode);
        
        // check if session userid matches with shortcode user id, if matches show analytics data
        if($userid == $shortcodeUserid){
        	$url = $piwik_domain."/?module=API&method=Actions.getPageUrl&pageUrl="."http://" . $_SERVER['SERVER_NAME']."/".$shortcode."&period=".$period."&date=".$date."&idSite=1&token_auth=".$piwik_token_auth."&format=json";
    	
	    	//  Initiate curl
			$ch = curl_init();
			// Disable SSL verification
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			// Will return the response, if false it print the response
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			// Set the url
			curl_setopt($ch, CURLOPT_URL,$url);
			// Execute
			$result=curl_exec($ch);
			// Closing
			curl_close($ch);
			
			// Will dump a beauty json :3
			$data = json_decode($result, true);
			
	        return new JsonResponse( $data );
        }else{
        	return new JsonResponse('You are not authorized to view this data.');
        }
     	
     }
     
     
     /**
     * @Route("/analytics/country", name="analytics countrywise")
     *
     * @QueryParam(name="shortcode", default="3r3gr", description="shortcode of which analytics is required")
     * @QueryParam(name="period", default="month", description="period of analytics data required")
     * @QueryParam(name="date", default="today", description="date from which analytics data required")
     *
     * @param ParamFetcher $paramFetcher
     *
     */
     public function analyticsMonthAction(ParamFetcher $paramFetcher)
     {
     	$shortcode = $paramFetcher->get('shortcode');
     	$period = $paramFetcher->get('period');
     	$date = $paramFetcher->get('date');
     	$piwik_token_auth = $this->container->getParameter('piwik_token_auth');
     	$piwik_domain = $this->container->getParameter('piwik_domain');
     	
     	
     	$response = file_get_contents($piwik_domain."/?module=API&method=UserCountry.getCountry&segment=pageUrl=="."http://" . $_SERVER['SERVER_NAME']."/".$shortcode."&period=".$period."&date=".$date."&idSite=1&token_auth=".$piwik_token_auth."&format=json");
        $responseDecode = json_decode($response, true);

        return new JsonResponse( $responseDecode );
     }
     
     /**
     * @Route("/analytics/referrer", name="analytics referrer data")
     *
     * @QueryParam(name="shortcode", default="3r3gr", description="shortcode of which analytics is required")
     * @QueryParam(name="period", default="month", description="period of analytics data required")
     * @QueryParam(name="date", default="today", description="date from which analytics data required")
     *
     * @param ParamFetcher $paramFetcher
     *
     */
     public function analyticsReferrerAction(ParamFetcher $paramFetcher)
     {
     	$shortcode = $paramFetcher->get('shortcode');
     	$period = $paramFetcher->get('period');
     	$date = $paramFetcher->get('date');
     	$piwik_token_auth = $this->container->getParameter('piwik_token_auth');
     	$piwik_domain = $this->container->getParameter('piwik_domain');
     	
     	$response = file_get_contents($piwik_domain."/?module=API&method=Referrers.getWebsites&segment=pageUrl=="."http://" . $_SERVER['SERVER_NAME']."/".$shortcode."&period=".$period."&date=".$date."&idSite=1&token_auth=".$piwik_token_auth."&format=json");
        $responseDecode = json_decode($response, true);

        return new JsonResponse( $responseDecode );
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

        // return $this->redirect($data);
        return $this->render('default/redirect.html.twig', array(
        	'redirecturl' => $data,
        ));
        
    }
}
