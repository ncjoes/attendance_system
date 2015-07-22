<div class="">
    <div class="navigation-bar">
        <div class="container">
            <div class="navigation-bar-content">
                <a class="element  bg-hover-dark" href="index.php"><?=COMPANY_NAME?></a>
                <span class="element-divider"></span>
                <a class="element1 pull-menu" href="#"></a>
                
                <div class="">
                    <span class="element-divider place-right"></span>
                    <?php if (isset($user) && $user->isLoggedIn()) { ?>
                        <a href="logout.php" title="Logout" class="element bg-transparent bg-hover-dark place-right">
                            <i class="icon-exit"></i> &nbsp Logout
                        </a>
                    <?php }?>
                </div>
            </div>
        </div>
    </div>
</div>
