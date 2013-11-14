<?php

// Check for existing index.html file in templates css-compiled folder. If not exist, create the file, folder will be created automatically
$indexFile = NRDKPATH_LIBRARIES . '/css-compiled/index.html';
if ( !file_exists($indexFile) ) {
	$buffer = '<!DOCTYPE html><title></title>' . "\n"
		. 'NRDKPATH_LIBRARIES: ' . NRDKPATH_LIBRARIES . "\n"
		. ' - NRDKPATH_TESTS: ' . NRDKPATH_TESTS . "\n"
		. ' - NRDKPATH_BASE: ' . NRDKPATH_BASE . "\n"
		. ' - NRDKPATH_ROOT: ' . NRDKPATH_ROOT . "\n"
	;
	file_put_contents($indexFile, $buffer);
}

	function compile( $fileIn, $fileNameOut = 'compiles.css', $lessVariables = false )
	{
		// Require Lessc
		require_once(NRDKPATH_LIBRARIES . '/compiler/lessc.inc.php');
		$lessc = new lessc;

		$lessc->setFormatter( 'compressed' );

		if ($lessVariables) {
			$lessc->setVariables($this->lessVariables);
		}

		$fileOut = NRDKPATH_LIBRARIES . '/css-compiled/' . $fileNameOut;

//		$lessc->compileFile( $fileIn, $fileOut );

		return $lessc->checkedCompile( $fileIn, $fileOut );
	}

$lessVariables = array();
$lessVariables = false;

$fileIn = NRDKPATH_LIBRARIES . '/assets/bootstrap/bootstrap.less';

$deb = compile( $fileIn, $fileNameOut, $lessVariables );












$debFile = NRDKPATH_LIBRARIES . '/deb.c';
	file_put_contents($debFile, $deb);
}