<?php

if (!defined('WP_UNINSTALL_PLUGIN'))
    exit();
global $wpdb;
$query = "DROP TABLE IF EXISTS {$wpdb->prefix}smart_calculator";
$query1 = "DROP TABLE IF EXISTS {$wpdb->prefix}smart_savings_calculator";

$wpdb->query($query);
$wpdb->query($query1);