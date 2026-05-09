<?php 
class custom_walker_nav_menu extends Walker_Nav_Menu {
    function start_lvl(&$output, $depth = 0, $args = array()) {
        $output .= '<div class="nav-child"><ul class="nav-child-ul">';
    }
    function end_lvl(&$output, $depth = 0, $args = array()) {
        $output .= '</ul></div>';
    }
}