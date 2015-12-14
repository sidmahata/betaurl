<?php

namespace AppBundle\Utils;

class UrlRESTUtil{
    
    protected $em;

    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function getUrlById($id){
        
        return $this->em->getRepository('AppBundle:Url')->find($id);
    }
    
    public function getUrlList($offset, $limit, $sort){
        
        // return $this->em->getRepository('AppBundle:Url')->findBy(array(), array('id' => $sort), $limit, $offset);
        
        $repository = $this->em
            ->getRepository('AppBundle:Url');
        
        // createQueryBuilder automatically selects FROM AppBundle:Product
        // and aliases it to "p"
        $query = $repository->createQueryBuilder('p')
            // ->where('p.price > :price')
            // ->setParameter('price', '19.99')
            ->orderBy('p.id', $sort)
            ->getQuery()
            ->useResultCache(true, 160);
        
        $result = $query->getResult();
        
        return $result;
    }
    
}