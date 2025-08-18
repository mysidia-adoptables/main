<?php
/* Smarty version 3.1.30, created on 2021-07-26 15:46:39
  from "/home/mysidia/public_html/adoptables/demos/mys136b/templates/main/header.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_60fed8df3bf3e5_29181318',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6b0406970d21bdce01397074db10a1dbdf061e05' => 
    array (
      0 => '/home/mysidia/public_html/adoptables/demos/mys136b/templates/main/header.tpl',
      1 => 1625765890,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_60fed8df3bf3e5_29181318 (Smarty_Internal_Template $_smarty_tpl) {
?>
<html>
    <head>
        <title><?php echo $_smarty_tpl->tpl_vars['browser_title']->value;?>
</title>
        <?php echo $_smarty_tpl->tpl_vars['header']->value->loadFavicon(((string)$_smarty_tpl->tpl_vars['home']->value)."favicon.ico");?>

        <?php echo $_smarty_tpl->tpl_vars['header']->value->loadStyle(((string)$_smarty_tpl->tpl_vars['home']->value).((string)$_smarty_tpl->tpl_vars['temp']->value).((string)$_smarty_tpl->tpl_vars['theme']->value)."/media/style-city.css");?>

        <?php echo $_smarty_tpl->tpl_vars['header']->value->loadStyle(((string)$_smarty_tpl->tpl_vars['home']->value).((string)$_smarty_tpl->tpl_vars['css']->value)."/menu.css");?>

        <?php echo $_smarty_tpl->tpl_vars['header']->value->loadStyles();?>

        <?php echo $_smarty_tpl->tpl_vars['header']->value->loadAdditionalStyle();?>

        <?php echo $_smarty_tpl->tpl_vars['header']->value->loadScript("//ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js");?>

        <?php echo $_smarty_tpl->tpl_vars['header']->value->loadScripts();?>

        <?php echo $_smarty_tpl->tpl_vars['header']->value->loadAdditionalScript();?>

    </head><?php }
}
