Import
======

The import command will create your content types, page types, pages and their content.

## Configuration

If you want to use our import command, you can configure the directory in which to find the source files.
If not defined, the import will parse var/acb directory.

By default, only creation is available. If you want to be able to update your entities, you can change the flag `allow_update`

By default, if not defined at field level, fields will be created as optional. If you want your fields to be mandatory by default, you can change the flag `field_default_required`

To import files to your contents, you need to configure the directory in which to find the source files. 
If not defined, the import will parse var/acb/files directory.
You can either use a relative path (relative from `%kernel.project_dir%`) or an absolute path (for example `/tmp/files`)


```yaml
# config/packages/sherlockode_advanced_content.yml
sherlockode_advanced_content:
    init_command:
        directory: custom/dir
        allow_update: true
        field_default_required: true
        files_directory: custom/dir/files
```

Some field types require specific options:
- Wysiwyg: type of the toolbar - basic, standard or full
- Date: include time in the format

By default, the Wysiwyg toolbar will be basic and the Date will include the time.
To change the default values, you can update the configuration : 

```yaml
# config/packages/sherlockode_advanced_content.yml
sherlockode_advanced_content:
    default_options:
        wysiwyg_toolbar: full
        date_include_time: false
```

## Command

```bash
$ php bin/console sherlockode:acb:import
```

The command has several options:
- type: type of entity to import. Allowed types are ContentType, Page and Content
- file: file name / file pattern to import
- dir: directory in which the command can find the files to import (will override your configuration) - You can either use a relative path (relative from `%kernel.project_dir%`) or an absolute path (for example `/tmp/files`)
- files-dir: directory in which the command can find the resource files (for File / Image FieldTypes) to import (will override your configuration) - You can either use a relative path (relative from `%kernel.project_dir%`) or an absolute path (for example `/tmp/files`)

```bash
$ php bin/console sherlockode:acb:import #import all types / all files in custom/dir
$ php bin/console sherlockode:acb:import --type=ContentType #import ContentType only / all files in custom/dir
$ php bin/console sherlockode:acb:import --type=ContentType --type=Content #import ContentType and Content / all files in custom/dir
$ php bin/console sherlockode:acb:import --file=custom.yaml #import all types / file named custom.yaml in custom/dir
$ php bin/console sherlockode:acb:import --file=*custom* #import all types / all files containing custom within their name in custom/dir
$ php bin/console sherlockode:acb:import --type=ContentType --type=Page --file=*custom* #import ContentType and Page / all files containing custom within their name in custom/dir
$ php bin/console sherlockode:acb:import --dir=specific/dir #import all types / all files in specific/dir
```

## Source files

### Basics

Source files must be in yaml format.
There must be one file per content type / per page.
There is no restriction on the file naming. All files of the source directories will be parsed.

### Content types

You need to define a name for your ContentType

```yaml
# var/acb/ContentType/custom_content_type.yaml
content_types:
    content-type-slug:
        name: Custom Content Type
```

You can link your ContentType to a PageType (which will be created on the fly if it does not already exist)

```yaml
# var/acb/ContentType/custom_content_type.yaml
content_types:
    content-type-slug:
        name: Custom Content Type
        pageType: Custom Page Type
        children: list of Fields of the ContentType
```

You can then define the Fields of your ContentType

Each Field will have the following structure: 

```yaml
# var/acb/ContentType/custom_content_type.yaml
name: Custom Field
type: Code of the FieldType
# optional
slug: custom slug - slugified name will be used by default
options: list of options and their values
required: true/false - default false
children: defines layouts - only for Flexible / Repeater field types
```

Each Layout will have the following structure: 

```yaml
# var/acb/ContentType/custom_content_type.yaml
layout_name: Custom Layout
children: list of Fields of the layout
```

You can find a ContentType import file example here [doc](import/ContentType/custom_content_type.yaml)

### Pages

You need to define an identifier for your Page and metas for at least one language

```yaml
# var/acb/Page/custom_page.yaml
pages:
    page-identifier:
        metas:
            en:
                title: Custom Page
                slug: custom-page
                meta_title: 'Meta Title' # optional
                meta_description: 'Meta Description' # optional
```

You can also define other optional information: 
```yaml
# var/acb/Page/custom_page.yaml
pages:
    page-identifier:
        status: 10 # (0: Draft (default) / 10: Published / 20: Trash)
        pageType: Custom Page Type (if defined, will automatically retrieve the ContentType linked to the PageType)
        contentType: slug of the Content Type (if you want a specific ContentType for this page)
        contents:
            en: # locale of the content
                #list of FieldValues of the Content linked to this Page
                - slug: my_text
                  value: hello
            fr:
                - slug: my_text
                  value: bonjour
        metas:
            en:
                title: Custom Page
                slug: custom-page-en
                meta_title: 'Meta Title'
                meta_description: 'Meta Description'
            fr:
                title: Page personnalisée
                slug: page-personnalisee
                meta_title: 'Méta Titre'
                meta_description: 'Méta Description'
```

Each FieldValue will have the following structure:
```yaml
# var/acb/Page/custom_page.yaml
slug: Slug of the Field
value: Custom value
```

Each FieldGroupValue will have the following structure: 

```yaml
# var/acb/Page/custom_page.yaml
layout_name: Name of the Layout
children: list of FieldValues for each Field of the Layout
```

You can find a Page import file example here [doc](import/Page/custom_page.yaml)


### Contents

You need to defined the name of your Content, as well as the ContentType to which it is linked : 

```yaml
# var/acb/Content/content.yaml
contents:
    content-slug:
        name: Custom Content
        contentType: slug of the Content Type
        locale: en
```

Then, as for the content of your Pages, you need to define your FieldValues under `children`

You can find a Content import file example here [doc](import/Content/standalone_content.yaml)
This Content is linked to the ContentType defined here [doc](import/ContentType/standalone_content_type.yaml) 
These example files also show you how to create and populate Choice type fields as well as File and Image field types.


### Specific field types

#### File / Image FieldType

When you export the content of File / Image FieldType for which you have already imported a resource,
you will obtain the following structure.

```yaml
# exported fields
-
    slug: file-slug
    value:
        src: file.pdf
        title: File Title
-
    slug: image-slug
    value:
        src: image.jpg
        alt: Image Alt
```

If you want to upload a new file for this field values, you need to add a `file` entry under `value`.
The file to import must be located in the `files-dir` directory (either in your configuration or in the option of the import command)

For example, if the new files are located in /tmp/new-files, you can launch the command:
 
```bash
$ php bin/console sherlockode:acb:import --files-dir=/tmp/new-files
```

And the field values must contain the following info : 

```yaml
# fields to import
-
    slug: file-slug
    value:
        file: new-file.pdf # file located in /tmp/new-files/new-file.pdf
        title: File Title
-
    slug: image-slug
    value:
        file: new-image.jpg # file located in /tmp/new-files/new-image.png
        alt: Image Alt
```
