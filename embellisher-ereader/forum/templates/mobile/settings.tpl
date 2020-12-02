{! --- defines are used by the engine and vars are used by the template --- }

{! --- How many px to indent for each level --- }
{DEFINE indentmultiplier 20}

{! --- This is used to load the message-bodies in the message-list for that template if set to 1 --- }
{DEFINE bodies_in_list 0}

{! --- This is used the number of page numbers shown on the list page in the paging section (eg. 1 2 3 4 5) --- }
{DEFINE list_pages_shown 3}

{! --- This is used the number of page numbers shown on the search page in the paging section (eg. 1 2 3 4 5) --- }
{DEFINE search_pages_shown 3}

{! --- Define on what page notifications should be displayed ---- }
{DEFINE show_notify_for_pages "index,list,cc"}

{! -- This is the image for the gauge bar to show how full the PM box is -- }
{VAR gauge_image "templates/emerald/images/gauge.gif"}

{! --- Apply some compression to the template data. This feature is      --- }
{! --- implemented by Phorum's template parsing code. Possible values    --- }
{! --- for this setting are:                                             --- }
{! --- 0 - Apply no compression at all.                                  --- }
{! --- 1 - Remove white space at start of lines and empty lines.         --- }
{! --- 2 - Additionally, remove some extra unneeded white space and HTML --- }
{!         comments. Note that this makes the output quite unreadable,   --- }
{!         so it is mainly useful for a production environment.          --- }
{DEFINE tidy_template 0}

{VAR template_dir "mobile"}
