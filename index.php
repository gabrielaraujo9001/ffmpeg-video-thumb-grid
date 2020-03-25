<?php

	require_once('config.php');
	
	$video_info = shell_exec("ffprobe -i " . $config["video_path"] . " -print_format json -show_streams");
	$video_info_json = json_decode($video_info, true);

	/* Frames per second */
	$video_framerate = explode("/",$video_info_json["streams"][0]["r_frame_rate"]);
	$dividend = (integer) $video_framerate[0];
	$divisor = (integer) $video_framerate[1];
	$frames_per_second = floor($dividend/$divisor);

	/* The amount of frames before taking a screenshot for any given delay (in seconds) */
	$screenshot_delay = $frames_per_second*$config["screenshot_delay"];

	/* Uses FFMpeg to create preview files */
	$preview_file = popen(
		"start ffmpeg -i " . $config["video_path"] . " -vf select=not(mod(n\," . $screenshot_delay . ")),scale=-1:" . $config["screenshot_width"] . ",tile=" . $config["screenshots_per_column"] . "x" . $config["screenshots_per_row"] . " -q:v 0 -vsync 0 " . $config["sequence_prefix"] . "%02d." . $config["preview_extension"], "r");
	pclose($preview_file);
