<div class="wrap">
	<h1><?php printf(__('Add/Edit Section in "%s"', 'chained'), $quiz->title)?></h1>

	<div class="postbox-container" style="width:73%;margin-right:2%;">

		<p><a href="admin.php?page=chained_quizzes"><?php _e('Back to quizzes', 'chained')?></a> | <a href="admin.php?page=chainedquiz_sections&quiz_id=<?php echo $quiz->id?>"><?php _e('Back to sections', 'chained')?></a>
			| <a href="admin.php?page=chainedquiz_results&quiz_id=<?php echo $quiz->id?>"><?php _e('Manage Results', 'chained')?></a>
			| <a href="admin.php?page=chained_quizzes&action=edit&id=<?php echo $quiz->id?>"><?php _e('Edit This Quiz', 'chained')?></a>
		</p>

		<form method="post" onsubmit="return chainedSectionValidate(this);">
			<p><label><?php _e('Section title', 'chained')?></label>
				<input type="text" name="title" size="40" value="<?php echo @$section->title?>"> <i><?php _e('to divide the quiz in sections', 'chained');?></i></p>
			<p><label><?php _e('Section description', 'chained')?></label>
				<?php echo wp_editor(stripslashes(@$section->description), 'description')?>
			</p>

			<p><input type="submit" value="<?php _e('Save section','chained')?>" class="button-primary"></p>
			<input type="hidden" name="ok" value="1">
			<input type="hidden" name="quiz_id" value="<?php echo $quiz->id?>">
			<?php wp_nonce_field('chained_question');?>
		</form>
	</div>

	<div id="chained-sidebar">
			<?php include(CHAINED_PATH."/views/sidebar.html.php");?>
	</div>
</div>

<script type="text/javascript" >


function chainedQuizValidate(frm) {
	if(frm.title.value == '') {
		alert("<?php _e('Please enter section title', 'chained')?>");
		frm.title.focus();
		return false;
	}

	return true;
}
</script>
