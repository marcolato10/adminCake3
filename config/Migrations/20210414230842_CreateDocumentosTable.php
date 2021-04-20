<?php
use Migrations\AbstractMigration;

class CreateDocumentosTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {

        $table = $this->table('documentos');
        $table->addColumn('documento', 'string', [
            'default' => null,
            'limit' => 50,
            'null' => false,
        ]);
        $table->addColumn('description', 'text', [
            'default' => null,
            'null' => false,
        ]);
       
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->create();


        /*
        $refTable = $this->table('documentos');
        $refTable->addColumn('user_id', 'integer', array('signed' => 'disable'))
                 ->addForeignKey('user_id', 'users', 'id', array('delete' => 'CASCADE', 'update' => 'NO_ACTION'))
                 ->update();
        */    


    }
}
