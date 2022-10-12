<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ContainsInArray extends Constraint
{
    public $message = 'You don\'t have right to add "{{ string }}" role';
    public $forbiddenvalues = [];

    #[HasNamedArguments]
    public function __construct(array $forbiddenvalues, array $groups = null, mixed $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->forbiddenvalues = $forbiddenvalues;
    }
}