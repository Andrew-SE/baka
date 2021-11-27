<?php
define("REDIRECT_URL", "http://localhost/Bakateam/microsoft");
//define("REDIRECT_URL", "https://smooth-penguin-95.loca.lt/bak_0.1/index.php");
define("AUTH_URL", "https://login.microsoftonline.com/common/oauth2/v2.0/authorize");
define("LOGOUT_URL", "https://login.microsoftonline.com/common/oauth2/v2.0/logout");
define("ACCESS_TOKEN_URL", "https://login.microsoftonline.com/common/oauth2/v2.0/token");
define("CLIENT_ID", "b152f06e-1464-4ee0-b177-235765ce1e0b");
define("CLIENT_SECRET", "wBKa4XX1LexMRVClj.P~~EuIJ3E9o3U-ts");
define("SCOPE", "https://graph.microsoft.com/User.Read%20Calendars.ReadWrite%20MailboxSettings.ReadWrite");

define("CATEGORY_CREATE_URL","https://graph.microsoft.com/v1.0/me/outlook/masterCategories");
define("CATEGORY_LIST_URL","https://graph.microsoft.com/v1.0/me/outlook/masterCategories");
define("EVENT_ADD_DEFAULT_CAL","https://graph.microsoft.com/v1.0/me/calendar/events");
//define("EVENT_ADD_SPECIFIC_CAL","https://graph.microsoft.com/v1.0/me/calendars/".$_SESSION['calendarID']."/events");
