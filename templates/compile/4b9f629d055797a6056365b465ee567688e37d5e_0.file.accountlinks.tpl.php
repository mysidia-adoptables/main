<?php
/* Smarty version 3.1.30, created on 2021-07-26 16:33:21
  from "/home/mysidia/public_html/adoptables/demos/mys136b/templates/common/accountlinks.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl,  [
  'version' => '3.1.30',
  'unifunc' => 'content_60fee3d183cb60_02009827',
  'has_nocache_code' => false,
  'file_dependency' => 
   [
    '4b9f629d055797a6056365b465ee567688e37d5e' => 
     [
      0 => '/home/mysidia/public_html/adoptables/demos/mys136b/templates/common/accountlinks.tpl',
      1 => 1627317199,
      2 => 'file',
    ],
  ],
  'includes' => 
   [
  ],
],false)) {
function content_60fee3d183cb60_02009827 (Smarty_Internal_Template $_smarty_tpl) {
?>
<a href='<?php echo $_smarty_tpl->tpl_vars['scriptPath']->value;?>
/myadopts'>Manage Adoptables</a>
<br>
<a href='<?php echo $_smarty_tpl->tpl_vars['scriptPath']->value;?>
/profile/view/<?php echo $_smarty_tpl->tpl_vars['mysidia']->value->user->getID();?>
'>View Profile</a>
<br>
<a href='<?php echo $_smarty_tpl->tpl_vars['scriptPath']->value;?>
/account/password'>Change Password</a>
<br>
<a href='<?php echo $_smarty_tpl->tpl_vars['scriptPath']->value;?>
/account/email'>Change Email Address</a>
<br>
<a href='<?php echo $_smarty_tpl->tpl_vars['scriptPath']->value;?>
/mys136b/account/friends'>View and Manage FriendList</a>
<br>
<a href='<?php echo $_smarty_tpl->tpl_vars['scriptPath']->value;?>
/mys136b/account/profile'>More Profile Settings</a>
<br>
<a href='<?php echo $_smarty_tpl->tpl_vars['scriptPath']->value;?>
/mys136b/account/contacts'>Change Other Settings</a><?php }
}
