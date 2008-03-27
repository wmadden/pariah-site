<?php
/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

class ErrorController extends Zend_Controller_Action
{
  public function errorAction()
  {
    // Clear the body
    $this->getResponse()->clearBody();
    
    // Clear placeholders
    
    
    $this->view->request = $this->getRequest();
    $errors = $this->_getParam('error_handler');
    $this->view->exception = $errors->exception;
    
    switch ($errors->type) {
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        // 404 error -- controller or action not found
        $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');

        // ... get some output to display...
        break;
      default:
        // application error; display error page, but don't change
        // status code
        break;
    }
  }
}

?>