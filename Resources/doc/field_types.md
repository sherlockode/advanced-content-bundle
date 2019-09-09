Field Types
===========

## Simple field types 

| Name | Code | ContentType Options | Field Raw value |
| ---- | ---- | ------- | ------- |
| Text | text | <ul><li>minLength</li><li>maxLength</li></ul> | string |
| Text Area | textarea | <ul><li>minLength</li><li>maxLength</li><li>nbRows</li></ul> | string |
| Password | password | <ul><li>minLength</li><li>maxLength</li></ul> | string |
| Phone number | phone | <ul><li>minLength</li><li>maxLength</li></ul> | string |

## Choice field types 

| Name | Code | ContentType Options | Field Raw value |
| ---- | ---- | ------- | ------- |
| Select | select | <ul><li>is_multiple : boolean</li><li>choices : array of available choices</li></ul> | array containing the selected choice(s) |
| Checkbox | checbox | <ul><li>choices : array of available choices</li></ul> | array containing the selected choice(s) |
| Radio | radio | <ul><li>choices : array of available choices</li></ul> | array containing the selected choice |
| Boolean | boolean |  | array containing the selected choice ("Yes" / "No") |

## Layout field types 

| Name | Code | ContentType Options | Field Raw value |
| ---- | ---- | ------- | ------- |
| Flexible | flexible |  | empty string |
| Repeater | repeater |  | empty string |

## Other field types 

| Name | Code | ContentType Options | Field Raw value |
| ---- | ---- | ------- | ------- |
| Wysiwyg Editor | wysiwyg | <ul><li>toolbar : basic / standard / full</li></ul> | string |
| Number | number | <ul><li>minValue</li><li>maxValue</li></ul> | string |
| Email | email |  | string |
| Link | link | <ul><li>target : _blank / _self</li></ul> | array indexed by "url", "title" |
| Iframe | iframe | <ul><li>width</li><li>height</li></ul> | array indexed by "href" |
| Message | message | <ul><li>message : message displayed on the Content form</li></ul> | empty string |
| Color Picker | color |  | string |
| Date Picker | date | <ul><li>time : includes time (1 => yes / 0 => no)</li></ul> | string |
| File upload | file |  | array indexed by "src" (full url to the file resource), "title" |
| Image upload | image |  | array indexed by "src" (full url to the image resource), "alt" |

## Custom FieldType

You can create your own FieldType

### FieldType Class

Your class must implement \Sherlockode\AdvancedContentBundle\FieldType\FieldTypeInterface

It can inherit \Sherlockode\AdvancedContentBundle\FieldType\AbstractFieldType 
which already define some of the interface methods for standard behavior.

You will need to define a unique code for your field.


### FieldType Service

```yaml
# config/services.yaml
app.field_type.custom_field_type:
    class: App\FieldType\CustomFieldType
    tags:
        - { name: sherlockode_advanced_content.fieldtype }
```
