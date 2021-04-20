<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Users Controller
 *
 */
class UsersController extends AppController
{

    
    public function beforeFilter(\Cake\Event\Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['add']); //nos permite usar las acciones aunque no estemos logueado
    }


   
    public function isAuthorized($user)
    {
        if(isset($user['role']) and $user['role'] === 'user')
        {
            if(in_array($this->request->action, ['home', 'view', 'logout'])) //acciones donde puede entrar
            {
                return true;
            }
        }

        return parent::isAuthorized($user);
    }





    public function login()
    {
        if($this->request->is('post'))
        {
            $user = $this->Auth->identify();
            if($user)
            {
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            }
            else
            {
                $this->Flash->error('Datos son invalidos, por favor intente nuevamente', ['key' => 'auth']);
            }
        }

            
        if ($this->Auth->user()) //nos redirecciona al home en caso que estemos autenticados
        {
            return $this->redirect(['controller' => 'Users', 'action' => 'home']);
        }
    }

    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }

    public function home()
    {
        $this->render();
    }

    public function index(){
       
        $users = $this->paginate($this->Users);
        $this->set('users',$users);

    }

    public function view(){
        echo "Estoy en el view";
        exit();
    }


    public function add(){
        $user = $this->Users->newEntity();

        if($this->request->is('post'))
        {
            $user = $this->Users->patchEntity($user, $this->request->data);

            $user->role = 'user';
            $user->active = 1;

            if($this->Users->save($user))
            {
                $this->Flash->success('El usuario ha sido creado correctamente.');
                return $this->redirect(['controller' => 'Users', 'action' => 'index']);
            }
            else
            {
                $this->Flash->error('El usuario no pudo ser creado. Por favor, intente nuevamente.');
            }
        }

        $this->set(compact('user'));
    }


    public function edit($id = null){

        $user = $this->Users->get($id);

        if ($this->request->is(['patch', 'post', 'put']))
        {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user))
            {
                $this->Flash->success('El usuario ha sido modificado');
                return $this->redirect(['action' => 'index']);
            }
            else
            {
                $this->Flash->error('El usuario no pudo ser modificado. Por favor, intente nuevamente.');
            }
        }

        $this->set(compact('user'));

    }



    public function delete($id = null)
    {
        
        debug('eliminamos a: '. $id);exit();

        /*
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);

        if ($this->Users->delete($user))
        {
            $this->Flash->success('El usuario ha sido eliminado.');
        }
        else
        {
            $this->Flash->error('El usuario no pudo ser eliminado. Por favor, intente nuevamente.');
        }
        return $this->redirect(['action' => 'index']);
        */
    
    }



}