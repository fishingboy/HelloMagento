<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AuthorizenetAcceptjs\Gateway\Response;

use Magento\AuthorizenetAcceptjs\Gateway\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * Processes payment information from a response
 *
 * @deprecated Starting from Magento 2.2.11 Authorize.net payment method core integration is deprecated in favor of
 * official payment integration available on the marketplace
 */
class PaymentResponseHandler implements HandlerInterface
{
    /**
     * @var int
     */
    private static $responseCodeHeld = 4;

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @param SubjectReader $subjectReader
     */
    public function __construct(SubjectReader $subjectReader)
    {
        $this->subjectReader = $subjectReader;
    }

    /**
     * @inheritdoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = $this->subjectReader->readPayment($handlingSubject);
        $payment = $paymentDO->getPayment();
        $transactionResponse = $response['transactionResponse'];

        if ($payment instanceof Payment) {
            $payment->setCcLast4($payment->getAdditionalInformation('ccLast4'));
            $payment->setCcAvsStatus($transactionResponse['avsResultCode']);
            $payment->setIsTransactionClosed(false);

            if ($transactionResponse['responseCode'] == self::$responseCodeHeld) {
                $payment->setIsTransactionPending(true)
                    ->setIsFraudDetected(true);
            }
        }
    }
}
