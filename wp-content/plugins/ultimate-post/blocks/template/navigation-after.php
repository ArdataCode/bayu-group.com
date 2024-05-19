<?php
defined( 'ABSPATH' ) || exit;

$attr["blockId"] = sanitize_html_class($attr["blockId"]);
$page_post_id = (isset($attr['currentPostId']) &&  $attr['currentPostId'])  ? sanitize_html_class($attr['currentPostId']) : ultimate_post()->get_page_post_id(ultimate_post()->get_ID(), $attr['blockId']);
$exclude_id = isset($curr_post_id) ? $curr_post_id  : "";
$self_post_id = 'data-selfpostid="' .( (isset($attr['currentPostId']) &&  $attr['currentPostId']) ? "yes" : "no"). '"';
$wraper_after .= '<div class="ultp-next-prev-wrap" data-pages="'.$pageNum.'" data-pagenum="1" data-blockid="'.$attr['blockId'].'" data-blockname="ultimate-post_'.$block_name.'" data-expost="'.$exclude_id.'" data-postid="'.$page_post_id.'" '.ultimate_post()->get_builder_attr($attr['queryType']).$self_post_id.'>';
    if ( 1 != $pageNum ) {
        $wraper_after .= ultimate_post()->next_prev();
    }
$wraper_after .= '</div>';