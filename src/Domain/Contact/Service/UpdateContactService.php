<?php

namespace App\Domain\Contact\Service;

use App\Domain\Contact\Entity\Phone;
use App\Domain\Contact\Exception\ContactNotFoundException;
use App\Domain\Contact\Repository\ContactRepositoryInterface;
use App\Domain\Contact\Service\Validator\PhoneNumberValidator;

readonly class UpdateContactService
{
    public function __construct(
        public ContactRepositoryInterface $repository,
        public PhoneNumberValidator $phoneNumberValidator
    ) {
    }

    public function update(string $email, ?string $name = null, array $phones = []): void
    {
        $contact = $this->repository->ofEmail($email);
        if (is_null($contact)) {
            throw new ContactNotFoundException(sprintf('Contact with email: %s was not found', $email));
        }

        if (!is_null($name)) {
            $contact->changeName($name);
        }

        if (!empty($phones)) {
            /*
             * TODO delete if you want to keep the old phone numbers.
             * Remove current phones
             */
            foreach ($contact->getPhones() as $phone) {
                $contact->removePhone($phone);
            }

            // Add new phones
            foreach ($phones as $number) {
                $this->phoneNumberValidator->validate($number);

                $contact->addPhone(new Phone($number));
            }
        }

        $this->repository->update($contact);
    }
}
