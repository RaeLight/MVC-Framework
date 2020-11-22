<?php 

class Route{
	
	public $routes = [];
	
	public $nf = [];
	
	private $prefixGroup = null;
	
	public function init()
	{
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		$matched = false;
		$routeName = str_replace([dirname($_SERVER['SCRIPT_NAME']), basename($_SERVER['SCRIPT_NAME'])], null, parse_url($_SERVER['REQUEST_URI'])['path']);
		
		if(!in_array($routeName, ['/']) && substr($routeName, -1) == "/") {
			$this->redirect((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")."://".$_SERVER['HTTP_HOST'].rtrim($_SERVER['REQUEST_URI'], '/'));
		}
		
		foreach($this->routes as $key => $route){
			
			if(preg_match('@^'.($route['uri'] != '/' && substr($route['uri'], -1, 1) == '/' ? substr($route['uri'], 0, -1) : $route['uri']).'$@i', $routeName, $params) && in_array($method, explode('|', $route['method']))){
				
				$matched = true;
				
				unset($params[0]);
				$params = array_map(function($v){
					return urldecode(htmlentities(htmlspecialchars(trim($v))));
				}, $params);
				if(is_callable($route['callback']) && !is_array($route['callback'])){
					
					echo call_user_func_array($route['callback'], $params);
				}else{
					$require_path = PATH . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, ['app','controllers', $route['callback']['controllerDir'] . ".php"]);
					if(file_exists($require_path)){
						require_once $require_path;
						echo call_user_func_array([new $route['callback']['controllerName'], $route['callback']['functionName']], $params);
						break;
					}else{
						echo 'Controller not found';
						exit;
					}
				}
			}
			
		}
		
		if(!$matched) {
			krsort($this->nf);
			
			foreach($this->nf as $nf){
				$pattern = $nf['uri'] . "([a-zA-Z0-9-_/]+)";
				
				if(preg_match('@^'.$pattern.'$@i', $routeName)){
					$matched = true;
					
					if(is_callable($nf['callback']) && !is_array($nf['callback'])){
						echo call_user_func_array($nf['callback'], []);
					}else{
						$require_path = PATH . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, ['app','controllers', $nf['callback']['controllerDir'] . ".php"]);
						if(file_exists($require_path)){
							require_once $require_path;
							echo call_user_func_array([new $nf['callback']['controllerName'], $nf['callback']['functionName']], []);
							break;
						}else{
							echo 'Controller not found';
							exit;
						}
					}
				}
			}
		}
	}
	
	public function prefix($url = false)
	{
		if(!$url) return;
		
		$this->prefixGroup = '/' . $url;
		
		return $this;
	}
	
	public function group($callback = false)
	{
		if(!$callback || !is_callable($callback)) return;
		
		call_user_func($callback);
		
		$this->prefixGroup = null;
	}
	
	public function get($url = false, $callback = false, $name = false)
	{
		if(!$url) return;
		
		$url = $this->url($this->prefixGroup . $url);
		
		$routeKey = array_search($url, array_column($this->routes, 'uri'));
		
		//var_dump($this);
		
		if($routeKey === FALSE)
		{
			$route = ['method' => 'get', 'uri' => $url];
			
			if($name) $route['name'] = $name;
			
			if(is_callable($callback) || !$callback){
				$route['callback'] = $callback;
			}else{
				$controller = explode('@', $callback);
				$controllerName = $controller[0];
				if(strstr($controller[0], '.') !== false){
					$controllerName = explode('.', $controller[0]);
					$controllerName = end($controllerName);
				}
				$controllerDir = str_replace('.', DIRECTORY_SEPARATOR, $controller[0]);
				
				$route['callback'] = [
					'controllerName' => $controllerName,
					'controllerDir' => $controllerDir, 
					'functionName' => $controller[1],
				];
			}
			
			array_push($this->routes, $route);
		}
	}
	
