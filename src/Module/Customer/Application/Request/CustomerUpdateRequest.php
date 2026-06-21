<?php

namespace App\Module\Customer\Application\Request;

use Symfony\Component\Validator\Constraints\GroupSequence;

#[GroupSequence(['CustomerUpdateRequest'])]
final class CustomerUpdateRequest extends AbstractBaseCustomerRequest
{
}
