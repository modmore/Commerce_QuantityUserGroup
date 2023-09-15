<?php

namespace modmore\Commerce_QuantityUserGroup;

use modmore\Commerce\Admin\Configuration\About\ComposerPackages;
use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Events\Admin\GeneratorEvent;
use modmore\Commerce\Events\Admin\PageEvent;
use modmore\Commerce\Events\Admin\PriceTypes;
use modmore\Commerce\Modules\BaseModule;
use modmore\Commerce\Dispatcher\EventDispatcher;

require_once dirname(__DIR__) . '/vendor/autoload.php';

class Module extends BaseModule
{
    public function getName()
    {
        $this->adapter->loadLexicon('commerce_quantityusergroup:default');
        return $this->adapter->lexicon('commerce_quantityusergroup');
    }

    public function getAuthor()
    {
        return 'modmore';
    }

    public function getDescription()
    {
        return $this->adapter->lexicon('commerce_quantityusergroup.description');
    }

    public function initialize(EventDispatcher $dispatcher)
    {
        // Load our lexicon
        $this->adapter->loadLexicon('commerce_quantityusergroup:default');

        // Add the xPDO package, so Commerce can detect the derivative classes
//        $root = dirname(__DIR__);
//        $path = $root . '/model/';
//        $this->adapter->loadPackage('commerce_quantityusergroup', $path);

        // Add template path to twig
//        $root = dirname(__DIR__);
//        $this->commerce->view()->addTemplatesPath($root . '/templates/');

        // Add composer libraries to the about section
        $dispatcher->addListener(\Commerce::EVENT_DASHBOARD_GET_PRICE_TYPES, [$this, 'addPriceType']);
        $dispatcher->addListener(\Commerce::EVENT_DASHBOARD_INIT_GENERATOR, [$this, 'registerCssHack']);
    }

    public function addPriceType(PriceTypes $event): void
    {
        $event->addPriceType(QuantityUserGroup::class);
    }

    public function registerCssHack(GeneratorEvent $event): void
    {
        $generator = $event->getGenerator();

        $generator->addHTMLFragment(<<<HTML
<style>
.c .c-price-type--quantity-usergroup {
  flex-basis: 100%;
}
[data-price-type="modmore\\\\Commerce_QuantityUserGroup\\\\QuantityUserGroup"] .c-price-type--fields .c-price-type--quantity-usergroup  {
  display: none;
 }

[data-price-type="modmore\\\\Commerce_QuantityUserGroup\\\\QuantityUserGroup"] .c-price-type--fields:first-of-type .c-price-type--quantity-usergroup  {
    display: initial;
}
</style>
HTML
        );
    }
}
