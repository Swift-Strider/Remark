<p align="center">
<img src="assets/icon.png" alt="Icon" width="262px" height="120px" />
</p>

# Remark - Easy and Asynchronous Commands and Forms
* [Quick Guide](https://swift-strider.github.io/Remark/quick-guide/index.html) - Learn Remark by building a plugin.
* [Install](#install) - Add Remark as a library to your plugin.
* [Example](https://github.com/Swift-Strider/ExampleRemarkPlugin) - View an example plugin using Remark.

# Benefits
* `TAB`-completion for commands
* Command argument validation
* Type-safe commands and forms
* Asynchronous Forms API
* Optional AwaitGenerator

# Install
```sh
composer require diamondstrider1/remark ^1.1.0
```

Add Remark as a library in `.poggit.yml`.
```yml
projects:
  ExampleRemarkPlugin:
    path: ""
    libs:
      - src: Swift-Strider/Remark/Remark
        version: ^1.1.0
        epitope: .random
```
