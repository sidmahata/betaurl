<?php

namespace AppBundle\Utils;

use Hashids\Hashids;
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
    
    public function getUrlList($offset, $limit, $sort, $userid){
        
        // return $this->em->getRepository('AppBundle:Url')->findBy(array(), array('id' => $sort), $limit, $offset);
        
        $repository = $this->em
            ->getRepository('AppBundle:Url');
        
        // createQueryBuilder automatically selects FROM AppBundle:Url
        // and aliases it to "p"
        $query = $repository->createQueryBuilder('p')
            ->select(array('p.shortcode', 'p.longurl'))
            ->where('p.userid = :userid')
            ->setParameter('userid', $userid)
            ->orderBy('p.id', $sort)
            ->setFirstResult( $offset )
            ->setMaxResults( $limit )
            ->getQuery();
            // ->useQueryCache(true, 160)
            // ->useResultCache(true, 160);
        
        $result = $query->getResult();
        
        return $result;
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
            ->where('p.longurlindex = :longurlindex AND p.userid = :userid')
            ->setParameters(array('longurlindex' => $longurlindex, 'userid' => $urlformdata->getUserid()) )
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