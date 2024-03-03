<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ChainedQuizResult {
	function add($vars) {
		global $wpdb;

		$vars['title'] = sanitize_text_field($vars['title']);
		$vars['redirect_url'] = esc_url_raw($vars['redirect_url']);
		if(!current_user_can('unfiltered_html')) {
			$vars['description'] = strip_tags($vars['description']);
		}

		$result = $wpdb->query($wpdb->prepare("INSERT INTO ".CHAINED_RESULTS." SET
			quiz_id=%d, section_id=%d, points_bottom=%f, points_top=%f, title=%s, description=%s, redirect_url=%s",
			$vars['quiz_id'], $vars['section_id'], $vars['points_bottom'], $vars['points_top'], $vars['title'],
			$vars['description'], $vars['redirect_url']));

		if($result === false) throw new Exception(__('DB Error', 'chained'));
		return $wpdb->insert_id;
	} // end add

	function save($vars, $id) {
		global $wpdb;

      $id = intval($id);

		$vars['title'] = sanitize_text_field($vars['title']);
		$vars['redirect_url'] = esc_url_raw($vars['redirect_url']);
		if(!current_user_can('unfiltered_html')) {
			$vars['description'] = strip_tags($vars['description']);
		}

		$result = $wpdb->query($wpdb->prepare("UPDATE ".CHAINED_RESULTS." SET
		 section_id=%d, points_bottom=%f, points_top=%f, title=%s, description=%s, redirect_url=%s WHERE id=%d",
		$vars['section_id'], $vars['points_bottom'], $vars['points_top'], $vars['title'], $vars['description'],
		$vars['redirect_url'], $id));

		if($result === false) throw new Exception(__('DB Error', 'chained'));
		return true;
	}

	function delete($id) {
		global $wpdb;
		$id = intval($id);

		// delete result
		$result =$wpdb->query($wpdb->prepare("DELETE FROM ".CHAINED_RESULTS." WHERE id=%d", $id));

		if($result === false) throw new Exception(__('DB Error', 'chained'));
		return true;
	}

	// calculate result based on points collected
	function calculate($quiz, $points) {
		global $wpdb;

		// select all results order by best
		$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".CHAINED_RESULTS."
			WHERE quiz_id = %d ORDER BY points_bottom DESC", $quiz->id));
		foreach($results as $result) {
			if(floatval($result->points_bottom) <= $points and $points <= floatval($result->points_top)) return $result;
    }

    return null; // in case of nothing found
	}

	//calculate results per section
	function calculatepersection($quiz, $completion_id){
		global $wpdb;

		// selecting points per section from CHAINED_USER_ANSWERS
		$answerspersection = $wpdb->get_results($wpdb->prepare("SELECT section_id, SUM(points) as points FROM ".CHAINED_USER_ANSWERS." WHERE completion_id = %d GROUP BY section_id", $completion_id));

		$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".CHAINED_RESULTS."
			WHERE quiz_id = %d ORDER BY points_bottom DESC", $quiz->id));

		$rps_obj = array();
		//$resultspersection = array();
		foreach($answerspersection as $aps) {
			$thissection_id = $aps->section_id;
			foreach($results as $result) {
				if (($result->section_id = $thissection_id) and floatval($result->points_bottom) <= $aps->points and $aps->points <= floatval($result->points_top)){
					$thisresult = $result;
				}//endif
			}//end results foreach
			$section_title = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_SECTIONS." WHERE id = %d", intval($aps->section_id)));

			$rps_obj[] = (object)array(
				'section_id' 					=> 	intval($aps->section_id),
				'section_title' 			=> 	$section_title->title,
				'total_points'				=> 	intval($aps->points),
				'result_title'				=> 	$thisresult->title,
				'result_description' 	=> 	$thisresult->description,
				'result_points_bottom'=>	$thisresult->points_bottom,
				'result_points_top' 	=>	$thisresult->points_top,
				'result'							=> 	$thisresult);

		}//end answerspersection foreach
		return $rps_obj;
	}
}
