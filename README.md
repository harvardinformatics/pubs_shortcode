# pubs_shortcode
Shortcode Wordpress plugin for the FAS pubs API.

Install by checking out this source code into the theme directory, then including it in the functions.php file.
The restclient.php file requires curl and it's PHP partners:

    apt-get install curl php-curl

Place a short code like this in a Wordpress page / post:
    
    [show_rc_pubs token = "restframeworktoken" url = "https://ifx.rc.fas.harvard.edu/pubs/"]
    
where the token is the Pubs application REST framework token and the URL is the base URL of the API.  

References are returned as a set of ordered lists, grouped by year, in descending order.


