<?php

class Blade{
	
	private $view = null;
	
	private $yields = [];
	
	private $sections = [];
	
	private $data = [];
	
	public $directives = [];
	
	private $regex = [
		'extends' => '/@extends\((.*)\)/',
		'yield' => '/@yield\((.*)\)/',
		'include' => '/@include\((.*)\)/',
		'section' => '/(?|(@section\(([\'|"](?P<name>[a-zA-Z_]*)[\'|"],(?P<value>.*))\))|(?s)(@section\(([\'|"](?P<name>[a-zA-Z_]*)[\'|"])\)(?P<value>.*)@endsection))/mi',
		'if' => '/@if\((?P<condition>.*)\)/',
		'elseif' => '/@elseif\((?P<condition>.*)\)/',
		'else' => '/@else/',
		'endif' => '/@endif/',
		'isset' => '/@isset\((?P<var>.*)\)/',
		'endisset' => '/@endisset/',
		'forelse' => '/@forelse\((?P<array_expression>.+)\)/',
		'empty' => '/@empty(?:(?P<var>.*))/',
		'endempty' => '/@endempty/',
		'continue' => '/@continue(?:(?P<condition>.*))/',
		'break' => '/@break(?:(?P<condition>.*))/',
		'endforelse' => '/@endforelse/',
		'foreach' => '/@foreach\((?P<array_expression>.+)\)/',
		'endforeach' => '/@endforeach/',
		'for' => '/@for\((?P<expression>.+)\)/',
		'endfor' => '/@endfor/',
		'while' => '/@while\((?P<condition>.+)\)/',
		'endwhile' => '/@endwhile/',
		'echo' => '/([^@]\{\{(?P<echo>.+)\}\})/U',
		'json' => '/@json\((?P<code>.+)\)/',
		'each' => '/@each\((.*)\)/',
		'debug' => '/@debug\((.*)\)/',
		'php' => '/@php/',
		'endphp' => '/@endphp/'
	];
	
	private $global_directives = [];
	
	public function __construct(){
		$this->global_directives = [
			$this->regex['extends'] => function($matches){
				//extract($this->data);
				$extend = trim(trim($matches[1]), "'\"");
				return $this->include_view($extend);
			},
			$this->regex['php'] => function(){
				return "<?php";
			},
			$this->regex['endphp'] => function(){
				return "?>";
			},
			$this->regex['echo'] => function($matches){
				extract($this->data);
				$echo = strip_tags(trim($matches['echo']));
				return "<?php echo {$echo}; ?>";
			},
			$this->regex['json'] => function($matches){
				//extract($this->data);
				$code = trim($matches['code']);
				return "<?php echo json_encode({$code}); ?>";
			},
			$this->regex['forelse'] => function($matches){
				//extract($this->data);
				$array_expression = $matches['array_expression'];
				return "<?php if(!empty(".current(explode(" as ",$array_expression)).")): foreach({$array_expression}): ?>";
			},
			$this->regex['empty'] => function($matches){
				$var = trim($matches["var"]);
				return "<?php ".(empty($var) ? "endforeach; else:" : "if(empty({$var})):")." ?>";
			},
			$this->regex['endempty'] => function(){
				return "<?php endif; ?>";
			},
			$this->regex['endforelse'] => function(){
				return "<?php endif; ?>";
			},
			$this->regex['foreach'] => function($matches){
				//extract($this->data);
				$array_expression = $matches['array_expression'];
				return "<?php foreach({$array_expression}): ?>";
			},
			$this->regex['endforeach'] => function(){
				return "<?php endforeach; ?>";
			},
			$this->regex['while'] => function($matches){
				//extract($this->data);
				$expression = trim($matches['expression']);
				return "<?php while({$expression}): ?>";
			},
			$this->regex['endwhile'] => function(){
				return "<?php endwhile; ?>";
			},
			$this->regex['for'] => function($matches){
				//extract($this->data);
				$expression = trim($matches['expression']);
				return "<?php for({$expression}): ?>";
			},
			$this->regex['endfor'] => function(){
				return "<?php endfor; ?>";
			},
			$this->regex['isset'] => function($matches){
				//extract($this->data);
				$var = $matches['var'];
				return "<?php if(isset({$var})): ?>";
			},
			$this->regex['endisset'] => function(){
				return "<?php endif; ?>";
			},
			$this->regex['each'] => function($matches){
				
				$parts = array_map("trim", explode(',', $matches[1]));
				$hasEmpty = isset($parts[3]) and !empty($parts[3]) ? true : false;
				
				extract($this->data);
				
				$html = "\n<?php ".(!$hasEmpty ? "" : "if(empty({$parts[1]})): ?>\n" . $this->include(trim($parts[3], "'\"")) . "\n<?php else: ")."foreach({$parts[1]} as $".trim($parts[2], '\"\'$')."): ?>\n".$this->include_view(trim($parts[0], "'\""))."\n<?php endforeach;".(!$hasEmpty ? "" : " endif;")." ?>";
				
				/*$array = ${trim($parts[1], "'\"$")};
				
				foreach($array as ${trim($parts[2], "'\"$")}){
					//${trim($parts[2], "'\"$")} = ${$val};
					var_dump($this->include(trim($parts[0], "'\""))."\n");
				}*/
				
				return $html;
			},
			$this->regex['debug'] => function($match){
				return "<?php var_dump({$match[1]}) ?>";
			},
			$this->regex['if'] => function($matches){
				//extract($this->data);
				$condition = trim($matches["condition"]);
				return "<?php if({$condition}): ?>";
			},
			$this->regex['elseif'] => function($matches){
				//extract($this->data);
				$condition = trim($matches["condition"]);
				return "<?php elseif({$condition}): ?>";
			},
			$this->regex['else'] => function(){
				return "<?php else: ?>";
			},
			$this->regex['endif'] => function(){
				return "<?php endif; ?>";
			},
			$this->regex['continue'] => function($matches){
				$condition = trim($matches["condition"]);
				return "<?php ".(empty($condition) ? "continue;" : "if{$condition}: continue; endif;")." ?>";
			},
			$this->regex['break'] => function($matches){
				$condition = trim($matches["condition"]);
				return "<?php ".(empty($condition) ? "break;" : "if{$condition}: break; endif;")." ?>";
			},
			$this->regex['include'] => function($matches){
				//extract($this->data);
				$include = trim(trim($matches[1]), "'\"");
				return $this->include($include);
			},
			$this->regex['yield'] => function($matches){
				$yield = trim(trim($matches[1]), "'\"");

				if(isset($this->yields[$yield]))
					return $this->yields[$yield];

				$this->yields[$yield] = $matches[0];

				return $matches[0];
			},
			$this->regex['section'] => function($matches){

				extract($this->data);
	
				if(isset($this->sections[$matches[1]])) 
					return $this->sections[$matches[1]];

				$section = trim($matches['value'], " ");

				if(!$this->is_html($section)){
					$section = eval("return {$section};");
				}else{
					$section = $this->decodeDirectives($section);
				}

				$this->sections[$matches["name"]] = $section;
				
				//return $section;
			}
		];
	}
		
