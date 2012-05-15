<?php
    echo View::block('header');
?>
            <!-- Header. Main part -->
            <div id="header-main"><div id='test'></div>
                <div class="container_12">
                    <div class="grid_12">
                        <div id="logo">
                            <ul id="nav">
                                <?php echo $mainmenu_for_layout; ?>
                            </ul>
                        </div><!-- End. #Logo -->
                    </div><!-- End. .grid_12-->
                    <div style="clear: both;"></div>
                </div><!-- End. .container_12 -->
            </div> <!-- End #header-main -->
            <div style="clear: both;"></div>
            <!-- Sub navigation -->
            <div id="subnav">
                <div class="container_12">
                    <div class="grid_12">
                        <?php echo $submenu_for_layout; ?>
                        
                    </div><!-- End. .grid_12-->
                </div><!-- End. .container_12 -->
                <div style="clear: both;"></div>
            </div> <!-- End #subnav -->
        </div> <!-- End #header -->
        
		<div class="container_12">


<?php echo $view_for_layout; ?>


        <div style="clear: both;"></div>

        </div>

<?php

    View::setLifeTime(1200);
    echo View::block('footer');
?>
