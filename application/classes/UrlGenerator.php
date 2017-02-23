<?php

class UrlGenerator {
    public static function generate_thumb_url($filename, $type = '', $size = '_thumb') {
        if (empty($filename)) {
            return ''; // Fallback error image
        }

        if (ENVIRONMENT === 'production') {
            $host = 'http://img.pickartyou.com/';
        } else {
            $host = '/uploads/';
        }

        if (!empty($type)) {
            $host .= $type . '/';
        }

        $path_parts = pathinfo($filename);

        if (!isset($path_parts['extension'])) {
            return ''; // Fallback error image
        }

        return $host . $path_parts['filename'] . $size . '.' . $path_parts['extension'];
    }

    public static function generate_original_image_url($filename, $type = '') {
        if (empty($filename)) {
            return ''; // Fallback error image
        }

        if (ENVIRONMENT === 'production') {
            $host = 'http://img.pickartyou.com/';
        } else {
            $host = '/uploads/';
        }

        if (!empty($type)) {
            $host .= $type . '/';
        }

        return $host . $filename;
    }

    public static function generate_static_url($filename) {
        if (ENVIRONMENT === 'production') {
            $host = 'http://static.pickartyou.com/';
        } else {
            $host = '../../static/';
        }

        return $host . $filename;
    }
}
