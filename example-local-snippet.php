<?php
// Example local snippet
add_action('wp_footer', 'wsu_local_example_snippet');
function wsu_local_example_snippet() {
    // Your code here
    echo '<!-- Local snippet loaded -->';
}