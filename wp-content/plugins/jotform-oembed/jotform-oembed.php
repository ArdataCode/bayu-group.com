<?php
/*
    Plugin Name: Jotform oEmbed
    Plugin URI: http://www.jotform.com/labs/wordpress
    Description: Adding a web form to your blog post is now very easy using Jotform’s oEmbed plugin. When you install it, WordPress will be aware of Jotform form URLs, and easily embed them to your blog posts.
    Version: 1.3.0
    Author: Jotform.com
    Author URI: http://www.jotform.com
    License: GNU General Public License v3
*/

class JotFormOEmbed {

    public function __construct() {
        wp_oembed_add_provider('#https?://(secure\.|www\.|form\.|app\.)?(my)?jotform(pro|eu|z)?\.(com|net|us|ca|me|co)(/form|/app)?/[0-9]*#i', 'https://www.jotform.com/oembed/', true);
    }

}

$jotformOEmbed = new JotFormOEmbed();

?>