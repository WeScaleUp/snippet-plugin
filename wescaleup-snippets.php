<?php
/**
 * Plugin Name: WeScaleUp Snippets
 * Description: Custom snippets manager with local and GitHub integration
 * Version: 1.2.7
 * Author: WeScaleUp
 */

if (!defined('ABSPATH')) exit;

define('WSU_DEBUG', true);

// Add admin menu
add_action('admin_menu', 'wsu_add_admin_menu');
function wsu_add_admin_menu() {
    add_menu_page(
        'WeScaleUp Snippets',
        'Snippets',
        'manage_options',
        'wescaleup-snippets',
        'wsu_snippets_page',
        'dashicons-code-standards',
        30
    );
}

// Get active GitHub snippets
function wsu_get_active_snippets() {
    return get_option('wsu_active_snippets', array());
}

// Admin page content
function wsu_snippets_page() {
    $active_snippets = wsu_get_active_snippets();
    ?>
    <div class="wrap wsu-snippets">
        <h1>WeScaleUp Snippets</h1>
        
        <div class="nav-tab-wrapper">
            <a href="#local" class="nav-tab nav-tab-active">Local Snippets</a>
            <a href="#github" class="nav-tab">GitHub Snippets</a>
        </div>

        <div class="tab-content" id="local-content">
            <div class="wsu-header">
                <h2>Local Snippets</h2>
                <button class="button button-primary" id="add-new-toggle">Add New Snippet</button>
            </div>
            
            <div class="snippet-grid">
                <?php
                $local_dir = plugin_dir_path(__FILE__) . 'local/';
                if (is_dir($local_dir)) {
                    foreach (glob($local_dir . "*.php") as $filename) {
                        $snippet_name = basename($filename);
                        $content = file_get_contents($filename);
                        ?>
                        <div class="snippet-card">
                            <div class="snippet-header">
                                <h3><?php echo esc_html($snippet_name); ?></h3>
                                <span class="badge badge-local">Local</span>
                            </div>
                            <div class="snippet-content">
                                <textarea class="snippet-editor" rows="10"><?php echo esc_textarea($content); ?></textarea>
                            </div>
                            <div class="snippet-footer">
                                <button class="button button-primary save-snippet" data-file="<?php echo esc_attr($snippet_name); ?>" data-type="local">Save Changes</button>
                                <button class="button button-link-delete delete-snippet" data-file="<?php echo esc_attr($snippet_name); ?>">Delete</button>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>

            <div class="add-new-snippet" style="display: none;">
                <div class="snippet-card">
                    <div class="snippet-header">
                        <h3>New Snippet</h3>
                    </div>
                    <div class="snippet-content">
                        <input type="text" id="new-snippet-name" placeholder="snippet-name.php" class="regular-text">
                        <textarea id="new-snippet-content" rows="10" placeholder="<?php echo esc_attr("<?php\n// Your code here\n"); ?>"></textarea>
                    </div>
                    <div class="snippet-footer">
                        <button class="button button-primary" id="create-snippet">Create Snippet</button>
                        <button class="button" id="cancel-create">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-content" id="github-content" style="display: none;">
            <div class="wsu-header">
                <h2>GitHub Snippets</h2>
                <p class="description">Enable or disable snippets per domain</p>
            </div>
            
            <div class="snippet-grid">
                <?php
                $github_dir = plugin_dir_path(__FILE__) . 'github/';
                if (is_dir($github_dir)) {
                    foreach (glob($github_dir . "*.php") as $filename) {
                        $snippet_name = basename($filename);
                        $content = file_get_contents($filename);
                        $is_active = in_array($snippet_name, $active_snippets);
                        ?>
                        <div class="snippet-card">
                            <div class="snippet-header">
                                <h3><?php echo esc_html($snippet_name); ?></h3>
                                <span class="badge badge-github">GitHub</span>
                            </div>
                            <div class="snippet-content">
                                <pre class="readonly-content"><?php echo esc_html($content); ?></pre>
                            </div>
                            <div class="snippet-footer">
                                <label class="switch">
                                    <input type="checkbox" class="snippet-toggle" 
                                           data-file="<?php echo esc_attr($snippet_name); ?>"
                                           <?php checked($is_active); ?>>
                                    <span class="slider"></span>
                                </label>
                                <span class="toggle-label"><?php echo $is_active ? 'Enabled' : 'Disabled'; ?></span>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <style>
        .wsu-snippets {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .wsu-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .snippet-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .snippet-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: box-shadow 0.3s ease;
        }

        .snippet-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .snippet-header {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .snippet-header h3 {
            margin: 0;
            font-size: 16px;
            color: #1e1e1e;
        }

        .snippet-content {
            padding: 15px;
        }

        .snippet-footer {
            padding: 15px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .snippet-editor, 
        .readonly-content {
            width: 100%;
            min-height: 200px;
            font-family: monospace;
            font-size: 13px;
            line-height: 1.4;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #f8f9fa;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-local {
            background: #e3f2fd;
            color: #1976d2;
        }

        .badge-github {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        /* Toggle Switch */
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #2196F3;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .toggle-label {
            margin-left: 10px;
            font-size: 14px;
            color: #666;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .snippet-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script>
    jQuery(document).ready(function($) {
        // Tab switching
        $('.nav-tab').click(function(e) {
            e.preventDefault();
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            $('.tab-content').hide();
            $($(this).attr('href') + '-content').show();
        });

        // Toggle new snippet form
        $('#add-new-toggle').click(function() {
            $('.add-new-snippet').slideToggle();
        });

        $('#cancel-create').click(function() {
            $('.add-new-snippet').slideUp();
            $('#new-snippet-name').val('');
            $('#new-snippet-content').val('');
        });

        // Save snippet changes
        $('.save-snippet').click(function() {
            const button = $(this);
            const textarea = button.closest('.snippet-card').find('.snippet-editor');
            const fileName = button.data('file');
            const type = button.data('type');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'wsu_save_snippet',
                    file: fileName,
                    content: textarea.val(),
                    type: type,
                    nonce: '<?php echo wp_create_nonce('wsu_snippet_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        alert('Snippet saved successfully!');
                    } else {
                        alert('Error saving snippet: ' + response.data);
                    }
                }
            });
        });

        // Create new snippet
        $('#create-snippet').click(function() {
            const name = $('#new-snippet-name').val();
            const content = $('#new-snippet-content').val();

            if (!name || !content) {
                alert('Please provide both name and content for the new snippet.');
                return;
            }

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'wsu_create_snippet',
                    name: name,
                    content: content,
                    nonce: '<?php echo wp_create_nonce('wsu_snippet_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        alert('Snippet created successfully!');
                        location.reload();
                    } else {
                        alert('Error creating snippet: ' + response.data);
                    }
                }
            });
        });

        // Toggle GitHub snippets
        $('.snippet-toggle').change(function() {
            const checkbox = $(this);
            const fileName = checkbox.data('file');
            const isEnabled = checkbox.prop('checked');
            const label = checkbox.closest('.snippet-footer').find('.toggle-label');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'wsu_toggle_snippet',
                    file: fileName,
                    enabled: isEnabled,
                    nonce: '<?php echo wp_create_nonce('wsu_snippet_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        label.text(isEnabled ? 'Enabled' : 'Disabled');
                    } else {
                        checkbox.prop('checked', !isEnabled);
                        alert('Error toggling snippet: ' + response.data);
                    }
                }
            });
        });

        // Delete snippet
        $('.delete-snippet').click(function() {
            if (!confirm('Are you sure you want to delete this snippet?')) {
                return;
            }

            const button = $(this);
            const fileName = button.data('file');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'wsu_delete_snippet',
                    file: fileName,
                    nonce: '<?php echo wp_create_nonce('wsu_snippet_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        button.closest('.snippet-card').fadeOut(function() {
                            $(this).remove();
                        });
                    } else {
                        alert('Error deleting snippet: ' + response.data);
                    }
                }
            });
        });
    });
    </script>
    <?php
}

