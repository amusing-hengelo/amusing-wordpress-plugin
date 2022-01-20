<?php


trait WP_Amusing_Hengelo_Group {


	private function page_group($content) {
		$group = $this->get('group/'.$_REQUEST['group']);
		if ($group && !is_wp_error($group)) {
			$genres = $this->get('genre');
			$genres = array_filter($genres, function($genre) use ($group) {
				return $genre->id == $group->genre;
			});
			$genre =  count($genres)>0 ? array_values($genres)[0]->name : 'Onbekend';
			$content = preg_replace('/\[group\](.*)\[\/group\]/s', '\1', $content);
			$content = preg_replace('/\[no-group\].*\[\/no-group\]/s', '', $content);
			$content = preg_replace('/\[name\]/s', htmlentities($group->name), $content);
			$content = preg_replace('/\[city\]/s', htmlentities($group->city), $content);
			$content = preg_replace('/\[genre\]/s', htmlentities($genre), $content);
			$url = preg_match('/^http[s]?:\/\//', $group->website) ? $group->website : 'https://'.$group->website;
			$content = preg_replace('/\[website\]/s', '<a href="'.$url.'" target="_blank">'.$url.'</a>', $content);
			$photo = imagecreatefromstring(base64_decode($group->photo));
			ob_start();
			imagepng($photo);
			$photo = base64_encode(ob_get_contents());
			ob_end_clean();
			$content = preg_replace('/\[photo\]/s', '<img src="data:image/png;base64,'.$photo.'" alt="'.htmlentities($group->name).'" />', $content);
			$content = preg_replace('/\[description\]/s', '<p>'.$group->description.'</p>', $content);
		} else {
			$content = preg_replace('/\[no-group\](.*)\[\/no-group\]/s', '\1', $content);
			$content = preg_replace('/\[group\].*\[\/group\]/s', '', $content);
		}
		return $content;
	}

}
