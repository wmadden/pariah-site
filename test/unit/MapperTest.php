<?php

/**
 * MapperTest.php
 * 
 * Tests the base Mapper class.
 * 
 * Copyright (C) 2008, Pariah Software,
 * all rights reserved.
 * 
 * @author William Madden
 */
 
require_once 'PHPUnit/Framework.php';
require_once '../../src/library/Pariah/Model.php';

class ModelTest extends PHPUnit_Framework_TestCase
{
  protected $model = null;
  
  public function setUp()
  {
    $this->model = new SomeModel();
  }
  
  public function tearDown()
  {
    unset($this->model);
  }
}