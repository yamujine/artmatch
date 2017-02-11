<?php

class Tag
{
	/**
	 * 입력받은 태그 string을 규칙에 맞게 정리하는 함수
	 * @param $tag_string
	 * @return string
	 */
	public function refine_tags($tag_string)
	{
		$tags = '';
		$tag_array = [];

		$parts = preg_split('/\s+/', $tag_string);
		foreach ($parts as $part) {
			if (!empty($part) && $part[0] === '#') {
				$tag_array[] = trim($part);
			}
		}

		if (count($tag_array) > 0) {
			$tags = implode(' ', $tag_array);
		}

		return $tags;
	}
}
