<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// ✅ Step 1: Agent Login Form Shortcode
function eventorbis_agent_login_form() {
    if (is_user_logged_in()) {
        wp_redirect(home_url('/agent-dashboard'));
        exit;
    }

    ob_start(); ?>

    <form method="post">
        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <input type="submit" name="agent_login" value="Login">
    </form>

    <?php
    return ob_get_clean();
}
add_shortcode('eventorbis_agent_login', 'eventorbis_agent_login_form');

// ✅ Step 2: Handle Login Logic
function eventorbis_handle_agent_login() {
    if (isset($_POST['agent_login'])) {
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];

        $user = get_user_by('email', $email);
        if ($user && wp_check_password($password, $user->user_pass, $user->ID)) {
            wp_set_auth_cookie($user->ID);
            wp_redirect(home_url('/agent-dashboard')); // Redirect to dashboard
            exit;
        } else {
            echo "Invalid email or password!";
        }
    }
}
add_action('init', 'eventorbis_handle_agent_login');
