<?php

namespace WebEtDesign\ActualityBundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use WebEtDesign\ActualityBundle\DependencyInjection\ActualityExtension;

class ActualityBundle extends Bundle
{
    /**
     * @inheritDoc
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new ActualityExtension();
    }

}
