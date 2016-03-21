<?php
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Faker;
use Hashids\Hashids;
use AppBundle\Entity\Url;
use AppBundle\Form\UrlType;
/**
 * Url controller.
 * @RouteResource("Url")
 */
class UrlRESTController extends Controller
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
            throw $this->createNotFoundException('The product does not exist');
        }

        return $data;
    }

    /**
     *
     * @View()
     *
     */
    public function getLongurlAction($shortcode)
    {
        // Using Utils Service app.UrlRESTUtil to get Url by shortcode
        $data = $this->get('app.UrlRESTUtil')->getUrlByShortcode($shortcode);

        if(!$data){
            throw $this->createNotFoundException('The Url does not exist');
        }

        return $data;
    }

    /**
     * @View()
     *
     * @QueryParam(name="limit", requirements="\d+", default="10", description="limit of the url list.")
     * @QueryParam(name="offset", requirements="\d+", default="0", nullable=true, description="offset for the url list.")
     * @QueryParam(name="sort", requirements="(asc|desc)+", allowBlank=false, default="desc", description="Sort direction")
     * @QueryParam(name="dateFrom", description="date from which analytics wanted")
     * @QueryParam(name="dateTo", description="date from which analytics wanted")
     *
     * @param ParamFetcher $paramFetcher
     */
    public function cgetAction(ParamFetcher $paramFetcher)
    {
        $limit = $paramFetcher->get('limit');
        $offset = $paramFetcher->get('offset');
        $sort = $paramFetcher->get('sort');
        $dateFrom = $paramFetcher->get('dateFrom');
        $dateTo = $paramFetcher->get('dateTo');
        
        // check if user is logged in - tehn set userid else userid is itself set to 0
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $userid = $this->getUser()->getId();
        }else{
            $userid = 0;
        }

        // Using Utils Service app.UrlRESTUtil to get all Url list
        $data = $this->get('app.UrlRESTUtil')->getUrlList($offset, $limit, $sort, $userid, $dateFrom, $dateTo);
 
        if(!$data){
            throw $this->createNotFoundException('No url in list yet.');
        }

        return $data;
    }
    
    
    /**
     *
     * @View()
     *
     */
    public function getUrltotalAction()
    {
    	if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $userid = $this->getUser()->getId();
        }else{
            $userid = 0;
        }
    	
        $data = $this->get('app.UrlRESTUtil')->getUrlTotal($userid);

	    if (!$data) {
	        throw $this->createNotFoundException(
	            'No record found'
	        );
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
        $form = $this -> createForm(new UrlType(), new Url(), array(
            'method' => 'POST',
            'csrf_protection' => false,
        ));
        
        $form->submit($request->request->all());

        if (! $form->isValid()){
            exit($form->getErrors());
        }
        // get form data
        $urlformdata = $form->getData();
        
        // check if longurl misses http:// and add it up.
        $longurlcheck = $urlformdata->getLongurl();
        // Recognizes ftp://, ftps://, http:// and https:// in a case insensitive way and adds http:// if not present
        if (!preg_match("~^(?:f|ht)tps?://~i", $longurlcheck)) {
            $longurlcheck = "http://" . $longurlcheck;
        }
        $urlformdata->setLongurl($longurlcheck);
        
        // check if longurl given is valid or not.
        $checkUrl = $this->get('app.ValidUrlUtil')->checkValidUrl($urlformdata->getLongurl());
        if($checkUrl == $urlformdata->getLongurl()){
            // check if user is logged in - then set userid else userid is itself set to 0
            if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
                
                $urlformdata->setUserid($this->getUser()->getId());
            }else{
                $urlformdata->setUserid(0);
            }
    
            $postdata = $this->get('app.UrlRESTUtil')->postUrlByLongurl($urlformdata);
    
            return $postdata;
            
        }else{
            return array('checkurl'=>$checkUrl);
        }

        
    }
    
    
    /**
     * @View()
     *
     * @QueryParam(name="id", description="youtube video id")
     * @QueryParam(name="limit", requirements="\d+", default="6", description="limit of the video topicIds related list.")
     *
     * @param ParamFetcher $paramFetcher
     */
    public function getYoutubeRelatedAction(ParamFetcher $paramFetcher)
    {
    	$id = $paramFetcher->get('id');
    	$limit = $paramFetcher->get('limit');
    	
        //query youtube api for related videos using video id
        $response = file_get_contents('https://www.googleapis.com/youtube/v3/search?part=snippet&fields=items(id,snippet)&relatedToVideoId='.$id.'&type=video&maxResults='.$limit.'&key=AIzaSyBiCpKe3uoVgjmi8PCVOyhMI89MhDcWsQc');
        $responseDecode = json_decode($response, true);
        
        //get related 1st topic id from youtube topicIds Response
        //if(isset($responseDecode['items'][0]['topicDetails']['topicIds'][0])){
        	//$topicId = $responseDecode['items'][0]['topicDetails']['topicIds'][0];
        //}else{
        	//$topicId = $responseDecode['items'][0]['topicDetails']['relevantTopicIds'][1];
        //}
        
        //get related topic ids videos list
        //$topicIdResponse = file_get_contents('https://www.googleapis.com/youtube/v3/search?part=snippet&topicId='.$topicId.'&maxResults='.$limit.'&key=AIzaSyBiCpKe3uoVgjmi8PCVOyhMI89MhDcWsQc');
        //$topicIdResponseDecode = json_decode($topicIdResponse, true);
        
        $result = [];
        foreach($responseDecode['items'] as $i=>$value) {
		    //array_push($result, 
        	//'title' => $topicIdResponseDecode['items'][$i]['snippet']['title']
        	//);
        	$resultItem = new \stdClass();
        	$resultItem->videoId = $responseDecode['items'][$i]['id']['videoId'];
        	$resultItem->title = $responseDecode['items'][$i]['snippet']['title'];
        	$resultItem->description = $responseDecode['items'][$i]['snippet']['description'];
        	$resultItem->thumbnails = $responseDecode['items'][$i]['snippet']['thumbnails']['medium']['url'];
        	$resultItem->channelTitle = $responseDecode['items'][$i]['snippet']['channelTitle'];

        	array_push($result, $resultItem);
		}
        
        return new JsonResponse($result);
    }
}