Checkersview

This project is a plugin intended for use with DokuWiki, written in PHP, that renders a checkersboard given text input.

Installation
To install, unzip the release to your plugins folder.

Syntax
The opening and closing tags are and for English- other languages supported will use their translation for the game.

Various options can be given before the checkers board to customize your board.

T, B, L, R - Defines which sides' borders are rendered.
b or r - Defines whether the checkerboard starts with a red or a black square.
Number - Defines the width for partial diagrams or larger checkerboards, but defaults to 8.
The diagram itself should consist of a square block of text, with the width specified in the options (or 8, if no width is specified.)

B - Black king
b - Black regular
R - Red king
r - Red regular
x - X marker
o - O marker
- - Empty space