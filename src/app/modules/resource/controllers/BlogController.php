<?php

/**
 * {filename}.php
 * 
 * Copyright (C) 2008, Pariah Software,
 * all rights reserved.
 * 
 * @author William Madden
 */

class Resource_BlogController extends ResourceController
{
  public function viewAction()
  {
    $blog_id = $this->_getParam('id', null);
    $article_id = $this->_getParam('article', null);
    
    // If not given blog id, redirect to error
    if( $blog_id == null )
      die("no blog ID");
    
    // Get a blog mapper
    $mapper = Mapper::getMapper('Blog');

    // Load the blog
    $blog = $mapper->loadModel( array('id' => $blog_id) );
    
    // Load its components (articles)
    $mapper->loadComponents($blog);
    
    // Set variables in view
    $this->view->blog = $blog;
    if( $article_id != null )
      $this->view->article = $article_id;
  }
}
