<?php
namespace App\Model\Table;

use App\Model\Entity\User;  //no se para que es
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \App\Model\Table\DocumentosTable&\Cake\ORM\Association\BelongsTo $Documentos
 *
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Documentos', [
            'foreignKey' => 'documento_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id','valid',['rule' => 'numeric'])
            ->notEmpty('id', 'create');

        $validator
            ->scalar('first_name')
            ->maxLength('first_name', 100)
            ->requirePresence('first_name', 'create') //permite que nuestro campo este presente
            ->notEmptyString('first_name','Rellene este campo'); //que no sea vacio

        $validator
            ->scalar('last_name')
            ->maxLength('last_name', 100)
            ->requirePresence('last_name', 'create')
            ->notEmptyString('last_name','Rellene este campo');

        $validator
            
            ->add('email','valid',['rule' => 'email','message' => 'Ingrese un correo electrónico válido.'])
            ->requirePresence('email', 'create')
            ->notEmptyString('email','Rellene este campo');

        $validator
            ->scalar('password')
            ->maxLength('password', 255)
            ->requirePresence('password', 'create') 
            ->notEmptyString('password','Rellene este campo','create'); //el 3er parametro "create" permite que solo se use esta validacion en el metodo create solamente

        $validator
            ->scalar('role')
            ->requirePresence('role', 'create')
            ->notEmptyString('role');

        $validator
            ->boolean('active')
            ->requirePresence('active', 'create')
            ->notEmptyString('active');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['email'], 'Ya existe un usuario con este correo electrónico.'));
        //$rules->add($rules->isUnique(['first_name'], 'Ya existe un usuario con este nombre.'));
        return $rules;
    }


    //restringe los campos que se usaran en la autenticacion
    //solo puede tener accesos a los campos definidos en este metodo
    //se puede condicionar usando un where
    public function findAuth(\Cake\ORM\Query $query, array $options)
    {
        $query
            ->select(['id', 'first_name', 'last_name', 'email', 'password', 'role'])
            ->where(['Users.active' => 1]);

        return $query;
    }

    //recupera el password del usuario 
    //llamada: desde la entidad User.php    
    public function recoverPassword($id)
    {
        $user = $this->get($id);
        return $user->password;
    }

    public function beforeDelete($event, $entity, $options)
    {
        if ($entity->role == 'admin')
        {
            return false;
        }
        return true;
    }
}
