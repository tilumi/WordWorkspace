<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php View::include_http_metas(); ?>
<?php View::include_title(); ?>
<?php View::include_stylesheets(); ?>
<?php View::include_javascripts(); ?>
<?php View::include_extra_headers(); ?>

<link href="<?php echo layout_url('main', '/css/default.css'); ?>" rel="stylesheet" type="text/css" media="all" />
<link href="<?php echo layout_url('main', '/css/colorbox.css'); ?>" rel="stylesheet" type="text/css" media="all" />
<style type="text/css">
@import "<?php echo layout_url('main', '/css/layout.css'); ?>";
</style>
<?php
$jsIndex = $tab_id;
if( isset($selectIndex) && is_numeric($selectIndex) ){
    $jsIndex = $selectIndex;
}
?>

<script type="text/javascript" src="<?php echo layout_url('main', '/js/jquery.min.js'); ?>"></script>
<?php $using_ie6 = (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.') !== false ); ?>
<?php if( ! $using_ie6 ){ //ie6的使用者，不啟用JS功能 ?>
<script type="text/javascript" src="<?php echo layout_url('main', '/js/jquery.ui.core.js'); ?>"></script>
<script type="text/javascript" src="<?php echo layout_url('main', '/js/jquery.ui.widget.js'); ?>"></script>
<script type="text/javascript" src="<?php echo layout_url('main', '/js/jquery.ui.mouse.js'); ?>"></script>
<script type="text/javascript" src="<?php echo layout_url('main', '/js/jquery.ui.slider.js'); ?>"></script>

<script type="text/javascript" src="<?php echo layout_url('main', '/js/jquery.colorbox-min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo url('/javascript/init/'.$jsIndex.'.js'); ?>"></script>

<?php } ?>

<meta name="google-site-verification" content="Sl77l_lzmUMySMszkvZsOp9FD5ussf3G14LvlThAP8w" />
<META name="y_key" content="742d689dc907a4c3" />

<link rel="image_src" type="image/jpeg" href="<?php echo layout_url('main', '/images/sticker.jpg'); ?>" />
<link rel="shortcut icon" href="<?php echo url('/favicon.ico'); ?>">

</head>
