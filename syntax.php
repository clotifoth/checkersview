<?php
/**
 * Plugin checkersview: Creates a checkers diagram
 * @author: Frederick Brunn
 */

if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
if(!defined('IMAGE_PATH')) define('IMAGE_PATH',DOKU_BASE.'lib/plugins/checkersview/images/');
require_once(DOKU_PLUGIN.'syntax.php');

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_checkersview extends DokuWiki_Syntax_Plugin {

    /**
     * Constructor.
     */
    function syntax_plugin_checkersview(){
      // enable direct access to language strings
      $this->setupLocale();
    }

    /**
     * Return the relevant info for this plugin. Author, email etc.
     */
    function getInfo(){
        return array(
            'author' => 'Frederick Brunn',
            'email'  => 'frederick.brunn@stonybrook.edu; clotifoth@aim.com',
            'date'   => '2015-07-23',
            'name'   => 'Checkers View Plugin',
            'desc'   => 'Creates a checkerboard with an image for each piece',
            'url'    => 'https://github.com/clotifoth/checkersview/'
        );
    }

    /**
     * What kind of syntax are we?
     */
    function getType(){
        return 'substition';
    }

    /**
     * Where to sort in?
     */
    function getSort(){
        return 999;
    }


    /**
     * Add this plugin as a delegate to the Doku renderer, using the wikisyntax 
	 * string specified in the current language file as the triggering action.
     */
    function connectTo($mode) {
      $this->Lexer->addSpecialPattern('<'.$this->lang['checkerswikisyntax'].'>.+?</'.$this->lang['checkerswikisyntax'].'>',$mode,'plugin_checkersview');
    }

    /**
     * Upon being triggered, this function formulates the proper arguments from
	 * the contents of the checkers tag. It looks for the pieces involved as well
	 * as the borders requested in the first line.
     */
    function handle($match, $state, $pos, &$handler)
    {
      $match=substr($match,strlen($this->lang['checkerswikisyntax'])+2,-(strlen($this->lang['checkerswikisyntax'])+3));
      foreach (preg_split('/\r\n|\r|\n/',$match) as $line)
      {
         $line=preg_replace("/#.*/","",trim($line));
         if(substr($line,0,1) != "") { $lines.=$line; };
      }
      $pos = strpos($lines,')');
      return strip_tags(strtr(substr($lines,0,$pos),$this->lang['checkersview'],'TBLRdl').strtr(substr($lines,$pos),$this->lang['checkerspieces'],'BbRr'));
    }

    /**
     * Triggers the checkerboard renderer.
     */
    function render($mode, &$renderer, $data)
    {
      if($mode == 'xhtml')
      {
        // for debug: $renderer->doc .= '<pre>'.$data.'</pre>';
        $renderer->doc .= checkersboard_render($data);
        return true;
      }
      return false;
    }
}

/*
 * Formulates filename for border pieces. Top and bottom / left and right share
 * the same border pieces but don't necessarily need to. Ideally, cases should 
 * be defined by calls to $lang so that other languages' letters for border names
 * are applicable.
 */
function checkersboard_border_filename($border)
{

  switch ($border) {
   case 'T':
   case 'B':
    return IMAGE_PATH.'h.png';
   case 'L':
   case 'R':
    return IMAGE_PATH.'v.png';
   default:
    return IMAGE_PATH.'c.png';
  }
}


/*
 * Formulates filename for game pieces. Ideally, special cases for black and white
 * should be defined by calls to $lang so that other languages' letters for B and R
 * are applicable.
 */
function checkersboard_piece_filename($piece, $square_color)
{
  switch ($piece) {
   case 'B':
    $name = 'black_k';
    break;
   case 'b':
    $name = 'black';
    break;
   case 'R':
    $name = 'white_k';
    break;
   case 'r':
    $name = 'white';
    break;
   case 'x':
    $name = 'xx';
    break;
   case 'o':
    $name = 'oo';
    break;
   case '-':
    $name = '';
    break;
   default:
    $name = strtolower($piece).(ctype_lower($piece)?'d':'l');
    break;
  }

  return IMAGE_PATH.$name.($square_color?'d':'l').'.png';
}

/* 
 * Returns a fully rendered checkboard, given the content of the tags. Content
 * is only accessed to parse for options (borders, width of checkerboard, starting
 * square.) Returned checkerboard formatted in XHTML (of course.)
 */
