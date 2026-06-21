<?php

namespace App\Module\Customer\Application\Request;

use Symfony\Component\Validator\Constraints\GroupSequence;

#[GroupSequence(['CustomerCreateRequest'])]
final class CustomerCreateRequest extends AbstractBaseCustomerRequest
{
}
