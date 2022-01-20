<?php


trait WP_Amusing_Hengelo_Settings {


	public function settings_menu() {
		add_options_page('Amusing Hengelo', 'Amusing Hengelo', 'manage_options', 'amusing', [$this, 'settings_page']);
	}


	public function settings_init() {

		register_setting('amusing-settings', 'amusing-settings');

		add_settings_section('settings', 'Settings', null, 'amusing');
		add_settings_field(
			'apiurl',
			'API URL',
			[$this, 'setting_apiurl'],
			'amusing',
			'settings',
			['label_for' => 'apiurl']
		);
		add_settings_field(
			'apitoken',
			'API token.',
			[$this, 'setting_apitoken'],
			'amusing',
			'settings',
			['label_for' => 'apitoken']
		);
		add_settings_field(
			'groupurl',
			'Local group URL',
			[$this, 'setting_groupurl'],
			'amusing',
			'settings',
			['label_for' => 'groupurl']
		);

		add_settings_section('help', 'Help', null, 'amusing');
		add_settings_field(
			'planning',
			'Planning',
			[$this, 'help_planning'],
			'amusing',
			'help'
		);
		add_settings_field(
			'enrollments',
			'Enrollments',
			[$this, 'help_enrollments'],
			'amusing',
			'help'
		);
		add_settings_field(
            'group',
            'Group',
            [$this, 'help_group'],
            'amusing',
            'help'
        );

	}


	public function settings_page() {
		global $wp_settings_sections;
		$tab = isset($_GET['tab']) ? $_GET['tab'] : 'settings';
?>
<div class="wrap">
	<div id="icon-themes" class="icon32"></div>
	<h2>Amusing Hengelo</h2>
	<?php settings_errors(); ?>

	<h2 class="nav-tab-wrapper">
<?php foreach ($wp_settings_sections['amusing'] as $section): ?>
		<a class="nav-tab <?php echo $tab==$section['id'] ? 'nav-tab-active' : ''; ?>" href="<?php echo admin_url('options-general.php?page=amusing&tab='.esc_attr($section['id'])); ?>"><?php echo esc_attr($section['title']); ?></a>
<?php endforeach; ?>
	</h2>
	
	<form method="post" action="options.php">
		<?php settings_fields('amusing-settings'); ?>
<?php
		foreach ($wp_settings_sections['amusing'] as $section)
			if ($tab==$section['id']) {
				echo '<div>';
				if ($section['callback'])
					call_user_func( $section['callback'], $section );
				echo '<table class="form-table" role="presentation">';
				do_settings_fields('amusing', $section['id']);
				echo '</table>';
				echo '</div>';
			}
?>
		<?php submit_button(); ?>
	</form>
</div>
<?php
	}


	public function setting_apiurl() {
		$settings = get_option('amusing-settings');
		printf(
			'<input type="%s" id="%s" name="%s" value="%s" class="regular-text" />',
			'url', 'apiurl', esc_attr('amusing-settings[apiurl]'), esc_attr($settings['apiurl'])
		);
		echo '<p class="description">The Amusing Hengelo API base URL.</p>';
	}


	public function setting_apitoken() {
		$settings = get_option('amusing-settings');
		printf(
			'<input type="%s" id="%s" name="%s" value="%s" class="regular-text" />',
			'text', 'apitoken', esc_attr('amusing-settings[apitoken]'), esc_attr($settings['apitoken'])
		);
		echo '<p class="description">The Amusing Hengelo API access token.</p>';
	}


	public function setting_groupurl() {
		$settings = get_option('amusing-settings');
		printf(
			'<input type="%s" id="%s" name="%s" value="%s" class="regular-text" />',
			'text', 'groupurl', esc_attr('amusing-settings[groupurl]'), esc_attr($settings['groupurl'])
		);
		echo '<p class="description">The performing group URL within this site, including <tt>[id]</tt>.</p>';
	}


	public function help_planning() {
		echo '<p>Tag: <tt>[amusing-table-planning]</tt></p>';
		echo '<p class="description">Display planning inside a page.</p>';
		echo '<p>Planning ready: <tt>[ready] ... [planning] ... [/ready]</tt></p>';
		echo '<p>Planning not ready: <tt>[not-ready] ... [/not-ready]</tt></p>';
	}


	public function help_enrollments() {
		echo '<p>Tag: <tt>[amusing-table-enrollments]</tt></p>';
		echo '<p class="description">Display enrollments inside a page.</p>';
		echo '<p>Groups are enrolled: <tt>[non-zero] ... [enrollments] ... [/non-zero]</tt></p>';
		echo '<p>No groups are enrolled: <tt>[zero] ... [/zero]</tt></p>';
		echo '<p>Number of enrollments: <tt>[count-enrollments]</tt></p>';
	}


	public function help_group() {
		echo '<p>Tag: <tt>[amusing-page-group]</tt></p>';
		echo '<p class="description">Display group information.</p>';
		echo '<p>Group found: <tt>[group] ... [group-tag...] ... [/group]</tt></p>';
		echo '<p>Group not found: <tt>[no-group] ... [/no-group]</tt></p>';
		echo '<p>Available group-tags: <tt>name</tt>, <tt>city</tt>, <tt>genre</tt>, <tt>website</tt>, <tt>photo</tt>, <tt>description</tt>.</p>';
	}



}
