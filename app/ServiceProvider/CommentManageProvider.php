<?php

namespace ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class CommentManageProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['router']->respond('GET',  '/comment_manage_login', fn(...$params) => self::onLogin(...$params),);
        $container['router']->respond('POST',  '/comment_manage_home', fn(...$params) => self::onListComment(...$params),);
        $container['router']->respond('POST', '/comment_manage_erase', fn(...$params) => self::onDoErase(...$params),);
        $container['router']->respond('POST', '/comment_manage_erase_comfirm', fn(...$params) => self::onEraseComfirm(...$params),);
    }

    public static function onLogin($request, $response, $service, $app)
    {
        self::beginHtml();
        ?>
        <form action="comment_manage_home" method="post">
            username: <br>
            <input type="text" name="user"></input><br>

            password: <br>
            <input type="text" name="password"></input><br>

            <button>Login In</button>
        </form>
        <?php
        self::endHtml();
    }

    public static function onListComment($request, $response, $service, $app)
    {
        if(!self::authenticate($request, $response))
            return;

        $db = $app->container['database'];
        $adminUser = $request->param('user');
        $adminPasswd = $request->param('password');

        self::beginHtml();
        ?>
        <table border="1" cellspacing="0">
            <tr>
                <th>Actions</th>
                <th>ID</th>
                <th>Parent</th>
                <th>Key</th>
                <th>Comment</th>
                <th>Nick</th>
                <th>Mail</th>
                <th>Website</th>
                <th>Content</th>
                <th>Approved</th>
                <th>Time</th>
                <th>IP</th>
                <th>UserAgent</th>
            </tr>

            <?php
                $sql = "select * from 'comments' order by time desc";
                $rows = $db->prepare($sql)->execute()->fetchAll(); 
            ?>

            <?php foreach($rows as $row): ?>
            <tr>
                <td>
                    <form action="comment_manage_erase_comfirm" method="post">
                        <input type="text" name="user" value="<?php echo($adminUser); ?>" style="display: none"></input>
                        <input type="text" name="password" value="<?php echo($adminPasswd); ?>" style="display: none"></input>
                        <button name="id" value="<?php echo($row['id']); ?>">Erase</button>
                    </form>
                </td>

                <td class="id"><?php echo($row['id']); ?></td>
                <td class="parent"><?php echo($row['parent']); ?></td>
                <td class="key"><?php echo($row['key']); ?></td>
                <td class="label"><?php echo($row['label']); ?></td>
                <td class="nick"><?php echo($row['nick']); ?></td>
                <td class="mail"><?php echo($row['mail']); ?></td>
                <td class="website"><?php echo($row['website']); ?></td>
                <td class="content"><?php echo($row['content']); ?></td>
                <td class="approved"><?php echo($row['approved']); ?></td>
                <td class="time"><?php echo($row['time']); ?></td>
                <td class="ip"><?php echo($row['ip']); ?></td>
                <td class="useragent"><?php echo($row['useragent']); ?></td>

            </tr>
            <?php endforeach; ?>
            
        </table>
        <style>
            td.content { max-width: 800px; }
        </style>
    
        <?php
        self::endHtml();
    }

    public static function onEraseComfirm($request, $response, $service, $app)
    {
        if(!self::authenticate($request, $response))
            return;

        $db = $app->container['database'];
        $adminUser = $request->param('user');
        $adminPasswd = $request->param('password');
        $id = $request->param('id');

        $sql = "select * from 'comments' where id = :id";
        $row = $db->prepare($sql)->execute(['id' => $id])->fetch(); 

        self::beginHtml();
        ?>
        <h3>Confirm to erase the following? or go back.</h3>

        <form action="comment_manage_erase" method="post">
            <input type="text" name="user" value="<?php echo($adminUser); ?>" style="display: none"></input>
            <input type="text" name="password" value="<?php echo($adminPasswd); ?>" style="display: none"></input>
            <button name="id" value="<?php echo($id); ?>">Confirm</button>
        </form>
        <table border="1" cellspacing="0">
            <tr><td>ID</td><td><?php echo($row['id']); ?></td></tr>
            <tr><td>Parent</td><td><?php echo($row['parent']); ?></td></tr>
            <tr><td>Key</td><td><?php echo($row['key']); ?></td></tr>
            <tr><td>Label</td><td><?php echo($row['label']); ?></td></tr>
            <tr><td>Nick</td><td><?php echo($row['nick']); ?></td></tr>
            <tr><td>Mail</td><td><?php echo($row['mail']); ?></td></tr>
            <tr><td>Website</td><td><?php echo($row['website']); ?></td></tr>
            <tr><td>Content</td><td><?php echo($row['content']); ?></td></tr>
            <tr><td>Approved</td><td><?php echo($row['approved']); ?></td></tr>
            <tr><td>Time</td><td><?php echo($row['time']); ?></td></tr>
            <tr><td>IP</td><td><?php echo($row['ip']); ?></td></tr>
            <tr><td>UserAgent</td><td><?php echo($row['useragent']); ?></td></tr>
        </table>
        <?php
        self::endHtml();
    }

    public static function onDoErase($request, $response, $service, $app)
    {
        if(!self::authenticate($request, $response))
            return;

        $db = $app->container['database'];
        $adminUser = $request->param('user');
        $adminPasswd = $request->param('password');
        $id = $request->param('id');
        
        $sql = "delete from 'comments' where id = :id";
        $row = $db->prepare($sql)->execute(['id' => $id])->end();

        self::beginHtml();
        echo($id.' has been erased');
        ?>
        
        <form action="comment_manage_home" method="post">
            <input type="text" name="user" value="<?php echo($adminUser); ?>" style="display: none"></input>
            <input type="text" name="password" value="<?php echo($adminPasswd); ?>" style="display: none"></input>
            <button>Back</button>
        </form>
        <?php
        self::endHtml();
    }

    public static function beginHtml()
    {
        ?>
        <!DOCTYPE HTML>
        <html>
            <head>
                <meta charset="utf-8">
                <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
            </head>
            <body style="width: max-content; margin: 30px auto;">
        <?php
    }

    public static function endHtml()
    {
        ?>
            </body>
        </html>
        <?php
    }

    public static function authenticate($request, $response)
    {
        $adminUser = $request->param('user');
        $adminPasswd = $request->param('password');
        if($adminUser != ADMIN_USER || $adminPasswd != ADMIN_PASSWORD)
        {
            $response->code(403);
            self::beginHtml();
            ?>
            <div>Wrong Username or Password</div>
            <form action="comment_manage_login" method="post">
                <button>Retry</button>
            </form>
            <?php
            self::endHtml();
            return false;
        }

        return true;
    }
}

?>