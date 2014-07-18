<?php
////////////////////////////////////////////////////
// GA_Parse - PHP Google Analytics Parser Class
//
// Version 1.0 - Date: 17 September 2009
// Version 1.1 - Date: 25 January 2012
// Version 1.2 - Date: 21 April 2012
//
// Define a PHP class that can be used to parse
// Google Analytics cookies currently with support
// for __utmz (campaign data) and __utma (visitor data)
//
// Author: Joao Correia - http://joaocorreia.pt
//
// License: LGPL
//
////////////////////////////////////////////////////

class GA_Parse
{
    var $utm_source;                // Campaign Source
    var $campaign_name;             // Campaign Name
    var $utm_medium;                // Campaign Medium
    var $utm_content;               // Campaign Content
    var $campaign_term;              // Campaign Term

    var $first_visit;               // Date of first visit
    var $previous_visit;            // Date of previous visit
    var $current_visit_started;     // Current visit started at
    var $times_visited;             // Times visited
    var $pages_viewed;              // Pages viewed in current session

    function __construct($_COOKIE) {
        // If we have the cookies we can go ahead and parse them.
        if (isset($_COOKIE["__utma"]) and isset($_COOKIE["__utmz"])) {
            $this->ParseCookies();
        }
    }

    function ParseCookies(){

        // Parse __utmz cookie
        list($domain_hash,$timestamp, $session_number, $campaign_numer, $campaign_data) = preg_split('/[\.]/', $_COOKIE["__utmz"], 5);

        // Parse the campaign data
        $campaign_data = parse_str(strtr($campaign_data, "|", "&"));

        if(strpos(trim($utmcsr), '(') === false && strpos(trim($utmcsr), ')') === false)
            $this->utm_source = urldecode(trim($utmcsr));
        if(strpos(trim($utmcmd), '(') === false && strpos(trim($utmcmd), ')') === false)
            $this->utm_medium = urldecode(trim($utmcmd));
        if(strpos(trim($utmccn), '(') === false && strpos(trim($utmccn), ')') === false)
            $this->campaign_name = urldecode(trim($utmccn));
        if (isset($utmcct) && strpos(trim($utmcct), '(') === false && strpos(trim($utmcct), ')') === false)
            $this->utm_content = urldecode(trim($utmcct));
        if (isset($utmctr) && strpos(trim($utmctr), '(') === false && strpos(trim($utmctr), ')') === false)
            $this->campaign_term = urldecode(trim($utmctr));

        // You should tag you campaigns manually to have a full view
        // of your adwords campaigns data.
        // The same happens with Urchin, tag manually to have your campaign data parsed properly.

        if (isset($utmgclid)) {
            $this->utm_source = "google";
            $this->campaign_name = "";
            $this->utm_medium = "cpc";
            $this->utm_content = "";
            $this->campaign_term = $utmctr;
        }

        // Parse the __utma Cookie
        list($domain_hash,$random_id,$time_initial_visit,$time_beginning_previous_visit,$time_beginning_current_visit,$session_counter) = preg_split('/[\.]/', $_COOKIE["__utma"]);

        $this->first_visit = date("d M Y - H:i",$time_initial_visit);
        $this->previous_visit = date("d M Y - H:i",$time_beginning_previous_visit);
        $this->current_visit_started = date("d M Y - H:i",$time_beginning_current_visit);
        $this->times_visited = $session_counter;

        // Parse the __utmb Cookie
        if(isset($_COOKIE["__utmb"]))
        {
            list($domain_hash,$pages_viewed,$garbage,$time_beginning_current_session) = preg_split('/[\.]/', $_COOKIE["__utmb"]);
            $this->pages_viewed = $pages_viewed;
        }
    }
}