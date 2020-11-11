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

namespace NFQ\SyliusOmnisendPlugin\Factory\Request;

use NFQ\SyliusOmnisendPlugin\Client\Request\Model\ContactIdentifier;
use NFQ\SyliusOmnisendPlugin\Client\Request\Model\ContactIdentifierChannel;
use NFQ\SyliusOmnisendPlugin\Client\Request\Model\ContactIdentifierChannelValue;
use NFQ\SyliusOmnisendPlugin\Utils\DatetimeFormatter;
use function ucfirst;
use DateTime;

class ContactIdentifierFactory implements ContactIdentifierFactoryInterface
{
    /** @var array */
    private $sendWelcomeMessageConfig;

    private $typesMap = [
        ContactIdentifier::TYPE_EMAIL => 'email',
        ContactIdentifier::TYPE_PHONE => 'sms',
    ];

    public function __construct(array $sendWelcomeMessageConfig)
    {
        $this->sendWelcomeMessageConfig = $sendWelcomeMessageConfig;
    }

    public function create(string $type, string $id, string $status): ContactIdentifier
    {
        $contactIdentifier = new ContactIdentifier();
        $contactIdentifier->setType($type);
        $contactIdentifier->setId($id);
        $contactIdentifier->setSendWelcomeMessage($this->sendWelcomeMessageConfig[$type]);

        $channel = new ContactIdentifierChannel();
        $channelValue = new ContactIdentifierChannelValue();
        $channelValue->setStatus($status);
        $channelValue->setStatusDate(DatetimeFormatter::format(new DateTime()));
        $key = 'set' . ucfirst($this->typesMap[$type]);
        $channel->$key($channelValue);
        $contactIdentifier->addChannel($channel);

        return $contactIdentifier;
    }
}
