<?php
namespace lordmatt\tools\cachedget;

/**
 * This getter should work for most cases. If not, roll your own.
 *
 * @author lordmatt
 */
class curl_getter implements i_file_getter {
    
    public function get_remote($path){
        
        $follow_redirects=true;
        
        $useragent='cURL /cachedget';
        
        // initialise the CURL library
        $ch = curl_init();

        // specify the URL to be retrieved
        curl_setopt($ch, CURLOPT_URL,$url);

        // we want to get the contents of the URL and store it in a variable
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

        // specify the useragent: this is a required courtesy to site owners
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);

        // ignore SSL errors
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // follow redirects - note this is disabled by default in most PHP installs from 4.4.4 up
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 

        // otherwise just return the contents as a variable
        $result=curl_exec($ch);

        // free resources
        curl_close($ch);

        // send back the data
        return $result;
        
    }
    
}
