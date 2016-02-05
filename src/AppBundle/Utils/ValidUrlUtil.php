<?php

namespace AppBundle\Utils;

class ValidUrlUtil{
    
    
    /**
     * @param $url
     *
     * @return string
     */
    public function checkValidUrl($url){

        if(empty($url)){
            $shortcode = "No URL was supplied.";
        }elseif($this->validateUrlFormat($url) == false){
            $shortcode = "URL does not have a valid format.";
        }elseif($this->checkOwnUrl($url) == true){
            $shortcode = "This url is already shortened by us.";
        }elseif (!$this->verifyUrlExists($url)) {
                $shortcode = "URL does not appear to exist.";
        }else{
            $shortcode = $url;
        }

        return $shortcode;
    }
    
    
    /**
     * @param $url
     *
     * @return bool
     */
    protected function checkOwnUrl($url) {

        // Recognizes ftp://, ftps://, http:// and https:// in a case insensitive way and adds http:// if not present
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "http://" . $url;
        }
        
        $whitelist = array(
                "http://".$_SERVER['SERVER_NAME']."/contact",
                "http://".$_SERVER['SERVER_NAME']."/about-us"
                );
        
        // getting the base domain name from url
        $parse = parse_url($url);
        
        if($parse['host'] == $_SERVER['SERVER_NAME']){
            
            if (in_array($url, $whitelist)) {
                return false;
            }
            
            return true;
        }
        
        return false;
    }

    
    /**
     * @param $url
     *
     * @return mixed
     */
    protected function validateUrlFormat($url) {

        // Recognizes ftp://, ftps://, http:// and https:// in a case insensitive way and adds http:// if not present
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "http://" . $url;
        }

        // Remove all illegal characters from a url
        $url = filter_var($url, FILTER_SANITIZE_URL);

        return filter_var($url, FILTER_VALIDATE_URL,
            FILTER_FLAG_HOST_REQUIRED);
    }

    
    /**
     * @param $url
     *
     * @return bool
     */
    protected function verifyUrlExists($url) {
        
        // Recognizes ftp://, ftps://, http:// and https:// in a case insensitive way and adds http:// if not present
        if (strpos($url, "https://")!== false) {
            $url = str_replace("https","http",$url);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
        curl_setopt($ch,  CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return (!empty($response) && $response != 404);
    }
    
}