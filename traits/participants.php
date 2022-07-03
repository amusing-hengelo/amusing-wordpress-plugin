<?php


trait WP_Amusing_Hengelo_Participants {


	private function participants($settings, $planning) {
		$table = '
<table class="amusing-participants">
    <tbody>';
		$groups = [];
		foreach ($planning as $performance) {
			if (!array_key_exists($performance->group, $groups))
				$groups[$performance->group] = [
					'id' => $performance->{'group-id'},
					'name' => $performance->group,
					'genre' => $performance->genre,
					'city' => $performance->city,
				];
		}
		ksort($groups);
		foreach ($groups as $id=>$group) {
			$table .= '
		<tr>
			<td><b><a href="'.preg_replace('/\[id\]/', $group['id'], $settings['groupurl']).'">'.htmlentities($group['name']).'</a></td>
			<td>'.htmlentities($group['genre']).'</td>
			<td>'.htmlentities($group['city']).'</td>
		</tr>';
			}
		$table .= '
    </tbody>
</table>';
		return $table;
	}


	private function table_participants($content, $year = 'next') {
        $year = is_numeric($year) || $year=='previous' ? $year : 'next';
        $festival = $this->get('festival/'.$year);
        $planning = $this->get('festival/planning/'.$year);
        $settings = stripslashes_deep(get_option('amusing-settings'));
        $festival_year = date('Y', strtotime($festival->date));
		$table = $this->participants($settings, $planning);
		$content = preg_replace('/\[participants\]/', $table, $content);
		$content = preg_replace('/\[year\]/', $festival_year, $content);
		return $content;
	}

}
