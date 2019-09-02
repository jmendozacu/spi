<?php
// enable, adjust and copy this code for each store you run
// Store #0, default one
//if (isHttpHost("example.com")) {
//    $_SERVER["MAGE_RUN_CODE"] = "default";
//    $_SERVER["MAGE_RUN_TYPE"] = "store";
//}

<?php
function isHttpHost($host)
{
    if (!isset($_SERVER['HTTP_HOST'])) {
    return false;
    }
    return $_SERVER['HTTP_HOST'] ===  $host;
}

if (isHttpHost("heartsoulscrubs.lexim.net")) {
    $_SERVER["MAGE_RUN_CODE"] = "hts";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}elseif (isHttpHost("infinityscrubsv2.lexim.net")){
    $_SERVER["MAGE_RUN_CODE"] = "base";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}
