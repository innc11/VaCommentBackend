<?php

namespace Model;

class CommentNotificationModel extends CommentModel
{
    public $subject; // string
    public $purpose; // string '评论通知' or '评论审核'
    public $testMode = false;  // bool

    public static function FromArray(array $obj)
    {
        $ob = new CommentNotificationModel();
        $ob->time = $obj['time'];
        $ob->content = $obj['content'];
        $ob->author = UserModel::FromArray($obj['author']);
        $ob->recipient = UserModel::FromArray($obj['recipient']);
        $ob->permalink = $obj['permalink'];

        $ob->subject = $obj['subject'];
        $ob->purpose = $obj['purpose'];
        $ob->testMode = $obj['testMode'];

        return $ob;
    }
}

?>