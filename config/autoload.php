<?php

ClassLoader::addClasses(
    [
        'Contao\WidgetJSON'              => 'system/modules/fiedsch-components/widgets/WidgetJSON.php',
        'Fiedsch\JsonGetterSetterTrait'  => 'system/modules/fiedsch-components/src/Fiedsch/JsonGetterSetterTrait.php',
        'Fiedsch\YamlConfigHelper'       => 'system/modules/fiedsch-components/src/Fiedsch/YamlConfigHelper.php',
    ]
);
