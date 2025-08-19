<?php
/* Smarty version 3.1.30, created on 2021-07-26 15:46:39
  from "/home/mysidia/public_html/adoptables/demos/mys136b/templates/main/template.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, [
  'version' => '3.1.30',
  'unifunc' => 'content_60fed8df38f324_76610138',
  'has_nocache_code' => false,
  'file_dependency' =>
   [
    '08997d28f07cccbd82132f7de712c24809017b48' =>
     [
      0 => '/home/mysidia/public_html/adoptables/demos/mys136b/templates/main/template.tpl',
      1 => 1625500105,
      2 => 'file',
    ],
  ],
  'includes' =>
   [
  ],
], false)) {
    function content_60fed8df38f324_76610138(Smarty_Internal_Template $_smarty_tpl)
    {
        $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['root']->value).((string)$_smarty_tpl->tpl_vars['temp']->value).((string)$_smarty_tpl->tpl_vars['theme']->value)."/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, [], 0, true);
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
                    <td id="menu"><?php echo $_smarty_tpl->tpl_vars['sidebar']->value;?>
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

</html><?php }
    }
