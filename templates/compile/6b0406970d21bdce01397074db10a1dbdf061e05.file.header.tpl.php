<?php /* Smarty version Smarty-3.1.12, created on 2021-07-03 06:04:25
         compiled from "/home/mysidia/public_html/adoptables/demos/mys136b/templates/main/header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:103976027060dfeacd2330e4-24807472%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6b0406970d21bdce01397074db10a1dbdf061e05' => 
    array (
      0 => '/home/mysidia/public_html/adoptables/demos/mys136b/templates/main/header.tpl',
      1 => 1625291832,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '103976027060dfeacd2330e4-24807472',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_60dfeacd23b7f8_12239656',
  'variables' => 
  array (
    'browser_title' => 0,
    'home' => 0,
    'header' => 0,
    'temp' => 0,
    'theme' => 0,
    'css' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_60dfeacd23b7f8_12239656')) {function content_60dfeacd23b7f8_12239656($_smarty_tpl) {?><html>
<head>
<title><?php echo $_smarty_tpl->tpl_vars['browser_title']->value;?>
</title>
<?php echo $_smarty_tpl->tpl_vars['header']->value->loadFavicon(((string)$_smarty_tpl->tpl_vars['home']->value)."favicon.ico");?>

<?php echo $_smarty_tpl->tpl_vars['header']->value->loadStyle(((string)$_smarty_tpl->tpl_vars['home']->value).((string)$_smarty_tpl->tpl_vars['temp']->value).((string)$_smarty_tpl->tpl_vars['theme']->value)."/media/style-city.css");?>

<?php echo $_smarty_tpl->tpl_vars['header']->value->loadStyle(((string)$_smarty_tpl->tpl_vars['home']->value).((string)$_smarty_tpl->tpl_vars['css']->value)."/menu.css");?>

<?php echo $_smarty_tpl->tpl_vars['header']->value->loadAdditionalStyle();?>

</head><?php }} ?>