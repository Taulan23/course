<?php
$output = shell_exec('nginx -T 2>&1');
echo "<pre>$output</pre>";

function check_max_request_size() {
    global $output;
    $max_size = ini_get('post_max_size');
    $upload_max = ini_get('upload_max_filesize');
    
    echo "PHP Configuration:\n";
    echo "Maximum POST request size: " . $max_size . "\n";
    echo "Maximum file upload size: " . $upload_max . "\n";
    echo "Memory limit: " . ini_get('memory_limit') . "\n\n";
    
    echo "Server Information:\n";
    echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
    echo "PHP Version: " . phpversion() . "\n\n";
    
    if (isset($_SERVER['CONTENT_LENGTH'])) {
        echo "Current request size: " . $_SERVER['CONTENT_LENGTH'] . " bytes\n\n";
    }
    
    echo "Nginx Configuration:\n";
    if (preg_match('/client_max_body_size\s+(\d+[mMgG]?);/', $output, $matches)) {
        echo "Nginx client_max_body_size: " . $matches[1] . "\n";
    } else {
        echo "Unable to find client_max_body_size in Nginx configuration.\n";
    }
    
    echo "\n.htaccess Configuration:\n";
    if (file_exists('.htaccess')) {
        $htaccess_content = file_get_contents('.htaccess');
        echo $htaccess_content . "\n";
    } else {
        echo ".htaccess file not found.\n";
    }
    
    echo "\nLocal php.ini Configuration:\n";
    if (file_exists('php.ini')) {
        $php_ini_content = file_get_contents('php.ini');
        echo $php_ini_content . "\n";
    } else {
        echo "Local php.ini file not found.\n";
    }
}

check_max_request_size();
?>
