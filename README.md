# Content Lock for WordPress

Show or hide some or all of your content by wrapping it in convenient shortcodes!

## Examples

Below are some examples of the features in content lock!

### Logged In
    [logged_in][/logged_in]
Shows content only if user is logged in. 

### Logged Out
    [logged_out][/logged_out]
Shows content only if user is logged out. 

### Referer
    [came_from="http://www.google.com/"][/came_from]
Shows content if user came from a certain URL

### User Roles
    [user_is="administrator"][/user_is]
Shows content if user is a certain user role

### Non User Roles

    [user_is_not="administrator"][/user_is_not]

Accepted Arguments:
 * administrator
 * author
 * contributor
 * editor
 * subscriber

### User Capabilities
    [user_can="edit_posts"][/user_can]
Shows content if a user can perform a certain capability

### Non User Capabilitie
    [user_cannot="edit_posts"][/user_cannot]
Shows content if a user cannot perform a certain capability

For full list see: http://www.wordpress.org/Roles_And_Capabilities

### URL Key
    [has_key="mysecretkey"][/has_key]
Shows content if the url parameter "lock_key" is set and matches the accepted argument

Example: http://www.example.com/?lock_key=mysecretkey

### Password Protection
    [password="mypassword"][/password]
Shows content if you put in a correct password from the form on the page.

__Why use this over the standard password for WordPress?__

You would use this in cases where you would want only certain parts of a page protected, but not necessarily the whole content itself, which wordpress does.  Great for teasers, banners, calls to action, etc. 

### Show/Hide Content with Links
    [click][/click]
Shows content if a user clicks on a link on the page.  Content also hides if link is clicked again.  Requires jQuery.
