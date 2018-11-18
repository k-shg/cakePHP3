<!-- File: src/Template/Articles/index.ctp  (削除リンク付き) -->

<h1>記事一覧</h1>
<p><?= $this->Html->link("記事の追加", ['action' => 'add']) ?></p>
<table>
    <tr>
        <th>タイトル</th>
        <th>作成日時</th>
        <th>操作</th>
        <th>ユーザーID</th>
        <th>email</th>
        <th>タグ</th>
    </tr>

<!-- ここで、$articles クエリーオブジェクトを繰り返して、記事情報を出力します -->

<?php foreach ($articles as $article): ?>
    <tr>
        <td>
            <?= $this->Html->link($article->title, ['action' => 'view', $article->slug]) ?>
        </td>
        <td>
            <?= $article->created->format(DATE_RFC850) ?>
        </td>
        <td>
            <?= $this->Html->link('編集', ['action' => 'edit', $article->slug]) ?>
            <?= $this->Form->postLink(
                '削除',
                ['action' => 'delete', $article->slug],
                ['confirm' => 'よろしいですか?'])
            ?>
        </td>
        <td>
            <?= $article->user->id ?>
        </td>
        <td>
            <?= $article->user->email ?>
        </td>
        <td>
            <?php
            if($article->tags){
                foreach($article->tags as $tag){
                    echo $tag['title'];
                    echo "/";
                }
            } else {
                echo "-";
            }
             ?>
        </td>
    </tr>
<?php endforeach; ?>

</table>
