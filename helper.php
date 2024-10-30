<?php

define('IR_REQUEST_URI', 'http://www.indiereign.com/ir-wp/player/embed');

// register IRPlayer widget
function register_irplayer_widget() {
    register_widget( 'IRPlayer' );
}

// register shortcode for player
function irplayer_shortcode_handler($attr, $content=null) {
    extract( shortcode_atts( array(
        'vslug'  => '',
        'width'  => '990',
        'height' => '560'
    ), $attr ) );

    echo get_player_iframe(load_ir_player($vslug), $width, $height);
}

// request on server to get player url 
function load_ir_player($code) {
    $ch     = curl_init();

    $params = array(
      'code' => $code
    );

    $opts  = array(
      CURLOPT_CONNECTTIMEOUT => 10,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT        => 60,
    );
    
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params, null, '&'));
    curl_setopt($ch, CURLOPT_URL, IR_REQUEST_URI);
    curl_setopt_array($ch, $opts);

    $result = curl_exec($ch);

    curl_close($ch);

    return $result;
}

// generate iframe tag for player
function get_player_iframe($ir_player, $width, $height)
{
    return '<iframe src="'.$ir_player.'" width="'.$width.'" height="'.$height.'"'.
           ' scrolling="no" frameborder="0" mozallowfullscreen="true" '.
           'webkitallowfullscreen="true" allowfullscreen="true"></iframe>';
}

?>
