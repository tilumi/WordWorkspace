<?php include('tpl_metadata.php'); ?>

<?php if( isset($fixing_topnav) ){ ?>
<style>
.lite #wrapper{
    background-attachment:fixed;
}
.lite #wrapper-bgtop{
    background:url(<?php echo layout_url('main', '/images/bg3_02-0.jpg')?>) center top;
    background-attachment:fixed;
}
.lite #page{
    background:url(<?php echo layout_url('main', '/images/bg3_02-1.jpg')?>) center top no-repeat;
}
#topnav-container{
    margin:0 auto;
    height:80px;
    width:1000px;
}
#topnav-container #topnav{
    background:url(<?php echo layout_url('main', '/images/bg3_02.jpg')?>) -89px 0px;
    position:fixed;
}
#topnav-container .midnav{
    background:url(<?php echo layout_url('main', '/images/bg3_02.jpg')?>) -89px -20px;
    position:fixed;
    top:20px;
}
</style>
<?php } ?>


<body class="lite">
<div id="wrapper">
	<div id="wrapper-bgtop">
		<div id="wrapper-bgbtm">

            <div id="topnav-container">
<?php include('tpl_topnav.php'); ?>


<?php include('tpl_mainmenu.php'); ?>
            </div>
