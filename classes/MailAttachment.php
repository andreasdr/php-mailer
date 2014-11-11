<?php
/**
 * Class representing an mail attachment to use with Mail class 
 * @author andreas.drewke
 * @version $Id$
 */
class MailAttachment {

	private $filename;
	private $mime;
	private $data;
	private $length;

	/**
	 * Creates an attachment from file
	 * @param string $file
	 * @param string $mime
	 * @return MailAttachment
	 */
	public static function createFromFile($file, $mime) {
		return new MailAttachment(
			basename($file),
			$mime,
			$data = @file_get_contents($file),
			strlen($data)
		);
	}

	/**
	 * Creates an attachment from string
	 * @param string $file
	 * @param string $mime
	 * @return MailAttachment
	 */
	public static function createFromString($filename, $string, $mime) {
		return new MailAttachment(
			$filename,
			$mime,
			$string,
			strlen($string)
		);
	}

	/**
	 * Private constructor
	 * @param string $filename
	 * @param string $mime
	 * @param string $data
	 * @param string $length
	 */
	private function __construct($filename, $mime, $data, $length) {
		$this->filename = $filename;
		$this->mime = $mime;
		$this->data = $data;
		$this->length = $length;
	}

	/**
	 * @return string
	 */
	public function getFilename() {
		return $this->filename;
	}

	/**
	 * @return string
	 */
	public function getMime() {
		return $this->mime;
	}

	/**
	 * @return string
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * @return int
	 */
	public function getLength() {
		return $this->length;
	}

}

?>