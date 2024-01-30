# Import

----

The import command will create your pages and contents.

## Configuration

----

If you want to use our import command, you can configure the directory in which to find the source files.
If not defined, the import will parse var/acb directory.

By default, only creation is available. If you want to be able to update your entities, you can change the flag `allow_update`

To import files to your contents, you need to configure the directory in which to find the source files. 
If not defined, the import will parse var/acb/files directory.
You can either use a relative path (relative from `%kernel.project_dir%`) or an absolute path (for example `/tmp/files`)


```yaml
# config/packages/sherlockode_advanced_content.yml
sherlockode_advanced_content:
    init_command:
        directory: custom/dir
        allow_update: true
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

----

```bash
$ php bin/console sherlockode:acb:import
```

The command has several options:
- type: type of entity to import. Allowed types are Page and Content
- file: file name / file pattern to import
- dir: directory in which the command can find the files to import (will override your configuration) - You can either use a relative path (relative from `%kernel.project_dir%`) or an absolute path (for example `/tmp/files`)
- files-dir: directory in which the command can find the resource files (for File / Image FieldTypes) to import (will override your configuration) - You can either use a relative path (relative from `%kernel.project_dir%`) or an absolute path (for example `/tmp/files`)

```bash
$ php bin/console sherlockode:acb:import #import all types / all files in custom/dir
$ php bin/console sherlockode:acb:import --type=Content #import Content only / all files in custom/dir
$ php bin/console sherlockode:acb:import --type=Content --type=Page #import Content and Page / all files in custom/dir
$ php bin/console sherlockode:acb:import --file=custom.yaml #import all types / file named custom.yaml in custom/dir
$ php bin/console sherlockode:acb:import --file=*custom* #import all types / all files containing custom within their name in custom/dir
$ php bin/console sherlockode:acb:import --type=Content --type=Page --file=*custom* #import Content and Page / all files containing custom within their name in custom/dir
$ php bin/console sherlockode:acb:import --dir=specific/dir #import all types / all files in specific/dir
```

## Source files

----

### Basics

----

Source files must be in yaml format.
There must be one file per content / per page.
There is no restriction on the file naming. All files of the source directories will be parsed.

### Pages

----

You need to define an identifier and meta information for your Page.

```yaml
# var/acb/Page/custom_page.yaml
pages:
    page-identifier:
        meta:
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
        pageType: Custom Page Type
        content:
            #list of elements of the Content linked to this Page
            -
                type: row
                elements:
                    -
                        type: column
                        elements: 
                            -
                                type: text
                                value: hello
                            -
                                type: text
                                value: How are you?
        meta:
            title: Custom Page
            slug: custom-page-en
            meta_title: 'Meta Title'
            meta_description: 'Meta Description'
```

Each element declaration has the following structure:
```yaml
# var/acb/Page/custom_page.yaml
type: Type of the Field
value: Custom value # string on array depending on the data
```

You can find a Page import file example here [doc](import/Page/custom_page.yaml)


### Contents

----

You need to define the name of your Content, its slug and its children elements: 

```yaml
# var/acb/Content/content.yaml
contents:
    content-slug:
        name: Custom Content
        children:
            -
                type: row
                elements:
                    -
                        type: column
                        elements:
                            -
                                type: heading
                                value: 
                                    text: Hello
                                    level: 1
                            -
                                type: text
                                value: How are you?
```

You can find a Content import file example here [doc](import/Content/standalone_content.yaml)
These example files also show you how to create and populate File and Image field types.

### Scopes

----

If you want to link scopes on your entities, you can use the following template:

```yaml
# var/acb/Page/custom_page.yaml
pages:
    page-identifier:
        scopes:
            -
                locale: en_GB
            -
                locale: en_US

# var/acb/Content/content.yaml
contents:
    content-slug:
        name: Custom Content
        scopes:
            -
                locale: en_GB
            -
                locale: en_US
```

### Specific field types

----

#### File / Image FieldType

----

When you export the content of File / Image FieldType for which you have already imported a resource,
you will obtain the following structure.

```yaml
# exported fields
    file_slug:
        src: file.pdf
        title: File Title
    image_slug:
        image:
            src: image.jpg
            alt: Image Alt
```

If you want to upload a new file for this field values, you need to add a `_file` entry under `value` or under `image` for an Image FieldType.
The file to import must be located in the `files-dir` directory (either in your configuration or in the option of the import command)

For example, if the new files are located in /tmp/new-files, you can launch the command:
 
```bash
$ php bin/console sherlockode:acb:import --files-dir=/tmp/new-files
```

And the field values must contain the following info : 

```yaml
# fields to import
    file_slug:
        _file: new-file.pdf # file located in /tmp/new-files/new-file.pdf
        title: File Title
    image_slug:
        image:
            _file: new-image.jpg # file located in /tmp/new-files/new-image.png
            alt: Image Alt
```

If you want to upload multiple sources for your Image field, you can add them under `sources`

```yaml
# fields to import
    image_slug:
        image:
            _file: new-image.jpg # file located in /tmp/new-files/new-image.png
            alt: Image Alt
        sources:
            - 
                _file: new-image-small.jpg # file located in /tmp/new-files/new-image-small.png
                media_query: '(max-width: 599px)'
            -
                _file: new-image-medium.jpg # file located in /tmp/new-files/new-image-medium.png
                media_query: '(min-width: 600px) and (max-width: 1000px)'
```
