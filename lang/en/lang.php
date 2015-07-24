<?php
/**
 * English language version.
 */

 /*
  * Listed description
  */
$lang['desc'] = 'Creates a checkerboard with an image for each piece';

/*
 * The pattern that will be searched for in tags to trigger checkerboard
 * rendering.
 */
$lang['checkerswikisyntax'] = 'checkers'; // wikisyntax

/*
 * All possible checkers pieces.
 */
$lang['checkerspieces'] = 'BbRr';

/** The possible options for a checkers board, listed in parenthesis anywhere
 * within the tags, outside the board:
 * -(T, B, L, R) Syntax for indicating borders ought to be on the Top, Bottom,
 * Left, or Right respectively.
 * -(b or r) Whether or not the checkersboard should start on a black or red square.
 * Also, one can specify a number within the parenthesis, which becomes the width
 * of the checkerboard. Defaults to 8
 */
$lang['checkersoptions'] = 'TBLRbr'; 
