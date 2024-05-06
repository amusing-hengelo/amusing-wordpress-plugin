<?php


/*
Plugin Name: Amusing Hengelo
Description: Amusing WordPress Plugin
Version: 0.4.1
Author: Robin Elfrink
Author URI: https://15augustus.nl
License: GPL2
*/


foreach (glob(dirname(__FILE__).DIRECTORY_SEPARATOR.'traits/*.php') as $filename)
	include_once $filename;


if (!class_exists('WP_Amusing_Hengelo')) {


	class WP_Amusing_Hengelo {


		use WP_Amusing_Hengelo_Settings;
		use WP_Amusing_Hengelo_Planning;
		use WP_Amusing_Hengelo_Participants;
		use WP_Amusing_Hengelo_Enrollments;
		use WP_Amusing_Hengelo_Group;


		private $db = null;
		private $language = 'nl';
		// Make sure all get()'s are really running only once per request.
		private $cache = [];


		// The logged-in user's information
		private $person = null;
		private $volunteer = null;
		private $volunteerplanning = null;


		public function __construct() {
			add_action('the_content', [$this, 'filter']);
			add_action('admin_menu', [$this, 'settings_menu']);
			add_action('admin_init', [$this, 'settings_init']);
			add_action('admin_enqueue_scripts', [$this, 'add_css']);
			if (preg_match('/^q=de\//', $_SERVER['QUERY_STRING']))
				$this->language = 'de';
			elseif (preg_match('/^q=en\//', $_SERVER['QUERY_STRING']))
				$this->language = 'en';
		}


		public function add_css() {
			wp_enqueue_style('admin-style', plugins_url('style.css', __FILE__));
		}


		public function filter($content) {
			preg_match_all('/\[amusing\-((count|table|form|link|page)\-([a-z][a-z0-9-_]+))(\s+([^\]]*))?\]/', $content, $matches);
			if (count($matches[0])) {
				foreach(array_keys($matches[0]) as $index) {
					$hook = $matches[1][$index];
					$method = preg_replace('/\-/', '_', $hook);
					$parameters = $matches[5][$index];
					$parts = preg_split('/\[\/?'.preg_quote('amusing-'.$hook).'[^\]]*\]/', $content);
					if (method_exists($this, $method))
						$parts[1] = call_user_func(array($this, $method), $parts[1], $parameters);
					$content = implode($parts);
				}
			}
			return $content;
		}


		private function get($route, $cache = true) {
			if (!$cache || !isset($this->cache[$route])) {
				$settings = stripslashes_deep(get_option('amusing-settings'));
				$curl = curl_init();
				$headers = [
					'Accept: application/json',
					'Content-Type: application/json',
					'Language: '.$this->language,
					'X-Amusing-Token: '.$settings['apitoken'],
				];
				curl_setopt_array($curl, array(
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_HTTPHEADER => $headers,
					CURLOPT_URL => rtrim(@$settings['apiurl'], '/').'/'.$route
				));
				$result = curl_exec($curl);
				if ($result === false) {
					$this->cache[$route] = new WP_Error(500, curl_error($curl));
				} else {
					$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
					$result = json_decode($result);
					if ((200<=$http_code) && (300>$http_code))
						$this->cache[$route] = $result;
					else
						$this->cache[$route] = new WP_Error($http_code, $result->error);
				}
			}
			return $this->cache[$route];
		}


		private function post($route, $data) {
			$settings = stripslashes_deep(get_option('amusing-settings'));
			$curl = curl_init();
			$json = json_encode($data);
			$headers = [
				'Accept: application/json',
				'Content-Type: application/json',
				'Content-Length: '.strlen($json),
				'Language: '.$this->language,
				'X-Amusing-Token: '.$settings['apitoken'],
				'X-Amusing-Remote: '.$_SERVER['REMOTE_ADDR']
			];
			curl_setopt_array($curl, array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_HTTPHEADER => $headers,
				CURLOPT_URL => rtrim(@$settings['apiurl'], '/').'/'.$route,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $json
			));
			$result = curl_exec($curl);
			if ($result === false) {
				return new WP_Error(500, curl_error($curl));
			} else {
				$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				$result = json_decode($result);
				if ((200<=$http_code) && (300>$http_code))
					$this->cache[$route] = $result;
				else
					$this->cache[$route] = new WP_Error($http_code, $result->error);
			}
		}

	}

}


if (class_exists('WP_Amusing_Hengelo')) {
	$wp_amusing_hengelo = new WP_Amusing_Hengelo();
}
