<?php
/* Smarty version 3.1.30, created on 2021-08-06 17:35:27
  from "/home/mysidia/public_html/adoptables/demos/mys136b/templates/green/header.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_610d72dff14220_53569543',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '29a6de443f2a9403af275a385c68f96998f9bd05' => 
    array (
      0 => '/home/mysidia/public_html/adoptables/demos/mys136b/templates/green/header.tpl',
      1 => 1628266009,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_610d72dff14220_53569543 (Smarty_Internal_Template $_smarty_tpl) {
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

    </head>       <?php }
}
