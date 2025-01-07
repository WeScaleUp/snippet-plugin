<?php
// Test GitHub snippet
add_action('wp_footer', 'wsu_test_github_snippet');
function wsu_test_github_snippet() {
    echo '<!-- Test GitHub snippet loaded -->';
}