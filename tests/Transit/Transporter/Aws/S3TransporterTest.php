<?php

namespace Transit\Transporter\Aws;

use Transit\File;
use Transit\Test\TestCase;
use \Exception;

class S3TransporterTest extends TestCase {

	/**
	 * Test that uploading a file to S3 returns a URL and deleting the file via the URL works.
	 */
	public function testTransportAndDelete() {
		$object = new S3Transporter(AWS_ACCESS, AWS_SECRET, array(
			'bucket' => S3_BUCKET,
			'region' => S3_REGION
		));

		try {
			if ($response = $object->transport(new File($this->baseFile))) {
				$this->assertEquals($response, sprintf('https://s3.amazonaws.com/%s/%s', S3_BUCKET, basename($this->baseFile)));
			} else {
				$this->assertTrue(false);
			}
		} catch (Exception $e) {
			$this->assertTrue(false);
		}

		if (isset($response)) {
			$this->assertTrue($object->delete($response));
		}
	}

	/**
	 * Test that parsing S3 URLs returns the bucket and key.
	 */
	public function testParseUrl() {
		$object = new S3Transporter(AWS_ACCESS, AWS_SECRET, array(
			'bucket' => S3_BUCKET,
			'region' => S3_REGION
		));

		$this->assertEquals($object->parseUrl('filename.jpg'), array(
			'bucket' => S3_BUCKET,
			'key' => 'filename.jpg'
		));

		$this->assertEquals($object->parseUrl('https://s3.amazonaws.com/bucket1/filename.jpg'), array(
			'bucket' => 'bucket1',
			'key' => 'filename.jpg'
		));

		$this->assertEquals($object->parseUrl('https://bucket2.s3.amazonaws.com/filename.jpg'), array(
			'bucket' => 'bucket2',
			'key' => 'filename.jpg'
		));

		$this->assertEquals($object->parseUrl('https://s3.amazonaws.com/bucket1/test/filename.jpg'), array(
			'bucket' => 'bucket1',
			'key' => 'test/filename.jpg'
		));

		$this->assertEquals($object->parseUrl('https://bucket2.s3.amazonaws.com/some/folder/filename.jpg'), array(
			'bucket' => 'bucket2',
			'key' => 'some/folder/filename.jpg'
		));
	}

	/**
	 * Test that exceptions are thrown if settings are missing.
	 */
	public function testExceptionHandling() {
		try {
			$object = new S3Transporter(AWS_ACCESS, AWS_SECRET, array(
				'bucket' => S3_BUCKET
			));

			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		try {
			$object = new S3Transporter(AWS_ACCESS, AWS_SECRET, array(
				'region' => S3_REGION
			));

			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}
	}

}
