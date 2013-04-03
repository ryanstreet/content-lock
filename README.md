Content Lock for WordPress
============

Show or hide some or all of your content by wrapping it in convenient shortcodes!

##Examples

###Logged In
    [logged_in][/logged_in]
Shows content only if user is logged in. 

###Logged Out
    [logged_out][/logged_out]
Shows content only if user is logged out. 

###Referer
    [came_from="http://www.google.com/"][/came_from]
Shows content if user came from a certain URL

###User Roles
    [user_is="administrator"][/user_is]
or

    [user_is role="administrator"][/user_is]
Shows content if user is a certain user role

_Non User Roles_

    [user_is_not="administrator"][/user_is_not]
or

    [user_is_not role="administrator"][/user_is_not]

Accepted Arguments:
 * administrator
 * author
 * contributor
 * editor
 * subscriber

