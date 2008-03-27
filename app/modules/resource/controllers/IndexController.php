<?php

/**
 * {filename}.php
 * 
 * Copyright (C) 2008, Pariah Software,
 * all rights reserved.
 * 
 * @author William Madden
 */

class Resource_IndexController extends ResourceController
{
  public function preDispatch()
  {
    // Forward to the appropriate ResourceController
    $resource = $this->_getParam('resource');
    
    // If no resource parameter is given, we don't know what to do
    if( empty($resource) )
    {
      throw new ResourceControllerException(
          "No resource type given.",
          1
      );
    }
    
    $this->_forward( $this->_getParam('action'), $resource );
  }
}
