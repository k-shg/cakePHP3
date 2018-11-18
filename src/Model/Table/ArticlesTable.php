<?php
// src/Model/Table/ArticlesTable.php
namespace App\Model\Table;

use Cake\ORM\Table;
// Text クラス
use Cake\Utility\Text;
use Cake\Validation\Validator;
use Cake\ORM\Query;

class ArticlesTable extends Table
{
    //created や modified カラムを自動的に更新する
    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');
        $this->belongsToMany('Tags');
        $this->belongsTo('Users');
    }

    public function beforeSave($event, $entity, $options)
    {
        if ($entity->tag_string) {
            $entity->tags = $this->_buildTags($entity->tag_string);
        }

        if ($entity->isNew() && !$entity->slug) {
            $sluggedTitle = Text::slug($entity->title);
            // スラグをスキーマで定義されている最大長に調整
            $entity->slug = substr($sluggedTitle, 0, 191);
        }
    }

    public function validationDefault(Validator $validator)
    {
        $validator
        ->notEmpty('title')
        ->minLength('title', 10)
        ->maxLength('title', 255)

        ->notEmpty('body')
        ->minLength('body', 10);

        return $validator;
    }
    public function findTagged(Query $query, array $options)
    {
        $columns = [
        'Articles.id', 'Articles.user_id', 'Articles.title',
        'Articles.body', 'Articles.published', 'Articles.created',
        'Articles.slug',
        ];

        $query = $query
            ->select($columns)
            ->distinct($columns);

        if (empty($options['tags'])) {
            // タグが指定されていない場合は、タグのない記事を検索します。
            $query->leftJoinWith('Tags')
                ->where(['Tags.title IS' => null]);
        } else {
            // 提供されたタグが1つ以上ある記事を検索します。
            $query->innerJoinWith('Tags')
                ->where(['Tags.title IN' => $options['tags']]);
        }

        return $query->group(['Articles.id']);
    }

    protected function _buildTags($tagString)
    {

        debug($tagString);
        // タグをトリミング
        $newTags = array_map('trim', explode(',', $tagString));
        echo "タグをトリミング";
        debug($newTags);
        // 全てのからのタグを削除
        $newTags = array_filter($newTags);
        echo "からのタグを削除";
        debug($newTags);
        // 重複するタグの削減
        $newTags = array_unique($newTags);
        echo "重複するタグの削減";
        debug($newTags);

        $out = [];
        //かぶったタグリストを取得
        $query = $this->Tags->find()
            ->where(['Tags.title IN' => $newTags]);
        echo "かぶったタグリストを取得";
        debug($query->toArray());

        echo "extractしたときの値リスト" ;
        debug($query->extract('title'));

        // 新しいタグのリストから既存のタグを削除。
        foreach ($query->extract('title') as $existing) {
            $index = array_search($existing, $newTags);
            if ($index !== false) {
                unset($newTags[$index]);
            }
        }
        echo "新しいタグのリストから既存のタグを削除。";
        debug($newTags);
        // 既存のタグを追加。
        foreach ($query as $tag) {
            $out[] = $tag;
        }
        echo "既存のタグを追加。";
        debug($out);
        // 新しいタグを追加。
        foreach ($newTags as $tag) {
            $out[] = $this->Tags->newEntity(['title' => $tag]);
        }
        echo "新しいタグを追加。";
        debug($out);
        return $out;
    }
}
