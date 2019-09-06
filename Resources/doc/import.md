Import
======

The import command will create your content types, page types, pages and their content.

## Configuration

If you want to use our import command, you can configure the directory in which to find the source files.
If not defined, the import will parse var/acb directory.
By default, only creation is available. If you want to be able to update your entities, you can change the flag `allow_update`

```yaml
# config/packages/sherlockode_advanced_content.yml
sherlockode_advanced_content:
    init_command:
        directory: custom/dir
        allow_update: true
```

## Command

```bash
$ php bin/console sherlockode:acb:init
```

## Source files

### Basics

Source files for content types must be placed in [init_command.directory]/ContentType
Source files for pages must be placed in [init_command.directory]/Page

Source files must be in yaml format.
There must be one file per content type / per page.
There is no restriction on the file naming. All files of the source directories will be parsed.

### Content types

You need to define a name for your ContentType

```yaml
# var/acb/ContentType/custom_content_type.yml
name: Custom Content Type
```

You can link your ContentType to a PageType (which will be created on the fly if it does not already exist)

```yaml
# var/acb/ContentType/custom_content_type.yml
name: Custom Content Type
pageType: Custom Page Type
children: list of Fields of the ContentType
```

You can then define the Fields of your ContentType

Each Field will have the following structure: 

```yaml
# var/acb/ContentType/custom_content_type.yml
name: Custom Field
type: Code of the FieldType
# optional
slug: custom slug - slugified name will be used by default
options: list of options and their values
required: true/false - default true
children: defines layouts - only for Flexible / Repeater field types
```

Each Layout will have the following structure: 

```yaml
# var/acb/ContentType/custom_content_type.yml
name: Custom Layout
children: list of Fields of the layout
```

You can find a ContentType import file example here [doc](import/ContentType/custom_content_type.yaml)

### Pages

You need to define a title and / or a slug for your Page

```yaml
# var/acb/Page/custom_page.yml
title: Custom Page
slug: custom-slug
```

You can also define other optional information: 
```yaml
# var/acb/Page/custom_page.yml
meta: 'Page Meta Description'
status: 10 (0: Draft (default) / 10: Published / 20: Trash)
pageType: Custom Page Type (if defined, will automatically retrieve the ContentType linked to the PageType)
contentType: Custom Content Type (if you want a specific ContentType for this page)
children: list of FieldValues of the Content linked to this Page
```

Each FieldValue will have the following structure:
```yaml
# var/acb/Page/custom_page.yml
slug: Slug of the Field
value: Custom value
```

Each FieldGroupValue will have the following structure: 

```yaml
# var/acb/Page/custom_page.yml
name: Name of the Layout
children: list of FieldValues for each Field of the Layout
```

You can find a Page import file example here [doc](import/Page/custom_page.yaml)