function checkersboard_render($content)
{
  static $table_xhtml;

  
  $table_xhtml = array('-0' => '<img src="'
                         . checkersboard_piece_filename('-', 0)
                         . '" alt="-" />',
                       '-1' => '<img src="'
                         . checkersboard_piece_filename('-', 1)
                         . '" alt="-" />',
                       'x0' => '<img src="'
                         . checkersboard_piece_filename('x', 0)
                         . '" alt="x" />',
                       'x1' => '<img src="'
                         . checkersboard_piece_filename('x', 1)
                         . '" alt="x" />',
                       'o0' => '<img src="'
                         . checkersboard_piece_filename('o', 0)
                         . '" alt="o" />',
                       'o1' => '<img src="'
                         . checkersboard_piece_filename('o', 1)
                         . '" alt="o" />',
                      );

  // Pieces
  $pieces = 'br';
  for ($i=0; $i<2; $i++) {
    $piece = $pieces[$i];
    $table_xhtml += array(strtoupper($piece) . '0' => '<img src="'
                            . checkersboard_piece_filename(strtoupper($piece), 0)
                            . '" alt="' . strtoupper($piece) . '" />',
                          strtoupper($piece) . '1' => '<img src="'
                            . checkersboard_piece_filename(strtoupper($piece), 1)
                            . '" alt="' . strtoupper($piece) . '" />',
                          $piece . '0' => '<img src="'
                            . checkersboard_piece_filename($piece, 0)
                            . '" alt="' . $piece . '" />',
                          $piece . '1' => '<img src="'
                            . checkersboard_piece_filename($piece, 1)
                            . '" alt="' . $piece . '" />',
                         );
  }

  // Borders
  $table_xhtml += array('T' => '<img src="'
                          . checkersboard_border_filename('T')
                          . '" alt="" style="border: 0px;"/>',
                        'B' => '<img src="'
                          . checkersboard_border_filename('B')
                          . '" alt="" />',
                        'L' => '<img src="'
                          . checkersboard_border_filename('L')
                          . '" alt="" />',
                        'R' => '<img src="'
                          . checkersboard_border_filename('R')
                          . '" alt="" />',
                        'TL' => '<img src="'
                          . checkersboard_border_filename('TL')
                          . '" alt="" />',
                        'TR' => '<img src="'
                          . checkersboard_border_filename('TR')
                          . '" alt="" />',
                        'BL' =>
                          '<img src="'
                          . checkersboard_border_filename('BL')
                          . '" alt="" />',
                        'BR' =>
                          '<img src="'
                          . checkersboard_border_filename('BR')
                          . '" alt="" />',
                       );
  /* The following regex basically searches out the selected options, which
   * should be enclosed in parenthesis somewhere between the tags.
   */
  preg_match('@^\s*(?:\((.*?)\))?(.*)$@s', $content, $matches);

  $params =& $matches[1];

  /* Determines width from the selected options presented. Defaults to 8. */
  $columns = preg_match('@[0-9]+@', $params, $m) ? $m[0] : 8;

  $square_color_first = (strpos($params, 'b') !== false) ? 1 : 0;

  // Borders
  $border = array('T' => (strpos($params, 'T') !== false),
                  'B' => (strpos($params, 'B') !== false),
                  'L' => (strpos($params, 'L') !== false),
                  'R' => (strpos($params, 'R') !== false));

  $board =& $matches[2];
  $returned_xhtml_board = ''; // Stores XHTML formatted checkerboard.

  // Top border
  if ($border['T']) {
    if ($border['L'])
      $returned_xhtml_board .= $table_xhtml['TL'];
    $returned_xhtml_board .= str_repeat($table_xhtml['T'], $columns);
    if ($border['R'])
      $returned_xhtml_board .= $table_xhtml['TR'];
    $column_position = $columns;
  }
  else {
    $column_position = 0; 
    $square_color = $square_color_first;
    $square_color_first = 1 - $square_color_first;
  }
  
  if($border['L'] && !$border['T']) // HUGE cruft to get the first left border
  {                                 // to render correctly
    $returned_xhtml_board .= $table_xhtml['L'];
  }
  
  // Left border, board, right border
  for ($i=0; isset($board[$i]); $i++) {

    // Ignore unknown characters
    if (strpos('BbRr-xo', $board[$i]) === false) {
      continue;
    }

    if ($column_position >= $columns) {
      $returned_xhtml_board .= '<br />';
      $column_position = 0;
      $square_color = $square_color_first;
      $square_color_first = 1 - $square_color_first;
      if ($border['L'])
        $returned_xhtml_board .= $table_xhtml['L'];
    }

    $key = $board[$i].$square_color;
	
    $returned_xhtml_board .= $table_xhtml[$key];
    $square_color = 1 - $square_color;
    $column_position++;
    

    if ($column_position >= $columns) {
      if ($border['R'])
        $returned_xhtml_board .= $table_xhtml['R'];
    }
  }

  // Bottom border
  if ($border['B']) {
    $returned_xhtml_board .= '<br />';
    if ($border['L'])
      $returned_xhtml_board .= $table_xhtml['BL'];
    $returned_xhtml_board .= str_repeat($table_xhtml['B'], $columns);
    if ($border['R'])
      $returned_xhtml_board .= $table_xhtml['BR'];
  }

  return $returned_xhtml_board;
}

