<?php
/**
 * Plugin Name: Eventorbis Agent Signup
 * Plugin URI:  https://eventorbis.com
 * Description: Custom signup and login for agents with a dashboard.
 * Version:     1.0
 * Author:      Isaac Mumo
 * Author URI:  https://isaacmumo.co.ke
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// Load required files
require_once plugin_dir_path(__FILE__) . 'includes/register-agent.php';
require_once plugin_dir_path(__FILE__) . 'includes/agent-login.php';

// Activation Hook
function eventorbis_activate() {
    eventorbis_create_agent_role();
}
register_activation_hook(__FILE__, 'eventorbis_activate');

// ✅ Enable GitHub Auto Updates
function eventorbis_check_for_plugin_update($transient) {
    if (empty($transient->checked)) {
        return $transient;
    }

    $plugin_slug = 'eventorbis-agent-signup';
    $plugin_version = '1.0'; // Change this when releasing a new version
    $github_repo = 'https://api.github.com/repos/isaac-mumo/eventorbis-agent-signup/releases/latest';

    $response = wp_remote_get($github_repo);

    if (is_wp_error($response)) {
        return $transient;
    }

    $release = json_decode(wp_remote_retrieve_body($response));

    if (isset($release->tag_name) && version_compare($plugin_version, $release->tag_name, '<')) {
        $transient->response[$plugin_slug] = (object) [
            'new_version' => $release->tag_name,
            'package'     => $release->zipball_url,
            'slug'        => $plugin_slug,
        ];
    }

    return $transient;
}
add_filter('site_transient_update_plugins', 'eventorbis_check_for_plugin_update');
