<?php
/* Smarty version 3.1.30, created on 2021-07-12 17:32:16
  from "/home/mysidia/public_html/adoptables/demos/mys136b/templates/acp/header.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, [
  'version' => '3.1.30',
  'unifunc' => 'content_60ec7ca0d9b417_79994069',
  'has_nocache_code' => false,
  'file_dependency' =>
   [
    '3a2a8dbfe1368d97f1808af4583b55d0afa23754' =>
     [
      0 => '/home/mysidia/public_html/adoptables/demos/mys136b/templates/acp/header.tpl',
      1 => 1626111131,
      2 => 'file',
    ],
  ],
  'includes' =>
   [
  ],
], false)) {
    function content_60ec7ca0d9b417_79994069(Smarty_Internal_Template $_smarty_tpl)
    {
        ?>
<html>
    <head>
        <title><?php echo $_smarty_tpl->tpl_vars['browser_title']->value;?>
</title>
        <?php echo $_smarty_tpl->tpl_vars['header']->value->loadFavicon(((string)$_smarty_tpl->tpl_vars['home']->value)."favicon.ico");?>

        <?php echo $_smarty_tpl->tpl_vars['header']->value->loadStyle(((string)$_smarty_tpl->tpl_vars['home']->value).((string)$_smarty_tpl->tpl_vars['temp']->value).((string)$_smarty_tpl->tpl_vars['theme']->value)."/media/acp-style.css");?>

        <?php echo $_smarty_tpl->tpl_vars['header']->value->loadStyles();?>

        <?php echo $_smarty_tpl->tpl_vars['header']->value->loadScript("//ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js");?>

        <?php echo $_smarty_tpl->tpl_vars['header']->value->loadScript(((string)$_smarty_tpl->tpl_vars['home']->value).((string)$_smarty_tpl->tpl_vars['js']->value)."/acp.js");?>

        <?php echo $_smarty_tpl->tpl_vars['header']->value->loadScripts();?>

    </head><?php }
    }
