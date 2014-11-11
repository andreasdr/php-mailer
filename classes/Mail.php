<?php

require_once './libs/html2text/html2text.php';

/**
 * Class to compose and send utf8 only mails with attachments
 * @author andreas.drewke
 * @version $Id$
 *
 */
class Mail {

	const CONTENTTYPE_HTML = 1;
	const CONTENTTYPE_PLAIN = 0;

	private $sender;
	private $receivers;
	private $reply = false;
	private $cc = false;
	private $bcc = false;
	private $contentType;
	private $subject;
	private $content;
	private $attachments;

	/**
	 * Compose a mail
	 * @param string $sender or false when using the default
	 * @param string[] $receivers
	 * @param int $contentType (see Mail::CONTENTTYPE_*) 
	 * @param string $subject
	 * @param string $content
	 * @param MailAttachment[] $attachments
	 * @return Mail
	 */
	public static function composeMail($sender, $receivers, $contentType = self::CONTENTTYPE_PLAIN, $subject = false, $content = false, $attachments = array()) {
		return new Mail(
			$sender,
			$receivers,
			$contentType,
			$subject,
			$content,
			$attachments
		);
	}

	/**
	 * Private constructor
	 * @param string $sender
	 * @param string[] $receivers
	 * @param int $contentType (see Mail::CONTENTTYPE_*)
	 * @param string $subject
	 * @param string $content
	 */
	private function __construct($sender, $receivers, $contentType, $subject, $content, $attachments) {
		$this->sender = $sender;
		$this->receivers = $receivers;
		$this->contentType = $contentType;
		$this->subject = $subject;
		$this->content = $content;
		$this->attachments = $attachments;
	}

	/**
	 * @return string
	 */
	public function getSender() {
		return $this->sender;
	}

	/**
	 * @param string $sender
	 */
	public function setSender($sender) {
		$this->sender = $sender;
	}

	/**
	 * @return string[]
	 */
	public function getReceivers() {
		return $this->receivers;
	}

	/**
	 * @param string[] $receivers
	 */
	public function setReceivers($receivers) {
		$this->receivers = $receivers;
	}

	/**
	 * @return string
	 */
	public function getReply() {
		return $this->reply;
	}

	/**
	 * @param string $reply
	 */
	public function setReply($reply) {
		$this->reply = $reply;
	}

	/**
	 * @return string[]
	 */
	public function getCC() {
		return $this->cc;
	}

	/**
	 * @param string[] $cc
	 */
	public function setCC($cc) {
		$this->cc = $cc;
	}
	
	/**
	 * @return string[]
	 */
	public function getBCC() {
		return $this->bcc;
	}

	/**
	 * @param string[] $bcc
	 */
	public function setBCC($bcc) {
		$this->bcc = $bcc;
	}

	/**
	 * @return int (see Mail::CONTENTTYPE_*)
	 */
	public function getContentType() {
		return $this->contentType;
	}

	/**
	 * @return bool if content type is valid 
	 */
	private function isContentTypeValid() {
		switch ($this->contentType) {
			case(self::CONTENTTYPE_PLAIN):
			case(self::CONTENTTYPE_HTML):
				return true;
			default:
				return false;
		}
	}

	/**
	 * @return string or false
	 */
	public function getContentTypeString() {
		switch ($this->contentType) {
			case(self::CONTENTTYPE_PLAIN):
				return 'text/plain';
			case(self::CONTENTTYPE_HTML):				
				return 'text/html';
			default:
				return false;
		}
	}

	/**
	 * @param int $contentType (see Mail::CONTENTTYPE_*)
	 */
	public function setContentType($contentType) {
		switch($contentType) {
			case(self::CONTENTTYPE_PLAIN):
				$this->contentType = $contentType;
				break;
			case(self::CONTENTTYPE_HTML):
				$this->contentType = $contentType;
				break;
			default:
		}
	}

	/**
	 * @return string
	 */
	public function getSubject() {
		return $this->subject;
	}

	/**
	 * @param string $subject
	 */
	public function setSubject($subject) {
		$this->subject = $subject;
	}

	/**
	 * @return string
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * @param string $content
	 */
	public function setContent($content) {
		$this->content = $content;
	}

	/**
	 * @return int
	 */
	public function haveAttachments() {
		return count($this->attachments) > 0;
	}

	/**
	 * @return MailAttachment[]
	 */
	public function getAttachments() {
		return $this->attachments;
	}

	/**
	 * @param MailAttachment[] $attachments
	 */
	public function setAttachments($attachments) {
		$this->attachments = $attachments;
	}

	/**
	 * Composes alternative mail
	 * @param string &$headers
	 * @param string &$content
	 * @param bool $asPart
	 */
	private function composeAlternativePart($boundary, &$headers, &$content, $asPart) {
		$boundary.= '-alternative';

		if ($asPart == true) {
			$content.= 'Content-Type: multipart/alternative; boundary="' . $boundary . '"' . "\n\n";
		} else {
			$headers.= 'Content-Type: multipart/alternative; boundary="' . $boundary . '"' . "\n";
		}

		// set up content
		$content.= 'This is a multi-part message in MIME format...' . "\n";
		$content.= "\n";

		// message plain text
		$content.= '--' . $boundary . "\n";
		$content.= 'Content-Type: text/plain; charset="utf-8"' . "\n";
		$content.= 'Content-Disposition: inline' . "\n";
		$content.= 'Content-Transfer-Encoding: quoted-printable' . "\n";
		$content.= 'MIME-Version: 1.0' . "\n";
		$content.= "\n";

		// we only translate paragraph end and break row for now
		//	try html2text first
		$tmp = convert_html_to_text($this->content);
		//	use strip_tags as fallback, will look ugly anyways
		if ($tmp === false) $tmp = strip_tags($tmp);

		//
		$content.= self::quoted_printable_encode($tmp, true) . "\n";

		// message html
		$content.= '--' . $boundary . "\n";
		$content.= 'Content-Type: text/html; charset="utf-8"' . "\n";
		$content.= 'Content-Disposition: inline' . "\n";
		$content.= 'Content-Transfer-Encoding: quoted-printable' . "\n";
		$content.= "\n";
		$content.= self::quoted_printable_encode($this->content, true) . "\n";

		//
		$content.= '--' . $boundary . '--' . "\n\n";
	}

