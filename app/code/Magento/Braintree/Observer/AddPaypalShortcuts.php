<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Braintree\Observer;

use Magento\Framework\Event\Observer;
use Magento\Catalog\Block\ShortcutButtons;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class AddPaypalShortcuts
 */
class AddPaypalShortcuts implements ObserverInterface
{
    /**
     * Alias for mini-cart block.
     */
    private static $paypalMinicartAlias = 'mini_cart';

    /**
     * Alias for shopping cart page.
     */
    private static $paypalShoppingcartAlias = 'shopping_cart';

    /**
     * @var string[]
     */
    private $buttonBlocks;

    /**
     * @param string[] $buttonBlocks
     */
    public function __construct(array $buttonBlocks = [])
    {
        $this->buttonBlocks = $buttonBlocks;
    }

    /**
     * Add Braintree PayPal shortcut buttons
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        // Remove button from catalog pages
        if ($observer->getData('is_catalog_product')) {
            return;
        }

        /** @var ShortcutButtons $shortcutButtons */
        $shortcutButtons = $observer->getEvent()->getContainer();

        if ($observer->getData('is_shopping_cart')) {
            $shortcut = $shortcutButtons->getLayout()
                ->createBlock($this->buttonBlocks[self::$paypalShoppingcartAlias]);
        } else {
            $shortcut = $shortcutButtons->getLayout()
                ->createBlock($this->buttonBlocks[self::$paypalMinicartAlias]);
        }

        $shortcutButtons->addShortcut($shortcut);
    }
}
