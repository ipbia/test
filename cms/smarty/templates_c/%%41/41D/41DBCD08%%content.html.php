<?php /* Smarty version 2.6.18, created on 2014-05-11 16:40:22
         compiled from tpls/v14/content.html */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['header'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php echo '
<style>
#content img{max-width:92%;}
</style>
'; ?>
<?php if ($this->_tpl_vars['ismap']): ?>
<?php echo $this->_tpl_vars['mapstr']; ?>

<?php else: ?>
<div class="sub">
    <h1><?php echo $this->_tpl_vars['content']['title']; ?>
</h1>
    <!--<center><img src="smarty/templates/tpls/<?php echo $this->_tpl_vars['site']['template']; ?>
/20130419161002-1033605144.jpg" tppabs="http://900029.3g/upfile/example/pic/20130419161002-1033605144.jpg" alt="招商加盟"></center>    <div class="view"><br />-->
　<?php echo $this->_tpl_vars['content']['content']; ?>
</div>
    <?php if ($this->_tpl_vars['previousContent']): ?><a href="<?php echo $this->_tpl_vars['previousContent']->link; ?>
" class="prevpage">上一条：<span><?php echo $this->_tpl_vars['previousContent']->title; ?>
</span></a><?php endif; ?>
	<?php if ($this->_tpl_vars['nextContent']): ?><a href="<?php echo $this->_tpl_vars['nextContent']->link; ?>
" class="nextpage">下一条：<span><?php echo $this->_tpl_vars['nextContent']->title; ?>
</span></a><?php endif; ?>
    <a href="<?php echo $this->_tpl_vars['channel']['link']; ?>
" title="返回列表" class="backlist"><span>返回列表</span></a>
</div>
<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['footer'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>