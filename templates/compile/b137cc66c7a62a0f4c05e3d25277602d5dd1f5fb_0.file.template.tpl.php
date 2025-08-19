<?php
/* Smarty version 3.1.30, created on 2021-07-08 17:43:07
  from "/home/mysidia/public_html/adoptables/demos/mys136b/templates/acp/template.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl,  [
  'version' => '3.1.30',
  'unifunc' => 'content_60e7392babe0b2_42016627',
  'has_nocache_code' => false,
  'file_dependency' => 
   [
    'b137cc66c7a62a0f4c05e3d25277602d5dd1f5fb' => 
     [
      0 => '/home/mysidia/public_html/adoptables/demos/mys136b/templates/acp/template.tpl',
      1 => 1625766185,
      2 => 'file',
    ],
  ],
  'includes' => 
   [
  ],
],false)) {
function content_60e7392babe0b2_42016627 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['root']->value).((string)$_smarty_tpl->tpl_vars['temp']->value).((string)$_smarty_tpl->tpl_vars['theme']->value)."/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, [], 0, true);
?>


    <body>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td colspan="2" class="banner">
                    <center>
                        <img src="<?php echo $_smarty_tpl->tpl_vars['home']->value;
echo $_smarty_tpl->tpl_vars['temp']->value;
echo $_smarty_tpl->tpl_vars['theme']->value;?>
/media/acp-banner.png" alt="Mysidia PHP Adoptables" title="Mysidia Adoptables" />
                    </center>
                </td>
            </tr>
            <tr>
                <th width="25%" id="logo"><strong>MyMysidia</strong> Admin</th>
                <th id="admin">Welcome Admin!</th>
            </tr>
            <tr>
                <td width="25%" id="menu"><?php echo $_smarty_tpl->tpl_vars['sidebar']->value;?>
</td>
                <td id="content">
                    <p><font size="5"><b><?php echo $_smarty_tpl->tpl_vars['document_title']->value;?>
</b></font></p>
                    <hr>
                    <p><?php echo $_smarty_tpl->tpl_vars['document_content']->value;?>
</p>
                </td>
            </tr>
        </table>

        <center>
            <b>MyMysidia</b> Powered By <a href="https://mysidiaadoptables.com">Mysidia Adoptables</a> &copy;Copyright 2011-2021.
        </center>
    </body>    
</html><?php }
}
