<?php

declare(strict_types=1);

if ( $argc < 3 ) {
	fwrite( STDERR, "Usage: php tests/php/check-coverage.php <clover.xml> <min_percent>\n" );
	exit( 1 );
}

$clover_path = $argv[1];
$minimum     = (float) $argv[2];

if ( ! file_exists( $clover_path ) ) {
	fwrite( STDERR, "Coverage file not found: {$clover_path}\n" );
	exit( 1 );
}

$xml = simplexml_load_file( $clover_path );
if ( false === $xml ) {
	fwrite( STDERR, "Invalid clover XML: {$clover_path}\n" );
	exit( 1 );
}

$project_metrics = $xml->project->metrics;
if ( ! $project_metrics ) {
	fwrite( STDERR, "Missing project metrics in clover XML.\n" );
	exit( 1 );
}

$statements = (int) $project_metrics['statements'];
$covered    = (int) $project_metrics['coveredstatements'];
$lines_pct  = 0.0;

if ( $statements > 0 ) {
	$lines_pct = ( $covered / $statements ) * 100;
}

$rounded = round( $lines_pct, 2 );

echo "PHP line coverage: {$rounded}% (min {$minimum}%)" . PHP_EOL;

if ( $lines_pct < $minimum ) {
	fwrite( STDERR, "PHP coverage threshold not met.\n" );
	exit( 1 );
}
