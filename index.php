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


$countries = [
    'None',
    'Afganistan',
    'Bangladesh',
    'India',
    'Pakistan'
];

function wordcount_init(){

}
add_action('init', 'wordcount_init');

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


function wordcount_qrcode_generator($content){
    $current_post_id = get_the_ID();
    $title = get_the_title($current_post_id);
    $post_url = urlencode(get_the_permalink($current_post_id));
    $height = get_option('wordcount_height')?get_option('wordcount_height'):150;
    $width = get_option('wordcount_width')?get_option('wordcount_width'):150;
    $dimension = apply_filters('qrcode_dimention',"{$height}*{$width}");


    $img_src = sprintf("https://api.qrserver.com/v1/create-qr-code/?size=%s&data=%s",$dimension, $post_url);
    $content .=sprintf("<div class='qrcode'><img src='%s' alt='%s' width='%s' height='%s' /></div>",$img_src, $title, $height, $width);
    // $height = get_post_meta($current_post_id, 'qrcode_height', true);
    return $content;

}
add_filter('the_content', 'wordcount_qrcode_generator');

function wordcount_admin_settings(){
    add_settings_section('qrcode_section', __('Post to QR Code', 'wordcount'), 'qrcode_callback','general');
    add_settings_field('wordcount_height', __('QR code height', 'wordcount'), 'wordcount_qrcode_field','general','qrcode_section',['wordcount_height']);
    add_settings_field('wordcount_width', __('QR code width', 'wordcount'), 'wordcount_qrcode_field','general','qrcode_section',['wordcount_width']);
    add_settings_field('wordcount_dropdown', __('Dropdown', 'wordcount'), 'wordcount_dropdown','general','qrcode_section');
    add_settings_field('wordcount_checkbox', __('Country Select', 'wordcount'), 'wordcount_checkbox','general','qrcode_section');
    add_settings_field('wordcount_toggle', __('Switch Toggle', 'wordcount'), 'wordcount_toggle','general','qrcode_section');

    register_setting('general', 'wordcount_height', ['sanitize_callback'=>'esc_attr']);
    register_setting('general', 'wordcount_width', ['sanitize_callback'=>'esc_attr']);
    register_setting('general', 'wordcount_dropdown', ['sanitize_callback'=>'esc_attr']);
    register_setting('general', 'wordcount_checkbox');
    register_setting('general', 'wordcount_toggle');
}

function wordcount_toggle(){
    printf("<input type='checkbox' checked data-toggle='toggle'>");
}


function wordcount_checkbox(){
    global $countries;
    $optionValue = get_option('wordcount_checkbox')?get_option('wordcount_checkbox'):[];
    
    foreach($countries as $country){
        $selected = '';

        if(in_array($country, $optionValue)){
            $selected = 'checked';
        }

        printf(" <input type='checkbox' name='wordcount_checkbox[]'  id='%s' value='%s' %s> %s", 'wordcount_checkbox', $country ,$selected, $country);
        
    }
    
}


function wordcount_dropdown(){
    global $countries;
    $optionValue = get_option('wordcount_dropdown');
    printf("<select id='%s' name='%s'>", 'wordcount_dropdown', 'wordcount_dropdown');
    foreach($countries as $country){
        $selected = '';

        if($country==$optionValue){
            $selected = 'selected';
        }
        printf("<option value='%s' %s>%s</option>", $country, $selected , $country);
    }
    echo "</select>";
}


function wordcount_qrcode_field($args){

    $option_value = get_option($args[0]);
    printf("<input type='text' name='%s' id='%s' value='%s'>",$args[0],$args[0], $option_value);
}

// function wordcount_qrcode_height(){
//     $height = get_option('wordcount_height');
//     printf("<input type='text' name='%s' id='%s' value='%s'>",'wordcount_height','wordcount_height', $height);
// }

// function wordcount_qrcode_width(){
//     $width = get_option('wordcount_height');
//     printf("<input type='text' name='%s' id='%s' value='%s'>",'wordcount_width','wordcount_width', $width);
// }

function qrcode_callback(){
    echo "<p>".__('Please Add some description here', 'wordcount')."</p>";
}

add_action('admin_init', 'wordcount_admin_settings');


function wordcount_assets($screen){
    
    if('options-general.php'==$screen){
        wp_enqueue_style('wordcount-bootstrap-toggle-css', plugin_dir_url(__FILE__).'/assets/css/bootstrap-toggle.min.css' );
        wp_enqueue_script('wordcount-bootstrap-toggle-js', plugin_dir_url(__FILE__).'/assets/js/bootstrap-toggle.min.js', null,time(), true );
        wp_enqueue_script('wordcount-main', plugin_dir_url(__FILE__).'/assets/js/wordcount-main.js', ['jquery'],time(), true );
        
    }
    

   
}
add_action('admin_enqueue_scripts', 'wordcount_assets');

?>
