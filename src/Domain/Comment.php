<?php

namespace MicroCMS\Domain;

class Comment 
{
    /**
     * Comment id.
     *
     * @var integer
     */
    private $id;

    /**
     * Comment author.
     *
     * @var \MicroCMS\Domain\User
     */
    private $author;

    /**
     * Comment content.
     *
     * @var integer
     */
    private $content;

    /**
     * Associated article.
     *
     * @var \MicroCMS\Domain\Article
     */
    private $article;

    /**
    * Associated comment
    *
    * @var integer
    */
    private $parentId = 0;

    /**
    * A list of all child comments
    *
    * @var Comment[]
    */
    private $childComments;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function setAuthor(User $author) {
        $this->author = $author;
        return $this;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    public function getArticle() {
        return $this->article;
    }

    public function setArticle(Article $article) {
        $this->article = $article;
        return $this;
    }

    public function getParentId() {
        return $this->parentId;
    }

    public function setParentId($parentComment) {
        $this->parentId = $parentComment;
    }

    public function getChildComments() {
        return $this->childComments;
    }

    public function setChildComments(array $comments) {
        $this->childComments = $comments;
    }
}