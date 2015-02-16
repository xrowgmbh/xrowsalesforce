<?php
////////////////////////////////////////////////////
// only for new universal analytics with js ga-campaign-loader.js
////////////////////////////////////////////////////

class GA_Parse
{
    var $utm_source;                // Campaign Source
    var $campaign_name;             // Campaign Name
    var $utm_medium;                // Campaign Medium
    var $utm_content;               // Campaign Content

    function __construct() {
        // If we have the cookies we can go ahead and parse them.
        if (isset($_COOKIE["UACName"]) || isset($_COOKIE["UACContent"]) || isset($_COOKIE["UACSource"]) || isset($_COOKIE["UACMedium"])) {
            $this->ParseCookies();
        }
    }

    function ParseCookies(){
        if(isset($_COOKIE["UACName"]))
        {
            $this->campaign_name = urldecode(trim($_COOKIE["UACName"]));
        }
        if(isset($_COOKIE["UACContent"]))
        {
            $this->utm_content = urldecode(trim($_COOKIE["UACContent"]));
        }
        if(isset($_COOKIE["UACSource"]))
        {
            $this->utm_source = urldecode(trim($_COOKIE["UACSource"]));
        }
        if(isset($_COOKIE["UACName"]))
        {
            $this->utm_medium = urldecode(trim($_COOKIE["UACMedium"]));
        }
    }
}