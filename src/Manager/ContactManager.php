<?php

/*
 * @copyright C UAB NFQ Technologies
 *
 * This Software is the property of NFQ Technologies
 * and is protected by copyright law – it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * Contact UAB NFQ Technologies:
 * E-mail: info@nfq.lt
 * http://www.nfq.lt
 */

declare(strict_types=1);

namespace NFQ\SyliusOmnisendPlugin\Manager;

use NFQ\SyliusOmnisendPlugin\Builder\Request\ContactBuilderDirector;
use NFQ\SyliusOmnisendPlugin\Client\OmnisendClientInterface;
use NFQ\SyliusOmnisendPlugin\Client\Response\Model\ContactSuccess;
use NFQ\SyliusOmnisendPlugin\Model\ContactAwareInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;

class ContactManager implements ContactManagerInterface
{
    /** @var ContactBuilderDirector */
    private $contactBuilderDirector;

    /** @var OmnisendClientInterface */
    private $omnisendClient;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    public function __construct(
        ContactBuilderDirector $contactBuilderDirector,
        OmnisendClientInterface $omnisendClient,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->contactBuilderDirector = $contactBuilderDirector;
        $this->omnisendClient = $omnisendClient;
        $this->customerRepository = $customerRepository;
    }

    public function create(CustomerInterface $customer)
    {
        /** @var CustomerInterface&ContactAwareInterface $customer */
        /** @var ContactSuccess $response */
        $response = $this->omnisendClient->postContact(
            $this->contactBuilderDirector->build($customer)
        );

        if ($response) {
            $customer->setOmnisendContactId($response->getContactID());
            $this->customerRepository->add($customer);
        }
    }

    /** @var CustomerInterface&ContactAwareInterface $customer */
    public function update(CustomerInterface $customer)
    {
        if ($customer->getOmnisendContactId()) {
            $this->omnisendClient->patchContact(
                $customer->getOmnisendContactId(),
                $this->contactBuilderDirector->build($customer)
            );
        } else {
            $this->create($customer);
        }
    }
}
