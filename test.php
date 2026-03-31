<?php
echo "<h2 style='color:green'>✓ PHP is working!</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Server: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Script: " . $_SERVER['SCRIPT_FILENAME'] . "</p>";
echo "<hr>";
echo "<p>If you see this page, PHP works fine.</p>";
echo "<p><a href='index.php'>Try index.php</a></p>";
