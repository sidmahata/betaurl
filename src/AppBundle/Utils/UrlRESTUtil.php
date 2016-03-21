<?php

namespace AppBundle\Utils;

use Hashids\Hashids;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
class UrlRESTUtil{
    
    protected $em;

    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function getHashidsShortcode($id)
    {
        // create hashids for shortcode using the url row id
        $hashids = new Hashids("this is my salt", 5, "bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ1234567890");
        $shortcode = $hashids->encode($id);
        // $numbers = $hashids->decode($id);
        return $shortcode;
    }
    
    public function getHashidsShortcodeId($shortcode)
    {
        // create hashids for shortcode using the url row id
        $hashids = new Hashids("this is my salt", 5, "bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ1234567890");
        // $shortcode = $hashids->encode($id);
        $numberId = $hashids->decode($shortcode);
        return $numberId;
    }
    
    public function getUrlById($id)
    {
        $result = $this->em->getRepository('AppBundle:Url')->find($id);
        
        // create hashids for shortcode using the url row id
        // $hashids = new Hashids("this is my salt", 8, "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890");
        // $shortcode = $hashids->encode($result->getId());
        // $numbers = $hashids->decode($id);
        
        // $result->shortcode = '123';
        // var_dump($result);
        
        // $output = (object)array();
        // // $output->longurl = $result->getLongurl();
        // $output->shortcode = $shortcode;
        
        return $result;
        
    }
    
    public function getUrlByShortcode($shortcode)
    {
        // get id from hashids function decode from shortcode
        $id = $this->getHashidsShortcodeId($shortcode);

        if( empty($id) ){
            throw new NotFoundHttpException("Shortcode does not exist");
        }

        $result = $this->em->getRepository('AppBundle:Url')->find($id[0]);

        if(!$result){
            throw $this->createNotFoundException('The url does not exist');
        }
        
        return $result->getLongurl();
        
    }
    
