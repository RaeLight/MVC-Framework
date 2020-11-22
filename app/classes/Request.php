<?php

class Request{
	
	public function has($name = false){
		return isset($_REQUEST[$name]);
	}
	
}