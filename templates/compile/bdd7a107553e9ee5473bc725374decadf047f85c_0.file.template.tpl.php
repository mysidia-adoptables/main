<?php
/* Smarty version 3.1.30, created on 2021-08-06 17:35:32
  from "/home/mysidia/public_html/adoptables/demos/mys136b/templates/elements/template.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_610d72e41fc698_27026195',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'bdd7a107553e9ee5473bc725374decadf047f85c' => 
    array (
      0 => '/home/mysidia/public_html/adoptables/demos/mys136b/templates/elements/template.tpl',
      1 => 1628271153,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_610d72e41fc698_27026195 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['root']->value).((string)$_smarty_tpl->tpl_vars['temp']->value).((string)$_smarty_tpl->tpl_vars['theme']->value)."/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>


    <body>
        <div id="wrapper">
            <div id="left">
                <img src="<?php echo $_smarty_tpl->tpl_vars['home']->value;
echo $_smarty_tpl->tpl_vars['temp']->value;
echo $_smarty_tpl->tpl_vars['theme']->value;?>
/inc/images/left.png">
                <ul><?php echo $_smarty_tpl->tpl_vars['menu']->value;?>
</ul> 
            </div>
            <div id="center">
                <img src="<?php echo $_smarty_tpl->tpl_vars['home']->value;
echo $_smarty_tpl->tpl_vars['temp']->value;
echo $_smarty_tpl->tpl_vars['theme']->value;?>
/inc/images/center.png">
                <h1><?php echo $_smarty_tpl->tpl_vars['document_title']->value;?>
</h1>
                <p><?php echo $_smarty_tpl->tpl_vars['document_content']->value;?>
</p>
            </div>

            <div id="right">
                <img src="<?php echo $_smarty_tpl->tpl_vars['home']->value;
echo $_smarty_tpl->tpl_vars['temp']->value;
echo $_smarty_tpl->tpl_vars['theme']->value;?>
/inc/images/right.png">
                <p><?php echo $_smarty_tpl->tpl_vars['sidebar']->value;?>
</p>
            </div>

            <div id="footer">
                <?php echo $_smarty_tpl->tpl_vars['footer']->value;?>

                <p>Theme by <a href="http://sessions-st.net">Sessions Street</a></p> 
            </div>
        </div>
    </body>

</html><?php }
}
