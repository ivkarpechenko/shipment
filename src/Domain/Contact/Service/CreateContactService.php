<?php

namespace App\Domain\Contact\Service;

use App\Domain\Contact\Entity\Contact;
use App\Domain\Contact\Entity\Phone;
use App\Domain\Contact\Exception\ContactAlreadyCreatedException;
use App\Domain\Contact\Exception\PhoneNumbersRequiredException;
use App\Domain\Contact\Repository\ContactRepositoryInterface;
use App\Domain\Contact\Service\Validator\EmailValidator;
use App\Domain\Contact\Service\Validator\PhoneNumberValidator;

readonly class CreateContactService
{
    public function __construct(
        public ContactRepositoryInterface $repository,
        public PhoneNumberValidator $phoneNumberValidator,
        public EmailValidator $emailValidator
    ) {
    }

    public function create(string $email, string $name, array $phones): void
    {
        $this->emailValidator->validate($email);

        $contact = $this->repository->ofEmail($email);
        if (!is_null($contact)) {
            throw new ContactAlreadyCreatedException(sprintf('Contact with email: %s already created', $email));
        }

        $contact = new Contact($email, $name);

        if (empty($phones)) {
            throw new PhoneNumbersRequiredException('The phones field cannot be empty');
        }

        foreach ($phones as $number) {
            $this->phoneNumberValidator->validate($number);

            $contact->addPhone(new Phone($number));
        }

        $this->repository->create($contact);
    }
}
