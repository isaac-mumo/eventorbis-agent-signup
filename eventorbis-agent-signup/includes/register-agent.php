<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// ✅ Step 1: Register a Custom User Role for Agents
function eventorbis_create_agent_role() {
    add_role('agent', 'Agent', array(
        'read' => true,
        'edit_posts' => false,
        'delete_posts' => false,
    ));
}

// ✅ Step 2: Agent Registration Form Shortcode
function eventorbis_agent_registration_form() {
    ob_start(); ?>

    <form method="post">
        <label>Full Name:</label>
        <input type="text" name="full_name" required>

        <label>Phone Number:</label>
        <input type="text" name="phone" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Location:</label>
        <input type="text" name="location" required>

        <label>Mpesa Number:</label>
        <input type="text" name="mpesa_number" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <input type="submit" name="register_agent" value="Register">
    </form>

    <?php
    return ob_get_clean();
}
add_shortcode('eventorbis_agent_signup', 'eventorbis_agent_registration_form');

// ✅ Step 3: Handle Agent Registration
function eventorbis_handle_agent_registration() {
    if (isset($_POST['register_agent'])) {
        $full_name = sanitize_text_field($_POST['full_name']);
        $phone = sanitize_text_field($_POST['phone']);
        $email = sanitize_email($_POST['email']);
        $location = sanitize_text_field($_POST['location']);
        $mpesa_number = sanitize_text_field($_POST['mpesa_number']);
        $password = $_POST['password'];

        if (!is_email($email) || username_exists($email) || email_exists($email)) {
            echo "Email already in use!";
            return;
        }

        // Create user and assign Agent role
        $user_id = wp_create_user($email, $password, $email);
        if (!is_wp_error($user_id)) {
            wp_update_user(array(
                'ID' => $user_id,
                'display_name' => $full_name,
            ));
            update_user_meta($user_id, 'phone', $phone);
            update_user_meta($user_id, 'location', $location);
            update_user_meta($user_id, 'mpesa_number', $mpesa_number);
            $user = new WP_User($user_id);
            $user->set_role('agent');

            echo "Registration successful! You can now log in.";
        } else {
            echo "Error: " . $user_id->get_error_message();
        }
    }
}
add_action('init', 'eventorbis_handle_agent_registration');
