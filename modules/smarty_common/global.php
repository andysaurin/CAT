<?php

$this->template->assign('module', $_GET['module'] );
$this->template->assign('class', $_GET['class'] );
$this->template->assign('event', $_GET['event'] );

$this->template->assign('module_title', $this->module->module_title);
$this->template->assign('class_title',$this->module->class_title);

?>