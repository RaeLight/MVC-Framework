<?php

class Home{
	
	public function index()
	{
		$title = "ttteee";
		$arr = [0, 1];
		
		$users = [0 => ["name" => "Name", "surname" => "Surname"], 1 => ["name" => "Name1", "surname" => "Surname2"]];
		
		return view('index', compact('title', 'arr', 'users'));
	}
	
}