<?php

namespace App\Services;

class EmbedVideo
{
	// Return a video diplay depending of where came form the url
    public function videoPlayer($urlAdress, $aAutoplay = true)
    {
        $video = $this->searchVideoId($urlAdress);
        if (empty($video)) {
            return null;
        }
        $h = $urlAdress;
        switch ($video['type']) {
        case'youtube':
            $h = 'https://www.youtube.com/embed/'.$video['videoId'].($aAutoplay ? '?autoplay=1' : '') . 'frameborder="0" allowfullscreen autoplay="1"';
            break;
        case 'vimeo':
            $h = 'https://player.vimeo.com/video/'.$video['videoId'].($aAutoplay ? '?autoplay=1' : '') . 'frameborder="0"  webkitallowfullscreen mozallowfullscreen allowfullscreen';
            break;
        case 'dailymotion':
            $h = 'https://www.dailymotion.com/embed/video/'.$video['videoId'].($aAutoplay ? '?autoplay=1' : '');
            break;
        }
        return $h;
    }

    // catch and return the id of the video
    public function searchVideoId($urlAdress)
    {
	$vid = '';
	$type = 0;
	if(strpos($urlAdress, 'youtube') !== false){
		// youtube
		if(preg_match('/(.+)youtube\.com\/watch\?v=([\w-]+)/', $urlAdress, $vid)){
			// we keep the id of the video extract by preg_match and put in an array
			$vid = $vid[2];
			$type = 'youtube';
		}

	}elseif(strpos($urlAdress, 'youtu.be') !== false){
		// youtu.be
		if(preg_match('/(.+)youtu.be\/([\w-]+)/', $urlAdress, $vid)){
			$vid = $vid[2];
			$type = 'youtube';
		}

	}elseif(strpos($urlAdress, 'vimeo') !== false){
		// vimeo
		if(preg_match('/https:\/\/vimeo.com\/([\w-]+)/', $urlAdress, $vid)){
			$vid = $vid[1];
			$type = 'vimeo';
		}

	}elseif(strpos($urlAdress, 'dailymotion') !== false){
		// dailymotion
		if(preg_match('/(.+)dailymotion.com\/video\/([\w-]+)/', $urlAdress, $vid)){
			$vid = $vid[2];
			$type = 'dailymotion';
		}

	}elseif(strpos($urlAdress, 'dai.ly') !== false){
		// dailymotion
		if(preg_match('/(.+)dai.ly\/([\w-]+)/', $urlAdress, $vid)){
			$vid = $vid[2];
			$type = 'dailymotion';
		}
    }
    return empty($type) ? 0 : ['type'=>$type, 'videoId'=>$vid];
    }
}