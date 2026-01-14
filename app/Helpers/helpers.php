<?php

if (!function_exists('formatDate')) {
    /**
     * Format a date to dd/mm/yyyy format
     *
     * @param string|Carbon\Carbon|null $date
     * @return string
     */
    function formatDate($date)
    {
        if (!$date) {
            return '-';
        }

        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date);
        }

        return $date->format('d/m/Y');
    }
}

if (!function_exists('formatDateTime')) {
    /**
     * Format a datetime to dd/mm/yyyy HH:mm format
     *
     * @param string|Carbon\Carbon|null $date
     * @return string
     */
    function formatDateTime($date)
    {
        if (!$date) {
            return '-';
        }

        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date);
        }

        return $date->format('d/m/Y H:i');
    }
}
