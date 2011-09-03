<?php
function isBot(){
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    //if no user agent is supplied then assume it's a bot
    if($userAgent == "")
        return 1;
    
    //array of bot strings to check for
    $bots = Array(
        "google",     "bot",
        "yahoo",     "spider",
        "archiver",   "curl",
        "python",     "nambu",
        "twitt",     "perl",
        "sphere",     "PEAR",
        "java",     "wordpress",
        "radian",     "crawl",
        "yandex",     "eventbox",
        "monitor",   "mechanize",
        "facebookexternal"
    );
    foreach($bots as $bot){
        if(strpos($userAgent,$bot) !== false) { return true; }
    }
    
    return false;
}

?>