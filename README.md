# Components for Contao

## Widgets

### JSON widget

The `jsonWidget` can be used in DCA files to create a text field that contains a JSON string.
While saving it will be checked if that sting is valid JSON. 
The widget displays the JSON string with `JSON_PRETTY_PRINT` so that checks by the reader are are easier.
  
#### Example

```php
$GLOBALS['TL_DCA']['tl_member']['fields']['json_data'] = [
   'inputType' => 'jsonWidget',
   'label'     => &$GLOBALS['TL_LANG']['tl_member']['json_data'],
   'eval'      => ['tl_style'=>'long', 'decodeEntities'=>true, 'allowHtml'=>true ], 
   'sql'       => "blob NULL",
 ];
 ```
Other valid options in `eval` are the same as for `textarea`s (as `WidgetJSON` extends `TextArea`), 
except that setting `rte` will be ignored because the editors provided do not make sense here. 


