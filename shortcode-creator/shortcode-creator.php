<?php
/**
 * Plugin Name: Shortcode Creator
 * Description: Création de shortcode (en dur)
 * Version: 1.0.0
 * Author: David
 */

namespace ShortcodeCreator;

require_once 'class/ShortcodeCreator.php';

add_action('init', function () {
    new ShortcodeCreator();
});