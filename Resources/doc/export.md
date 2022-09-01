Export
======

The export command will generate yaml files of your existing content types, page types, pages and their content.

## Configuration

If you want to use our export command, you can configure the directory in which to write the files.
If not defined, the export will write into var/acb directory.

```yaml
# config/packages/sherlockode_advanced_content.yml
sherlockode_advanced_content:
    init_command:
        directory: custom/dir
```

## Command

```bash
$ php bin/console sherlockode:acb:export
```

The command has several options:
- type: type of entity to export. Allowed types are Page and Content
- dir: directory into which the command can write the files to export (will override your configuration)
You can either use a relative path (relative from `%kernel.project_dir%`) or an absolute path (for example `/tmp/files`)

```bash
$ php bin/console sherlockode:acb:export #export all types in custom/dir
$ php bin/console sherlockode:acb:export --type=Content #export Content only in custom/dir
$ php bin/console sherlockode:acb:export --type=Content --type=Page #export Content and Page in custom/dir
$ php bin/console sherlockode:acb:export --dir=specific/dir #export all types in specific/dir
```
