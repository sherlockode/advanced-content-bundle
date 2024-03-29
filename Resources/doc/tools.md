# Tools

----

The page acb/tools allows you to [import](import.md) / [export](export.md) your entities.
It also includes a CRUD system for scopes and page types

## Configuration

----

By default, the template used for this page is `@SherlockodeAdvancedContent/Tools/index.html.twig`
You can override this by adding your own file in the configuration :

```yaml
# config/packages/sherlockode_advanced_content.yml
sherlockode_advanced_content:
    templates:
        tools: 'dir/template.html.twig'
```

If you are using our extension for Sonata ([SonataBundle](https://github.com/sherlockode/SonataAdvancedContentBundle/)), 
there is already a customized template for Sonata that will change the default template to `SherlockodeSonataAdvancedContent/Tools/index.html.twig`
To bypass this and use your template, you will also have to add the following configuration:

```yaml
# config/packages/sherlockode_sonata_advanced_content.yml
sherlockode_sonata_advanced_content:
    use_bundle_templates:
        tools: false
```

## Import / Exports

----

On this page, you will be able to :
- import your entities : upload any yaml file containing your entity. 
As long as you respect the [format](import.md), you can include as many entities in the file as you want
- export your entities : you will be able to select which entity/entities you want to export. 
This will result in a zip file being downloaded in your browser.


## Scopes

----

By defaut, scopes are not enabled on the bundle.
If you want to handle scopes on your Pages and Contents, you need to enable scope management and also create some scopes.
By default, the provided Scope class is locale-based. You can override the entity if it does not meet your requirements.
Scopes can be created on the tools page.

```yaml
# config/packages/sherlockode_advanced_content.yml
sherlockode_advanced_content:
    scopes:
        enabled: true
```

## Page Types

----

Typing pages allows you to categorize them and use different layouts.
However, if this functionality is not useful to you, you can setup your pages without any page type. 
