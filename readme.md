Checkersview
======

This project is a plugin intended for use with DokuWiki, written in PHP, that renders an image-based visual representation of a checkerboard provided to it in text notation.

Installation
------
To install, unzip the release to the plugins folder of your DokuWiki instllation.

Syntax
------
The opening and closing tags are and for English- other languages supported will use their translation for the game.

Various options can be given before the checkers board to customize your board.

- `T`, `B`, `L`, `R` - Defines which sides' borders are rendered.
- `b`, `r` - Defines whether the checkerboard starts with a red or a black square.
- `Number` - Defines the width for partial diagrams or larger checkerboards, but defaults to 8.
- The diagram itself should consist of a square block of text, with the width specified in the options (or 8, if no width is specified.)

The pieces should be notated as such:

- `B` - Black king
- `b` - Black regular
- `R` - Red king
- `r` - Red regular
- `x` - X marker
- `o` - O marker
- `-` - Empty space


----

If you like this project, visit my personal website at http://clotifoth.github.io/ to see what else I've been up to.