// AJAX handlers
add_action('wp_ajax_wsu_save_snippet', 'wsu_ajax_save_snippet');
add_action('wp_ajax_wsu_create_snippet', 'wsu_ajax_create_snippet');
add_action('wp_ajax_wsu_toggle_snippet', 'wsu_ajax_toggle_snippet');
add_action('wp_ajax_wsu_delete_snippet', 'wsu_ajax_delete_snippet');

function wsu_ajax_save_snippet() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }

    check_ajax_referer('wsu_snippet_nonce', 'nonce');

    $file = sanitize_file_name($_POST['file']);
    $content = wp_unslash($_POST['content']);
    $type = $_POST['type'];

    if ($type !== 'local') {
        wp_send_json_error('Only local snippets can be edited');
    }

    $file_path = plugin_dir_path(__FILE__) . 'local/' . $file;
    
    if (file_put_contents($file_path, $content) !== false) {
        wp_send_json_success();
    } else {
        wp_send_json_error('Failed to save file');
    }
}

function wsu_ajax_create_snippet() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }

    check_ajax_referer('wsu_snippet_nonce', 'nonce');

    $name = sanitize_file_name($_POST['name']);
    if (!preg_match('/\.php$/', $name)) {
        $name .= '.php';
    }

    $content = wp_unslash($_POST['content']);
    $file_path = plugin_dir_path(__FILE__) . 'local/' . $name;

    if (file_exists($file_path)) {
        wp_send_json_error('A snippet with this name already exists');
    }

    if (file_put_contents($file_path, $content) !== false) {
        wp_send_json_success();
    } else {
        wp_send_json_error('Failed to create file');
    }
}

