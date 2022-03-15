<?php 
/*
Plugin Name: Word Count
Plugin URI: https://wordpress.org/themes/twentytwenty/
Author: Talha Ekhlas
Author URI: https://wordpress.org/
Description: education, learning
Version: 1.0.0
Requires PHP: Janina
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wordcount
This theme, like WordPress, is licensed under the GPL.
Use it to make something cool, have fun, and share what you've learned with others.
*/


// function wordcount_register_activation_hook(){}

// register_activation_hook(__FILE__,'wordcount_register_activation_hook');

// function wordcount_register_deactivation_hook(){}

// register_deactivation_hook(__FILE__,'wordcount_register_deactivation_hook');

function wordcount_load_textdomain(){
    load_plugin_textdomain('wordcount', false, dirname(__FILE__).'/languages');
}

add_action('plugins_loaded', 'wordcount_load_textdomain');

function wordcount_count_words($content){
    $striped_content = strip_tags($content);
    $number_of_words = str_word_count($striped_content);
    $title = __('The number of words','wordcount');
    $title = apply_filters('wordcount_heading',$title);
    $tag = apply_filters('wordcount_tag','h2');
    $content .= sprintf("<%s>%s: %s</%s>", $tag, $title, $number_of_words, $tag);
    return $content;
    

}
add_filter('the_content', 'wordcount_count_words');

?>
