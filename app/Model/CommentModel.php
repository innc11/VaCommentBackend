<?php

namespace Model;

class CommentModel extends ModelBase
{
    public int $time;
    public string $content;
    public UserModel $author;
    public UserModel $recipient;
    public string $permalink;

    public function getFormatedDateTime()
    {
        return date('Y-n-j H-i-s', $this->time);
    }

    public static function FromArray(array $obj)
    {
        $comment = new CommentModel();
        $comment->time = $obj['time'];
        $comment->content = $obj['content'];
        $comment->author = UserModel::FromArray($obj['author']);
        $comment->recipient = UserModel::FromArray($obj['recipient']);
        $comment->permalink = $obj['permalink'];

        return $comment;
    }
}

?>