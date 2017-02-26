<?php

class Tag {
    /**
     * 입력받은 태그 string을 규칙에 맞게 정리하는 함수
     * @param string $tag_string
     * @return string
     */
    public function refine_tags($tag_string) {
        $tags = '';
        $tag_array = [];

        $parts = preg_split('/\s+/', $tag_string);
        foreach ($parts as $part) {
            $part = trim($part);
            // 태그는 #으로 시작하고 공백을 제외한 2글자 이상인 경우를 사용함
            if (!empty($part) && $part[0] === '#' && strlen($part) > 1) {
                $tag_array[] = $part;
            }
        }

        if (count($tag_array) > 0) {
            $tags = implode(' ', $tag_array);
        }

        return trim($tags);
    }

    /**
     * 입력 받은 태그 string을 적당한 길이로 자른 다음, anchor 태그를 만들어서 html string으로 변환하는 함수
     * @param string $tag_string
     * @param string $type
     * @param boolean $truncate
     * @return string
     */
    public function render_tag_html($tag_string, $type = 'places', $truncate = true) {
        if (empty($tag_string)) {
            return '';
        }

        $CI =& get_instance();
        $CI->load->helper('url');
        $truncated_string = $tag_html = '';

        $parts = preg_split('/\s+/', $tag_string);
        foreach ($parts as $part) {
            if ($truncate) {
                if (mb_strlen($truncated_string) + mb_strlen($part) <= 20) {
                    $truncated_string .= ' ' . $part; // To check original character length
                } else {
                    continue;
                }
            }
            if (!empty($part)) {
                $tag_without_sharp = mb_substr($part, 1, mb_strlen($part));
                $tag_html .= ' ' . anchor('/?type=' . $type . '&q=' . $tag_without_sharp, '#' . $tag_without_sharp);
            }
        }

        return $tag_html;
    }
}
