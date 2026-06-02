<?php
$_POST = [];
parse_str('setting_files[site.logo]=test&setting_files[site_favicon]=test2', $_POST);
var_dump($_POST);
