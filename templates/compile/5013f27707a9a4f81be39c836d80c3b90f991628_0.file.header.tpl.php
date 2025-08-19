<?php
/* Smarty version 3.1.30, created on 2021-07-26 15:46:42
  from "/home/mysidia/public_html/adoptables/demos/mys136b/templates/green/header.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl,  [
  'version' => '3.1.30',
  'unifunc' => 'content_60fed8e26669d2_03025142',
  'has_nocache_code' => false,
  'file_dependency' => 
   [
    '5013f27707a9a4f81be39c836d80c3b90f991628' => 
     [
      0 => '/home/mysidia/public_html/adoptables/demos/mys136b/templates/green/header.tpl',
      1 => 1626365680,
      2 => 'file',
    ],
  ],
  'includes' => 
   [
  ],
],false)) {
function content_60fed8e26669d2_03025142 (Smarty_Internal_Template $_smarty_tpl) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
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

    </head>      <?php }
}
