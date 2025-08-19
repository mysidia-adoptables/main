<?php
/* Smarty version 3.1.30, created on 2021-08-06 19:43:48
  from "/home/mysidia/public_html/adoptables/demos/mys136b/templates/main/header.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl,  [
  'version' => '3.1.30',
  'unifunc' => 'content_610d90f4abe808_45210551',
  'has_nocache_code' => false,
  'file_dependency' => 
   [
    'eb9ba7a28cd5dcf6a8a52b57182fed3284ffda68' => 
     [
      0 => '/home/mysidia/public_html/adoptables/demos/mys136b/templates/main/header.tpl',
      1 => 1628279019,
      2 => 'file',
    ],
  ],
  'includes' => 
   [
  ],
],false)) {
function content_610d90f4abe808_45210551 (Smarty_Internal_Template $_smarty_tpl) {
?>
<html>
    <head>
        <title><?php echo $_smarty_tpl->tpl_vars['browser_title']->value;?>
</title>
        <?php echo $_smarty_tpl->tpl_vars['header']->value->loadFavicon(((string)$_smarty_tpl->tpl_vars['home']->value)."favicon.ico");?>

        <?php echo $_smarty_tpl->tpl_vars['header']->value->loadStyle(((string)$_smarty_tpl->tpl_vars['home']->value).((string)$_smarty_tpl->tpl_vars['temp']->value).((string)$_smarty_tpl->tpl_vars['theme']->value)."/style.css");?>

        <?php echo $_smarty_tpl->tpl_vars['header']->value->loadStyle(((string)$_smarty_tpl->tpl_vars['home']->value).((string)$_smarty_tpl->tpl_vars['css']->value)."/menu.css");?>

        <?php echo $_smarty_tpl->tpl_vars['header']->value->loadStyles();?>

        <?php echo $_smarty_tpl->tpl_vars['header']->value->loadAdditionalStyle();?>

        <?php echo $_smarty_tpl->tpl_vars['header']->value->loadScript("//ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js");?>

        <?php echo $_smarty_tpl->tpl_vars['header']->value->loadScripts();?>

        <?php echo $_smarty_tpl->tpl_vars['header']->value->loadAdditionalScript();?>

    </head> <?php }
}
