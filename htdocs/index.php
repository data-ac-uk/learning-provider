<?php
date_default_timezone_set( "Europe/London" );
try {
    $f3=require('lib/base.php');
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

#if ((float)strstr(PCRE_VERSION,' ',TRUE)<7.9)
#	trigger_error('Outdated PCRE library version');

if (function_exists('apache_get_modules') &&
	!in_array('mod_rewrite',apache_get_modules()))
	trigger_error('Apache rewrite_module is disabled');

$f3->set('DEBUG',2);
$f3->set('UI','ui/');


$f3->route('GET /',
	function() {
		$content = file_get_contents( "homepage.html" );
		print render_page( "UK Learning Providers", $content );
	}
);
#$f3->route('GET /ns/*', 
#	function() {
#		header( "Location: http://learning.data.ac.uk/academic-session-vocab.ttl" );
#		exit;
#	} );
#$f3->route('GET /academic-session-vocab.ttl', 
#	function() {
#		turtle_intro();
#		uk_class();
#		world_class();
#	} );
#
#$f3->route('GET /academic-session-100.ttl',
#	function() {
#		header( "Content-type: text/turtle" );
#		turtle_intro();
#		uk_class();
#		world_class();
#		for( $year = 1950; $year < 2150; $year++ )
#		{
#			print year_in_turtle( $year );
#			print world_year_in_turtle( $year );
#		}
#	}
#);
#$f3->route('GET /academic-session-300.ttl',
#	function() {
#		header( "Content-type: text/turtle" );
#		turtle_intro();
#		uk_class();
#		world_class();
#		for( $year = 1850; $year < 2250; $year++ )
#		{
#			print year_in_turtle( $year );
#			print world_year_in_turtle( $year );
#		}
#	}
#);
#$f3->route('GET /@path' , 
#	function() use($f3) {
#	        $path = $f3->get('PARAMS.path');
#		render_year_document( $f3, $path, "uk" );
#	});
#$f3->route('GET /world/@path' , 
#	function() use($f3) {
#	        $path = $f3->get('PARAMS.path');
#		render_year_document( $f3, $path, "world" );
#	});

$f3->run();

exit;

