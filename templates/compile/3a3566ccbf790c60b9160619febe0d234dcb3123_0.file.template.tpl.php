<?php
/* Smarty version 3.1.30, created on 2021-08-06 19:43:48
  from "/home/mysidia/public_html/adoptables/demos/mys136b/templates/main/template.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_610d90f4a7fa50_37694815',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3a3566ccbf790c60b9160619febe0d234dcb3123' => 
    array (
      0 => '/home/mysidia/public_html/adoptables/demos/mys136b/templates/main/template.tpl',
      1 => 1628279019,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_610d90f4a7fa50_37694815 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['root']->value).((string)$_smarty_tpl->tpl_vars['temp']->value).((string)$_smarty_tpl->tpl_vars['theme']->value)."/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>


    <body>
        <div id="wrapper">
            <table cellspacing="0" cellpadding="0">
                <tr><th colspan="2"><?php echo $_smarty_tpl->tpl_vars['menu']->value;?>
</th></tr>
                <tr><td colspan="2" id="image"><span><a href="<?php echo $_smarty_tpl->tpl_vars['path']->value;?>
index"><?php echo $_smarty_tpl->tpl_vars['site_name']->value;?>
</a></span></td></tr>
                <tr>
                    <td id="menu">
                        <?php echo $_smarty_tpl->tpl_vars['sidebar']->value;?>

                        <br>
                        <?php echo $_smarty_tpl->tpl_vars['stats']->value;?>

                    </td>
                    <td id="content">
                        <h1><?php echo $_smarty_tpl->tpl_vars['document_title']->value;?>
</h1>
                        <p><?php echo $_smarty_tpl->tpl_vars['document_content']->value;?>
</p>

                    </td>
                </tr>
                <tr>
                    <td colspan="2" id="footer"><?php echo $_smarty_tpl->tpl_vars['footer']->value;?>
</td>
                </tr>
            </table>
        </div>
    </body>

</html> <?php }
}
