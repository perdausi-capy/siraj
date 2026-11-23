<?php
// This file is part of Siraj Learning, built on Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Siraj theme version.
 *
 * @package   theme_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'theme_siraj';
$plugin->version   = 2026111701;
$plugin->release   = '0.2 (phase 1)';
$plugin->requires  = 2025041400; // Moodle 5.0.
$plugin->maturity  = MATURITY_ALPHA;
$plugin->dependencies = ['theme_boost' => 2025041400];