	public function directive($directive_name = false, $callback = false){
		
		if(!$directive_name) return;
		
		if(!isset($this->directives[$directive_name]))
		{
			$this->directives[$directive_name] = [
				'regex' => '/(@'.$directive_name.')(?:\((.+)\))*/', 
				'callback' => !isset($callback) && !$callback ? function(){return "";} : function($matches) use($callback){
				return call_user_func_array($callback, isset($matches[2]) ? array_map("trim", explode(",", $matches[2])) : []);
			}];
		}
	}
	
	public function view($viewDir = false, $data = [])
	{
		if(!$viewDir) return;
		
		$cacheDir = page('cache' . DIRECTORY_SEPARATOR . $this->dotToSlash($viewDir));
		
		$this->data = $data;
		
		extract($data);
		
		$this->view = $this->decodeDirectives($this->include_view($viewDir));
		
		foreach($this->yields as $key => $value){
			$this->view = str_replace($value, $this->sections[$key] ?? "", $this->view);
		}
		
		if(!file_exists($cacheDir) || $this->view != file_get_contents($cacheDir)){
			file_put_contents($cacheDir, $this->view);
		}
		
		require_once $cacheDir;
		
		/*return eval("?>".$this->view);*/
	}
	
	private function include_view($dir = false)
	{
        if(!$dir) 
            return null;
		
		$dir = $this->dotToSlash($dir);
		
		ob_start();
		extract($this->data);
		require page($dir);
		$include = ob_get_contents();
		ob_end_clean();
		
		return $include;
	}
	
	private function include($match)
	{
		return $this->decodeDirectives($this->include_view($match));
	}
	
	private function dotToSlash($dir)
	{
		return str_replace('.', DIRECTORY_SEPARATOR, $dir);
	}
	
	private function decodeDirectives($code)
	{
		return preg_replace_callback_array(array_merge($this->global_directives, array_column($this->directives, 'callback', 'regex')), $code);
	}

	private function is_html($string)
	{
		return preg_match("/<[^<]+>/",$string,$m) != 0;
	}

	private function is_function($string)
	{
		preg_match("/(\w+)/",$string, $matches);

		return function_exists($matches[0]);
	}

	private function is_var($string)
	{
		return $GLOBALS[substr($string, 1)] ?? false;
	}
	
}