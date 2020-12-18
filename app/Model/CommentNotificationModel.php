<?php

namespace Model;

class CommentNotificationModel extends CommentModel
{
    public string $subject;
    public string $purpose; // '评论通知' or '评论审核'
    public bool $testMode = false;

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