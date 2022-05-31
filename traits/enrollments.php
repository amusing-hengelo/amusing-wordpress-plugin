<?php


trait WP_Amusing_Hengelo_Enrollments {


	private function table_enrollments($content, $year = 'next') {
		$year = is_numeric($year) ? $year : 'next';
		$enrollments = $this->get('festival/enrollments/'.$year);
		if ($enrollments && !is_wp_error($enrollments) && (count($enrollments)>0)) {
			$settings = stripslashes_deep(get_option('amusing-settings'));
			$table = '
<table class="amusing-enrollments">
	<thead>
		<tr>
			<th>Naam</th>
			<th>Genre</th>
			<th>Plaats</th>
		</tr>
	</thead>
	<tbody>';
			foreach ($enrollments as $enrollment)
				$table .= '
		<tr>
			<td><a href="'.preg_replace('/\[id\]/', $enrollment->id, $settings['groupurl']).'">'.htmlentities($enrollment->name).'</a></td>
			<td>'.htmlentities($enrollment->genre).'</a></td>
			<td>'.htmlentities($enrollment->city).'</td>
		</tr>';
			$table .= '
	</tbody>
</table>';
			$content = preg_replace('/\[non-zero\](.*)\[\/non-zero\]/s', '\1', $content);
			$content = preg_replace('/\[zero\].*\[\/zero\]/s', '', $content);
			$content = preg_replace('/\[count-enrollments\]/', count($enrollments), $content);
			$content = preg_replace('/\[enrollments\]/', $table, $content);
		} else {
			$content = preg_replace('/\[zero\](.*)\[\/zero\]/s', '\1', $content);
			$content = preg_replace('/\[non-zero\].*\[\/non-zero\]/s', '', $content);
		}
		return $content;
	}

}
