<?php

/**
 * {filename}.php
 * 
 * Copyright (C) 2008, Pariah Software,
 * all rights reserved.
 * 
 * @author William Madden
 */

class ResourceController extends Zend_Controller_Action
{
  public function init()
  {
    // Identify and retrieve the resource
    $resource_class = $this->_getParam('resource');
    $resource_id = $this->_getParam('id');
    
    $this->resource = Mapper::loadModel( $resource_class, $resource_id );
  }
  
  public function preDispatch()
  {
    // Check that the user has permission to access the resource
    $acl = new ResourceAcl( $this->resource );
    
    $user = null;
    try
    {
      // Get the logged in user
      $user = User::getLoggedInUser();
    }
    // If no user is logged in, create a blank user
    catch( UserException $e )// TODO: ensure that it's the right exception
    {
      $user = new User();
    }
    
    // Get the action
    $action = $this->_getParam('action');
    
    // Check permissions
    // If not permitted, fail
    if( !$acl->isAllowed($user, $action) )
    {
      throw new ResourceControllerException(
          "The requested action ($action) is not permitted for this user.",
          0
      );
    }
  }
  
  public function postDispatch()
  {
    // Decorate
  }
}