    public function getUseridByShortcode($shortcode)
    {
        // get id from hashids function decode from shortcode
        $id = $this->getHashidsShortcodeId($shortcode);

        if( empty($id) ){
            throw new NotFoundHttpException("Shortcode does not exist");
        }

        $result = $this->em->getRepository('AppBundle:Url')->find($id[0]);

        if(!$result){
            throw $this->createNotFoundException('The shortcode does not exist');
        }
        
        return $result->getUserid();
        
    }
    
    
    public function getUrlTotal($userid){
    	
    	$repository = $this->em
            ->getRepository('AppBundle:Url');
            
        $query = $repository->createQueryBuilder('p')
            ->where('p.userid = :userid')
            ->setParameter('userid', $userid)
            ->getQuery();
            
        $result = $query->getResult();
        $total = count($result);
        
        return $total;
    }
    
    
    public function getUrlList($offset, $limit, $sort, $userid, $dateFrom, $dateTo){
    	
        
        // return $this->em->getRepository('AppBundle:Url')->findBy(array(), array('id' => $sort), $limit, $offset);
        
        $repository = $this->em
            ->getRepository('AppBundle:Url');
        
        // createQueryBuilder automatically selects FROM AppBundle:Url
        // and aliases it to "p"
        $query = $repository->createQueryBuilder('p')
            ->select(array('p.shortcode', 'p.longurl', 'p.createdAt'))
            ->where('p.userid = :userid')
            ->setParameter('userid', $userid)
            ->orderBy('p.id', $sort)
            ->setFirstResult( $offset )
            ->setMaxResults( $limit )
            ->getQuery();
            // ->useQueryCache(true, 160)
            // ->useResultCache(true, 160);
        
        $queryResult = $query->getResult();
        

        
        $urls = [];
        foreach($queryResult as $key=>$value){
        	$urls[$key] = urlencode('method=Actions.getPageUrl&pageUrl='.'http://' . $_SERVER['SERVER_NAME'].'/'.$value['shortcode'].'&period=range&date='.$dateFrom.','.$dateTo.'&idSite=1');
        }
        
        $urlsCollection="";
        foreach ($urls as $key=>$urlsData) {
		    $urlsCollection .= '&urls['.$key.']='.$urlsData;
		}
        
        // $response = file_get_contents(
        // 	'http://analytics.feedbit.net/?module=API&method=API.getBulkRequest&urls[0]=method%3dActions.getPageUrl%26pageUrl%3dhttp://feedbit.net/Zj7vr%26period%3drange%26date%3d2016-02-01%2c2016-03-15%26idSite%3d1&urls[1]=method%3dActions.getPageUrl%26pageUrl%3dhttp://feedbit.net/3r3gr%26period%3drange%26date%3d2016-02-01%2c2016-03-15%26idSite%3d1&token_auth=0afa1bd061671c26e449d06a62838b79&format=json'
        // 	);
        	
        	$response = file_get_contents(
        	'http://analytics.feedbit.net/?module=API&method=API.getBulkRequest&token_auth=0afa1bd061671c26e449d06a62838b79&format=json'.$urlsCollection
        	);
        $responseDecode = json_decode($response, true);
        
        $visits = [];
        foreach($responseDecode as $key=>$value) {
        	$visitsItem = new \stdClass();
        	
        	if(isset($responseDecode[$key][0])){
        		$visitsItem = $responseDecode[$key][0]['nb_visits'];
        	}else{
        		$visitsItem = 0;
        	}
        	
        	array_push($visits, $visitsItem);
		}
		// var_dump($responseDecode);
        // return $queryResult;
        // return new JsonResponse($visits);
        
        $result = [];
        foreach($queryResult as $key=>$value){
        	$resultItem = new \stdClass();
        	
        	$resultItem->shortcode = $value['shortcode'];
        	$resultItem->longurl = $value['longurl'];
        	$resultItem->nb_visits = $visits[$key];
        	$resultItem->createdAt = $value['createdAt'];
        	
        	array_push($result, $resultItem);
        }
        
        return new JsonResponse($result);
    }

    public function get64BitHash($str)
    {
        return gmp_strval(gmp_init(substr(md5($str), 0, 14), 16), 10);
    }
    
    public function postUrlByLongurl($urlformdata)
    {
        
        // converting the submitted longurl to unique BIGINT
        $longurlindex = $this->get64BitHash($urlformdata->getLongurl());
        // setting longurlindex to converted longurl bigint
        $urlformdata->setLongurlindex($longurlindex);

        // check if this $longurlindex exixts in database
        $repository = $this->em
            ->getRepository('AppBundle:Url');
        // createQueryBuilder automatically selects FROM AppBundle:Url
        // and aliases it to "p"
        $query = $repository->createQueryBuilder('p')
            ->where('p.longurlindex = :longurlindex AND p.longurl = :longurl AND p.userid = :userid')
            ->setParameters(array('longurlindex' => $longurlindex, 'longurl' => $urlformdata->getLongurl(), 'userid' => $urlformdata->getUserid()) )
            ->getQuery();

        $result = $query->getOneOrNullResult();

        if( $result == null ){
            // push urlformdata to database
             $em = $this->em;
             $em->persist($urlformdata);
             $em->flush();
             
            // generate shortcode from id of url row generated (using getHashidsShortcode)
            $shortcode = $this->getHashidsShortcode($urlformdata->getId());

            // update the shortcode generated in the database
            $urlformdata->setShortcode($shortcode);
            $em->persist($urlformdata);
            $em->flush();

            // return $urlformdata;
            return array(
                'shortcode' => $urlformdata->getShortcode(),
                'longurl' => $urlformdata->getLongurl(),
                );
        }else{
            return array(
                'shortcode' => $result->getShortcode(),
                'longurl' => $result->getLongurl(),
                );
        }

        // return $urlformdata;
    }



}