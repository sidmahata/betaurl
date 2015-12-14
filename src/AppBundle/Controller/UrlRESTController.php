<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcherInterface;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Faker;

use AppBundle\Entity\Url;
use AppBundle\Form\UrlType;

/**
 * Url controller.
 * @RouteResource("Url")
 */
class UrlRESTController extends FOSRestController
{
     /**
     * This method will return Url information by using the id.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Get Url information by id",
     *  section="UrlREST",
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when the requested Url is not found"
     *  }
     * )
     * 
     * @View()
     * 
     * @param int   $id     The Url id
     */
    public function getAction($id)
    {
        // Using Utils Service app.UrlRESTUtil to get Url by id
        $data = $this->get('app.UrlRESTUtil')->getUrlById($id);
        
        if(!$data){
            // return array('error' => (Codes::HTTP_NOT_FOUND));
            throw $this->createNotFoundException('The product does not exist');
        }
        
        return $data;
    }
    
    /**
    * @View()
    * 
    * @QueryParam(name="limit", requirements="\d+", default="5", description="limit of the url list.")
    * @QueryParam(name="offset", requirements="\d+", default="0", nullable=true, description="offset for the url list.")
    * @QueryParam(name="sort", requirements="(asc|desc)+", allowBlank=false, default="desc", description="Sort direction")
    * 
    * @param ParamFetcher $paramFetcher
    */
    public function cgetAction(ParamFetcher $paramFetcher)
    {
        $limit = $paramFetcher->get('limit');
        $offset = $paramFetcher->get('offset');
        $sort = $paramFetcher->get('sort');
        
        // Using Utils Service app.UrlRESTUtil to get all Url list
        $data = $this->get('app.UrlRESTUtil')->getUrlList($offset, $limit, $sort);
        
        if(!$data){
            throw $this->createNotFoundException('The product does not exist');
        }
        
        return $data;
    }
    
    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Post a url",
     *  section="UrlREST",
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when the requested Url is not found"
     *  }
     * )
     * 
     * @View()
     * 
     */
    public function postAction(Request $request)
    {
        $faker = Faker\Factory::create();
        
        $form = $this -> createForm(new UrlType(), new Url(), array(
            'method' => 'POST',
            'csrf_protection' => false,
        ));    
        
        $form->submit($request->request->all());
        
        if (! $form->isValid()){
            exit($form->getErrors());
        }
//        get form data
        $urldata = $form -> getData();
        
        // discarding the form values and inserting fake values in the database
        $urldata->setLongurl($faker->url);
        $urldata->setLongurlindex($faker->ean13);
        
        $em = $this -> getDoctrine() -> getManager();
        $em -> persist($urldata);
        $em -> flush();
        
        
        return $urldata;
    }
}