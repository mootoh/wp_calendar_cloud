<?php
/*
  Plugin Name: Calendar Cloud Plugin
  Plugin URI: http://blog.deadbeaf.org/wp_calendar_cloud_plugin/
  Description: A Calendar visualization like TagCloud.
  Version: 0.2
  Author: Motohiro Takayama
  Author URI: http://blog.deadbeaf.org/


  Copyright 2007  Motohiro Takayama  (mootoh@gmail.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function calendar_cloud() {
	global $wpdb, $wp_locale;
	$smallest = 8;
	$largest = 22;
	$unit = 'pt';
	$number = 45;

	$after = '';
	$show_post_count = false;

	$arcresults = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, count(ID) as posts FROM $wpdb->posts $join $where GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date DESC" . $limit);
	if ( $arcresults ) {
		$afterafter = $after;
		foreach ( $arcresults as $arcresult ) {
			$url  = get_month_link($arcresult->year,  $arcresult->month);
			$text = sprintf(__('%1$s %2$d'), $wp_locale->get_month($arcresult->month), $arcresult->year);
		}
	}

	$ret = $counts = array();

	foreach ( $arcresults as $arcresult ) {
		$counts[$arcresult->year.'-'.$arcresult->month] = $arcresult->posts;
		if (!$ret[$arcresult->year]) {
			$ret[$arcresult->year] = array();
		}
		$ret[$arcresult->year][$arcresult->month] = $arcresult->posts;
	}

	$min_count = min($counts);
	$spread = max($counts) - $min_count;
	if ( $spread <= 0 )
		$spread = 1;
	$font_spread = $largest - $smallest;
	if ( $font_spread <= 0 )
		$font_spread = 1;
	$font_step = $font_spread / $spread;

	print ('<div class="calendar-cloud">');
	foreach ($ret as $year => $val) {
		if (0 == $year) continue;

		print ('<div class="year">'.$year.'</div>');
		print ('<div class="month">');
		for ($i=1; $i<=12; $i++) {
			$v = $val[$i];
			if ($v) {
				$url  = get_month_link($year, $i);
				$size = ($smallest + ( ( $v - $min_count ) * $font_step )).'pt';
				print('<span><a title="'.$v.'entries" href="'.$url.'" style="font-size:'.$size.';">'.$i.'</a></span>');
			} else {
				print ('<span class="absent">'.$i.'</span>');
			}
			if ($i % 4 == 0) {
				print("<br/>");
			}
		}
		/*
			 foreach ($val as $month) {
			 print($month[0].':'.$month[1].', ');
			 }
		 */
		print ('</div>');
	}
	//print(count($ret));
	print('</div>');
}
?>
