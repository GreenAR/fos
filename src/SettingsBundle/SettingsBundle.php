<?php

namespace SettingsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SettingsBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
