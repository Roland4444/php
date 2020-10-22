<?php

use Phinx\Migration\AbstractMigration;

class UpdateSparesTableRenameArticleToComment extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('spares');
        $table->renameColumn('article', 'comment');
    }

    public function down()
    {
        $table = $this->table('spares');
        $table->renameColumn('comment', 'article');
    }
}
