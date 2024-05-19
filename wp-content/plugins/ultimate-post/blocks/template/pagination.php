<?php
defined( 'ABSPATH' ) || exit;

$page_post_id = (isset($attr['currentPostId']) &&  $attr['currentPostId'])  ? sanitize_html_class($attr['currentPostId']) : ultimate_post()->get_page_post_id(ultimate_post()->get_ID(), $attr['blockId']);
$exclude_id = isset($curr_post_id) ? $curr_post_id  : "";
$self_post_id = 'data-selfpostid="' .( (isset($attr['currentPostId']) &&  $attr['currentPostId']) ? "yes" : "no"). '"';
$wraper_after .= '<div class="ultp-pagination-wrap'.($attr["paginationAjax"] ? " ultp-pagination-ajax-action" : "").'" data-paged="1" data-expost="'.$exclude_id.'"  data-blockid="'.sanitize_html_class($attr['blockId']).'" data-postid="'.$page_post_id.'" data-pages="'.$pageNum.'" data-blockname="ultimate-post_'.$block_name.'" '.ultimate_post()->get_builder_attr($attr['queryType']).$self_post_id.'>';
    $wraper_after .= ultimate_post()->pagination($pageNum, $attr['paginationNav'], $attr['paginationText'], $attr["paginationAjax"]);
$wraper_after .= '</div>';