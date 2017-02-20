<?php

class Address {
    /**
     * 입력 받은 주소 string을 공백으로 나누어 앞 2개 파트만 리턴
     * @param string $address_string
     * @return string
     */
    public function extract_foremost_part($address_string) {
        $address_parts = preg_split('/\s+/', $address_string);
        $modified_address = '';
        foreach ($address_parts as $i => $part) {
            if ($i > 1) {
                break;
            } else if (!empty($part)) {
                $modified_address .= ' ' . $part;
            }
        }

        return trim($modified_address);
    }
}
