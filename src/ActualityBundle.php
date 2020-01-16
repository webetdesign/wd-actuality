<?php

namespace WebEtDesign\ActualityBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use WebEtDesign\ActualityBundle\DependencyInjection\ActualityExtension;

class ActualityBundle extends Bundle
{
    /**
     * @inheritDoc
     */
    public function getContainerExtension()
    {
        return new ActualityExtension();
    }

}
