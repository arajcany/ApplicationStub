<?php
/**
 * @var App\View\AppView $this
 */

$user = $this->request->getSession()->read('Auth.User');
?>
<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
    <?php
    $title = 'Application Stub';
    $url = ['controller' => 'Pages'];
    $options = [
        'class' => "navbar-brand"
    ];
    echo $this->Html->link($title, $url, $options)
    ?>

    <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">

            <li class="nav-item active">
                <?php
                $title = 'Item';
                $url = ['controller' => '#', 'action' => '#'];
                $options = [
                    'class' => "nav-link"
                ];
                echo $this->Html->link($title, $url, $options)
                ?>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="http://example.com" id="dropdown01" data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false">Dropdown</a>
                <div class="dropdown-menu" aria-labelledby="dropdown01">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <a class="dropdown-item" href="#">Something else here</a>
                </div>
            </li>

        </ul>

        <?php if (isset($user) && $user) { ?>
            <?php if ($this->AuthUser->hasRoles(['superadmin', 'admin'])) { ?>
                <ul class="navbar-nav ml-auto">

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown"
                           aria-haspopup="true" aria-expanded="false">Admin Menu</a>
                        <div class="dropdown-menu" aria-labelledby="dropdown02">
                            <?php
                            $title = 'Settings';
                            $url = ['controller' => 'settings', 'action' => 'index'];
                            $options = [
                                'class' => "dropdown-item"
                            ];
                            echo $this->Html->link($title, $url, $options)
                            ?>
                        </div>
                    </li>

                </ul>
            <?php } ?>

            <ul class="nav navbar-nav ml-5">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle nav-link" data-toggle="dropdown" href="#" role="button"
                       aria-haspopup="true" aria-expanded="false">

                        <?= $user['first_name'] ?> <?= $user['last_name'] ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <?php
                        $navLinkOpts = [
                            "class" => "dropdown-item",
                            'escape' => false
                        ];
                        echo $this->Html->link(
                            '<i class="fa fa-user"></i> Profile',
                            ['controller' => 'users', 'action' => 'profile'],
                            $navLinkOpts
                        )
                        ?>
                        <?php
                        $navLinkOpts = [
                            "class" => "dropdown-item",
                            'escape' => false
                        ];
                        echo $this->Html->link(
                            '<i class="fa fa-lock"></i> Logout',
                            ['controller' => 'users', 'action' => 'logout'],
                            $navLinkOpts
                        )
                        ?>
                    </div>
                </li>
            </ul>
        <?php } ?>
    </div>
</nav>
