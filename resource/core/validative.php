<?php

namespace Resource\Core;

interface Validative
{
    // The Validator interface for Mysidia Adoptables
    public function validate();
    public function setError($error, $overwrite);

}
