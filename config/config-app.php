<?php
// default path /
// Pokud je aplikace jinde než ve root adresáři tak je třeba upravit na relativní cestu
// př: "/slozka/slozka/"
define("APP_PATH","/bakateam/bakateam/");


//define("REDIRECT_URL", "https://sluzby.uzlabina.cz".APP_PATH."microsoft");
define("REDIRECT_URL", "http://localhost".APP_PATH."microsoft");
define("MC_AUTH_URL","https://login.microsoftonline.com/common/oauth2/v2.0/authorize");
define("LOGOUT_URL", "https://login.microsoftonline.com/common/oauth2/v2.0/logout");
define("ACCESS_TOKEN_URL", "https://login.microsoftonline.com/common/oauth2/v2.0/token");
define("CLIENT_ID", "b152f06e-1464-4ee0-b177-235765ce1e0b");
define("CLIENT_SECRET", "JHF7Q~uPpCdElH9Pcd~P1gSspasp6PzeJM-SM");
define("SCOPE", "https://graph.microsoft.com/User.Read%20Calendars.ReadWrite%20MailboxSettings.ReadWrite");

define("CATEGORY_CREATE_URL","https://graph.microsoft.com/v1.0/me/outlook/masterCategories");
define("CATEGORY_LIST_URL","https://graph.microsoft.com/v1.0/me/outlook/masterCategories");
define("EVENT_ADD_DEFAULT_CAL","https://graph.microsoft.com/v1.0/me/calendar/events");
//define("EVENT_ADD_SPECIFIC_CAL","https://graph.microsoft.com/v1.0/me/calendars/".$_SESSION['calendarID']."/events");

define("CERT_PATH", '/etc/ssl/uzlabina.cz.pem');

define("CATEGORY","Rozvrh Bakaláře");