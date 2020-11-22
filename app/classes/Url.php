<?php

class Url{
	
	public function base($url = false)
	{
		return URL . '/' . trim($url, " \t\n\r\0\x0B/\\");
	}
	
	public function assets($url = false)
	{
		return URL . '/assets/' . $url;
	}
	
	public function getFull()
	{
		return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	}
	
}