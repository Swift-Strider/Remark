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
