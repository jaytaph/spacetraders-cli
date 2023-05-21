# Spacetraders Console app

The commandline application is a simple wrapper around the API. It is not very useful, but it can be used to test the API.

The application is called `spacetraders`. It has a number of commands, each with their own options. The commands are:

- `spacetraders register <callsign> <faction> --save` - Creates a new user. If `--save` is given, the token is saved in the `.token` file. 
- `spacetraders fleet:list` - List all ships in your fleet
- `spacetraders fleet:show <shipsymbol>` - Show details of a given ship
- and many more.

Please read the spacetraders.io getting started documentation to use the application. Most of it will make sense then.
