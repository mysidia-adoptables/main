<?php /* Smarty version Smarty-3.1.12, created on 2021-07-03 04:42:53
         compiled from "/home/mysidia/public_html/adoptables/demos/mys136b/templates/main/template.tpl" */ ?>
<?php /*%%SmartyHeaderCode:8064769160dfeacd20d2d1-04219111%%*/if (!defined('SMARTY_DIR')) {
    exit('no direct access allowed');
}
$_valid = $_smarty_tpl->decodeProperties([
  'file_dependency' =>
   [
    '08997d28f07cccbd82132f7de712c24809017b48' =>
     [
      0 => '/home/mysidia/public_html/adoptables/demos/mys136b/templates/main/template.tpl',
      1 => 1625283093,
      2 => 'file',
    ],
  ],
  'nocache_hash' => '8064769160dfeacd20d2d1-04219111',
  'function' =>
   [
  ],
  'variables' =>
   [
    'root' => 0,
    'temp' => 0,
    'theme' => 0,
    'menu' => 0,
    'path' => 0,
    'site_name' => 0,
    'sidebar' => 0,
    'document_title' => 0,
    'document_content' => 0,
    'footer' => 0,
  ],
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_60dfeacd2312e0_03492132',
], false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_60dfeacd2312e0_03492132')) {
    function content_60dfeacd2312e0_03492132($_smarty_tpl)
    {?><?php echo $_smarty_tpl->getSubTemplate(((string)$_smarty_tpl->tpl_vars['root']->value).((string)$_smarty_tpl->tpl_vars['temp']->value).((string)$_smarty_tpl->tpl_vars['theme']->value)."/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, [], 0);?>


<body>
<div id="wrapper">
<table cellspacing="0" cellpadding="0">
<tr><th colspan="2"><?php echo $_smarty_tpl->tpl_vars['menu']->value;?>
</th></tr>
<tr><td colspan="2" id="image"><span><a href="<?php echo $_smarty_tpl->tpl_vars['path']->value;?>
index"><?php echo $_smarty_tpl->tpl_vars['site_name']->value;?>
</a></span></td></tr>
<tr><td id="menu">
<?php echo $_smarty_tpl->tpl_vars['sidebar']->value;?>



</td><td id="content">
<h1><?php echo $_smarty_tpl->tpl_vars['document_title']->value;?>
</h1>
<p><?php echo $_smarty_tpl->tpl_vars['document_content']->value;?>
</p>

</td></tr>
<tr><td colspan="2" id="footer"><?php echo $_smarty_tpl->tpl_vars['footer']->value;?>
</td></tr>

</table>
</div>

</body>

</html><?php }
    } ?>