	/**
	 * Sends the mail
	 * @return bool success
	 */
	public function send() {
		// check for valid content type
		if ($this->isContentTypeValid() == false) {
			return false;
		}

		// construct headers & content
		$headers = '';
		$content = '';

		// sender
		if ($this->sender != false) {
			$headers .= 'From: ' . $this->sender . "\n";
		}
		// reply
		if ($this->reply != false) {
			$headers .= 'Reply-To: ' . $this->reply . "\n";
		}
		// cc
		if ($this->cc != false) {
			$headers .= 'Cc: ' . implode(',', $this->cc) . "\n";
		}
		// bcc
		if ($this->bcc != false) {
			$headers .= 'Bcc: ' . implode(',', $this->bcc) . "\n";
		}

		$headers.= 'MIME-Version: 1.0' . "\n";

		// do we have attachments, send multipart messages
		if ($this->haveAttachments()) {
			$boundaryBase = md5(uniqid(mt_rand(), 1));
			$boundary =  $boundaryBase . '-mixed';

			$headers.= 'Content-Type: multipart/mixed; boundary="' . $boundary . '"' . "\n\n";

			// set up content
			$content.= 'This is a multi-part message in MIME format...' . "\n";
			$content.= "\n";

			// plain text only?
			if ($this->contentType == self::CONTENTTYPE_PLAIN) {
				$content.= '--' . $boundary . "\n";
				$content.= 'Content-Type: ' . $this->getContentTypeString() . '; charset="utf-8"' . "\n";
				$content.= 'Content-Disposition: inline' . "\n";
				$content.= 'Content-Transfer-Encoding: quoted-printable' . "\n";
				$content.= 'MIME-Version: 1.0' . "\n";
				$content.= "\n";
				$content.= self::quoted_printable_encode($this->content, true) . "\n";
			} else {
				$content.= '--' . $boundary . "\n";
				// plain text + html
				$this->composeAlternativePart($boundaryBase, $headers, $content, true);
			}

			// attachments
			foreach($this->attachments as $attachment) {
				$content.= '--' . $boundary . "\n";
				$content.= 'Content-Type: ' . $attachment->getMime() . '; name="' . $attachment->getFilename() . '"' . "\n";
				$content.= 'Content-Disposition: attachment; filename="' . $attachment->getFilename() . '"' . "\n";
				$content.= 'Content-Transfer-Encoding: base64' . "\n";
				$content.= "\n";
				$content.= chunk_split(base64_encode($attachment->getData()));
			}

			// end of part
			$content.= '--' . $boundary . '--' . "\n";
		} else {
			// plain text only?
			if ($this->contentType == self::CONTENTTYPE_PLAIN) {
				// setup headers
				$headers.= 'Content-Type: ' . $this->getContentTypeString() . '; charset="utf-8"' . "\n";
				$headers.= 'Content-Disposition: inline' . "\n";
				$headers.= 'Content-Transfer-Encoding: quoted-printable';
	
				// setup message
				$content = self::quoted_printable_encode($this->content, true);
			} else {
				$this->composeAlternativePart(md5(uniqid(mt_rand(), 1)), $headers, $content, false);
			}
		}

		// send mail
		return @mail(
			implode(',', $this->receivers),
			'=?utf-8?Q?' . self::quoted_printable_encode($this->subject, false) . '?=',
			$content,
			$headers
		);
	}

	/**
	 * Verifies that an email is valid.
	 * @param string $email Email address to verify.
	 * @return bool
	 */
	public static function isEmailValid($email) {
		// Test for invalid characters
		if (!preg_match( '|^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]{2,})+$|i', $email)) {
			return false;
		}

		// mail is valid address
		return true;
	}

	/**
	 * Function to encode quoted printable strings, compatible with outlook, found at http://stackoverflow.com/
	 * @param string $txt
	 * @param bool body
	 */
	private static function quoted_printable_encode($txt, $body) {
	    $tmp="";
	    $line="";
	    for ($i=0;$i<strlen($txt);$i++) {
	        if (($txt[$i]>='a' && $txt[$i]<='z') || ($txt[$i]>='A' && $txt[$i]<='Z') || ($txt[$i]>='0' && $txt[$i]<='9'))
	            $line.=$txt[$i];
	        else
	            $line.="=".sprintf("%02X",ord($txt[$i]));
	        if ($body && strlen($line)>=75) {
	            $tmp.="$line=\n";
	            $line="";
	        }
	    }
	    $tmp.="$line";
	    if ($body) {
	    	$tmp.= "\n";
	    }
	    return $tmp;
	}

}

?>