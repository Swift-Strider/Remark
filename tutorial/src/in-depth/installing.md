# Installing

Setting up your plugin to use Remark is quite easy!

It's advised that during development [DEVirion](#using-devirion) is used and when the plugin is ready production that this library is either installed [by Poggit](#installing-with-poggit) or installed [by hand](#manually-installing-into-a-plugin-phar).

## Using DEVirion

DEVirion allows your plugin to use Remark.

1. Install `Remark.phar` from this project's [Github Releases](https://github.com/Swift-Strider/Remark/releases/).
1. Place `Remark.phar` into your server's `virions` folder, which is next to the `plugins` folder.
1. If not already downloaded, get DEVirion from [Poggit](https://poggit.pmmp.io/p/DEVirion/).

## Installing with Poggit

Plugins that are built on poggit and use virions should declare there dependencies in `.poggit.yml`. To use Remark add an entry like this.
```yml
projects:
  my-pugin-project:
    path: ""
    libs:
      - src: Swift-Strider/Remark/Remark
        version: ^3.3.0
        epitope: .random
```

## Manually Installing into a Plugin Phar

Install `Remark.phar` from this project's [Github Releases](https://github.com/Swift-Strider/Remark/releases/).

If you haven't already, build your plugin into a phar file. This example script assumes you're in your plugin's directory and that the files/directories `plugin.yml`, `src`, and `resources` exist. The following works on both `Windows 10 (Powershell)` and `Ubuntu`.
```sh
wget https://raw.githubusercontent.com/pmmp/DevTools/master/src/ConsoleScript.php -O ConsoleScript.php
php -dphar.readonly=0 ConsoleScript.php --make src,resources,plugin.yml --relative . --out plugin.phar
```

Next you will "infect" your plugin's `.phar` file, embedding the Remark library inside of your plugin.
```sh
php -dphar.readonly=0 Remark.phar plugin.phar
```