	public function post($url = false, $callback = false, $name = false)
	{
		if(!$url) return;
		
		$url = $this->url($this->prefixGroup . $url);
		
		$routeKey = array_search($url, array_column($this->routes, 'uri'));
		
		if($routeKey === FALSE)
		{
			$route = ['method' => 'post', 'uri' => $url];
			
			if($name) $route['name'] = $name;
			
			if(is_callable($callback) || !$callback){
				$route['callback'] = $callback;
			}else{
				$controller = explode('@', $callback);
				$controllerName = $controller[0];
				if(strstr($controller[0], '.') !== false){
					$controllerName = explode('.', $controller[0]);
					$controllerName = end($controllerName);
				}
				$controllerDir = str_replace('.', DIRECTORY_SEPARATOR, $controller[0]);
				
				$route['callback'] = [
					'controllerName' => $controllerName,
					'controllerDir' => $controllerDir, 
					'functionName' => $controller[1],
				];
			}
			
			array_push($this->routes, $route);
		}
	}
	
	private function url($url)
	{
		return preg_replace('/({([a-z0-9A-Z\_\-]+)})/i', '([a-z0-9A-Z\_\-]+)', $url);
	}
	
	public function match($methodes = [], $url = false, $callback = false, $name = false)
	{
		if(!$url) return;
		
		$url = $this->url($this->prefixGroup . $url);
		
		$routeKey = array_search($url, array_column($this->routes, 'uri'));
		
		if($routeKey === FALSE)
		{
			$route = ['method' => strtolower(implode('|', $methodes)), 'uri' => $url];
			
			if($name) $route['name'] = $name;
			
			if(is_callable($callback) || !$callback){
				$route['callback'] = $callback;
			}else{
				$controller = explode('@', $callback);
				$controllerName = $controller[0];
				if(strstr($controller[0], '.') !== false){
					$controllerName = explode('.', $controller[0]);
					$controllerName = end($controllerName);
				}
				$controllerDir = str_replace('.', DIRECTORY_SEPARATOR, $controller[0]);
				
				$route['callback'] = [
					'controllerName' => $controllerName,
					'controllerDir' => $controllerDir, 
					'functionName' => $controller[1],
				];
			}
			
			array_push($this->routes, $route);
		}
	}
	
	public function notfound($url = false, $callback = false)
	{
		if(!$url) return;
		
		$nfKey = array_search($url, array_column($this->nf, 'uri'));
		
		if($nfKey === FALSE)
		{
			$nf = ['uri' => $url];
			
			if(is_callable($callback) || !$callback){
				$nf['callback'] = $callback;
			}else{
				$controller = explode('@', $callback);
				$controllerName = $controller[0];
				if(strstr($controller[0], '.') !== false){
					$controllerName = explode('.', $controller[0]);
					$controllerName = end($controllerName);
				}
				$controllerDir = str_replace('.', DIRECTORY_SEPARATOR, $controller[0]);
				
				$nf['callback'] = [
					'controllerName' => $controllerName,
					'controllerDir' => $controllerDir, 
					'functionName' => $controller[1],
				];
			}
			
			array_push($this->nf, $nf);
		}
	}
	
	public function getByName($name = false)
	{
		if($name){
			$key = array_search($name, array_column($this->routes, 'name'));
			return $key !== FALSE ? $this->routes[$key] : false;
		}
		
		return false;
	}
	
	public function redirect($uri)
	{
		return header('Location: '.$uri);
	}
	
	public function is($name = false)
	{
		if($name){
			$uri = str_replace([dirname($_SERVER['SCRIPT_NAME']), basename($_SERVER['SCRIPT_NAME'])], null, parse_url($_SERVER['REQUEST_URI'])['path']);
			$route = $this->getByName($name);
			if(!$route) return false;
			
			if(preg_match('@^'. $route['uri'] .'$@i', $uri, $params))
				return true;
			
			return false;
		}
		
		return false;
	}
	
}