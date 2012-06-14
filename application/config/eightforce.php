<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| 8force
|--------------------------------------------------------------------------
|
| file_storage_system
| S3 | local | network
| S3 = Amazon S3, local = local folder, network = network folder
|
| temp_folder
| path to a folder to store files temporary for parsing/uploading, must end the path with a /
|
| file_folder
| if Local or Network folder is selected, enter the path to that folder, must end the path with a /
|
*/
$config['file_storage_system'] = 'S3';
$config['temp_folder'] = '../tcode-tmp/';
$config['file_folder'] = 'file/';

/*
|--------------------------------------------------------------------------
| Sendgrid
|--------------------------------------------------------------------------
|
|
|
*/
$config['sendgrid_api_user'] = 'tcsteam';
$config['sendgrid_api_key'] = 'express0810';

/*
|--------------------------------------------------------------------------
| AWS S3
|--------------------------------------------------------------------------
|
| s3_use_ssl
| Run this over HTTP or HTTPS. HTTPS (SSL) is more secure but can cause problems
| on incorrectly configured servers.
|
| s3_verify_peer
| Enable verification of the HTTPS (SSL) certificate against the local CA
| certificate store.
|
| s3_access_key
| Your Amazon S3 access key.
|
| s3_secret_key
| Your Amazon S3 Secret Key.
|
| s3_bucket
| S3 bucket to use
|
*/

$config['s3_use_ssl'] = TRUE;
$config['s3_verify_peer'] = TRUE;
$config['s3_access_key'] = 'AKIAIU6Q5FU7II2GKW7A';
$config['s3_secret_key'] = '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';
$config['s3_bucket'] = '8f-sg';