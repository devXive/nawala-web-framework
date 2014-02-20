# NDocument


## addScript
Will look first in the active template js folder (eg: template/my_template/js) if the file to load is here. If no file is found, the script will look in the libraries/nawala/assets/js folder if the file to load is there.

This can also be used to override core functionalities.

Please note that folders added by addScriptFolder will be processed right before the template directory is checked for an existing file!

_The first match load the appropriate file, all other folders will be ignored!_

 So the structure is as followed:
    1. WebOS_ROOT_DIR >custom_path/to/js_folder
    2. WebOS_ROOT_DIR >template/active_template/js
    3. WebOS_ROOT_DIR >libraries/nawala/assets/js


### Compiler/Compressor functions
The addScript method has an in-built javascript compressor which stores a minified version of the appropriate js file to the nawala/media/js cache folder and create a checkSwap.
The checkSwap informations are used to determine if the file exist or has changed and build a new copy in the file cache.



## addStyle
Will look first in the active template css folder (eg: template/my_template/css) if the file to load is here. If no file is found, the script will look in the libraries/nawala/assets/css folder if the file to load is there.

This can also be used to override core functionalities.

Please note that folders added by addStyleFolder will be processed right before the template directory is checked for an existing file!

_The first match load the appropriate file, all other folders will be ignored!_

 So the structure is as followed:

    1. WebOS_ROOT_DIR >custom_path/to/css_folder
    2. WebOS_ROOT_DIR >template/active_template/css
    3. WebOS_ROOT_DIR >libraries/nawala/assets/css


### Compiler/Compressor functions
The addScript method has an in-built javascript compressor which stores a minified version of the appropriate js file to the nawala/media/js cache folder and create a checkSwap.
The checkSwap informations are used to determine if the file exist or has changed and build a new copy in the file cache.



## addLess
Special notes to the addLess function with its inbuild compiler and the file cache