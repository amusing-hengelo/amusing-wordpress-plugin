<?php


trait WP_Amusing_Hengelo_Planning {


	private function table_planning($content, $year = 'next') {
		$year = is_numeric($year) ? $year : 'next';
		$planning = $this->get('festival/planning/'.$year);
		if ($planning && !is_wp_error($planning)) {
			$settings = stripslashes_deep(get_option('amusing-settings'));
			$type = array_key_exists('by', $_REQUEST) ? $_REQUEST['by'] : null;
			$table = '
<table class="amusing-planning">
	<tbody>';
			if ($type=='time') {
				$times = [];
				foreach ($planning as $performance) {
					if (!array_key_exists($performance->time, $times))
						$times[$performance->time] = [
							'podium' => $performance->podium,
							'performances' => []
						];
					$times[$performance->time]['performances'][$performance->{'podium-id'}] = $performance;
				}
				ksort($times);
				foreach ($times as $start=>$time) {
					$end = date('H:i', strtotime($start)+(30*60));
					$table .= '
		<tr><td colspan="4"><b>'.$start.' - '.$end.'</b></td></tr>';
					ksort($time['performances']);
					foreach ($time['performances'] as $id=>$performance)
						$table .= '
		<tr>
			<td>Podium '.$id.' - '.htmlentities($performance->podium).'</td>
			<td><a href="'.preg_replace('/\[id\]/', $performance->{'group-id'}, $settings['groupurl']).'">'.htmlentities($performance->group).'</a></td>
            <td>'.htmlentities($performance->genre).'</td>
			<td>'.htmlentities($performance->city).'</td>
		</tr>';
				}
			} elseif ($type=='group') {
				$groups = [];
				foreach ($planning as $performance) {
					if (!array_key_exists($performance->group, $groups))
						$groups[$performance->group] = [
							'id' => $performance->{'group-id'},
							'name' => $performance->group,
							'genre' => $performance->genre,
							'city' => $performance->city,
							'performances' => []
						];
					$groups[$performance->group]['performances'][$performance->time] = $performance;
				}
				ksort($groups);
				foreach ($groups as $id=>$group) {
					$table .= '
		<tr>
			<td><b><a href="'.preg_replace('/\[id\]/', $performance->{'group-id'}, @$settings['groupurl']).'">'.htmlentities($group['name']).'</a></td>
			<td>'.htmlentities($group['genre']).'</td>
			<td>'.htmlentities($group['city']).'</td>
		</tr>';
					ksort($group['performances']);
					foreach ($group['performances'] as $start=>$performance) {
						$end = date('H:i', strtotime($start)+(30*60));
						$table .= '
		<tr>
			<td colspan="3">'.$start.' - '.$end.'&nbsp;&nbsp;Podium '.$performance->{'podium-id'}.' - '.htmlentities($performance->podium).'</td>
		</tr>';
					}
				}
			} else {
				$podia = [];
				foreach ($planning as $performance) {
					if (!array_key_exists($performance->{'podium-id'}, $podia))
						$podia[$performance->{'podium-id'}] = [
							'name' => $performance->podium,
							'performances' => []
						];
					$podia[$performance->{'podium-id'}]['performances'][$performance->time] = $performance;
				}
				ksort($podia);
				foreach ($podia as $id=>$podium) {
					$table .= '
		<tr><td colspan="4"><b>Podium '.$id.' - '.htmlentities($podium['name']).'</b></td></tr>';
					ksort($podium['performances']);
					foreach ($podium['performances'] as $start=>$performance) {
						$end = date('H:i', strtotime($start)+(30*60));
						$table .= '
		<tr>
			<td>'.$start.' - '.$end.'</td>
			<td><a href="'.preg_replace('/\[id\]/', $performance->{'group-id'}, $settings['groupurl']).'">'.htmlentities($performance->group).'</a></td>
			<td>'.htmlentities($performance->genre).'</td>
			<td>'.htmlentities($performance->city).'</td>
		</tr>';
					}
				}
			}
				$table .= '
	</tbody>
</table>';
			$content = preg_replace('/\[ready\](.*)\[\/ready\]/s', '\1', $content);
			$content = preg_replace('/\[not-ready\].*\[\/not-ready\]/s', '', $content);
			$content = preg_replace('/\[planning\]/', $table, $content);
		} else {
			$content = preg_replace('/\[not-ready\](.*)\[\/not-ready\]/s', '\1', $content);
			$content = preg_replace('/\[ready\].*\[\/ready\]/s', '', $content);
		}
		return $content;
	}

}
