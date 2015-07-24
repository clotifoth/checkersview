<?php
/**
 * Plugin checkersview: Creates a checkers diagram
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
     * function constructor
     */
    function syntax_plugin_checkersview(){
      // enable direct access to language strings
      $this->setupLocale();
    }

    /**
     * return some info
     */
    function getInfo(){
        return array(
            'author' => 'Frederick Brunn',
            'email'  => 'frederick.brunn@stonybrook.edu; clotifoth@aim.com',
            'date'   => '2015-07-23',
            'name'   => 'Checkers View Plugin',
            'desc'   => $lang['desc'],
            'url'    => 'www.example.com'
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
     * Connect pattern to lexer
     */
    function connectTo($mode) {
      $this->Lexer->addSpecialPattern('<'.$this->lang['checkerswikisyntax'].'>.+?</'.$this->lang['checkerswikisyntax'].'>',$mode,'plugin_checkersview');
    }

    /**
     * Handle the match
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
     * Create output
     */
    function render($mode, &$renderer,$data)
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

  preg_match('@^\s*(?:\((.*?)\))?(.*)$@s', $content, $matches);

  $params =& $matches[1];

  // Number of files: any integer (default: 8)
  $file_max = preg_match('@[0-9]+@', $params, $m) ? $m[0] : 8;

  $square_color_first = (strpos($params, 'b') !== false) ? 1 : 0;

  // Borders
  $border = array('T' => (strpos($params, 'T') !== false),
                  'B' => (strpos($params, 'B') !== false),
                  'L' => (strpos($params, 'L') !== false),
                  'R' => (strpos($params, 'R') !== false));

  // Render the board in XHTML
  $board =& $matches[2];
  $xhtml = '';

  // Top border
  if ($border['T']) {
    if ($border['L'])
      $xhtml .= $table_xhtml['TL'];
    $xhtml .= str_repeat($table_xhtml['T'], $file_max);
    if ($border['R'])
      $xhtml .= $table_xhtml['TR'];
    $file = $file_max;
  }
  else {
    $file = 0;
    $square_color = $square_color_first;
    $square_color_first = 1 - $square_color_first;
  }
  
  if($border['L'] && !$border['T']) // HUGE cruft to get the first left border
  {                                 // to render correctly
    $xhtml .= $table_xhtml['L'];
  }
  
  // Left border, board, right border
  for ($i=0; isset($board[$i]); $i++) {

    // Ignore unknown characters
    if (strpos('BbRr-xo', $board[$i]) === false) {
      continue;
    }

    if ($file >= $file_max) {
      $xhtml .= '<br style="height: 0px; visibility: hidden;" />';
      $file = 0;
      $square_color = $square_color_first;
      $square_color_first = 1 - $square_color_first;
      if ($border['L'])
        $xhtml .= $table_xhtml['L'];
    }

    $key = $board[$i].$square_color;
	
    $xhtml .= $table_xhtml[$key];
    $square_color = 1 - $square_color;
    $file++;
    

    if ($file >= $file_max) {
      if ($border['R'])
        $xhtml .= $table_xhtml['R'];
    }
  }

  // Bottom border
  if ($border['B']) {
    $xhtml .= '<br />';
    if ($border['L'])
      $xhtml .= $table_xhtml['BL'];
    $xhtml .= str_repeat($table_xhtml['B'], $file_max);
    if ($border['R'])
      $xhtml .= $table_xhtml['BR'];
  }

  return $xhtml;
}

