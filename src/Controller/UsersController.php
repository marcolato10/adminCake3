<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Mailer\Email;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Utility\Security;
use Cake\ORM\TableRegistry;

/**
 * Users Controller
 *
 */
class UsersController extends AppController
{

    
    public function beforeFilter(\Cake\Event\Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['add','forgotpassword','resetpassword']); //nos permite usar las acciones aunque no estemos logueado
       

     

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




    public function forgotpassword(){

        if($this->request->is('post')){
            $myemail = $this->request->getData('email');
            $mytoken = Security::hash(Security::randomBytes(25));

            $userTable = TableRegistry::get('Users');
            $user = $userTable->find('all')->where(['email'=>$myemail])->first();
            $user->password='';
            $user->token=$mytoken;
            if($userTable->save($user)){
                $this->Flash->success('El enlace de restablecimiento de contraseña se ha enviado a su correo electrónico ('.$myemail.' 
                por favor abra su bandeja de entrada.)');

              

                    Email::configTransport('mail', [
                        'host' => 'ssl://smtp.gmail.com', //servidor smtp con encriptacion ssl
                        'port' => 465, //puerto de conexion
                        
                        
                        //cuenta de correo gmail completa desde donde enviaran el correo
                        'username' => 'latoprogramador@gmail.com', 
                        'password' => 'profesorsi', //contrasena
                        
                        //Establecemos que vamos a utilizar el envio de correo por smtp
                        'className' => 'Smtp', 
                        
                        //evitar verificacion de certificado ssl ---IMPORTANTE---
                        /*'context' => [
                          'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                          ]
                        ]*/
                      ]); 
                      /*fin configuracion de smtp*/

                 /*enviando el correo*/
                $correo = new Email(); //instancia de correo
                $correo
                ->transport('mail') //nombre del configTrasnport que acabamos de configurar
                ->emailFormat('html') //formato de correo
                ->to($myemail) //correo para
                ->from('latoprrogramador@gmail.com') //correo de
                ->subject('Correo de prueba en cakephp3') //asunto
                ->send();
               ;          

                /*
                Email::configTransport('mailtrap', [
                    'host' => 'smtp.mailtrap.io',
                    'port' => 2525,
                    'username' => 'cb21c197330efc',
                    'password' => 'f35bd3ca28ba24',
                    'className' => 'Smtp'
                  ]);

                $email = new Email('default');
                $email->transport('mailtrap');
                $email->emailFormat('html');
                $email->from('latoprogramador@gmail.com','Marco Latorre');
                $email->subject('Por favor confirme su restablecimiento de contraseña.');
                $email->to($myemail);
                $email->send('Hola '.$myemail.'</br></br>haga clic en el enlace de abajo para restablecer su contraseña</br></br><a href="http://adminCake3/users/resetpassword/'.$mytoken.'">
                restablecer la contraseña</a>');    
                */    
                    
            }
        
        }

    }
    
    
    public function resetpassword($token){

        if($this->request->is('post')){
            $hasher= new DefaultPasswordHasher();
            //$mypass= $hasher->hash($this->request->getData('password'));
            $mypass= $this->request->getData('password');
            $userTable = TableRegistry::get('Users');
            $user = $userTable->find('all')->where(['token'=>$token])->first();
            $user->password = $mypass;
            
            if($userTable->save($user)){
            return $this->redirect(['action' => 'login']);
            }
        
        }    
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
        //$this->set('users',$users);
        $this->set(compact('users'));

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

    /**
     * delete method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */

    public function delete($id = null)
    {
        //$token = $this->request->getParam('_csrfToken');
        //debug($token);exit();

        
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);

        //debug($user);exit();

        if ($this->Users->delete($user))
        {
            $this->Flash->success('El usuario ha sido eliminado.');
        }
        else
        {
            $this->Flash->error('El usuario no pudo ser eliminado. Por favor, intente nuevamente.');
        }
        return $this->redirect(['action' => 'index']);
        
    
    }



}
