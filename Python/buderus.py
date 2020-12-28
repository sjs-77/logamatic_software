#!/usr/bin/python3 -u

# Main script
# ~~~~~~~~~~
#
# This script mainly serves as a "container" for the logamatic2107 class.
#
# License: CC-BY-SA 3.0
# Author: Sebastian Suchanek

from logamatic import logamatic2107

print("Starting daemon")

ende = False
a = logamatic2107()
a.run()
