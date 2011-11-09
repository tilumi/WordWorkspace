<?php include('tpl_metadata.php'); ?>

<body class="homepage">
<div id="wrapper">
	<div id="wrapper-bgtop">
		<div id="wrapper-bgbtm">

<?php include('tpl_topnav.php'); ?>

			<div id="header" class="container">
				<div id="logo">
					<h1><a href="<?php echo url('/');?>">The Bible 線上聖經</a></h1>
				</div>
			</div>
<style>
.tooltip {
	display:none;
	position: absolute;
	background:transparent url(<?php echo layout_url('main', '/images/tooltip/white_arrow_wide.png'); ?>);
	font-size:12px;
	height:90px;
	width:730px;
    left: 55px;
    top: 50px;
	padding:25px 35px;
	color:#113e5f;
	line-height:16px;
	text-align:left;
}
.tooltip strong{
	color:#113e5f;	
}
#menu-container{float:right; width:890px;}
.logo{display:none;}
</style>
<!-- use gif image for IE -->
<!--[if lt IE 7]>
<style>
.tooltip {
	background-image:url(<?php echo layout_url('main', '/images/tooltip/black_arrow.gif'); ?>);
}

</style>
<![endif]-->
			<div id="splash" class="container">
<!--
				<h2>Veroeros lorem ipsum dolor<br />
					<span>et consequat sit amet.</span></h2>
				<p class="byline">Maecenas luctus dapibus adipiscing donec at elit<br />
					ut mollis augue rhoncus suspendisse.</p>
				<p><a href="#" class="link">Ante ipusm faucibus &#8230;</a></p>
-->
                <div class="tooltip">
                    <div style="float:left;width:170px;margin-right:20px;">
                        <img src="<?php echo layout_url('main', '/images/tooltip/scroller-1.png'); ?>" style="width:140px;margin:10px 0px 10px 0;"><br>
                        <strong>快速切換</strong><br>點選書卷位置，或移動快選框
                    </div>
                    <div style="float:left;width:150px;margin-right:60px;">
                        <img src="<?php echo layout_url('main', '/images/tooltip/scroller-2.png'); ?>" style="width:140px;height:27px;margin:10px 0px 10px 0;"><br>
                        <strong>快速閱讀</strong><br>選擇書卷及章節
                    </div>
                    <div style="float:left;width:150px;margin-right:30px;">
                        <img src="<?php echo layout_url('main', '/images/tooltip/scroller-3.png'); ?>" style="width:140px;height:27px;margin:10px 0px 10px 0;"><br>
                        <strong>輕輕移動</strong><br>選擇鄰近書卷
                    </div>
                    <div style="float:left;width:150px;">
                        <img src="<?php echo layout_url('main', '/images/tooltip/scroller-4.png'); ?>" style="width:140px;height:27px;margin:10px 0px 10px 0;"><br>
                        <strong>恢復狀態</strong><br>恢復原始位置
                    </div>
                </div>
                <div class="tooltip-button" title="操作說明"></div>
			</div>



<?php include('tpl_mainmenu.php'); ?>

