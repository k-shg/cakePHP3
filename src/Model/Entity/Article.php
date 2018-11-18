<?php
// src/Model/Entity/Article.php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Collection\Collection;
use Cake\Utility\Text;

class Article extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'slug' => false,
    ];

    protected function _getTagString()
    {
        
        debug("tag_stringの値は". $this->_properties['tag_string']);

        if (isset($this->_properties['tag_string'])) {
            return $this->_properties['tag_string'];
        }
        if (empty($this->tags)) {
            return '';
        }
        $tags = new Collection($this->tags);
        echo "コレクションreduceをします<br>" ;
        $str = $tags->reduce(function ($string, $tag) {
            debug("stringの値は、".$string);
            debug("タグの値は、".$tag);
            debug("リターンする文字列は、".$string . $tag->title . ",");
            return $string . $tag->title . ', ';
        }, '');
        echo "strの文字は、".$str."<br>";
        $this->_properties['tag_string'] = $str;
        echo "セットしました<br>" ;
        debug($this);
        debug("tag_stringの値は". $this->_properties['tag_string']);
        return trim($str, ', ');
    }



    // protected function _getTitle($title) {
    //     return ucwords($title);
    // }
    // protected function _getBody($body) {
    //     return ucwords($body);
    // }

    protected function _setTitle($title) {
        $this->_properties['shogo_string'] = "shogo";
        return $title;
    }

    protected function _getTitleBody() {
        return $this->_properties['title'].$this->_properties['body'];
    }
}
