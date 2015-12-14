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
        
        return $this->em->getRepository('AppBundle:Url')->findBy(array(), array('id' => $sort), $limit, $offset);
    }
    
}