<?php

use Phinx\Migration\AbstractMigration;

class AlterTableSpareAddColumns extends AbstractMigration
{

    /**
     * @var string название таблицы
     */
    protected $table = 'spares';
    protected $columnIsComposite = 'is_composite';
    protected $columnArticle = 'article';

    /**
     * Добавление колоноки
     *
     */
    public function up()
    {
        $table = $this->table($this->table);
        if (! $table->hasColumn($this->columnIsComposite) && ! $table->hasColumn($this->columnArticle)) {
            $table->addColumn($this->columnIsComposite, 'integer',
                [
                    'null' => true,
                    'limit' => 1,
                    'default' => 0,
                    'comment' => 'Определяет, что может делиться на штуки, литры итп'
                ])
                ->addColumn($this->columnArticle, 'string',
                    [
                        'limit' => 20,
                        'null' => true,
                    ])
                ->addIndex([$this->columnArticle], ['name' => $this->columnArticle])
                ->update();
        }
    }

    /**
     * Удаление колоноки
     *
     */
    public function down()
    {
        $table = $this->table($this->table);
        if ($table->hasColumn($this->columnIsComposite) && $table->hasColumn($this->columnArticle)) {
            $table->removeColumn($this->columnIsComposite)
                ->removeColumn($this->columnArticle)
                ->update();
        }
    }
}
