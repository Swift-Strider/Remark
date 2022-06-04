# v1.2.1
* Fixed potentially crashing bug in SetParameterTrait.php
* Fixed Composer settings so Arg's with underscores play nicely with static analyzers.

# v1.2.0
* Allow `Arg`'s to be optional by making the parameter's type accept null

# v1.1.2
* Fix bug that treated non-HandlerMethods as HandlerMethods in `Remark::command()`.

# v1.1.1
* `permission()` Guard errors if any passed permission does not exist.
* Fixed bug that caused an error when a player closed out of a Custom Form.
* Enum names assigned to subcommand parameters are prefixed to reduce the likelihood of conflicting with the `enum()` Arg.

# v1.1.0
* Added await-generator as a dependency.
* Added args: bool_arg, command_arg, float_arg, int_arg, vector_arg.
* `string[] CmdConfig->permissions` changed to `?string CmdConfig->permission`

# v1.0.0
* Command Handling and Forms implemented.
