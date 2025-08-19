<?php
/* Smarty version 3.1.30, created on 2021-07-26 15:47:18
  from "/home/mysidia/public_html/adoptables/demos/mys136b/templates/green/template.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl,  [
  'version' => '3.1.30',
  'unifunc' => 'content_60fed906678752_94681767',
  'has_nocache_code' => false,
  'file_dependency' => 
   [
    'e89fb66e817d43993e76c80c93429dad70ead6b6' => 
     [
      0 => '/home/mysidia/public_html/adoptables/demos/mys136b/templates/green/template.tpl',
      1 => 1626365680,
      2 => 'file',
    ],
  ],
  'includes' => 
   [
  ],
],false)) {
function content_60fed906678752_94681767 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['root']->value).((string)$_smarty_tpl->tpl_vars['temp']->value).((string)$_smarty_tpl->tpl_vars['theme']->value)."/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, [], 0, true);
?>

    <body>
        <div id='content'>
            <ul id='toplist'><?php echo $_smarty_tpl->tpl_vars['menu']->value;?>
</ul>
            <h1><?php echo $_smarty_tpl->tpl_vars['site_name']->value;?>
</h1>
	        <div id='container'>
                <div id='right'>
                    <h2><?php echo $_smarty_tpl->tpl_vars['document_title']->value;?>
</h2>
                    <p id='text'><?php echo $_smarty_tpl->tpl_vars['document_content']->value;?>
</p>
                </div>
	            <div id='left'><?php echo $_smarty_tpl->tpl_vars['sidebar']->value;?>
</div>
                <div style='clear: both'> </div>
            </div>
	        <center><?php echo $_smarty_tpl->tpl_vars['footer']->value;?>
Theme by <a href="http://www.pixelpuppy.net" target="_blank">Arianna</a>.</center>
        </div>
        <div id='v1'> </div>
        <div id='v2'></div>
        <div id='v3'></div>
        <div id='v4'></div>
        <div id='v5'></div>
        <div id='h1'></div>
        <div id='h2'></div>
        <div id='h3'></div>
        <div id='h4'></div>
    </body>

</html>      <?php }
}