function wsu_ajax_toggle_snippet() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }

    check_ajax_referer('wsu_snippet_nonce', 'nonce');

    $file = sanitize_file_name($_POST['file']);
    $enabled = (bool) $_POST['enabled'];

    if (WSU_DEBUG) {
        error_log('Toggling snippet: ' . $file . ' - Enabled: ' . ($enabled ? 'true' : 'false'));
    }

    $active_snippets = get_option('wsu_active_snippets', array());

    if ($enabled && !in_array($file, $active_snippets)) {
        $active_snippets[] = $file;
    } else if (!$enabled) {
        $active_snippets = array_diff($active_snippets, array($file));
    }

    $active_snippets = array_values(array_unique($active_snippets));

    if (WSU_DEBUG) {
        error_log('New active snippets: ' . print_r($active_snippets, true));
    }

    delete_option('wsu_active_snippets');
    $update_result = add_option('wsu_active_snippets', $active_snippets);

    if (WSU_DEBUG) {
        error_log('Update result: ' . ($update_result ? 'success' : 'failed'));
    }

    if ($update_result) {
        wp_send_json_success();
    } else {
        wp_send_json_error('Failed to update snippet status. Please try again.');
    }
}

function wsu_ajax_delete_snippet() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }

    check_ajax_referer('wsu_snippet_nonce', 'nonce');

    $file = sanitize_file_name($_POST['file']);
    $file_path = plugin_dir_path(__FILE__) . 'local/' . $file;

    if (unlink($file_path)) {
        wp_send_json_success();
    } else {
        wp_send_json_error('Failed to delete file');
    }
}

// Load snippets
function wsu_load_snippets() {
    $active_snippets = get_option('wsu_active_snippets', array());

    // Load GitHub snippets (only active ones)
    $github_dir = plugin_dir_path(__FILE__) . 'github/';
    if (is_dir($github_dir)) {
        foreach (glob($github_dir . "*.php") as $filename) {
            if (in_array(basename($filename), $active_snippets)) {
                require_once $filename;
            }
        }
    }

    // Load all local snippets
    $local_dir = plugin_dir_path(__FILE__) . 'local/';
    if (is_dir($local_dir)) {
        foreach (glob($local_dir . "*.php") as $filename) {
            require_once $filename;
        }
    }
}

add_action('plugins_loaded', 'wsu_load_snippets');