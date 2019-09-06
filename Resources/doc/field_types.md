Field Types
===========

## Simple field types 

| Name | Code | ContentType Options |
| ---- | ---- | ------- |
| Text | text | <ul><li>minLength</li><li>maxLength</li></ul> |
| Text Area | textarea | <ul><li>minLength</li><li>maxLength</li><li>nbRows</li></ul> |
| Password | password | <ul><li>minLength</li><li>maxLength</li></ul> |
| Phone number | phone | <ul><li>minLength</li><li>maxLength</li></ul> |

## Choice field types 

| Name | Code | ContentType Options |
| ---- | ---- | ------- |
| Select | select | <ul><li>is_multiple : boolean</li><li>choices : array of available choices</li></ul> |
| Checkbox | checbox | <ul><li>choices : array of available choices</li></ul> |
| Radio | radio | <ul><li>choices : array of available choices</li></ul> |
| Boolean | boolean |  |

## Layout field types 

| Name | Code | ContentType Options |
| ---- | ---- | ------- |
| Flexible | flexible |  |
| Repeater | repeater |  |

## Other field types 

| Name | Code | ContentType Options |
| ---- | ---- | ------- |
| Wysiwyg Editor | wysiwyg | <ul><li>toolbar : basic / standard / full</li></ul> |
| Number | number | <ul><li>minValue</li><li>maxValue</li></ul> |
| Email | email |  |
| Link | link | <ul><li>target : _blank / _self</li></ul> |
| Iframe | iframe | <ul><li>width</li><li>height</li></ul> |
| Message | message | <ul><li>message : message displayed on the Content form</li></ul> |
| Color Picker | color |  |
| Date Picker | date | <ul><li>time : includes time (1 => yes / 0 => no)</li></ul> |
| File upload | file |  |
| Image upload | image |  |

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
