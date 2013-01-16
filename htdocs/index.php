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

function render_year_document( $f3, $path, $mode )
{
	if( !preg_match( '/^(\d\d\d\d)-(\d\d\d\d)\.(ttl|html)$/', $path, $b ) )
	{
		$f3->error(404);
		return;
	}
	if( $b[1] + 1 != $b[2] )
	{
		$f3->error(404);
		return;
	}

	list( $dummy, $start_year, $end_year, $format ) = $b;


	if( $b[3] == 'ttl' )
	{
		header( "Content-type: text/turtle" );
		turtle_intro();
		if( $mode == "uk" )
		{
			uk_class();
			print year_in_turtle( $b[1] );
		}
		else
		{
			world_class();
			print world_year_in_turtle( $b[1] );
		}
	}
	else
	{	
		$title = "UK Academic Session $start_year to $end_year";
		$content = "
<div><strong>UK Academic Session URI:</strong> http://id.academic-session.data.ac.uk/$start_year-$end_year</div>
<div><strong>Worldwide Academic Session URI:</strong> http://id.academic-session.data.ac.uk/world/$start_year-$end_year</div>
";
		print render_page( $title, $content );
	}
}

function render_page( $title, $content )
{
	$page = file_get_contents( "template.html" );
	$page = preg_replace( "/\\\$CONTENT/", $content, $page );
	$page = preg_replace( "/\\\$TITLE/", $title, $page );
	return $page;
}


function turtle_intro()
{?>
@base <http://id.academic-session.data.ac.uk/> .
@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .
@prefix skos: <http://www.w3.org/2004/02/skos/core#> .
@prefix timeline: <http://purl.org/NET/c4dm/timeline.owl#> .
@prefix xsd: <http://www.w3.org/2001/XMLSchema#> .

<?php } 
function world_class()
{ ?>
<ns/WorldAcademicSession> a rdfs:Class ;
	rdfs:label "World Academic Session"@en ;
	rdfs:comment "A specific instance of an annual World Academic Session"@en .
<?php } 
function uk_class()
{ ?>
<ns/UKAcademicSession> a rdfs:Class ;
	rdfs:label "UK Academic Session"@en ;
	rdfs:comment "A specific instance of an annual UK Academic Session"@en .
<?php }
function world_year_in_turtle( $start_year )
{
	$prev_year = $start_year-1;
	$end_year = $start_year+1;
	$next_year = $end_year+1;
	$uri = "$start_year-$end_year";
	return "
<world/$uri> rdfs:label \"World Academic Session $start_year to $end_year\"@en ;
	a skos:Concept , <ns/WorldAcademicSession> , timeline:Interval;
	timeline:after <world/$prev_year-$start_year> ;
	timeline:before <world/$end_year-$next_year> ;
	skos:narrower <$uri> .
";
#	timeline:beginsAtDuration <world/$uri#start> ;
#	timeline:endsAtDuration <world/$uri#end> ;
#<world/$uri#start> a timeline:Interval ;
#	timeline:startsAtDateTime \"$start_year-01-01T00:00:00\"^^xsd:dateTime ;
#	timeline:endsAtDateTime \"$start_year-12-31T23:59:59\"^^xsd:dateTime .
#<world/$uri#end> a timeline:Interval ;
#	timeline:startsAtDateTime \"$start_year-12-31T23:59:59\"^^xsd:dateTime ;
#	timeline:endsAtDateTime \"$end_year-12-31T23:59:59\"^^xsd:dateTime .
}
function year_in_turtle( $start_year )
{
	$prev_year = $start_year-1;
	$end_year = $start_year+1;
	$next_year = $end_year+1;
	$uri = "$start_year-$end_year";
	return "
<$uri> rdfs:label \"UK Academic Session $start_year to $end_year\"@en ;
	a skos:Concept , <ns/UKAcademicSession> , timeline:Interval ;
	timeline:after <$prev_year-$start_year> ;
	timeline:before <$end_year-$next_year> ;
	skos:broader <world/$uri> .
";
	#timeline:beginsAtDuration <$uri#start> ;
	#timeline:endsAtDuration <$uri#end> ;
#<$uri#start> a timeline:Interval ;
#	timeline:startsAtDateTime \"$start_year-01-01T00:00:00Z\"^^xsd:dateTime ;
#	timeline:endsAtDateTime \"$start_year-12-31T23:59:59Z\"^^xsd:dateTime .
#<$uri#end> a timeline:Interval ;
#	timeline:startsAtDateTime \"$start_year-12-31T23:59:59\"^^xsd:dateTime ;
#	timeline:endsAtDateTime \"$end_year-12-31T23:59:59Z\"^^xsd:dateTime .
}
