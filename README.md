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