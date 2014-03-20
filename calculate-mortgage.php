<?php
/*
Plugin Name:    Calculate mortgage
Description:    The plugin allows you to calculate the amortization schedule of a loan or a mortgage with the French method
Plugin URI:     http:///www.kiwiadv.net
Author:         Kiwi adv
Author URI:     http://www.kiwiadv.net
Version:        1.0
License:        GPL2
Text Domain:    kiwiadv
Domain Path:    Domain Path

Copyright (C) 2014  Kiwi adv  info@kiwiadv.net

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//internationalization

function kiwiadv_calculate_mortgage_init() {
  load_plugin_textdomain( 'kiwiadv', false, dirname( plugin_basename( __FILE__ ) ) );
}
add_action('plugins_loaded', 'kiwiadv_calculate_mortgage_init');

// start function to calculate the repayment of the loan "kiwiadv_calculate_mortgage"

function kiwiadv_calculate_mortgage( $balance, $rate, $term, $period ) {

    if( isset( $balance ) && isset( $rate ) && isset( $term ) && isset( $period )) {

        $I = '';
        $v = '';
        $t = '';

        if( $period ) {

            $I = (($rate/100)/$period);

            $N = $term * $period;

            $v = pow((1+$I),$N);
        }

        if( $I != 0 && $v != 0 ) {
            $t = ($I*$v)/($v-1);
        }

        $result = $balance*$t;

        return $result;
    }

}

// end function to calculate the repayment of the loan "kiwiadv_calculate_mortgage"

// start shortcode function

function kiwiadv_calculate_mortgage_shortcode() {

	wp_enqueue_style('calculate-mortgage', WP_PLUGIN_URL . '/calculate-mortgage/css/calculate-mortgage.css', false, null);

    $errors = array();
	$all_fields = '';
	$only_numbers = '';

    $balance	= isset($_POST['balance']) ? $_POST['balance'] : '';
    $rate		= isset($_POST['rate']) ? $_POST['rate'] : '';
    $term		= isset($_POST['term']) ? $_POST['term'] : '';
    $period		= isset($_POST['period']) ?  $_POST['period'] : '';

    // calculate the repayment of the loan
    kiwiadv_calculate_mortgage($balance, $rate, $term, $period);

    if ((isset($_POST['submitBtn']) && ($balance == '') || ($rate == '') || ($term == ''))) {
				$all_fields = __('Error: To calculate the repayment of the loan you must complete all fields','kiwiadv');
    }

		if ((isset($_POST['submitBtn']) && (!preg_match("/^([0-9])*$/", $balance )) || (!preg_match("/^([0-9.,])*$/", $rate)) || (!preg_match("/^([0-9])*$/", $term )))) {
				$only_numbers = __('Error: Allowed only numbers','kiwiadv');
		}

   ?>
    <div class="request">
    <h3><?php _e('Calculate the repayment of your loan','kiwiadv').': ';?></h3>
      <form action="<?php echo get_permalink(); ?>" method="post">
        <table class="detail">
          <tr><td><?php _e('Amount requested','kiwiadv').': ';?></td><td><input class="text" name="balance" type="text" size="15" value="<?php echo $balance; ?>" /> &#8364;</td></tr>
          <tr><td><?php _e('Interest rate (fixed rate)','kiwiadv').': ';?></td><td> <input class="text" name="rate" type="text" size="15" value="<?php echo $rate; ?>" /> %</td></tr>
          <tr><td><?php _e('Duration','kiwiadv').': ';?></td><td> <input class="text" name="term" type="text" size="15" value="<?php echo $term; ?>" /> <?php _e('years','kiwiadv');?></td></tr>

          <tr><td><?php _e('Date','kiwiadv').': ';?></td><td>
            <select name="period">
              <option value="12" selected="<?php _e('monthly','kiwiadv');?>"><?php _e('monthly','kiwiadv');?></option>
              <option value="2"><?php _e('six-monthly','kiwiadv');?></option>
              </select>
            </tr>

          <tr><td colspan="2"><br/><input class="btn btn-primary pull-right" type="submit" name="submitBtn" value="<?php echo __('Calculate mortgage','kiwiadv');?>" /></td></tr>
          </table>
        </form>
    </div>
    <?php if ((isset($_POST['submitBtn']) && ($balance != '')) && ($rate != '') && ($term != '') && ($only_numbers == '')) {     ?>
        <div class="caption"><h3><?php _e('Repayment plan','kiwiadv').': ';?></h3></div>
        <div class="result">
        <div>
            <?php
            $str = str_replace(',','.', $rate);
            $float_val = floatval(str_replace(',','.', $str));
            $rate = $str;

            $pay = round(kiwiadv_calculate_mortgage($balance,$rate,$term,$period),4);

            echo __('Rate','kiwiadv').": ".$rate." %"."<br>";
            echo __('Amount of tranche','kiwiadv').": ".number_format(($pay),2 , ',', '.')." &#8364;"."<br>";
            echo __('Total interest','kiwiadv').": ".number_format((($term*$pay*$period)-$balance),2 , ',', '.')." &#8364;"."<br>";
            echo __('Total loan','kiwiadv').": ".number_format(($term*$pay*$period),2 , ',', '.')." &#8364;"."<br>";
            ?>
        </div>
        <?php
        echo "<br/><br/>";
        echo "<table class='detail'>";
        echo "<tr><td><h4>".__('Tranche','kiwiadv'); echo ": </h4></td><td><h4>".__('Portion capital','kiwiadv'); echo ": </h4></td><td><h4>".__('Portion interest','kiwiadv'); echo ": </h4></td><td><h4>".__('Amount of tranche','kiwiadv'); echo ": </h4></td><td><h4>".__('Residual capital','kiwiadv'); echo ": </h4></td></tr>";

        $balance = round($balance,4);
        $cap_res = $balance;
        for ($i=0;$i<($term*$period);$i++)
        {
            $int  = round($cap_res*($rate/100/$period),4);
            $diff = $pay-$int;
            $diff = round($diff,4);
            $quota_capitale = round(($pay-$int),4);
            $cap_res = round(($cap_res-$quota_capitale),4);
            $stamp = $i+1;
            echo "<tr><td>$stamp</td><td>   ".number_format($quota_capitale,2 , ',', '.')." &#8364;</td><td>".number_format($int,2 , ',', '.')." &#8364;</td><td>   ".number_format($pay,2 , ',', '.')." &#8364;</td><td>   ".number_format($cap_res,2 , ',', '.')." &#8364;</td></tr>";
        }
        echo "</table>";
    } elseif ((isset($_POST['submitBtn']) && ($all_fields != '') || ($only_numbers != ''))) {

				//$html = implode('', $errors);
				echo 	'<div class="danger">';
				echo	'<ul>';
				if ($all_fields != ''){
						echo	'<li>'.$all_fields.'</li>';
				}
				if	($only_numbers != ''){
						echo	'<li>'.$only_numbers.'</li>';
				}
				echo	'</ul>';
				echo	'<div>';
		}
}

add_shortcode('calculate', 'kiwiadv_calculate_mortgage_shortcode');

// end shortcode function

// TINYMCE SHORTCODE BUTTONS

// initialize the button

add_action('init', 'calculate_button');

// make function for button

function calculate_button() {

    if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {

        return;

        }

    if ( get_user_option('rich_editing') == 'true' ) {

        add_filter( 'mce_external_plugins', 'add_plugin' );

        add_filter( 'mce_buttons_2', 'register_button' );

        }

}

// register button

function register_button( $buttons ) {

    array_push( $buttons, "|", "calculate" );

    return $buttons;

}

// register plugin for TinyMCE (name of WordPress editor)

    function add_plugin( $plugin_array ) {

    $plugin_array['calculate'] = plugin_dir_url(__FILE__) . 'calc-button.js';

    return $plugin_array;

}