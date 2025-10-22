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
 * Utility class for SNAP payment gateway.
 *
 * @package    paygw_snap
 * @copyright  2025 Your Name <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace paygw_snap;

defined('MOODLE_INTERNAL') || die();

/**
 * Utility class for SNAP payment gateway.
 */
class util {
    /**
     * Log a message to the Moodle logs.
     *
     * @param string $message The message to log
     * @param int $courseid Optional course ID for context
     * @param \Throwable $exception Optional exception to log
     * @param string $level The log level (debug, info, warning, error)
     * @return void
     */
    public static function log(
        string $message,
        int $courseid = 0,
        ?\Throwable $exception = null,
        string $level = 'info'
    ): void {
        global $CFG;

        // Build the context array.
        $context = [
            'context' => \context_system::instance(),
            'other' => [
                'component' => 'paygw_snap',
                'courseid' => $courseid,
            ]
        ];

        // Add exception details if provided.
        if ($exception !== null) {
            $context['other']['exception'] = [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ];
            
            // Include previous exception if it exists.
            if ($previous = $exception->getPrevious()) {
                $context['other']['previous_exception'] = [
                    'message' => $previous->getMessage(),
                    'code' => $previous->getCode(),
                ];
            }
        }

        // Log to Moodle's logging system.
        $logmessage = $message;
        if ($exception !== null) {
            $logmessage .= "\n" . $exception->getMessage();
        }

        // Map our log level to Moodle's constants.
        $moodlelevel = null;
        switch (strtolower($level)) {
            case 'debug':
                $moodlelevel = DEBUG_DEVELOPER;
                break;
            case 'warning':
                $moodlelevel = WARNING_NORMAL;
                break;
            case 'error':
                $moodlelevel = ERROR_NORMAL;
                break;
            case 'info':
            default:
                $moodlelevel = INFO_NORMAL;
                break;
        }

        // Log to Moodle's standard logging system.
        if (!empty($CFG->debugdisplay) && $moodlelevel <= DEBUG_DEVELOPER) {
            // Only show in debug mode for developer-level logs.
            debugging($logmessage, $moodlelevel, $exception ? $exception->getTrace() : null);
        }

        // Log to the database.
        $event = \paygw_snap\\event\payment_log::create([
            'context' => $context['context'],
            'other' => [
                'message' => $message,
                'level' => $level,
                'courseid' => $courseid,
                'exception' => $exception ? $exception->getMessage() : null,
            ]
        ]);
        $event->trigger();
    }

    /**
     * Log a successful payment.
     *
     * @param int $paymentid The payment ID
     * @param int $userid The user ID
     * @param int $courseid The course ID
     * @param float $amount The amount paid
     * @param string $currency The currency code
     * @return void
     */
    public static function log_payment(
        int $paymentid,
        int $userid,
        int $courseid,
        float $amount,
        string $currency
    ): void {
        $message = sprintf(
            'Payment successful - Payment ID: %d, User ID: %d, Course ID: %d, Amount: %.2f %s',
            $paymentid,
            $userid,
            $courseid,
            $amount,
            $currency
        );
        
        self::log($message, $courseid, null, 'info');
    }

    /**
     * Log a payment error.
     *
     * @param string $message The error message
     * @param int $courseid The course ID (if applicable)
     * @param \Throwable|null $exception The exception (if any)
     * @return void
     */
    public static function log_error(
        string $message,
        int $courseid = 0,
        ?\Throwable $exception = null
    ): void {
        self::log($message, $courseid, $exception, 'error');
    }
}
