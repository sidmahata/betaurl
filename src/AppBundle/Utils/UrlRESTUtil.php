<?php

namespace AppBundle\Utils;

use Hashids\Hashids;

class UrlRESTUtil{
    
    protected $em;

    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
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
        // create hashids for shortcode using the url row id
        $hashids = new Hashids("this is my salt", 8, "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890");
        // $shortcode = $hashids->encode($result->getId());
        $id = $hashids->decode($shortcode);
        $result = $this->em->getRepository('AppBundle:Url')->find($id[0]);
        
        return $result->getLongurl();
        
    }
    
    public function getUrlList($offset, $limit, $sort){
        
        // return $this->em->getRepository('AppBundle:Url')->findBy(array(), array('id' => $sort), $limit, $offset);
        
        $repository = $this->em
            ->getRepository('AppBundle:Url');
        
        // createQueryBuilder automatically selects FROM AppBundle:Url
        // and aliases it to "p"
        $query = $repository->createQueryBuilder('p')
            // ->where('p.price > :price')
            // ->setParameter('price', '19.99')
            ->orderBy('p.id', $sort)
            ->setFirstResult( $offset )
            ->setMaxResults( $limit )
            ->getQuery()
            ->useQueryCache(true, 160)
            ->useResultCache(true, 160);
        
        $result = $query->getResult();
        
        return $result;
    }
    
    public function postUrlByLongurl($urlformdata)
    {
        // converting the submitted longurl to unique BIGINT
        $urlformdata->setLongurlindex(gmp_strval(gmp_init(substr(md5($urlformdata->getLongurl()), 0, 16), 16), 10));
        
        // $em = $this->em->getRepository('AppBundle:Url');
        // $em->persist($urlformdata);
        // $em->flush();
        
        return $urlformdata;
    }
    
}