<?php

namespace SiteAudit\AuditResponse;

use SiteAudit\Executor\DoesNotApplyException;
use SiteAudit\Executor\ResultException;
use SiteAudit\Check\Check;

class AuditResponse {

  const AUDIT_NA = -1;
  const AUDIT_SUCCESS = 0;
  const AUDIT_WARNING = 1;
  const AUDIT_FAILURE = 2;
  const AUDIT_ERROR = 3;

  protected $check;

  protected $status;

  public $exception;

  public function __construct(Check $check) {
    $this->check = $check;
  }

  /**
   * AuditResponse to string.
   */
  public function __toString() {
    try {
      switch ($this->status) {
        case self::AUDIT_SUCCESS :
          return '<info>' . $this->getMessage('success') . '</info>';
        case self::AUDIT_NA :
          return '<info>' . $this->getMessage('na') . '</info>';
        case self::AUDIT_WARNING :
          return '<comment>' . $this->getMessage('warning') . '</comment>';
        case self::AUDIT_FAILURE :
          return '<error>' . $this->getMessage('failure') . '</error>';
        case self::AUDIT_ERROR :
        default :
          return '<error>' . $this->getMessage('exception') . '</error>';
      }
    }
    catch (\Exception $e) {
      var_dump($e->getMessage());
    }
    return 'doh';
  }

  protected function getMessage($type = 'success') {
    switch ($type) {
      case 'success':
      case 'not_available':
      case 'warning':
      case 'failure':
      case 'exception':
      case 'notice':
        $message = $this->check->getInfo()->{$type};
        break;

      case 'na':
        return $this->getMessage('not_available');

      default:
        throw new \Exception("Cannot format message. Unknown type $type.");
    }

    $tokens = $this->check->getTokens();
    if ($type == 'exception') {
      $tokens[':exception'] = $this->exception->getMessage();
    }
    return strtr($message, $tokens);
  }

  public function getDescription() {
    return $this->check->getInfo()->description;
  }

  public function getRemediation() {
    return $this->check->getInfo()->remediation;
  }

  public function setStatus($status) {
    $this->status = $status;
  }

  public function getStatus() {
    return $this->status;
  }

  public function getTitle() {
    return $this->check->getInfo()->title;
  }
}
