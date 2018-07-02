<?php

namespace Vivo\TwitterBootstrapBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class VivoTwitterBootstrapBundle extends Bundle
{
    public function getParent()
    {
        return 'MopaBootstrapBundle';
    }
}
