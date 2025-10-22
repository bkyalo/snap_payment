<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * SNAP Payment Gateway autoloader.
 *
 * @package    paygw_snap
 * @copyright  2025 Your Name <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// This is a placeholder autoloader for the SNAP payment gateway.
// If you have a specific SNAP SDK, you should replace this file with the SDK's autoloader.

// Register any namespaced classes if needed.
// Example:
// $classmap = [
//     'Snap\Payment' => __DIR__ . '/Snap/Payment.php',
// ];

// spl_autoload_register(function($class) use ($classmap) {
//     if (isset($classmap[$class])) {
//         require $classmap[$class];
//     }
// });

// For now, we'll just return true to prevent errors.
return true;
