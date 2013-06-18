<?php
	/*
	 * jQuery File Upload Plugin PHP Example 5.7
	 * https://github.com/blueimp/jQuery-File-Upload
	 *
	 * Copyright 2010, Sebastian Tschan
	 * https://blueimp.net
	 *
	 * Licensed under the MIT license:
	 * http://www.opensource.org/licenses/MIT
	 */

	error_reporting( E_ALL | E_STRICT );

	require('upload.class.php');

	if( !isset($_REQUEST['class-name']) ) exit;
	$class_name = $_REQUEST['class-name'];
	$class_file = "class.$class_name.php";
	if( !file_exists( $class_file ) ){
		echo "Class file `$class_file` doesn't exist.";
		exit;
	}

	require('class.sectionuploadhandler.php');
	require_once $class_file;

	if( !class_exists( $class_name ) ){
		echo "Class `$class_name` doesn't exist.";
		exit;
	}
	/** @var $upload_handler SectionUploadHandler */
	$upload_handler = new $class_name();

	header( 'Pragma: no-cache' );
	header( 'Cache-Control: no-store, no-cache, must-revalidate' );
	header( 'Content-Disposition: inline; filename="files.json"' );
	header( 'X-Content-Type-Options: nosniff' );
	header( 'Access-Control-Allow-Origin: *' );
	header( 'Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE' );
	header( 'Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size' );

	switch( $_SERVER['REQUEST_METHOD'] ){
		case 'OPTIONS':
			break;
		case 'HEAD':
		case 'GET':
			$upload_handler->get();
			break;
		case 'POST':
			if( isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE' ){
				$upload_handler->delete();
			} else{
				$upload_handler->post();
			}
			break;
		case 'DELETE':
			$upload_handler->delete();
			break;
		default:
			header( 'HTTP/1.1 405 Method Not Allowed' );
	}
