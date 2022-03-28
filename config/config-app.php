<?php
/**
 * Tento soubor slouží ke konfiguraci aplikace
 */



/**
 * Obecná konfigurace
 */
// default path (root) "/"
// Pokud je aplikace jinde než ve root adresáři tak je třeba upravit na relativní cestu
// př: "/slozka/slozka/"
define("ROUTE_PATH","/");

//URL s aplikací Podle configu serveru, normálně jen doména př: "https://sluzby.uzlabina.cz"
define("REDIRECT_PATH","https://sluzby.uzlabina.cz/bakateam/");

//ssl certifikáty
// cesta | null
define("CERT_PATH", '/etc/ssl/uzlabina.cz.pem');

/**
 * Microsoft
 */

//Název kategorie pod kterou se bude ukládat rozvrh
define("CATEGORY","Rozvrh Bakaláři");

//Redirect login
define("REDIRECT_URL", REDIRECT_PATH."microsoft");
//Redirect Logout
define("REDIRECT_LOGOUT_URL", REDIRECT_PATH);
// Client id pro microsoft
define("CLIENT_ID", "b152f06e-1464-4ee0-b177-235765ce1e0b");
// Client secret pro microsoft
define("CLIENT_SECRET", "JHF7Q~uPpCdElH9Pcd~P1gSspasp6PzeJM-SM");
// Práva pro Microsoft
define("SCOPE", "https://graph.microsoft.com/User.Read%20Calendars.ReadWrite");


/**
 * MC GRAPH API ENDPOINTS
*/

//pro autorizaci
define("MC_AUTH_URL","https://login.microsoftonline.com/common/oauth2/v2.0/authorize");
//pro získání tokenu
define("ACCESS_TOKEN_URL", "https://login.microsoftonline.com/common/oauth2/v2.0/token");
//pro odhlášení
define("LOGOUT_URL", "https://login.microsoftonline.com/{0}/oauth2/logout?post_logout_redirect_uri=".REDIRECT_LOGOUT_URL);
//define("LOGOUT_URL", "https://login.microsoftonline.com/common/oauth2/v2.0/logout");
// vytvoření kategorie v kalendáři
define("CATEGORY_CREATE_URL","https://graph.microsoft.com/v1.0/me/outlook/masterCategories");
// zobrazení existujících kategorií
define("CATEGORY_LIST_URL","https://graph.microsoft.com/v1.0/me/outlook/masterCategories");
//Přidání eventu (hodiny) do kalendáře
define("EVENT_ADD_DEFAULT_CAL","https://graph.microsoft.com/v1.0/me/calendar/events");
// Úprava eventu (hodiny) podle id
//define("EVENT_ADD_SPECIFIC_CAL","https://graph.microsoft.com/v1.0/me/calendars/'{id}'/events");

/**
 * Bakaláři
 */

define("BAKALARI_URL","https://bakalari.uzlabina.cz");


