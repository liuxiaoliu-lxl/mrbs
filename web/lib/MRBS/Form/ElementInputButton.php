<?php

namespace MRBS\Form;

class ElementInputButton extends ElementInput
{

  public function __construct()
  {
    parent::__construct();
    $this->setAttribute('type', 'button');
  }

}
