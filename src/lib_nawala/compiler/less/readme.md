### Nawala Framework CompilerLess Class adoption
- @package       Framework
- @subpackage    NCompilerLess
- @since         1.0
 
This class allows to compile less files and add ability to store file informations for simple and
secure caching and is taken near verbatim from:

changes marked with /** * NRDK -- BEGIN CHANGE ** */ and /** * NRDKcomment markers

lessphp v0.4.0
http://leafo.net/lessphp

LESS CSS compiler, adapted from http://lesscss.org

Copyright 2013, Leaf Corcoran <leafot@gmail.com>
Licensed under MIT or GPLv3, see LICENSE
 
At a glance:
The LESS compiler and parser.

Converting LESS to CSS is a three stage process. The incoming file is parsed
by `NCompilerLessParser` into a syntax tree, then it is compiled into another tree
representing the CSS structure by `NCompilerLess`. The CSS tree is fed into a
formatter, like `NCompilerLessFormatter` which then outputs CSS as a string.

During the first compile, all values are *reduced*, which means that their
types are brought to the lowest form before being dump as strings. This
handles math equations, variable dereferences, and the like.

The `parse` function of `NCompilerLess` is the entry point.

In summary:

The `NCompilerLess` class creates an instance of the parser, feeds it LESS code,
then transforms the resulting tree to a CSS tree. This class also holds the
evaluation context, such as all available mixins and variables at any given
time.

The `NCompilerLessParser` class is only concerned with parsing its input.

The `NCompilerLessFormatter` takes a CSS tree, and dumps it to a formatted string,
handling things like indentation.
