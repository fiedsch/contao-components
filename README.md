# Components for Contao


## Widgets


### JSON widget

The `jsonWidget` can be used in DCA files to create a text field that contains a JSON string.
While saving it will be checked if that sting is valid JSON. 
The widget displays the JSON string with `JSON_PRETTY_PRINT` so that checks by the reader are are easier.
  

#### Example: extending Members

```php
$GLOBALS['TL_DCA']['tl_member']['fields']['json_data'] = [
   'inputType' => 'jsonWidget',
   'label'     => &$GLOBALS['TL_LANG']['tl_member']['json_data'],
   'eval'      => ['tl_style'=>'long', 'decodeEntities'=>true, 'allowHtml'=>true ], 
   'sql'       => "blob NULL",
 ];
 
 // Add json_data to $GLOBALS['TL_DCA']['tl_member']['palettes']['default'] 
 // where ever you like
 ```
Other valid options in `eval` are the same as for `textarea`s (as `WidgetJSON` extends `TextArea`), 
except that setting `rte` will be ignored because the editors provided do not make sense here. 


#### How to use that?

Extend `tl_member` as in the above example. Then create an `ExtendedMemberModel` that 
extends Contao's `MemberModel`. In the magic methodd `__set()` and `_get` you can intercept
the "fields" stored in `json_data`. The `Fiedsch\JsonGetterSetterTrait` takes care of that:

```php
// models/ExtendedMemberModel.php
namespace Contao;

class ExtendedMemberModel extends \MemberModel
{
    // let __set() and __get take care of the JSON data
    use \Fiedsch\JsonGetterSetterTrait;

  /**
    * The column name we selected for the `jsonWidget` in the example above
    * @var string
    */
    protected static $strJsonColumn = 'json_data';

}
```

```php
// config/config.php
$GLOBALS['TL_MODELS']['tl_member'] = 'Contao\ExtendedMemberModel';
```

```php
// config/autoload.php
// ...
ClassLoader::addClasses(
    [
        // ...
        'Contao\ExtendedMemberModel' => 'system/modules/your_extension/models/ExtendedMemberModel.php',
        // ...
    ]
);
// ...
```

#### And finally ...

```php
$member = \ExtendedMemberModel::findById(42);

// access fields columns created by contao's default DCA
printf("read member %s %s\n", $member->firstname, $member->lastname);

// access a field stored in our JSON data column
printf("transparently accessing a field from the JSON data ... '%s'\n", $member->whatever);

// set values and store in database
$member->a_key_for_a_scalar_value = "fourtytwo";
$member->key_for_an_array = ['an','array','containing','some','strings'];
$member->save();
```

## Helper Classes

### Reading YAML config files
 
 ```php
use Fiedsch\YamlConfigHelper;

$defaults = [
    'messages' => [
                    'de' => 'Guten Tag',
                    'fr' => 'Bonjour',
                    'en' => 'Hello',
                   ]
            ];
$config = new YamlConfigHelper('files/config/config.yml', $defaults);
```
If `files/config/config.yml` does not exist it will be created with 
the data specified in (the optional parameter) `$defaults`:
```yaml
messages:
    de: 'Guten Tag'
    fr: 'Bonjour'
    en: 'Hello'
``` 
Use the `YamlConfigHelper` instance `$config` like so:
```php
$config->getEntry('data.messages.de'); // 'Guten Tag'
$config->getEntry('data.messages.es'); // null
```
Let's say that as expected `files/config/config.yml` exists and contains  
```yaml
   messages:
       de: 'Guten Morgen'
       fr: 'Bonjour'
       en: 'Good Morning'
 ```
You would get  
```php
$config->getEntry('data.messages.de'); // 'Guten Morgen'
$config->getEntry('data.messages.es'); // null
```
with the data in `$defaults` being ignored. There will be no merge of what is read from 
the config file and the specified `$defaults`!

For details using Symfony's `ExpressionLanguage` see 
http://symfony.com/doc/current/components/expression_language.html

#### Specifying a default

With the config data as above:

```php
$config->getEntry('data.messages.es'); // null
$config->getEntry('data.messages.es', "¡buenos días!"); // "¡buenos días!"
```

#### Data types

withe the configuration file as above
```php
$config->getEntry('data.messages');
/*
object(stdClass)#233 (3) {
  ["de"]=>
  string(12) "Guten Morgen"
  ["fr"]=>
  string(7) "Bonjour"
  ["en"]=>
  string(12) "Good Morning"
}
*/
```
returns a `stdClass` Instance. If you need the data as an `array`, you have to type cast to `array`
```php
(array)$config->getEntry('data.messages');
/*
array(3) {
  ["de"]=>
  string(12) "Guten Morgen"
  ["fr"]=>
  string(7) "Bonjour"
  ["en"]=>
  string(12) "Good Morning"
}
*/
```
